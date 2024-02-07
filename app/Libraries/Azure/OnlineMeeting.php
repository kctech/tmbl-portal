<?php

namespace App\Libraries\Azure;

class OnlineMeeting {

    //Meeting fields
    public string $email='';
    public string $object_id = '';
    public string $subject = '';
    public string $location = '';
    public string $description = '';
    public string $date = ''; //ISO date format Y-m-d EG 2023-03-28
    public string $time = ''; //24h hours and minutes only H:i  EG 14:00
    public int $duration=0; //int minutes
    private array $attendees = []; //Each attendee needs to be in a wrapped array format, so helpers are used to make them

    //Error flag which can be set to any of the GraphConnector class errors
    public ?int $error;
    //On success this property is updated to reflect the joining link for the meeting
    public $meeting_url;
    //This is the MS GUID for the instance of the meeting
    public $meeting_id;

    public function __construct(string $email='',string $object_id='') {
        //We need at least one of the two parameters to get the ID
        if(empty($email) && empty($object_id)){
            throw new \Exception('Either an email address or object id must be provided');
        }
        $this->email = $email;
        $this->object_id = $object_id;
    }

    /**
     * Attendees do NOT include the lead interviewer, as their email address or object id should have been provided in the constructor.
     * They are the owner of the meeting and so aren't treated as attending as an invitee.
     */
    public function addAttendee(string $name,string $email) {
        $this->attendees[] = ['emailAddress' => ['address'=>$email,'name'=>$name],'type'=>'required'];
    }

    /**
     * This should be used to get the attendees as they're marked private to prevent direct access to the array because of the structure required
     */
    public function getAttendees() {
        return $this->attendees;
    }

}
