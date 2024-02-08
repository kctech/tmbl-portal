<?php

namespace App\Libraries\Azure;

use App\Libraries\SSO\Provider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Libraries\Azure\OnlineMeeting;
use \Carbon\Carbon;

class GraphConnector
{

    //Online meeting setup errors
    public const NO_ERROR = null;
    public const USER_NOT_FOUND = 1;
    public const USER_FETCH_FAILED = 3;
    public const MEETING_CREATION_FAILED = 2;
    public const DATA_MISSING = 4;
    public const NO_ATTENDEES = 8;

    //Base service URLS
    private const BASE_LOGIN_URL = 'https://login.microsoftonline.com/';
    private const BASE_GRAPH_URL = 'https://graph.microsoft.com/v1.0/';

    //Session token
    private $access_token = null;
    //Guzzle client
    private Client $client;
    private $_email = null; //interviewer email

    //holds an array of objects containing mail and id from microsoft
    //We have the user's email, but the meetings need to be requested with the microsoft object id
    private $users = [];

    public function __construct(private Provider $provider) {
        $this->client = new Client(); //Guzzle
    }


    /**
     * Gets the account credentials and requests an access token for the session
     * Can be called optionally, as anything relying on it will automatically call it
     */
    public function initialise()
    {

        $credentials = $this->provider->getCredentials();


        $url =  self::BASE_LOGIN_URL . $credentials->tenant_id . '/oauth2/v2.0/token';

        try {
            $token = json_decode($this->client->post($url, [
                'form_params' => [
                    'client_id' => $credentials->client_id,
                    'client_secret' => $credentials->client_secret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials'
                ],
            ])->getBody()->getContents());

            $this->access_token = $token->access_token;
        } catch (\Exception $exception) {
            Log::critical($exception->getMessage(),['tenant_id' => $this->provider->getTenantId()]);
            return null;
        }

        return $this->access_token;
    }

    /**
     * Gets all of the current Azure users from microsoft
     * Can be called optionally to set a specific filter on the resultset to cut out non required user accounts
     */
    public function getUsers($filter='')
    {
        $email_filter = $this->_email ?? $filter;

        if(!empty($email_filter)){
            //NOTE: reason for str_replace - escaping single quotes in email addresses
            //https://stackoverflow.com/questions/41491222/single-quote-escaping-in-microsoft-graph
            //http://docs.oasis-open.org/odata/odata/v4.0/errata02/os/complete/abnf/odata-abnf-construction-rules.txt
            //SQUOTE-in-string = SQUOTE SQUOTE ; two consecutive single quotes represent one within a string literal
            $url = 'users?$select=mail,id&$filter=startswith(mail,\'' . str_replace("'", "''", $email_filter) . '\')';
        }else{
            $url = 'users?$select=mail,id';
        }

        $data = $this->getAll('GET',$url,[],[],"value");
        foreach($data as $user) {
            if(is_null($user->mail)) continue;
            if(!empty($filter)){
                if(stripos($user->mail,$filter)!==false){
                    $this->users[] = $user;
                }
            }else{
                $this->users[] = $user;
            }
        }
        return $this->users;
    }

    /**
     * simple lookup to get the object id for a user from the results of the getUsers() call
     * Can be used on a case by case basis as it's use is automatic
     */
    public function getIdentifierByEmail(string $email) {
        $this->_email = $email;
        if(count($this->users)==0){
            $this->getUsers();
        }
        $email = Str::lower($email);

        foreach($this->users as $user){
            if(Str::lower($user->mail) == $email){
                return $user->id;
            }
        }
        return null;
    }

    /**
     * Gets presence status of a current Azure users from microsoft
     */
    public function getUserPresence($user_id)
    {
        return $this->callApi('GET', 'users/'.$user_id.'/presence');
    }

