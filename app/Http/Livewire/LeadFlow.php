<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Lead;
use App\Models\LeadChaser;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeadFlow extends Component
{
    private static $session_prefix = '_leads_flow_';
    public $data = [];

    //general list vars
    public $filtersActive = 0;
    public $message_bar = '';
    public $chase_straties = [];

    //filters
    public $chase_strategy = 1;

    public function mount()
    {
        //$this->chase_strategy = session(self::$session_prefix . 'chase_strategy') ?? '';
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);
        $this->message_bar = '';
    }

    public function data()
    {
        $data = [];

        $chaser = LeadChaser::where('account_id',session('account_id'))->where('status',LeadChaser::ACTIVE)->where('strategy_id',$this->chase_strategy)->get();
        foreach($chaser as $c) {
            $data[] = (object) [
                'colour' => "primary",
                'info' => $c,
                'data'  => Lead::with('source')->where('strategy_id',$c->strategy_id)->where('strategy_position_id',$c->id)->whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED,Lead::CLAIMED,Lead::PAUSE_CONTACTING])->orderBy('id','desc')->get()->toArray()
            ];
        }

        $this->data = $data;
    }

    public function render()
    {
        $this->data();
        return view('livewire.lead-flow');
    }

}
