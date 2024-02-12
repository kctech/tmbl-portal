<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Lead;
use App\Models\PortalCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeadContact extends Component
{
    private static $session_prefix = '_leads_dashboard_';
    public $data = [];

    //deferred loading
    public $readyToLoad = false;
    public $isLoaded = false;

    //general list vars
    public $message_bar = '';
    public $calendar = [];
    public $advisers = [];
    public $adviser_list = [];

    public $weeks = 3;
    public $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    public $start_hour = 6;
    public $end_hour = 23;

    public function mount()
    {
        $calendar = [];
        $comparison_datetime = Carbon::now()->startOfWeek()->startOfDay();
        for($w=1; $w<=$this->weeks; $w++){
            $calendar['Week '. $w] = [];
            foreach($this->days as $day){
                $calendar['Week '. $w][$day] = [
                    'date' => $comparison_datetime->copy()->format("Y-m-d"),
                    'is_past' => $comparison_datetime->copy()->endOfDay()->isPast(),
                    'hours' => []
                ];
                for($h=$this->start_hour; $h<=$this->end_hour; $h++){
                    $calendar['Week '. $w][$day]['hours'][$h] = [
                        'availability' => 0,
                        'is_past' => $comparison_datetime->copy()->startOfDay()->addHours($h)->isPast()
                    ];
                }
                $comparison_datetime->addDay();
            }
        }

        $this->calendar = $calendar;
        $this->adviser_list = buildAdvisersList();
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

    private function data(){

        if($this->readyToLoad){

            $cache = PortalCache::where('cache_key','azure_calendars')->first()->data;
            $adviser_availabiltiy = (array) json_decode($cache);
            $this->data = $adviser_availabiltiy;
            $calendar = $this->calendar;

            foreach($adviser_availabiltiy as $adviser => $availabilty){
                //for each selected adviser (or all)
                if(empty($this->advisers) || in_array(strtolower($adviser),$this->advisers)){
                    //get adviser working days & times
                    $work_days = ($availabilty->workingHours->daysOfWeek ?? []);
                    if(!is_null($availabilty->workingHours->startTime ?? null)){
                        $work_start_time = intval(Carbon::parse(date("Y-m-d")." ".$availabilty->workingHours->startTime)->format('g'));
                    }else{
                        $work_start_time = 8;
                    }
                    if(!is_null($availabilty->workingHours->endTime ?? null)){
                        $work_end_time = intval(Carbon::parse(date("Y-m-d")." ".$availabilty->workingHours->endTime)->format('H'));
                    }else{
                        $work_end_time = 17;
                    }

                    //dump("working hours:".$work_start_time." - ".$work_end_time);

                    //loop weeks
                    $comparison_datetime = Carbon::now()->startOfWeek()->startOfDay()->addHours($this->start_hour);
                    foreach($calendar as $week_number => $week){
                        //loop days
                        foreach($week as $day => $date){
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
                                        if(empty($availabilty->scheduleItems)){
                                            ++$calendar[$week_number][$day]['hours'][$hour]['availability'];
                                        }else{
                                            //assume available
                                            ++$calendar[$week_number][$day]['hours'][$hour]['availability'];
                                            //loop meetings
                                            foreach($availabilty->scheduleItems as $meeting){
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
                                                        if($calendar[$week_number][$day]['hours'][$hour]['availability'] > 0){
                                                            --$calendar[$week_number][$day]['hours'][$hour]['availability'];
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
            }

            $this->calendar = $calendar;
            $this->isLoaded = true;
        }

    }

    public function render()
    {
        $this->data();
        return view('livewire.lead-contact');
    }

}
