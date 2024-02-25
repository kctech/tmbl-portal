<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Lead;
use App\Models\PortalCache;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeadContactOld extends Component
{
    private static $session_prefix = '_leads_dashboard_';
    public $data = [];

    //deferred loading
    public $readyToLoad = false;

    //general list vars
    public $message_bar = '';
    public $calendar = [];

    public $lead_id = null;
    public $lead = null;


    public function mount($lead_id)
    {
        $this->lead_id = $lead_id;
        $this->lead = Lead::find($lead_id);
        $weeks = 3;
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $start_hour = 6;
        $end_hour = 11;
        $calendar = [];

        for($w=1; $w<=$weeks; $w++){
            $calendar['Week'. $w] = [];
            foreach($days as $day){
                $calendar['Week'. $w][$day] = [];
                for($h=$start_hour; $h<=$end_hour; $h++){
                    $calendar['Week'. $w][$day][$h] = "No";
                }
            }
        }

        $this->calendar = $calendar;
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

        $output = '';

        if($this->readyToLoad){

            $cache = PortalCache::where('cache_key','azure_calendars')->first()->data;
            $output = $cache;

        }

        return $output;

    }

    public function render()
    {
        return view('livewire.lead-contact',
        [
            'data' => $this->data(),
        ]);
    }

}
