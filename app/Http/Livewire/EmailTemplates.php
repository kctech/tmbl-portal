<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\EmailTemplate;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailTemplates extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    private static $session_prefix = '_email_templates_editor_';
    private $data;
    public $user_id = null;

    //general list vars
    public $view = 'list';
    public $filtersActive = 0;
    public $message_bar = '';

    //form vars
    public $status = [EmailTemplate::ACTIVE => 'Inactive', EmailTemplate::INACTIVE => 'Active'];
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
    public $chaser_status = EmailTemplate::ACTIVE;

    public function mount()
    {
        $this->user_id = session('user_id');
        $this->chaser_status = session(self::$session_prefix . 'chaser_status') ?? EmailTemplate::ACTIVE;
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
        $query = EmailTemplate::where('account_id', session('account_id'));

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
        $email_template = EmailTemplate::find($id);
        if ($email_template) {
            $email_template->status = EmailTemplate::INACTIVE;
            $email_template->save();
            if ($email_template->save() !== false) {
                $this->emit('updated', ['message' => 'Email Template "' . $email_template->name. '" has been deleted']);
                $this->message_bar = 'Email Template "' . $email_template->name. '" has been deleted.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Email Template [" . $email_template->chaser ?? 'unknown' . "] couldn't be deleted."]);
    }

    public function restore($id)
    {
        $email_template  = EmailTemplate::find($id)->withTrashed();
        if ($email_template ) {
            $email_template->status = EmailTemplate::ACTIVE;
            if ($email_template->save() !== false && $email_template->restore()) {
                $this->emit('updated', ['message' => 'Email Template "' . $email_template->name. '" has been restored']);
                $this->message_bar = 'Email Template "' . $email_template->name. '" has been restored.';
                $this->search_filter = '';
                session()->forget(self::$session_prefix . 'search_filter');
                return true;
            }
        }
        $this->emit('updated', ['message' => "Email Template [" . $email_template ->chaser . "] couldn't be restored."]);
    }

    public function create()
    {
        $this->message_bar = '';
        $this->save_mode = 'create';

        $this->name = null;
        $this->subject = null;
        $this->body = null;
        $this->attachments = null;
        $this->status = EmailTemplate::ACTIVE;

        $this->view = 'form';
    }

    public function edit($id)
    {
        $this->message_bar = '';
        $this->update_id = $id;
        $this->save_mode = 'update';
        $base = EmailTemplate::where('id', $id)->first();
        if ($base) {
            $this->name = $base->name;
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
        $base = EmailTemplate::where('id', $id)->first();
        if($base){
            $this->name = $base->name;
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
                'name' => $this->name,
                'subject' => $this->subject,
                'body' => $this->body,
                'attachments' => $this->attachments,
                'status' => $this->status,
            ],
            [
                'account_id' => 'required|numeric',
                'name' => 'required',
                'subject' => 'required',
                'body' => 'required',
                'attachments' => '',
                'status' => 'required|in:0,1',
            ]
        );
        $validatedData->validate();

        if(!$validatedData->fails()){

            if($this->save_mode == 'create'){
                $save = EmailTemplate::create($validatedData->validated());
                $success_msg = 'New Email Template "' . $this->name. '" has been created.';
            }else{
                $save = EmailTemplate::where('id', $this->update_id)->update($validatedData->validated());
                $success_msg = 'Email Template "' . $this->name. '" has been edited.';
            }

            if ($save) {
                $this->emit('updated', ['message' => 'Email Template "' . $this->name. '" Saved']);
                $this->message_bar = $success_msg;
                $this->save_mode = 'create';
                $this->update_id = null;
                session()->forget(self::$session_prefix . 'update_id');
                $this->view = 'list';
            } else {
                $this->emit('error', ['message' => "Email Template \"" . $this->name . "\" couldn't be saved"]);
            }
        }
    }

    public function add_contact_step(){

    }

    public function render()
    {
        $list = [];
        if($this->view == 'list'){
            $this->data();
            $list = $this->data->paginate(Session::get('database.pagination_size', config('database.pagination_size')));
        }
        return view('livewire.email-templates', [
            'list' => $list
        ]);
    }

}
