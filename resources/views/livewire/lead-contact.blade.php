<div wire:init="loadData">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

        <div id="topbar_title"><h1 class="h2">Contacting [{{$lead->id}}] {{$lead->full_name()}}</h1></div>

        <div class="btn-toolbar mb-2 mb-md-0">
            {{ Breadcrumbs::render('leads') }}
            <a href="{{ route('leads.manager') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Leads</a>
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

    <div class="card mb-3 p-3">
        <div class="row">
            <div class="col-2 d-flex align-items-center justify-content-center"><strong>Filter Adviser List</strong></div>
            <div class="col-10">
                <x-select2-tags wire:model="advisers" id="advisers" class="form-control select2-tags" placeholder="Select adviser(s). Showing All by default.">
                    @foreach($adviser_list as $adv)
                        @php $adv = (object) $adv @endphp
                        @if($adv->email)
                            <option value="{{$adv->email}}">
                                {{$adv->first_name}} {{$adv->last_name}} [{{$adv->presence->availability ?? 'Unknown'}}]
                            </option>
                        @endif
                    @endforeach
                </x-select2-tags>
            </div>
        </div>
    </div>

    <div wire:loading.remove>

        @if($isLoaded)

            <div class="row">
                <div class="col-md-4  d-flex flex-column">
                    <h2>Availabile Advisers</h2>
                    <div class="card p-0 w-100 h-100 position-relative mb-3 flex-grow-1" style="min-height: @if(!empty($advisers)) 100px; @else 300px; @endif">
                        <div class="card-body p-0 position-absolute overflow-auto" style="top:0; bottom:0; left:0; right:0;">
                            <div class="list-group list-group-flush">
                                @foreach($adviser_list as $adviser)
                                    @php
                                        $adviser = (object) $adviser;
                                        $adviser->presence = (object) $adviser->presence;
                                    @endphp
                                    @if(
                                        (!in_array($adviser->email,$advisers) && !empty($advisers))
                                        ||
                                        (in_array(($adviser->presence->activity ?? 'PresenceUnknown'),['PresenceUnknown','Unknown','Offline','OutOfOffice']) && empty($advisers))
                                    )
                                        @continue
                                    @endif
                                    <div class="list-group-item p-1">
                                        <div onclick="@this.set('selected_adviser','{{$adviser->email}}')" class="cursor-pointer row @if($adviser->email == $selected_adviser)) bg-primary text-white @endif">
                                            <div class="col-1 text-right">
                                                <span class="tip" title="
                                                    {{\App\Libraries\Interpret::AzureStatus(($adviser->presence->activity ?? 'Unknown'))}}
                                                    @if(!is_null(($adviser->presence->statusMessage->message->content ?? null)))
                                                        - {{$adviser->presence->statusMessage->message->content ?? 'Blank Message'}}
                                                    @endif
                                                ">
                                                    @switch(($adviser->presence->availability ?? 'unknown'))
                                                        @case('Available')
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            @break
                                                        @case('Busy')
                                                        @case('Presenting')
                                                        @case('DoNotDisturb')
                                                            <i class="fas fa-circle text-danger"></i>
                                                            @break
                                                        @case('Away')
                                                            <i class="fas fa-circle text-warning"></i>
                                                            @break
                                                        @case('Offline')
                                                            <i class="fas fa-times-circle text-muted"></i>
                                                            @break
                                                        @default
                                                            <i class="fas fa-question-circle text-muted"></i>
                                                    @endswitch
                                                </span>
                                            </div>
                                            <div class="col-7 text-truncate">
                                                {{$adviser->first_name ?? '?'}} {{$adviser->last_name ?? '?'}}
                                            </div>
                                            <div class="col-4 text-right">
                                                {{$adviser->leads_count ?? 0 }} this mo.
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="overflow-msg overflow-msg-scroll position-absolute text-muted text-center" style="bottom:0; left:0; right:0; background-image: linear-gradient(180deg, rgba(255,255,255,0), rgba(255,255,255,1));">
                            <small>scroll to see more</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <h2>Weekly Availabilty <small class="float-right badge badge-primary">Updated {{$cache_date ?? 'Unknown'}}</small></h2>
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
                                                        <div class="card px-1 mb-1 w-100 @if($availability['is_past']) text-muted @else cursor-pointer @endif @if($selected_date == $hour['date'] && $selected_time == (str_pad($hour_number,  2, "0", STR_PAD_LEFT).':00')) bg-primary text-white @else bg-light @endif" @if($availability['is_past']) style="opacity:0.5;" @endif @if(!$availability['is_past']) wire:click="select_slot('{{$hour['date']}}','{{ str_pad($hour_number,  2, "0", STR_PAD_LEFT) }}:00')" @endif>
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
                    <button class="btn btn-secondary" wire:click="mark_as_contacted()">Mark as contacted</button>
                    @if(!empty($selected_adviser) && !empty($selected_date) && !empty($selected_time))
                        <button class="ml-3 btn btn-primary" wire:click="allocate_and_transfer()">Allocate and Transfer Lead</button>
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