    /**
     * Gets presence status of a current Azure users from microsoft
     */
    public function getMultipleUserPresence($user_ids)
    {
        $data = $this->getAll('POST','communications/getPresencesByUserId', [
            'headers' => [
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode(['ids' => $user_ids])
        ]);
        $output = [];
        foreach(($data ?? []) as $user){
            $output[$user->id] = (object)[
                "availability" => $user->availability,
                "activity" => $user->activity,
                "statusMessage" => $user->statusMessage
            ];

        }
        return $output;
    }

    public function getUsersWithPresence(){

        //vars
        $ids = $statuses = [];
        $status_orders = [
            "Available" => 1,
            "Busy" => 2,
            "InAMeeting" => 3,
            "InACall" => 4,
            "DoNotDisturb" => 5,
            "BeRightBack" => 6,
            "Away" => 7,
            "Offline" => 8
        ];

        //get users and an array of theirs ids
        $users = $this->getUsers();
        foreach($users as $user){
            $ids[] = $user->id;
        }
        //get all user id's presences - faster than individual as avoids multiple api calls per user
        $presences = $this->getMultipleUserPresence($ids);
        foreach($users as $user){
            if ($presences[$user->id]->activity == "PresenceUnknown") { $presences[$user->id]->activity = "Offline"; }
            $statuses[strtolower($user->mail)] = $presences[$user->id]->activity;
        }

        //sort users by online status
        uasort($statuses, function($a,$b) use ($status_orders){
            return (($status_orders[$a] ?? 10)) <=> (($status_orders[$b] ?? 10));
        });

        return $statuses;
    }

    public function createOnlineMeeting(OnlineMeeting $online_meeting, $is_online_meeting = true) {

        //Reset the error flag,as the same object might be re-passed over and over
        $online_meeting->error = self::NO_ERROR;

        if(count($online_meeting->getAttendees())==0) {
            $online_meeting->error = self::NO_ATTENDEES;
            return $online_meeting;
        }

        // app/Helpers/Validation
        if(anyEmpty([
            $online_meeting->subject,
            $online_meeting->description,
            $online_meeting->date,
            $online_meeting->time,
            $online_meeting->duration
        ])) {
            $online_meeting->error = self::DATA_MISSING;
            return $online_meeting;
        }

        // Look up the object ID for the user if it's not already set
        if (empty(($online_meeting->object_id ?? null))) {
            try {
                $online_meeting->object_id = ($this->getIdentifierByEmail($online_meeting->email) ?? "");
            } catch (\Exception $exception) {
                $online_meeting->error = self::USER_FETCH_FAILED;
                Log::critical($exception->getMessage(), ['tenant_id' => $this->provider->getTenantId()]);
                return $online_meeting;
            }
            if (empty($online_meeting->object_id)) {
                //no user found as an azure user to attach the meeting to
                $online_meeting->error = self::USER_NOT_FOUND;
                return $online_meeting;
            }
        }

        $json = [];
        $json['subject'] = $online_meeting->subject;
        $json['body'] = [
            'contentType' => 'HTML',
            'content' => $online_meeting->description
        ];

        $start_datetime = "{$online_meeting->date}T{$online_meeting->time}:00";
        $start_object = Carbon::parse($start_datetime);
        $end_object = $start_object->addMinutes($online_meeting->duration);
        $end_datetime = $end_object->format('Y-m-d\TH\:i\:\0\0');

        $json['start'] = [
            'dateTime' => $start_datetime,
            'timeZone' => 'Europe/London'
        ];

        $json['end'] = [
            'dateTime' => $end_datetime,
            'timeZone' => 'Europe/London'
        ];

        $json['attendees'] = $online_meeting->getAttendees(); //This is preformatted

        $json['allowNewTimeProposals'] = false;
        $json['isOnlineMeeting'] = $is_online_meeting;
        if(!$is_online_meeting){
            $json['location'] = [
                'displayName' => $online_meeting->location,
                'locationType' => 'default'
            ];
        }else{
            $json['onlineMeetingProvider'] = 'teamsForBusiness';
        }
        $json['isOrganizer'] = true;

        try {

            $options = [
                'json' => $json,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json'
                ]
            ];

            //object_id identifies the reach user. it is the GUID of the person in the remote azure directory
            $url = self::BASE_GRAPH_URL . "users/{$online_meeting->object_id}/calendar/events";

            $response = $this->client->post($url, $options);

            $data = json_decode($response->getBody()->getContents());

            $online_meeting->meeting_id = $data->id;
            $online_meeting->meeting_url = $data->onlineMeeting->joinUrl;

        } catch (\Exception $exception) {
            $online_meeting->error = self::MEETING_CREATION_FAILED;
            Log::critical($exception->getMessage(),['tenant_id' => $this->provider->getTenantId()]);
        }

        return $online_meeting;

    }

    private function getAll($method, $url, $options=[], $currentData=[],$key="value"){
        $data = $this->callApi($method, $url, $options);
        $currentData = array_merge($currentData,($data->{$key} ?? []));
        if(!empty(($data->{"@odata.nextLink"} ?? null))){
            return $this->getAll($method, $data->{"@odata.nextLink"}, $options, $currentData,$key);
        }else{
            return $currentData;
        }
    }

    private function callApi($method,$url,$options=[]){
        if (is_null($this->access_token)) {
            $this->initialise();
        }
        try {
            $base_options = ['headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                    'Accept'        => 'application/json',
                ]
            ];

            $call_options = array_merge_recursive($base_options, $options);

            $response = $this->client->request($method, self::BASE_GRAPH_URL.(str_ireplace(self::BASE_GRAPH_URL,'',$url)), $call_options);

            //return $response;
            return json_decode($response?->getBody()?->getContents() ?? '');

        } catch (\Exception $exception) {
            Log::critical($exception->getMessage(),['tenant_id' => $this->provider->getTenantId()]);
            return null;
        }
    }

}
