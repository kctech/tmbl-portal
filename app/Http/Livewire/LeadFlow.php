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
    public $flow_type = 'user';
    public $flow_include = 'unclaimed';
    public $contact_route = null;

    //filters
    public $chase_strategy = 1;

    public function mount()
    {
        //$this->chase_strategy = session(self::$session_prefix . 'chase_strategy') ?? '';
        $this->flow_include = session(self::$session_prefix . 'flow_include') ?? 'unclaimed';

        if(auth()->user()->can('lead_admin') && $this->flow_include == 'unclaimed'){
            $this->flow_type = 'unclaimed';
            $this->contact_route = 'leads.manager-contact';
        }elseif(auth()->user()->can('lead_admin') && $this->flow_include == 'all'){
            $this->flow_type = 'admin';
            $this->contact_route = 'leads.manager-contact';
        }else{
            $this->flow_type = 'user';
            $this->contact_route = 'leads.contact';
        }
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);
        $this->message_bar = '';

        if($prop == 'flow_include'){
            if(auth()->user()->can('lead_admin') && $this->flow_include == 'unclaimed'){
                $this->flow_type = 'unclaimed';
            }elseif(auth()->user()->can('lead_admin') && $this->flow_include == 'all'){
                $this->flow_type = 'admin';
            }else{
                $this->flow_type = 'user';
            }
        }
    }

    public function data()
    {
        $data = [];

        $chaser = LeadChaser::where('account_id',session('account_id'))->where('status',LeadChaser::ACTIVE)->where('strategy_id',$this->chase_strategy)->get();
        foreach($chaser as $c) {
            if($this->flow_type == 'admin'){
                $lead_data = Lead::with('source','owner')->where('strategy_id',$c->strategy_id)->where('strategy_position_id',$c->id)->whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED,Lead::PAUSE_CONTACTING,Lead::CLAIMED])->orderBy('id','asc');
            }elseif($this->flow_type == 'unclaimed'){
                $lead_data = Lead::with('source','owner')->where('strategy_id',$c->strategy_id)->where('strategy_position_id',$c->id)->whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED,Lead::PAUSE_CONTACTING]);
                //if after first 2 steps, show all leads, inclusing claimed
                if($c->chase_order < 3 ){
                    $lead_data = $lead_data->whereNull('user_id');
                }
                $lead_data = $lead_data->orderBy('id','asc');
            }else{
                $lead_data = Lead::with('source')->where('strategy_id',$c->strategy_id)->where('strategy_position_id',$c->id)->whereIn('status',[Lead::CLAIMED,Lead::CONTACT_ATTEMPTED,Lead::PAUSE_CONTACTING])->where('user_id', session('user_id'))->orderBy('id','asc');
            }
            $data[] = (object) [
                'colour' => "primary",
                'info' => $c,
                'data'  => $lead_data->get()->toArray()
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
