<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Libraries\MABApi;
use App\Libraries\Azure\GraphConnector;
use App\Libraries\SSO\AzureProvider;
use App\Libraries\Azure\OnlineMeeting;

use App\Models\Lead;
use App\Models\LeadEvent;
use App\Models\LeadChaser;
use App\Models\PortalCache;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadContact extends Component
{
    private static $session_prefix = '_lead_contact_';
    protected $calendar = [];
    protected $availability = [];
    protected $cache_date = null;
    protected $isLoaded = false;

    //deferred loading
    public $readyToLoad = false;

    //general list vars
    public $message_bar = '';
    public $lead_id = null;
    public $lead = null;

    public $weeks = 4;
    public $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    public $start_hour = 6;
    public $end_hour = 23;

    public $selected_adviser = null;
    public $selected_week = null;
    public $selected_date = null;
    public $selected_time = null;
    public $lead_notes = '';

    public $contact_schedule = [];

    public function mount($lead_id)
    {
        $this->lead_id = $lead_id;
        $this->lead = Lead::where('id',$lead_id)->first();

        if(is_null($this->lead)){
            $this->skipRender();
            session()->flash('alert-danger','Unable to load lead ID '.$this->lead_id);
            return $this->redirectRoute('leads.table');
        }

        $this->lead_notes = json_decode($this->lead->data)->contact_notes ?? '';

        $this->selected_adviser = User::where('id',session('user_id'))->first()->emailAddress;

        $this->contact_schedule = LeadChaser::where('method','email')->where('status',LeadChaser::ACTIVE)->get();
    }

    public function loadData()
    {
        $this->readyToLoad = true;
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);
        $this->message_bar = '';
    }

    public function select_slot($date, $hour)
    {
        //$this->skipRender();
        $this->selected_date = $date;
        $this->selected_time = $hour;
        //Carbon::createFromFormat("Y-m-d H:i", $date." ".$hour)->format("d-m-Y H:i");
        $this->emit('updated',['message'=>'Selected: '.$this->selected_date .' '. $this->selected_time]);
    }

    private function buildCalendar(){
        $calendar = [];
        $comparison_datetime = Carbon::now()->startOfWeek()->startOfDay();
        for($w=1; $w<=$this->weeks; $w++){
            $calendar[$w] = [];
            switch($w){
                case 1:
                    $title = 'This Week';
                    break;
                case 2:
                    $title = 'Next Week';
                    break;
                default:
                    $title = 'Week '. $w;
            }
            $calendar[$w]['title'] = $title;
            $calendar[$w]['start_date'] = $comparison_datetime->copy()->format("d/m/Y");
            $calendar[$w]['slots'] = 0;
            $calendar[$w]['available_slots'] = 0;
            foreach($this->days as $day){
                $calendar[$w]['days'][$day] = [
                    'date' => $comparison_datetime->copy()->format("Y-m-d"),
                    'is_past' => $comparison_datetime->copy()->endOfDay()->isPast(),
                    'hours' => []
                ];
                for($h=$this->start_hour; $h<=$this->end_hour; $h++){
                    $calendar[$w]['days'][$day]['hours'][$h] = [
                        'availability' => 0,
                        'is_past' => $comparison_datetime->copy()->startOfDay()->addHours($h)->isPast()
                    ];
                }
                $comparison_datetime->addDay();
            }
        }

        return $calendar;
    }

    private function data(){

        if($this->readyToLoad){

            $cache = PortalCache::where('cache_key','azure_calendars')->orderBy('updated_at','desc')->first();
            $this->cache_date = $cache->updated_at;
            $adviser_availability = (array) json_decode($cache->data);
            $this->availability = $adviser_availability;
            $calendar = $this->buildCalendar();

            foreach($adviser_availability as $adviser => $availability){

                //remove adviser from list if no current status info
                if(strtolower($this->selected_adviser) != strtolower($adviser)){
                    continue;
                }

                //get adviser working days & times
                $work_days = ($availability->workingHours->daysOfWeek ?? []);
                if(!is_null($availability->workingHours->startTime ?? null)){
                    $work_start_time = intval(Carbon::parse(date("Y-m-d")." ".$availability->workingHours->startTime)->format('g'));
                }else{
                    $work_start_time = 8;
                }
                if(!is_null($availability->workingHours->endTime ?? null)){
                    $work_end_time = intval(Carbon::parse(date("Y-m-d")." ".$availability->workingHours->endTime)->format('H'));
                }else{
                    $work_end_time = 17;
                }

                //dump("working hours:".$work_start_time." - ".$work_end_time);

                //loop weeks
                $comparison_datetime = Carbon::now()->startOfWeek()->startOfDay()->addHours($this->start_hour);
                foreach($calendar as $week_number => $week){
                    //loop days
                    foreach($week['days'] as $day => $date){
                        //if adviser works on these days
                        if(in_array(strtolower($day), $work_days)){
                            //dump("working day:".$day);
                            //dd($hours);
                            //loop hours
                            foreach($date['hours'] as $hour => $available_count){
                                $comparison_datetime->startOfDay()->addHours($hour);
                                //if hour within working hours
                                if($hour >= $work_start_time && $hour < $work_end_time){
                                    //dump("working hour:".$hour);
                                    //if no meetings - assume available
                                    if(empty($availability->scheduleItems)){
                                        ++$calendar[$week_number]['days'][$day]['hours'][$hour]['availability'];
                                        ++$calendar[$week_number]['slots'];
                                        if(!$date['is_past']){
                                            ++$calendar[$week_number]['available_slots'];
                                        }
                                    }else{
                                        //assume available
                                        ++$calendar[$week_number]['days'][$day]['hours'][$hour]['availability'];
                                        ++$calendar[$week_number]['slots'];
                                        if(!$date['is_past']){
                                            ++$calendar[$week_number]['available_slots'];
                                        }
                                        //loop meetings
                                        foreach($availability->scheduleItems as $meeting){
                                            //if meeting is the current calendar day
                                            $compare = $comparison_datetime->copy();
                                            $meeting_date_start = Carbon::parse($meeting->start->dateTime, $meeting->start->timeZone); //'Y-m-d\TH:i:s.u'
                                            //dump("compare:".$compare->format('c')." - ".$meeting_date_start->format('c'));
                                            if($meeting_date_start->isSameDay($compare)){
                                                //dump("clash:".$meeting_date_start->format('c'));
                                                //is meeting not clashing with current hour/day
                                                $meeting_date_end = Carbon::parse($meeting->end->dateTime, $meeting->end->timeZone); //'Y-m-d\TH:i:s.u'
                                                if($compare->between($meeting_date_start,$meeting_date_end)){
                                                    //remove availability
                                                    //dump("clash:".$meeting_date_start->format('c'));
                                                    if($calendar[$week_number]['days'][$day]['hours'][$hour]['availability'] > 0){
                                                        --$calendar[$week_number]['days'][$day]['hours'][$hour]['availability'];
                                                        --$calendar[$week_number]['slots'];
                                                        if(!$date['is_past']){
                                                            --$calendar[$week_number]['available_slots'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $comparison_datetime->addDay();
                    }
                }
            }

            if(is_null($this->selected_week)){
                foreach($calendar as $num => $week){
                    if($week['available_slots'] != 0){
                        $this->selected_week = $num;
                        break;
                    }
                }
            }

            $this->calendar = $calendar;
            $this->isLoaded = true;
        }

    }

    public function mark_as_contacted(){

        $lead_data = json_decode($this->lead->data);
        $lead_data->contact_notes = $this->lead_notes;
        $this->lead->data = json_encode($lead_data);
        $this->lead->last_contacted_at = date("Y-m-d H:i:s");
        //$this->lead->status = Lead::CONTACT_ATTEMPTED;
        ++$this->lead->contact_count;
        $this->lead->save();
        //$this->emit('updated', ['message' => "Lead status updated [" . $this->lead_id . "]"]);

        $this->lead->events()->create([
            'account_id' => $this->lead->account_id,
            'user_id' => session('user_id'),
            'event_id' => LeadEvent::MANUAL_CONTACT_ATTEMPTED,
            'information' => $this->lead_notes
        ]);

        $this->skipRender();
        session()->flash('alert-success','Marked lead ID '.$this->lead_id.' as contacted');
        return $this->redirectRoute('leads.table');
    }

    public function mark_as_pause_contacting(){

        $lead_data = json_decode($this->lead->data);
        $lead_data->contact_notes = $this->lead_notes;
        $this->lead->data = json_encode($lead_data);
        $this->lead->last_contacted_at = date("Y-m-d H:i:s");
        $this->lead->status = Lead::PAUSE_CONTACTING;
        ++$this->lead->contact_count;
        $this->lead->save();
        //$this->emit('updated', ['message' => "Lead status updated [" . $this->lead_id . "]"]);

        $this->lead->events()->create([
            'account_id' => $this->lead->account_id,
            'user_id' => session('user_id'),
            'event_id' => LeadEvent::MANUAL_CONTACT_ATTEMPTED,
            'information' => $this->lead_notes
        ]);

        $this->skipRender();
        session()->flash('alert-success','Lead ID '.$this->lead_id.' will no longer recieve automatic messages');
        return $this->redirectRoute('leads.table');
    }

    public function allocate_and_transfer(){

        $lead_data = json_decode($this->lead->data);
        $lead_data->contact_notes = $this->lead_notes;
        $this->lead->data = json_encode($lead_data);
        $this->lead->last_contacted_at = date("Y-m-d H:i:s");
        $this->lead->status = Lead::CONTACT_ATTEMPTED;
        ++$this->lead->contact_count;
        if($this->lead->save()){

            $this->lead->events()->create([
                'account_id' => $this->lead->account_id,
                'user_id' => session('user_id'),
                'event_id' => LeadEvent::MANUAL_CONTACT_ATTEMPTED,
                'information' => $this->lead_notes
            ]);

            $adviser = \App\Models\User::where('email',$this->selected_adviser)->first() ?? null;
            if(is_null($adviser->mab_id)){
                $mab = new \App\Libraries\MABApi(false,'introducers:read:authorizedfirms',true);
                $mab_id = $mab->getAdviser($adviser->full_name());
                if(!is_null($mab_id)){
                    $adviser->mab_id = $mab_id;
                    $adviser->save();
                }
            }

            if(is_null($adviser->mab_id)){
                $this->emit('error', ['message' => "Unable to find user '".$adviser->full_name()."' in MAB portal."]);
                return;
            }

            list($mab_firm_id,$mab_branch_id,$mab_user_id) = explode("|",$adviser->mab_id);

            $data = [
                //"mortgageBasis" => 1,
                //"prospectType" => 1,
                "contactMethodTypeId" => 4,
                //"groupId" => 1,
                "introducerId" => "6388e19c-feb1-4df2-8bcc-704a090999b0",
                "introducerBranchId" => "1061f882-a004-4b6c-84d4-ab5bb9a03826",
                //"introducerStaffId" => "03580d2b-4aee-4983-b904-6016f142d9e6",
                //"groupEmailAddress" => "Devnoreply1@mab.org.uk",
                //"submittedByName" => "Create Local Lead Referer",
                "dateTimeGdprConsent" => \Carbon\Carbon::parse($this->lead->created_at)->format("Y-m-d\TH:i:s\Z"),
                //"mortgagePurpose" => 1,
                //"currentBuyingPosition" => 2,
                //"howCanWeHelp" => 1,
                //"plotNumber" => 42,
                //"foundFutureHome" => false,
                //"totalGrossSalary" => 80000,
                //"propertyValue" => 300000,
                //"deposit" => 30000,
                "distributionType" => 3,
                //"distributionGroupId" => "5a4aaeb5-484e-4018-a2e6-a2e988fef65e",
                //"leadReferralType" => 0,
                //"timeOfReferral" => "2023-02-06T09:17:18.684Z",
                //"creationDate" => "2023-02-06T09:17:18.684Z",
                "notes" => $this->lead_notes,
                //"consenter" => 0,
                "customers" => [
                    [
                        //"id" => "feecfecc-8cad-4175-a781-37ea4cb1e8b2",
                        //"title" => 1,
                        "firstName" => $this->lead->first_name,
                        "lastName" => $this->lead->last_name,
                        "emailAddress" => $this->lead->email_address,
                        "telephoneNumber" => $this->lead->contact_number,
                        "dateOfBirth" => "1900-01-01T00:00:00Z",
                        //"gender" => 1,
                        //"maritalStatus" => 2,
                        "index" => 0,
                        //"employmentStatus" => 1,
                        //"workedLongerThan6MonthsForCurrentEmployer" => true,
                        //"retirementAge" => 65,
                        //"hasActiveUserAccount" => false,
                        //"midasProClientId" => null
                    ]
                ],
                "allocatedFirmId" => $mab_firm_id,
                "allocatedFirmBranchId" => $mab_branch_id,
                "allocatedAdviserId" => $mab_user_id,
                //"shouldSendCustomerInviteEmail" => true,
                //"midasProClientFolderID" => null,
                "customFields" => [
                    "Portal_Lead_ID" => $this->lead->id,
                ]
            ];

            $mab = new MABApi(false);
            $mab_lead_response = $mab->newLead($data);
            if($mab_lead_response->status){
                $this->emit('updated', ['message' => "Lead allocated [" . $this->lead_id . "]"]);
                $this->lead->status = Lead::TRANSFERRED;
                $this->lead->user_id = $adviser->id;
                $this->lead->allocated_at = date("Y-m-d H:i:s");
                $this->lead->transferred_at = date("Y-m-d H:i:s");
                $this->lead->save();

                $this->lead->events()->create([
                    'account_id' => $this->lead->account_id,
                    'user_id' => session('user_id'),
                    'event_id' => LeadEvent::TRANSFERRED_TO_MAB,
                    'information' => json_encode($mab_lead_response)
                ]);

                //Try and instantiate an Azureprovider instance for teams. It'll throw if there's no account config for it or it's not enabled
                try {
                    $azure = new AzureProvider($this->lead->account_id, 'TEAMS', true);
                    $meeting_date = Carbon::createFromFormat("Y-m-d H:i", $this->selected_date." ".$this->selected_time);

                    //TEAMS
                    $meeting = new OnlineMeeting($adviser->email);
                    $meeting->subject = "New Lead: ".$this->lead->full_name();
                    $meeting->description = "Testing Azure/Teams API credentials";
                    $meeting->date = $meeting_date->format("Y-m-d");
                    $meeting->time = $meeting_date->startOfHour()->addHour()->format("H:i");
                    $meeting->duration = 60;
                    $meeting->addAttendee($this->lead->full_name(), $this->lead->email_address);

                    $graph = new GraphConnector($azure);
                    $confirmation = $graph->createOnlineMeeting($meeting);
                    switch($confirmation->error){
                        case null:
                            $err = null;
                            break;
                        case 1:
                            $err = "User ".$this->selected_adviser." not found in Miscosoft AD.";
                            break;
                        case 2:
                            $err = "Failed to create Teams meeting";
                            break;
                        case 3:
                            $err = "User ".$this->selected_adviser." couldn't be fetched form Miscosoft AD.";
                            break;
                        case 4:
                            $err = "Missing required Information";
                            break;
                        case 8:
                            $err = "No attendees added";
                            break;
                        default:
                            $err = null;
                    }
                    if(!is_null($err)){
                        $this->emit('error', ['message' => "Teams create failed: ".$err]);
                    }else{

                        $this->lead->events()->create([
                            'account_id' => $this->lead->account_id,
                            'user_id' => session('user_id'),
                            'event_id' => LeadEvent::BOOKED_TEAMS_MEETING,
                            'information' => json_encode(["adviser"=>strtolower($adviser->email),"date"=>$meeting_date->format("Y-m-d H:i:s")])
                        ]);

                        $this->skipRender();
                        session()->flash('alert-success','Lead ID '.$this->lead_id.' transferred to '.$this->selected_adviser." and a Teams meeting booked at ".$meeting_date->format("d/m/Y H:i"));
                        return $this->redirectRoute('leads.manager');
                    }
                } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
                    Log::critical($exception->getMessage(),['tenant_id' => $this->provider->getTenantId()],json_encode($meeting));
                    //no teams setup
                    $this->skipRender();
                    session()->flash('alert-success','Lead ID '.$this->lead_id.' transferred to '.$this->selected_adviser." and a Teams meeting couldn't be booked");
                    return $this->redirectRoute('leads.manager');
                }
            }else{
                $this->emit('error', ['message' => "Unable to send to MAB Portal [" . $this->lead_id . "]"]);
            }

        }else{
            $this->emit('error', ['message' => "Cant load lead [" . $this->lead_id . "]"]);
        }
    }

    public function render()
    {
        $this->data();
        return view('livewire.lead-contact',[
            'cache_date' => $this->cache_date,
            'calendar' => $this->calendar,
            'availability' => $this->availability,
            'isLoaded' => $this->isLoaded
        ]);
    }

}
