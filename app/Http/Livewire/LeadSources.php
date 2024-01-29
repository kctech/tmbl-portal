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

    //form vars
    public $status = [0 => 'Inactive', 1 => 'Active'];
    public $source = null;
    public $api_token = null;

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
        $this->source_status = session(self::$session_prefix . 'source_status') ?? ApiKey::ACTIVE;
        $this->search_filter = session(self::$session_prefix . 'search_filter') ?? '';
        $this->sort_order = session(self::$session_prefix . 'sort_order') ?? '';

        $this->source = session(self::$session_prefix . 'source') ?? '';
        $this->api_token = session(self::$session_prefix . 'api_token') ?? '';
        $this->status = session(self::$session_prefix . 'status') ?? '';
    }

    public function updated($prop, $value)
    {
        session()->put(self::$session_prefix . $prop, $value);

        $this->message_bar = '';
    }

    public function data()
    {
        $this->filtersActive = 0;
        $query = ApiKey::where('account_id', session('account_id'));

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

        if ($this->source_status != '') {
            $query = $query->where('status', $this->source_status);
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
        session()->put(self::$session_prefix . 'source_status', $this->source_status);
        session()->put(self::$session_prefix . 'search_filter', $this->search_filter);
        session()->put(self::$session_prefix . 'sort_order', $this->sort_order);
        $this->emit('updated',['message'=>'Filtering applied']);
    }

    public function resetFilters()
    {
        $this->source_status = '';
        $this->search_filter = '';
        $this->sort_order = '';
        session()->forget(self::$session_prefix . 'source_status');
        session()->forget(self::$session_prefix . 'search_filter');
        session()->forget(self::$session_prefix . 'sort_order');
        $this->emit('updated', ['message' => 'Filters reset']);
    }

    public function delete($id)
    {
        $account_source  = ApiKey::find($id);
        if ($account_source) {
            $account_source->status = ApiKey::INACTIVE;
            $account_source->save();
            if ($account_source->save() !== false) {
                $this->emit('updated', ['message' => 'Source [' . $account_source ->source . '] has been deleted']);
                $this->message_bar = 'Source [' . $account_source ->source . '] has been deleted.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Source [" . $account_source->source ?? 'unknown' . "] couldn't be deleted."]);
    }

    public function restore($id)
    {
        $account_source  = ApiKey::find($id)->withTrashed();
        if ($account_source ) {
            $account_source->status = ApiKey::ACTIVE;
            if ($account_source->save() !== false && $account_source->restore()) {
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
        $this->api_token = null;
        $this->status = 0;

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
            $this->api_token = $base->api_token;
            $this->status = $base->status;

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
            $this->api_token = $base->api_token;
            $this->status = $base->status;

            $this->view = 'form';
        }else{
            $this->emit('error', ['message' => "Cant find source [" . $id . "]"]);
        }
    }

    public function save()
    {
        if($this->save_mode == 'update'){
            $api_token_validation = 'required|alpha_num|min:50|max:60';
        }else{
            $api_token_validation = 'required|alpha_num|min:50|max:60|unique:\App\Models\ApiKey,api_token';
        }
        $validatedData = Validator::make(
            [
                'account_id' => session('account_id'),
                'source' => $this->source,
                'api_token' => $this->api_token,
                'status' => $this->status
            ],
            [
                'account_id' => 'required|numeric',
                'source' => 'required',
                'api_token' => $api_token_validation,
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
        $list = [];
        if($this->view == 'list'){
            $this->data();
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }
        return view('livewire.lead-sources', [
            'list' => $list
        ]);
    }

}
