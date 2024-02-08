<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Lead;

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
    public $user_id = null;

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';

    //filters
    public $sort_order;
    public $search_filter;
    public $lead_status = '';

    public $lead_id = null;
    public $lead = null;
    public $advisers = [];

    public function mount()
    {
        $this->user_id = session('user_id');
        $this->lead_status = session(self::$session_prefix . 'lead_status') ?? '';
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
        $this->sort_order = session(self::$session_prefix . 'sort_order') ?? '';
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);

        $this->message_bar = '';
    }

    public function data()
    {
        $this->filtersActive = 0;
        $query = Lead::query();

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
            $this->advisers = $this->buildAdvisersList();
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $id . "]"]);
        }
    }

    private function buildAdvisersList(){
        $list = [];
        $users = [];
        $default_status = (object)[
            "availability" => "PresenceUnknown",
            "activity" => "PresenceUnknown",
            "statusMessage" => null
        ];
        $system_users = \App\Models\User::withCount('leads')->where('account_id', session('account_id'))->orderBy('leads_count', 'asc')->get();

        try {
            $azure = new \App\Libraries\SSO\AzureProvider(1, 'TEAMS', true);
        } catch (\App\Exceptions\AccountNotConfiguredException $exception) {
            dd("No teams meeting support for account: 1");
        }
        $graph = new \App\Libraries\Azure\GraphConnector($azure);
        //$users = $graph->getUsers();
        $azure_user_presence = $graph->getUsersWithPresence();

        //Preserve presence order and show Portal users only
        foreach($system_users as $user){
            $users[$user->email] = $user;
        }
        foreach($azure_user_presence as $email => $status){
            if(isset($users[$email])){
                $users[$email]->presence = $status ?? $default_status;
                $list[] = $users[$email];
            }
        }
        return $list;

        /*
        list of all Azure Users
        foreach($system_users as $user){
            $user->presence = $azure_user_presence[$user->email] ?? $default_status;
        }
        return $system_users;
        */
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

    public function allocate($lead_id, $adviser_id){
        $lead = Lead::find($lead_id);
        if($lead){
            $lead->status = Lead::CLAIMED;
            $lead->user_id = $adviser_id;
            $lead->allocated_at = date('Y-m-d H:i:s');
            $lead->save();
        }else{
            $this->emit('error', ['message' => "Cant find lead [" . $lead_id . "]"]);
        }
    }

    public function transfer($lead_id, $adviser_email=null){
        $lead = Lead::find($lead_id);
        if($lead){
            if(is_null($adviser_email)){
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
                    "dateTimeGdprConsent" => \Carbon\Carbon::parse($lead->created_at)->format("Y-m-d\TH:i:s\Z"),
                    //"mortgagePurpose" => 1,
                    //"currentBuyingPosition" => 2,
                    //"howCanWeHelp" => 1,
                    //"plotNumber" => 42,
                    //"foundFutureHome" => false,
                    //"totalGrossSalary" => 80000,
                    //"propertyValue" => 300000,
                    //"deposit" => 30000,
                    "distributionType" => 4,
                    "distributionGroupId" => "5a4aaeb5-484e-4018-a2e6-a2e988fef65e",
                    //"leadReferralType" => 0,
                    //"timeOfReferral" => "2023-02-06T09:17:18.684Z",
                    //"creationDate" => "2023-02-06T09:17:18.684Z",
                    "notes" => "From TMBL Portal",
                    //"consenter" => 0,
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
                    //"allocatedFirmId" => null,
                    //"allocatedFirmBranchId" => null,
                    //"allocatedAdviserId" => null,
                    //"shouldSendCustomerInviteEmail" => true,
                    //"midasProClientFolderID" => null,
                    //"customFields" => [
                    //    "Portal_Lead_ID" => $lead->id,
                // ]
                ];

                $mab = new MABApi(false);
                $mab_lead_response = $mab->newLead($data);
                if($mab_lead_response->status){
                    $this->emit('updated', ['message' => "Lead allocated [" . $lead_id . "]"]);
                    $lead->status = Lead::TRANSFERRED;
                    $lead->user_id = 0;
                    $lead->allocated_at = date('Y-m-d H:i:s');
                    $lead->save();
                }else{
                    $this->emit('error', ['message' => "Unable to send to MAB Portal [" . $lead_id . "]"]);
                }
            }else{
                $adviser = \App\Models\User::where('email',$adviser_email)->first() ?? null;
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
                    //"distributionGroupId" => "5a4aaeb5-484e-4018-a2e6-a2e988fef65e",
                    //"leadReferralType" => 0,
                    //"timeOfReferral" => "2023-02-06T09:17:18.684Z",
                    //"creationDate" => "2023-02-06T09:17:18.684Z",
                    "notes" => "From DEV TMBL Portal",
                    //"consenter" => 0,
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
                    //"shouldSendCustomerInviteEmail" => true,
                    //"midasProClientFolderID" => null,
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

    public function delete($id)
    {
        $account_Lead  = Lead::find($id);
        if ($account_Lead ) {

            if ($account_Lead ->delete() !== false) {
                $this->emit('updated', ['message' => 'Lead [' . $account_Lead ->tag . '] has been deleted']);
                $this->message_bar = 'Lead [' . $account_Lead ->tag . '] has been deleted.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Lead [" . $account_Lead ->tag . "] couldn't be deleted."]);
    }

    public function render()
    {
        $this->data();
        $list = [];
        if($this->view == 'list'){
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }elseif(!is_null($this->lead_id)){
            $this->advisers = $this->buildAdvisersList();
        }
        return view('livewire.lead-table', [
            'list' => $list
        ]);
    }

}
