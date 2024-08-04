<?php

namespace App\Http\Livewire;

use App\Models\EmailTemplate;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\LeadChaseStep;
use App\Models\LeadChaseStepContactMethod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadChasers extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private static $session_prefix = '_lead_chase_steps_editor_';
    private $data;
    public $user_id = null;

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';

    //form vars
    public $status_options = [LeadChaseStep::ACTIVE => 'Inactive', LeadChaseStep::INACTIVE => 'Active'];
    public $auto_progress_options = [LeadChaseStep::ACTIVE => 'Yes', LeadChaseStep::INACTIVE => 'No'];
    public $auto_contact_options = [LeadChaseStep::ACTIVE => 'Yes', LeadChaseStep::INACTIVE => 'No'];
    public $status = LeadChaseStep::INACTIVE;
    public $auto_progress = LeadChaseStep::INACTIVE;
    public $auto_contact = LeadChaseStep::INACTIVE;
    public $contact_methods = [];
    public $strategy_id = 1;
    public $chase_order = 0;
    public $name = null;
    public $available_template_ids = null;
    public $selected_template_id = null;
    public $templates = [];

    //save vars
    public $save_mode = 'create';
    public $update_id = null;

    //filters
    public $sort_order;
    public $search_filter;
    public $chaser_status = LeadChaseStep::ACTIVE;

    /*protected $listeners = [
        'approve_request' => 'approve_request',
        'decline_request' => 'decline_request',
        'amend_request' => 'amend_request'
    ];*/

    public function mount()
    {
        $this->user_id = session('user_id');
        $this->chaser_status = session(self::$session_prefix . 'chaser_status') ?? LeadChaseStep::ACTIVE;
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
        $this->sort_order = session(self::$session_prefix . 'sort_order') ?? '';

        $this->templates = EmailTemplate::where('account_id', session('account_id'))->where('status', EmailTemplate::ACTIVE)->get()->toArray();
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);

        $this->message_bar = '';
    }

    public function data()
    {
        $this->filtersActive = 0;
        $query = LeadChaseStep::with('strategy')->where('account_id', session('account_id'));

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
            $query = $query->orderBy('strategy_id', 'asc')->orderBy('chase_order', 'asc');
        }

        if ($this->chaser_status != '') {
            $query = $query->where('status', $this->chaser_status);
        }

        if (!empty(trim($this->search_filter))) {
            ++$this->filtersActive;
            $query = $query->where(function($q){
                $q->where('name', 'like', '%' . $this->search_filter . '%')->orWhere('subject', 'like', '%' . $this->search_filter . '%');
            });
        }

        $this->data = $query;
    }

    public function filter()
    {
        session()->put(self::$session_prefix . 'chaser_status', $this->chaser_status);
        session()->put(self::$session_prefix . 'search_filter', $this->search_filter);
        session()->put(self::$session_prefix . 'sort_order', $this->sort_order);
        $this->emit('updated',['message'=>'Filtering applied']);
    }

    public function resetFilters()
    {
        $this->chaser_status = '';
        $this->search_filter = '';
        $this->sort_order = '';
        session()->forget(self::$session_prefix . 'chaser_status');
        session()->forget(self::$session_prefix . 'search_filter');
        session()->forget(self::$session_prefix . 'sort_order');
        $this->emit('updated', ['message' => 'Filters reset']);
    }

    public function delete($id)
    {
        $lead_chaser = LeadChaseStep::find($id);
        if ($lead_chaser) {
            $lead_chaser->status = LeadChaseStep::INACTIVE;
            $lead_chaser->save();
            if ($lead_chaser->save() !== false) {
                $this->emit('updated', ['message' => 'Chaser "' . $lead_chaser->name. '" has been deleted']);
                $this->message_bar = 'Chaser "' . $lead_chaser->name. '" has been deleted.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Chaser [" . $lead_chaser->chaser ?? 'unknown' . "] couldn't be deleted."]);
    }

    public function restore($id)
    {
        $lead_chaser  = LeadChaseStep::find($id)->withTrashed();
        if ($lead_chaser ) {
            $lead_chaser->status = LeadChaseStep::ACTIVE;
            if ($lead_chaser->save() !== false && $lead_chaser->restore()) {
                $this->emit('updated', ['message' => 'Chaser "' . $lead_chaser->name. '" has been restored']);
                $this->message_bar = 'Chaser "' . $lead_chaser->name. '" has been restored.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Chaser [" . $lead_chaser ->chaser . "] couldn't be restored."]);
    }

    public function create()
    {
        $this->message_bar = '';
        $this->save_mode = 'create';

        $this->contact_methods = [];
        $this->strategy_id = 1;
        $this->auto_progress = LeadChaseStep::INACTIVE;
        $this->name = '';

        $this->view = 'form';
    }

    public function edit($id)
    {
        $this->message_bar = '';
        $this->update_id = $id;
        $this->save_mode = 'update';
        $base = LeadChaseStep::where('id', $id)->first();
        if ($base) {
            $contact_methods = $base->contact_methods->toArray();
            foreach($contact_methods as $cm_key => $cm){
                $time_unit_parts = explode(" ", $cm['chase_duration']);
                $contact_methods[$cm_key]["time_amount"] = $time_unit_parts[0] ?? 0;
                $contact_methods[$cm_key]["time_unit"] = $time_unit_parts[1] ?? 'minutes';
                $contact_methods[$cm_key]["template_ids"] = explode(',',$cm['template_ids']);
            }
            $this->contact_methods = $contact_methods;
            $this->strategy_id = $base->strategy_id;
            $this->chase_order = $base->chase_order;
            $this->auto_progress = $base->auto_progress;
            $this->name = $base->name;
            $this->status = $base->status;

            $this->view = 'form';
        } else {
            $this->emit('error', ['message' => "Cant find chaser [" . $id . "]"]);
        }
    }

    public function copy($id)
    {
        $this->message_bar = '';
        $this->save_mode = 'create';
        $base = LeadChaseStep::where('id', $id)->first();
        if($base){
            $contact_methods = $base->contact_methods->toArray();
            foreach($contact_methods as $cm_key => $cm){
                $time_unit_parts = explode(" ", $cm['chase_duration']);
                $contact_methods[$cm_key]["time_amount"] = $time_unit_parts[0] ?? 0;
                $contact_methods[$cm_key]["time_unit"] = $time_unit_parts[1] ?? 'minutes';
                $contact_methods[$cm_key]["template_ids"] = explode(',',$cm['template_ids']);
            }
            $this->contact_methods = $contact_methods;
            $this->strategy_id = $base->strategy_id;
            $this->auto_progress = $base->auto_progress;
            $this->name = $base->name;
            $this->status = $base->status;

            $this->view = 'form';
        }else{
            $this->emit('error', ['message' => "Cant find chaser [" . $id . "]"]);
        }
    }

    public function save()
    {
        $validatedData = Validator::make(
            [
                'account_id' => session('account_id'),
                'strategy_id' => $this->strategy_id,
                'chase_order' => $this->chase_order,
                'name' => $this->name,
                'auto_progress' => $this->auto_progress,
                'status' => $this->status,
                'contact_methods' => $this->contact_methods
            ],
            [
                'account_id' => 'required|numeric',
                'strategy_id' => 'required|numeric',
                'chase_order' => 'required|numeric',
                'name' => 'required',
                'auto_progress' => 'required|in:0,1',
                'status' => 'required|in:0,1',

                //'contact_methods.*.chase_order' => 'required',
                'contact_methods.*.time_amount' => 'required',
                'contact_methods.*.time_unit' => 'required',
                'contact_methods.*.method' => 'required',
                'contact_methods.*.auto_contact' => 'required|in:0,1',
                'contact_methods.*.name' => 'required',
                //'contact_methods.*.template_ids' => 'required_if=contact_methods.*.method,email',
                //'contact_methods.*.default_template_id' => 'required_if=contact_methods.*.method,email',
            ]
        );
        $validatedData->validate();

        if(!$validatedData->fails()){
            $object_data = $validatedData->validated();
            $contact_methods = $object_data['contact_methods'];
            unset($object_data['contact_methods']);
            if($this->save_mode == 'create'){
                $save = LeadChaseStep::create($object_data);
                $success_msg = 'New Chaser "' . $this->name. '" has been created.';
                foreach($contact_methods as $order => $cm){
                    $cm['account_id'] = $save->account_id;
                    $cm['strategy_id'] = $save->strategy_id;
                    $cm['step_id'] = $save->id;
                    $cm['chase_order'] = $order;
                    $cm['chase_duration']= $cm['time_amount']." ".$cm['time_unit'];
                    if(isset($cm['template_ids'])){
                        $cm['template_ids'] = implode(",",$cm['template_ids']);
                    }
                    $cm['status']= 0;
                    unset($cm['time_amount'],$cm['time_unit']);
                    LeadChaseStepContactMethod::create($cm);
                }
            }else{
                $save = LeadChaseStep::where('id', $this->update_id)->update($object_data);
                $success_msg = 'Chaser "' . $this->name. '" has been edited.';

                LeadChaseStepContactMethod::where('step_id', $this->update_id)->delete();
                foreach($contact_methods as $order => $cm){
                    $cm['chase_duration']= $cm['time_amount']." ".$cm['time_unit'];
                    if(isset($cm['template_ids'])){
                        $cm['template_ids'] = implode(",",$cm['template_ids']);
                    }
                    $cm['status'] = 0;
                    $cm['deleted_at'] = null;
                    unset($cm['time_amount'],$cm['time_unit']);
                    LeadChaseStepContactMethod::updateOrCreate(
                        [
                            'account_id' => $save->account_id,
                            'strategy_id' => $save->strategy_id,
                            'step_id' => $save->id,
                            'chase_order' => $order
                        ],
                        $cm
                    );
                }
            }

            if ($save) {
                $this->emit('updated', ['message' => 'Chaser "' . $this->name. '" Saved']);
                $this->message_bar = $success_msg;
                $this->save_mode = 'create';
                $this->update_id = null;
                session()->forget(self::$session_prefix . 'update_id');
                $this->view = 'list';
            } else {
                $this->emit('error', ['message' => "Chaser \"" . $this->name . "\" couldn't be saved"]);
            }

            //TODO: add contact steps
        }
    }

    public function add_contact_step(){
        $contact_methods = $this->contact_methods;
        $contact_methods[] = [
            "strategy_id" => $contact_methods[0]["strategy_id"] ?? $this->strategy_id,
            "step_id" => $contact_methods[0]["step_id"] ?? 0,
            "default_template_id" => $contact_methods[0]["default_template_id"] ?? null,
            "chase_order" => count($contact_methods), //zero based
            "time_amount" => 0,
            "time_unit" => "minutes",
            "method" => "call",
            "auto_contact" => 1,
            "name" => "Contact Step ".(count($contact_methods) + 1)
        ];
        $this->contact_methods = $contact_methods;
    }

    public function remove_contact_step($step_key){
        $contact_methods = $this->contact_methods;
        unset($contact_methods[$step_key]);
        $this->contact_methods = array_values($contact_methods);
    }

    public function reorder_contact_step($step_key,$direction){
        $contact_methods = $this->contact_methods;
        $move_step = $contact_methods[$step_key]; //info
        $current_order = $step_key;
        //$current_order = (int) $move_step["chase_order"];
        if($direction == "earlier"){
            $new_order = $current_order - 1;
        }else{
            $new_order = $current_order + 1;
        }
        if($new_order < 0){
            $new_order = 0;
        }elseif($new_order > count($contact_methods)){
            $new_order = count($contact_methods);
        }
        $new_step["chase_order"] = $new_order;
        unset($contact_methods[$step_key]);
        $contact_methods = array_values($contact_methods);
        //dd($contact_methods);

        $new_contact_methods = [];
        $new_contact_methods[$new_order] = $move_step; //place new step in correct order
        foreach($contact_methods as $cm_key => $cm){
            //dump($cm_key."|".$cm["name"]);
            if(!isset($new_contact_methods[$cm_key])){
                $new_contact_methods[$cm_key] = $cm;
            }else{
                $new_contact_methods[$cm_key+1] = $cm;
            }
        }
        //dd("-");
        //dd(count($new_contact_methods));
        $this->contact_methods = $new_contact_methods;
    }

    public function render()
    {
        $list = [];
        if($this->view == 'list'){
            $this->data();
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }
        return view('livewire.lead-chasers', [
            'list' => $list
        ]);
    }

}
