<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\LeadChaser;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadChasers extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private static $session_prefix = '_lead_chasers_editor_';
    private $data;
    public $user_id = null;

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';

    //form vars
    public $status = [LeadChaser::ACTIVE => 'Inactive', LeadChaser::INACTIVE => 'Active'];
    public $method = 'email';
    public $time_unit = 0;
    public $time_amount = 'minutes';
    public $name = null;
    public $subject = null;
    public $body = null;
    public $attachments = null;

    //save vars
    public $save_mode = 'create';
    public $update_id = null;

    //filters
    public $sort_order;
    public $search_filter;
    public $chaser_status = LeadChaser::ACTIVE;

    /*protected $listeners = [
        'approve_request' => 'approve_request',
        'decline_request' => 'decline_request',
        'amend_request' => 'amend_request'
    ];*/

    public function mount()
    {
        $this->user_id = session('user_id');
        $this->chaser_status = session(self::$session_prefix . 'chaser_status') ?? LeadChaser::ACTIVE;
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
        $query = LeadChaser::where('account_id', session('account_id'));

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
        $lead_chaser = LeadChaser::find($id);
        if ($lead_chaser) {
            $lead_chaser->status = LeadChaser::INACTIVE;
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
        $lead_chaser  = LeadChaser::find($id)->withTrashed();
        if ($lead_chaser ) {
            $lead_chaser->status = LeadChaser::ACTIVE;
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

        $this->method = 'email';
        $this->time_amount = 0;
        $this->time_unit = 'minutes';
        $this->name = null;
        $this->subject = null;
        $this->body = null;
        $this->attachments = null;
        $this->status = LeadChaser::ACTIVE;

        $this->view = 'form';
    }

    public function edit($id)
    {
        $this->message_bar = '';
        $this->update_id = $id;
        $this->save_mode = 'update';
        $base = LeadChaser::where('id', $id)->first();
        if ($base) {
            $this->method = $base->method;
            $this->name = $base->name;
            $this->time_amount = explode(" ",$base->chase_duration)[0];
            $this->time_unit = explode(" ",$base->chase_duration)[1] ?? 'minutes';
            $this->subject = $base->subject;
            $this->body = $base->body;
            $this->attachments = $base->attachments;
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
        $base = LeadChaser::where('id', $id)->first();
        if($base){
            $this->method = $base->method;
            $this->name = $base->name;
            $this->time_amount = explode(" ",$base->chase_duration)[0];
            $this->time_unit = explode(" ",$base->chase_duration)[1] ?? 'minutes';
            $this->subject = $base->subject;
            $this->body = $base->body;
            $this->attachments = $base->attachments;
            $this->status = $base->status;

            $this->view = 'form';
        }else{
            $this->emit('error', ['message' => "Cant find chaser [" . $id . "]"]);
        }
    }

    public function save()
    {
        $chase_duration = $this->time_amount." ".$this->time_unit;
        $validatedData = Validator::make(
            [
                'account_id' => session('account_id'),
                'method' => $this->method,
                'name' => $this->name,
                'chase_duration' => $chase_duration,
                'subject' => $this->subject,
                'body' => $this->body,
                'attachments' => $this->attachments,
                'status' => $this->status,
            ],
            [
                'account_id' => 'required|numeric',
                'method' => 'required',
                'name' => 'required',
                'chase_duration' => 'required',
                'subject' => 'required',
                'body' => 'required',
                'attachments' => '',
                'status' => 'required|in:0,1'
            ]
        );
        $validatedData->validate();

        if(!$validatedData->fails()){

            if($this->save_mode == 'create'){
                $save = LeadChaser::create($validatedData->validated());
                $success_msg = 'New Chaser "' . $this->name. '" has been created.';
            }else{
                $save = LeadChaser::where('id', $this->update_id)->update($validatedData->validated());
                $success_msg = 'Chaser "' . $this->name. '" has been edited.';
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
        }
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
