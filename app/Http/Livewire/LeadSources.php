<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\ApiKey;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LeadSources extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private static $session_prefix = '_account_sources_editor_';
    private $data;
    public $user_id = null;

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';
    public $active_only_switch="1";
    public $show_active_only = true;

    //form vars
    public $match_types = ['EQUAL', 'ANY', 'ANY_STARTS_WITH'];
    public $confidential_types = [0 => 'No', 1 => 'Yes'];


    //actionable vars
    public $source = null;
    public $value = null;
    public $match_type = 'EQUAL';
    public $icon = null;
    public $tooltip = null;
    public $order = 0;
    public $confidential = 0;

    //save vars
    public $save_mode = 'create';
    public $update_id = null;

    //filters
    public $sort_order;
    public $search_filter;
    public $source_status = ApiKey::ACTIVE;

    /*protected $listeners = [
        'approve_request' => 'approve_request',
        'decline_request' => 'decline_request',
        'amend_request' => 'amend_request'
    ];*/

    public function mount()
    {
        $this->user_id = session('user_id');
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
        $this->sort_order = session(self::$session_prefix . 'sort_order') ?? '';
        $this->source = session(self::$session_prefix . 'source') ?? '';
        $this->value = session(self::$session_prefix . 'value') ?? '';
        $this->match_type = session(self::$session_prefix . 'match_type') ?? 'EQUAL';
        $this->icon = session(self::$session_prefix . 'icon') ?? '';
        $this->confidential = session(self::$session_prefix . 'confidential') ?? 0;
        $this->tooltip = session(self::$session_prefix . 'tooltip') ?? '';
        $this->order = session(self::$session_prefix . 'order') ?? 0;
        $this->active_only_switch = session(self::$session_prefix . 'active_only_switch') ?? "1";
        if ($this->active_only_switch == "1") {
            $this->show_active_only = true;
        } else {
            $this->show_active_only = false;
        }
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);

        $this->message_bar = '';
    }

    public function updatedActiveOnlySwitch($value){
        if($value == "1"){
            $this->show_active_only = true;
        }else{
            $this->show_active_only = false;
        }
    }

    public function data()
    {
        $this->filtersActive = 0;
        $query = ApiKey::query();

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

        if ($this->show_active_only) {
            $query = $query->where('status',0);
        }

        if (!empty(trim($this->search_filter))) {
            ++$this->filtersActive;
            $query = $query->where(function($q){
                $q->where('source', 'like', '%' . $this->search_filter . '%')->orWhere('api_token', 'like', '%' . $this->search_filter . '%');
            });
        }

        $this->data = $query;
    }

    public function filter()
    {
        session()->put(self::$session_prefix . 'search_filter', $this->search_filter);
        session()->put(self::$session_prefix . 'sort_order', $this->sort_order);
        $this->emit('updated',['message'=>'Filtering applied']);
    }

    public function resetFilters()
    {
        $this->search_filter = '';
        $this->sort_order = '';
        session()->forget(self::$session_prefix . 'search_filter');
        session()->forget(self::$session_prefix . 'sort_order');
        $this->emit('updated', ['message' => 'Filters reset']);
    }

    public function delete($id)
    {
        $account_source  = ApiKey::find($id);
        if ($account_source ) {

            if ($account_source ->delete() !== false) {
                $this->emit('updated', ['message' => 'Source [' . $account_source ->source . '] has been deleted']);
                $this->message_bar = 'Source [' . $account_source ->source . '] has been deleted.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Source [" . $account_source ->source . "] couldn't be deleted."]);
    }

    public function restore($id)
    {
        $account_source  = ApiKey::find($id);
        if ($account_source ) {
            $account_source ->status = 0;
            if ($account_source ->save() !== false) {
                $this->emit('updated', ['message' => 'Source [' . $account_source ->source . '] has been restored']);
                $this->message_bar = 'Source [' . $account_source ->source . '] has been restored.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Source [" . $account_source ->source . "] couldn't be restored."]);
    }

    public function create()
    {
        $this->message_bar = '';
        $this->save_mode = 'create';

        $this->source = null;
        $this->value = null;
        $this->match_type = 'EQUAL';
        $this->icon = null;
        $this->tooltip = null;
        $this->order = null;
        $this->confidential = 0;

        $this->view = 'form';
    }

    public function edit($id)
    {
        $this->message_bar = '';
        $this->update_id = $id;
        $this->save_mode = 'update';
        $base = ApiKey::where('id', $id)->first();
        if ($base) {
            $this->source = $base->source;
            $this->value = $base->value;
            $this->match_type = $base->match_type;
            $this->icon = $base->icon;
            $this->confidential = $base->confidential;
            $this->tooltip = $base->tooltip;
            $this->order = $base->order;

            $this->view = 'form';
        } else {
            $this->emit('error', ['message' => "Cant find source [" . $id . "]"]);
        }
    }

    public function copy($id)
    {
        $this->message_bar = '';
        $this->save_mode = 'create';
        $base = ApiKey::where('id', $id)->first();
        if($base){
            $this->source = $base->source;
            $this->value = $base->value;
            $this->match_type = $base->match_type;
            $this->icon = $base->icon;
            $this->confidential = $base->confidential;
            $this->tooltip = $base->tooltip;
            $this->order = $base->order;

            $this->view = 'form';
        }else{
            $this->emit('error', ['message' => "Cant find source [" . $id . "]"]);
        }
    }

    public function save_account_source()
    {

        $this->source = preg_replace('/\[\]/', '', $this->source);

        $validatedData = Validator::make(
            [
                'account_id' => session('account_id'),
                'source' => $this->source,
                'value' => $this->value,
                'match_type' => $this->match_type,
                'icon' => $this->icon,
                'confidential' => $this->confidential,
                'tooltip' => $this->tooltip,
                'order' => $this->order,
                'status' => 0
            ],
            [
                'account_id' => 'required|numeric',
                'source' => 'required|alpha_num',
                'value' => 'required',
                'match_type' => 'required|in:'.implode(",", $this->match_types),
                'icon' => 'required',
                'confidential' => 'required|in:0,1',
                'tooltip' => 'required',
                'order' => 'required|integer|min:0',
                'status' => 'required|in:0,1'
            ]
        );
        $validatedData->validate();

        if(!$validatedData->fails()){

            if($this->save_mode == 'create'){
                $save = ApiKey::create($validatedData->validated());
                $success_msg = 'New Source [' . $this->source . '] has been created.';
            }else{
                $save = ApiKey::where('id', $this->update_id)->update($validatedData->validated());
                $success_msg = 'Source [' . $this->source . '] has been edited.';
            }

            if ($save) {
                $this->emit('updated', ['message' => 'Source [' . $this->source . '] Saved']);
                $this->message_bar = $success_msg;
                $this->save_mode = 'create';
                $this->update_id = null;
                session()->forget(self::$session_prefix . 'update_id');
                $this->view = 'list';
            } else {
                $this->emit('error', ['message' => "Source [" . $this->source . "] couldn't be saved"]);
            }
        }
    }

    public function render()
    {
        $this->data();
        $list = [];
        if($this->view == 'list'){
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }
        return view('livewire.lead-sources', [
            'list' => $list
        ]);
    }

}
