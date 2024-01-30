<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Lead;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeadDashboard extends Component
{
    private static $session_prefix = '_leads_dashboard_';
    public $data = [];

    //general list vars
    public $filtersActive = 0;
    public $message_bar = '';

    //filters
    public $search_filter;

    public function mount()
    {
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);
        $this->message_bar = '';
    }

    public function data()
    {
        $data = [];

        //totals
        $lead_totals = ['TODAY','YESTERDAY','THIS_WEEK','LAST_WEEK','LAST_MONTH','YTD'];
        foreach($lead_totals as $total) {
            $data['Totals'][] = (object) [
                'tpl' => 'total',
                'size' => "col-md-2",
                'colour' => "primary",
                'title' => "Leads",
                'date' => $total,
                'icon' => "fal fa-star",
                'data'  => (object) [
                    'current' => Lead::whereRaw("1=1".Lead::date_filter_query('created_at',$total))->count(),
                    'previous' => Lead::whereRaw("1=1".Lead::date_filter_query('created_at',Lead::date_filter_prev_period($total)))->count()
                ]
            ];
        }

        //top sources
        $source_stats = [];
        $sources = \App\Models\ApiKey::withCount('leads')->where('account_id', session('account_id'))->orderBy('leads_count', 'desc')->get();
        foreach($sources as $source){
            $source_stats[$source->source] = (object) [
                'current' => Lead::where('source_id', $source->id)->whereRaw("1=1".Lead::date_filter_query('created_at','THIS_MONTH'))->count(),
                'previous' => Lead::where('source_id', $source->id)->whereRaw("1=1".Lead::date_filter_query('created_at',Lead::date_filter_prev_period('THIS_MONTH')))->count()
            ];
        }
        $data['Overviews'][] = (object) [
            'tpl' => 'list',
            'size' => "col-md-6",
            'colour' => "primary",
            'title' => "Leads from Source",
            'date' => 'THIS_MONTH',
            'icon' => "fal fa-star",
            'data'  => $source_stats,
            'count' => $sources->count()
        ];

        //top users
        $user_stats = [];
        $users = \App\Models\User::withCount('leads')->where('account_id', session('account_id'))->orderBy('leads_count', 'desc')->get();
        foreach($users as $user){
            $user_stats[$user->full_name()] = (object) [
                'current' => Lead::where('user_id', $user->id)->whereRaw("1=1".Lead::date_filter_query('created_at','THIS_MONTH'))->count(),
                'previous' => Lead::where('user_id', $user->id)->whereRaw("1=1".Lead::date_filter_query('created_at',Lead::date_filter_prev_period('THIS_MONTH')))->count()
            ];
        }
        $data['Overviews'][] = (object) [
            'tpl' => 'list',
            'size' => "col-md-6",
            'colour' => "primary",
            'title' => "User Allocated Leads",
            'date' => 'THIS_MONTH',
            'icon' => "fal fa-star",
            'data'  => $user_stats,
            'count' => $users->count()
        ];


        $this->data = $data;
    }

    public function render()
    {
        $this->data();
        return view('livewire.lead-dashboard');
    }

}
