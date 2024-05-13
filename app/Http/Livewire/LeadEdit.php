<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Lead;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadEdit extends Component
{
    public $user_id = null;
    public $lead_id = null;
    public $lead = null;

    public $redirect = null;

    public $first_name = null;
    public $last_name = null;
    public $email_address = null;
    public $contact_number = null;

    public $message_bar = '';

    public function mount($lead_id, $redirect = 'leads.table')
    {
        $this->user_id = session('user_id');
        $this->lead_id = $lead_id;
        $lead = Lead::where('id',$lead_id)->first();
        $this->lead = $lead;

        $this->redirect = $redirect;

        $this->first_name = $lead->first_name;
        $this->last_name = $lead->last_name;
        $this->email_address = $lead->email_address;
        $this->contact_number = $lead->contact_number;
    }

    public function updated($prop, $value)
    {
        $this->message_bar = '';
    }

    public function save()
    {
        $validatedData = Validator::make(
            [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email_address' => $this->email_address,
                'contact_number' => $this->contact_number
            ],
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email_address' => 'required|email',
                'contact_number' => 'required|numeric',
            ]
        );
        $validatedData->validate();

        if(!$validatedData->fails()){

            $save = Lead::where('id', $this->lead_id)->update($validatedData->validated());
            $success_msg = 'Lead [' . $this->lead_id . '] has been edited.';

            if ($save) {
                $this->emit('updated', ['message' => 'Lead [' . $this->lead_id . '] Saved']);
                $this->message_bar = $success_msg;
                session()->flash('alert-success','Lead ID '.$this->lead_id.' has been saved.');
                return $this->redirectRoute($this->redirect);
            } else {
                $this->emit('error', ['message' => "Lead [" . $this->lead_id . "] couldn't be saved"]);
            }
        }
    }

    public function render()
    {
        return view('livewire.lead-edit');
    }

}
