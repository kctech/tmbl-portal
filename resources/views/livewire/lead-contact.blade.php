<div wire:init="loadData">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

        <div id="topbar_title"><h1 class="h2">Contacting [{{$lead->id}}] {{$lead->full_name()}}</h1></div>

        <div class="btn-toolbar mb-2 mb-md-0">
            {{ Breadcrumbs::render('leads') }}
            <a href="{{ route('leads.table') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Leads</a>
        </div>
    </div>

    <div class="flash-message py-2">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;§</a></div>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <h4>Lead</h4>
                    {{ $lead->id }}<br />
                    {{ $lead->full_name() }}
                </div>
                <div class="col">
                    <h4>Contact</h4>
                    {{ $lead->email_address }}<br />
                    {{ $lead->contact_number }}
                </div>
                <div class="col">
                    <h4>Source</h4>
                    {{ $lead->source->source ?? 'Unknown' }}<br />
                    {{\Carbon\Carbon::parse($lead->created_at)->format('d/m/Y H:i')}}<br />
                    <span class="badge badge-primary">{{\Carbon\Carbon::parse($lead->created_at)->diffForHumans()}}</span>
                </div>
                <div class="col">
                    <h4>Attempts</h4>
                    {{ $lead->contact_count }} times<br />
                    @if(!empty($lead->contacted_at))
                        <br /><span class="badge badge-primary">{{\Carbon\Carbon::parse($lead->contacted_at)->diffForHumans()}}</span>
                    @endif
                    <div class="d-block w-100">
                        <i class="fa fa-envelope"></i>
                        @foreach($contact_schedule as $chaser)
                            @if(in_array($chaser->id, $lead->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->pluck('information')->toArray()))
                                <i class="fas fa-check-circle text-success tip" title="Chaser {{$chaser->name}} sent {{$lead->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->where('information',$chaser->id)->first()->created_at}}"></i>
                            @else
                                <i class="fas fa-times-circle text-muted tip" title="Chaser {{$chaser->name}} not sent yet"></i>
                            @endif
                        @endforeach
                    </div>
                    @if($lead->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->count() != 0)
                        <div class="d-block w-100">
                            <i class="fa fa-phone"></i>
                            @foreach($lead->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->get() as $contact)
                                <i class="fas fa-phone-square text-success tip" title="Contacted at {{$contact->created_at}}"></i>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h4>Additional Information</h4>
                    <ul class="list-group">
                        @foreach(json_decode($lead->data) as $d_key => $d_val)
                            @if(in_array($d_key,['full_name','first_name','last_name','phone_number','email'])) @continue @endif
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>{{$d_key}}</strong>
                                    </div>
                                    <div class="col-md-8">
                                        {{$d_val}}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(!$show_contact)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <h3>Lead Notes</h3>
                    </div>
                    <div class="col-md-10 mb-3">
                        <textarea wire:model.defer="lead_notes" id="lead_notes" class="form-control w-100" rows="10">{{json_decode($this->lead->data)->contact_notes ?? ''}}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <button class="btn btn-info text-white" wire:click="$set('show_contact',true)">Show Meeting Organiser</button>
                <button class="ml-3 btn btn-success" wire:click="allocate_and_transfer()">Transfer to MAB without Teams Meeting</button>
                {{--<button class="ml-3 btn btn-secondary" wire:click="mark_as_contacted()">Mark as contacted, leave at current step</button>--}}
                <button class="ml-3 btn btn-primary" wire:click="contact_progress()">Move to next step in chase process (send email)</button>
                {{--<button class="ml-3 btn btn-dark" wire:click="contact_progress_silent()">Move to next step in chase process (without email)</button>--}}
                <button class="float-right ml-3 btn btn-danger" wire:click="archive()">Archive Lead</button>
            </div>
        </div>
    @endif

    <div wire:loading.remove>

        @if($isLoaded && $show_contact)

            <div class="row">
                <div class="col-md-12 mb-3">
                    <h2>Weekly Availabilty <button class="btn btn-danger float-right mx-2" wire:click="$set('show_contact',false)">Hide Organiser</button> <small class="float-right badge badge-primary">Updated {{$cache_date ?? 'Unknown'}}</small></h2>
                    <div class="accordion" id="accordionWeeklyeAvailability">
                        @foreach($calendar as $week_number => $week)
                            <div class="card mb-3 p-3">
                                <div onclick="@this.set('selected_week','{{$week_number}}')" class="cursor-pointer w-100 d-flex flex-row align-items-center justify-content-center" data-toggle="collapse" data-target="#collapseWeek{{$week_number}}" aria-expanded="@if($week_number == $selected_week) true @else false @endif" aria-controls="collapseWeek{{$week_number}}"  id="heading{{$week_number}}">
                                    <div>
                                        <h3>
                                            <small>
                                                @if($week['available_slots'] == 0)
                                                    <i class="fas fa-circle text-danger tip" title="{{$week['available_slots']}} slots available"></i>
                                                @elseif($week['available_slots'] > 0 && $week['available_slots'] < 10)
                                                    <i class="fas fa-circle text-warning tip" title="{{$week['available_slots']}} slots available"></i>
                                                @else
                                                    <i class="fas fa-circle text-success tip" title="{{$week['available_slots']}} slots available"></i>
                                                @endif
                                            </small>
                                            {{$week['title']}}
                                        </h3>
                                    </div>
                                    <div class="ml-auto"><span class="badge badge-primary">Starts {{$week['start_date']}}</span></div>
                                </div>
                                <div class="collapse @if($week_number == $selected_week) show @endif" id="collapseWeek{{$week_number}}"  aria-labelledby="heading{{$week_number}}" data-parent="#accordionWeeklyeAvailability">
                                    <hr />
                                    <div class="row">
                                        @foreach($week['days'] as $day => $hour)
                                            <div class="col" @if($hour['is_past']) style="opacity:0.5;" @endif>
                                                <div class="mb-1">
                                                    <h3 class="mb-0">{{$day}}</h3>
                                                    <span class="badge badge-primary">{{$hour['date']}}</span>
                                                </div>
                                                @foreach($hour['hours'] as $hour_number => $availability)
                                                    @if($availability['availability'] > 0)
                                                        <div class="card px-1 mb-1 w-100
                                                            @if($availability['is_past']) text-muted @else cursor-pointer @endif
                                                            @if($selected_date == $hour['date'] && $selected_time == (str_pad($hour_number,  2, "0", STR_PAD_LEFT).':00')) bg-primary text-white @else bg-light @endif"
                                                            @if($availability['is_past']) style="opacity:0.5;" @endif
                                                            @if(!$availability['is_past'])
                                                                @if($selected_date == $hour['date'] && $selected_time == (str_pad($hour_number,  2, "0", STR_PAD_LEFT).':00'))
                                                                    wire:click="select_slot(null,null)"
                                                                @else
                                                                    wire:click="select_slot('{{$hour['date']}}','{{ str_pad($hour_number,  2, "0", STR_PAD_LEFT) }}:00')"
                                                                @endif
                                                            @endif
                                                        >
                                                            <div class="row">
                                                                <div class="col-7">{{ str_pad($hour_number,  2, "0", STR_PAD_LEFT) }}:00</div>
                                                                <div class="col-5 text-right">{{$availability['availability']}}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <h3>Appointment Notes</h3>
                    @if(!empty($selected_adviser) && !empty($selected_date) && !empty($selected_time))
                        <hr>
                        {{$selected_adviser}}<br />
                        {{\Carbon\Carbon::createFromFormat("Y-m-d H:i", $selected_date." ".$selected_time)->format("l jS \of F Y \a\\t h:i A")}}
                    @endif
                </div>
                <div class="col-md-8 mb-3">
                    <textarea wire:model.defer="lead_notes" id="lead_notes" class="form-control w-100" rows="10"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="col ml-auto text-right">
                    {{--<button class="btn btn-secondary" wire:click="mark_as_pause_contacting()">Pause auto-contacting</button>
                    <button class="ml-3 btn btn-secondary" wire:click="mark_as_contacted()">Mark as contacted</button>--}}
                    @if(!empty($selected_adviser))
                        <button class="ml-3 btn btn-success" wire:click="allocate_and_transfer()">Allocate and Transfer to MAB @if(!empty($selected_date) && !empty($selected_time)) (with Teams meeting) @endif</button>
                    @endif
                </div>
            </div>
        @endif

    </div>

    <div class="w-100" wire:loading.delay>
        <div class="card mt-4 border-0 shadow">
            <div class="card-body p-1">
                <div class="p-3 d-flex align-items-center justify-content-center text-center">
                    {{ __('Loading...') }}
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
    <script>

        Livewire.on('updated', data => {
            if(typeof data.message != 'undefined'){
                app.alerts.toast(data.message,'success');
            }
        });

        Livewire.on('error', data => {
            if(typeof data.message != 'undefined'){
                app.alerts.toast(data.message,'error');
            }else{
                app.alerts.toast('An error occurred','error');
            }
        });

    </script>
@endpush
