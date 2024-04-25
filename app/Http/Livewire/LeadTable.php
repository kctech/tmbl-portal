<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Lead;
use App\Models\LeadChaser;

use App\Libraries\MABApi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LeadTable extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private static $session_prefix = '_leads_list_';
    private $data;
    public $stats = [];

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';
    public $claimable_id = 0;

    //filters
    public $sort_order;
    public $search_filter;
    public $lead_status = '';

    public $lead_id = null;
    public $lead = null;
    public $contact_schedule = [];

    public function mount()
    {
        $this->lead_status = session(self::$session_prefix . 'lead_status') ?? '';
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
        $this->sort_order = session(self::$session_prefix . 'sort_order') ?? '';

        $this->contact_schedule = LeadChaser::where('method','email')->where('status',LeadChaser::ACTIVE)->get();
        $this->claimable_id = Lead::where(function($q){
            $q->whereNull('user_id')->orWhere('user_id',session('user_id'));
        })->whereNotIn('status',[Lead::ARCHIVED,Lead::TRANSFERRED])->orderBy('id', 'asc')->first()->id ?? 0;

        $this->stats();
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);

        $this->message_bar = '';
    }

    public function data()
    {
        $this->filtersActive = 0;
        $query = Lead::where(function($q){
            $q->whereNull('user_id')->orWhere('user_id',session('user_id'));
        });

        //remove archived
        $query = $query->whereNotIn('status',[Lead::ARCHIVED,Lead::TRANSFERRED]);

        if ($this->sort_order != '') {
            ++$this->filtersActive;
            switch ($this->sort_order) {
                case 'id_desc':
                    $query = $query->orderBy('id', 'desc');
                    break;
                case 'id_asc':
                    $query = $query->orderBy('id', 'asc');
                    break;
                default:
                    $query = $query->orderBy('id', 'asc');
                    break;
            }
        } else {
            $query = $query->orderBy('id', 'asc');
        }

        if ($this->lead_status != '') {
            $query = $query->where('status', $this->lead_status);
        }

        if (!empty(trim($this->search_filter))) {
            ++$this->filtersActive;
            $query = $query->where(function($q){
                $q->where('first_name', 'like', '%' . $this->search_filter . '%')->orWhere('last_name', 'like', '%' . $this->search_filter . '%')->orWhere('email_address', 'like', '%' . $this->search_filter . '%');
            });
        }

        $this->data = $query;
    }

    public function stats (){
        $stats = [];

        //totals
        $stats['Totals'][] = (object) [
            'link' => route('leads.table',['lead_status' => Lead::PROSPECT]),
            'tpl' => 'total',
            'size' => "col-md-4",
            'colour' => null,
            'title' => "New Leads Available",
            'date' => null,
            'icon' => "far fa-alarm-clock",
            'data'  => (object) [
                'current' => Lead::whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED])->whereNull('user_id')->count()
                //'current' => Lead::whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED])->whereDoesntHave('events', function($q){ $q->where('event_id', LeadEvent::MANUAL_CONTACT_ATTEMPTED); })->count()
            ]
        ];

        $stats['Totals'][] = (object) [
            'tpl' => 'total',
            'size' => "col-md-4",
            'title' => "Contacted Leads",
            'date' => null,
            'icon' => "far fa-phone",
            'data'  => (object) [
                'current' => Lead::where('status',Lead::CONTACT_ATTEMPTED)->where('user_id',session('user_id'))->count()
                //'current' => Lead::whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED])->whereDoesntHave('events', function($q){ $q->where('event_id', LeadEvent::MANUAL_CONTACT_ATTEMPTED); })->count()
            ]
        ];

        $stats['Totals'][] = (object) [
            'tpl' => 'total',
            'size' => "col-md-4",
            'title' => "Transferred Leads",
            'date' => 'THIS_MONTH',
            'icon' => "far fa-download",
            'data'  => (object) [
                'current' => Lead::whereRaw("1=1".Lead::date_filter_query('created_at','THIS_MONTH'))->where('status',Lead::TRANSFERRED)->where('user_id',session('user_id'))->count(),
                'previous' => Lead::whereRaw("1=1".Lead::date_filter_query('created_at',Lead::date_filter_prev_period('THIS_MONTH')))->where('status',Lead::TRANSFERRED)->where('user_id',session('user_id'))->count()
            ]
        ];

        $this->stats = $stats;
    }

    public function filter()
    {
        session()->put(self::$session_prefix . 'lead_status', $this->lead_status);
        session()->put(self::$session_prefix . 'search_filter', $this->search_filter);
        session()->put(self::$session_prefix . 'sort_order', $this->sort_order);
        $this->emit('updated',['message'=>'Filtering applied']);
    }

    public function close()
    {
        $this->message_bar = '';
        $this->lead_id = null;
        $this->lead = null;
    }

    public function info($id)
    {
        $this->message_bar = '';
        $base = Lead::find($id);
        if($base){
            $this->lead_id = $id;
            $this->lead = $base;
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $id . "]"]);
        }
    }

    public function deallocate($lead_id){
        $lead = Lead::find($lead_id);
        if($lead){
            $lead->status = Lead::PROSPECT;
            $lead->user_id = null;
            $lead->allocated_at = null;
            $lead->save();
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $lead_id . "]"]);
        }
    }

    public function allocate($lead_id){
        $lead = Lead::find($lead_id);
        if($lead){
            if($lead->status == Lead::PROSPECT){
                $lead->status = Lead::CLAIMED;
                $lead->user_id = session('user_id');
                $lead->allocated_at = date('Y-m-d H:i:s');
                $lead->save();
            }else{
                $this->emit('error', ['message' => "Sorry, this lead has already been claimed"]);
            }
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $lead_id . "]"]);
        }
    }

    public function transfer($lead_id){
        $lead = Lead::find($lead_id);
        if($lead){

                $adviser = \App\Models\User::where('id',session('user_id'))->first() ?? null;
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
                    "contactMethodTypeId" => 4,
                    "introducerId" => "6388e19c-feb1-4df2-8bcc-704a090999b0",
                    "introducerBranchId" => "1061f882-a004-4b6c-84d4-ab5bb9a03826",
                    //"groupEmailAddress" => "Devnoreply1@mab.org.uk",
                    //"submittedByName" => "Create Local Lead Referer",
                    "dateTimeGdprConsent" => \Carbon\Carbon::parse($lead->created_at)->format("Y-m-d\TH:i:s\Z"),
                    //"mortgagePurpose" => 1,
                    //"currentBuyingPosition" => 2,
                    //"howCanWeHelp" => 1,
                    //"plotNumber" => 42,
                    //"foundFutureHome" => false,
                    //"totalGrossSalary" => 80000,
                    //"propertyValue" => 300000,
                    //"deposit" => 30000,
                    "distributionType" => 3,
                    "notes" => "From DEV TMBL Portal",
                    "customers" => [
                        [
                            //"id" => "feecfecc-8cad-4175-a781-37ea4cb1e8b2",
                            //"title" => 1,
                            "firstName" => $lead->first_name,
                            "lastName" => $lead->last_name,
                            "emailAddress" => $lead->email_address,
                            "telephoneNumber" => $lead->contact_number,
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
                    "customFields" => [
                        "Portal_Lead_ID" => $lead->id,
                    ]
                ];

                $mab = new MABApi(false);
                $mab_lead_response = $mab->newLead($data);
                if($mab_lead_response->status){
                    $this->emit('updated', ['message' => "Lead allocated [" . $lead_id . "]"]);
                    $lead->status = Lead::TRANSFERRED;
                    $lead->user_id = $adviser->id;
                    $lead->save();
                }else{
                    $this->emit('error', ['message' => "Unable to send to MAB Portal [" . $lead_id . "]"]);
                }
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $lead_id . "]"]);
        }
    }

    public function resetFilters()
    {
        $this->lead_status = '';
        $this->search_filter = '';
        $this->sort_order = '';
        session()->forget(self::$session_prefix . 'lead_status');
        session()->forget(self::$session_prefix . 'search_filter');
        session()->forget(self::$session_prefix . 'sort_order');
        $this->emit('updated', ['message' => 'Filters reset']);
    }

    public function render()
    {
        $this->data();
        $list = [];
        if($this->view == 'list'){
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }
        return view('livewire.lead-table', [
            'list' => $list
        ]);
    }

}
