@push('css')
    <style type="text/css">
        .not_current_step {
            position: relative;
        }
        .not_current_step::before {
            content: '';
            position: absolute;
            top:0;
            bottom:0;
            left: 0;
            right: 0;
            background: #fff;
            opacity: 0.75;
            z-index: 10;
        }
        .ck-editor__editable,
        #enhancedTemplatePreview {
            min-height: calc(100vh - 510px) !important;
            max-height: calc(100vh - 510px) !important;
            min-width:100%;
        }
        #templates_container {
            min-height: calc(100vh - 365px);
            max-height: calc(100vh - 365px);
            overflow:auto;
        }

        </style>
@endpush

<div wire:init="loadData">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

        <div id="topbar_title"><h1 class="h2 d-inline-block">Contacting: {{$lead->full_name()}} </h1><span class="ml-2 badge badge-primary">ID:{{$lead->id}}</span></div>

        <div class="btn-toolbar mb-2 mb-md-0">
            {{ Breadcrumbs::render('leads') }}
            <a href="{{ route('leads.manager') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Leads</a>
        </div>
    </div>

    <div class="flash-message py-2">
        @foreach (['danger', 'warning', 'success', 'info'] as $msg)
            @if(Session::has('alert-' . $msg))
                <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
            @endif
        @endforeach
    </div> <!-- end .flash-message -->

    <div class=" mb-3">
        <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white"><h4 class="mb-0">Contact</h4></div>
                            <div class="card-body">
                                {{ $lead->full_name() }}<br />
                                {{ $lead->email_address }}<br />
                                {{ $lead->contact_number }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white"><h4 class="mb-0">Source</h4></div>
                            <div class="card-body">
                                {{ $lead->source->source ?? 'Unknown' }}<br />
                                Added to system {{\Carbon\Carbon::parse($lead->created_at)->format('d/m/Y H:i')}}
                                <span class="badge badge-light">{{\Carbon\Carbon::parse($lead->created_at)->diffForHumans()}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white"><h4 class="mb-0">Additional Information</h4></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Lead ID</strong>
                            </div>
                            <div class="col-md-8">
                                {{$lead->id}}
                            </div>
                        </div>
                        @foreach(json_decode($lead->data) as $d_key => $d_val)
                            @if(in_array($d_key,['full_name','first_name','last_name','phone_number','email'])) @continue @endif
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>{{str_replace("_"," ",$d_key)}}</strong>
                                </div>
                                <div class="col-md-8">
                                    {{$d_val}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card mb-0">
                    <div class="card-header bg-primary text-white"><h4 class="mb-0 d-inline-block">Lead Notes</h4> <button class="btn btn-success btn-sm float-right" wire:click="save_notes">Save Notes</button></div>
                    <div class="card-body">
                        <textarea wire:model.defer="lead_notes" id="lead_notes" class="form-control w-100" rows="5">{{json_decode($this->lead->data)->contact_notes ?? ''}}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-6 h-100">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 d-inline-block">Chase Strategy</h4>
                        <div class="float-right">
                            <span class="badge badge-light">contacted {{ $lead->contact_count }} times</span>
                            <span class="badge badge-light">last contacted: {{ $lead->last_contacted_at }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        {{--
                            POS {{$lead->strategy_position_id}}</br />
                            @if(!empty($lead->contacted_at))
                                <br /><span class="badge badge-primary">{{\Carbon\Carbon::parse($lead->contacted_at)->diffForHumans()}}</span>
                            @endif
                            <div class="d-block w-100">
                                <i class="fa fa-envelope"></i>
                                @foreach($contact_schedule as $chaser)
                                    @if(in_array($chaser->id, $lead->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->pluck('information')->toArray()))
                                        <span class="tip" title="Chaser {{$chaser->name}} sent {{$lead->events()->where('event_id',\App\Models\LeadEvent::AUTO_CONTACT_ATTEMPTED)->where('information',$chaser->id)->first()->created_at}}"><i class="fas fa-check-circle text-success"></i></span>
                                    @else
                                        <span class="tip" title="Chaser {{$chaser->name}} not sent yet"><i class="fas fa-times-circle text-muted"></i></span>
                                    @endif
                                @endforeach
                            </div>
                            @if($lead->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->count() != 0)
                                <div class="d-block w-100">
                                    <i class="fa fa-phone"></i>
                                    @foreach($lead->events()->where('event_id',\App\Models\LeadEvent::MANUAL_CONTACT_ATTEMPTED)->get() as $contact)
                                        <span class="tip" title="Contacted at {{$contact->created_at}}"><i class="fas fa-phone-square text-success"></i></span>
                                    @endforeach
                                </div>
                            @endif
                        --}}
                        @foreach($contact_schedule as $chaser)
                            @php
                                $step_contact_methods = array_map('strval', $chaser->contact_methods->pluck('id')->toArray());
                                //dump($step_contact_methods);
                                //dump(count($step_contact_methods));
                                $step_contact_events = array_map('strval', $lead->contact_events->whereIn('information', $step_contact_methods)->pluck('information')->toArray());
                                //dump($step_contact_events);
                                //dump(count($step_contact_events));
                            @endphp
                            <div class="row mb-2 @if($lead->strategy_position_id != $chaser->id) not_current_step @endif">
                                <div class="col-2 d-flex align-items-center justify-content-center">
                                    <span class="badge badge-pill @if($lead->strategy_position_id == $chaser->id) badge-primary @else badge-dark @endif"><span class="h1">&nbsp;{{$chaser->chase_order}}&nbsp;</span></span>
                                </div>
                                <div class="col-10">
                                    <div class="card">
                                        <div class="card-header p-2 @if($lead->strategy_position_id == $chaser->id) bg-primary text-white @endif">
                                            <h5 class="mb-0 d-inline-block">{{$chaser->name}}</h5>
                                            @if($lead->strategy_position_id == $chaser->id && count($step_contact_methods) == count($step_contact_events))
                                                <button class="btn btn-success btn-sm float-right" wire:click="contact_progress_silent()">Move to next step</button>
                                            @else
                                                <span class="float-right"></span>
                                            @endif
                                        </div>
                                        <div class="card-body p-2">
                                            @foreach($chaser->contact_methods as $chase_step)
                                                <div class="mb-1">
                                                    @if(in_array(strval($chase_step->id), array_values($step_contact_events)))
                                                        <i class="fa fa-check-square text-success"></i> {{$chase_step->name}}
                                                    @else
                                                        <div
                                                            @if($chase_step->method == "email")
                                                                wire:click="record_email_modal({{$chase_step->id}})"
                                                            @else
                                                                onclick="confirm('Mark this lead as called?') || event.stopImmediatePropagation()" wire:click="record_call({{$chase_step->id}})"
                                                            @endif
                                                        class="hover-pointer tip @if(\Carbon\Carbon::parse($lead->created_at)->add($chase_step->chase_duration) <= \Carbon\Carbon::now() && $lead->strategy_position_id == $chaser->id) text-danger text-bold @endif" data-title="mark as complete">
                                                            <i class="fa fa-square"></i>
                                                            {{$chase_step->name}}
                                                            ({{$chase_step->method}} after {{$chase_step->chase_duration}})
                                                            @if(\Carbon\Carbon::parse($lead->created_at)->add($chase_step->chase_duration) <= \Carbon\Carbon::now() && $lead->strategy_position_id == $chaser->id)
                                                                - DUE NOW
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$show_contact)
        <div class="card mb-3">
            <div class="card-body">
                <button class="btn btn-sm btn-danger" onclick="confirm('Are you sure you want to archive this lead?') || event.stopImmediatePropagation()" wire:click="archive()">Archive Lead</button>

                <button class="float-right ml-3 btn btn-sm btn-success text-white" wire:click="allocate_and_transfer()">Transfer to MAB</button>
                <button class="float-right ml-3 btn btn-sm btn-info text-white" wire:click="$set('show_contact',true)">Teams Meeting Organiser</button>
                {{--
                <button class="ml-3 btn-sm btn btn-secondary" wire:click="mark_as_contacted()">Mark as contacted, leave at current step</button>
                <button class="ml-3 btn-sm btn btn-primary" wire:click="contact_progress()">Move to next step in chase process (send email)</button>
                <button class="ml-3 btn-sm btn btn-dark" wire:click="contact_progress_silent()">Move to next step in chase process (without email)</button>
                --}}

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

            {{--
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
            --}}

            <div class="row">
                <div class="col ml-auto text-right">
                    {{--<button class="btn btn-secondary" wire:click="mark_as_pause_contacting()">Pause auto-contacting</button>
                    <button class="ml-3 btn btn-secondary" wire:click="mark_as_contacted()">Mark as contacted</button>--}}
                    @if(!empty($selected_adviser))
                        <button class="ml-3 btn btn-success" wire:click="allocate_and_transfer()">Transfer to MAB @if(!empty($selected_date) && !empty($selected_time)) (with Teams meeting) @endif</button>
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

    <div id="emailContactModal" class="modal @if($show_contact_email) show @endif" tabindex="-1" role="dialog" @if($show_contact_email) style="display:block;" @endif>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Select Email Template</h5>
                    <button type="button" class="btn btn-danger btn-circle btn-sm" wire:click="close_email_modal()" {{--data-dismiss="modal"--}} aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3">
                            {{--<div class="input-group mb-3">
                                <input wire:model.defer="email_template_search" type="text" class="form-control text-dark" placeholder="Search All Templates">
                                <div class="input-group-append">
                                    <span class="input-group-text" style="cursor:pointer;" wire:click="load_templates('all')"><i class="fa fa-times"></i></span>
                                </div>
                            </div>--}}
                            <div class="border rounded">
                                <ul  id="templates_container" class="list-group bg-white list-group-flush">
                                    @foreach($email_templates as $template)
                                        <li class="list-group-item hover-pointer @if($email_template_id == $template->id) bg-primary text-white @endif" id="t_{{$template->id}}" wire:click="$set('email_template_id',{{$template->id}})">
                                            <div>{{$template->name}}</div>
                                            <div><small>ID: {{$template->id}}</small></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-9">
                            <div class="row">
                                <div class="col-2"><strong>Subject</strong></div>
                                <div class="col-10"><input wire:model.defer="contact_email_subject" type="text" class="form-control" placeholder="Type a subject line here"></div>
                            </div>
                            <hr>
                            <iframe id="enhancedTemplatePreview" class="border-0 w-100 h-100 d-none"></iframe>
                            <div id="standardTemplatePreview">
                                <textarea wire:model.defer="contact_email_content" id="contact_email_content" style="height:100%; width:100%;" rows=10></textarea>
                                <input type="hidden" id="contact_email_content_holding" value="{{$contact_email_content}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" wire:click="close_email_modal()" {{--data-dismiss="modal"--}}>Cancel</button>
                    <button type="button" class="btn btn-success" id="send_email_btn">Send Email</button>
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

        Livewire.on('show_email_modal', data => {
            $('#emailContactModal').modal('show');
            //app.makeTextEditorLivewire("#contact_email_content");
        });

        Livewire.on('contact_email_updated', data => {
            //app.makeTextEditorLivewire("#contact_email_content");
        });

        Livewire.on('close_email_modal', data => {
            $('#emailContactModal').modal('hide');
        });

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', (message, component) => {
                app.makeTextEditorLivewire("#contact_email_content");
            });
        });

        app.makeTextEditorLivewire = function(element){
            if (typeof ck_editor === 'undefined') {
                let ck_editor;
                if($(element).length > 0){
                    ClassicEditor.create( document.querySelector( element ),{
                        toolbar: ['heading', '|', 'bold', 'italic' ,'link','|', 'indent','outdent','|', 'bulletedList', 'numberedList', 'blockQuote','|', 'insertTable' ] //'underline',
                    } ).then( editor => {
                        ck_editor = editor; // Save for later use.
                        ck_editor.editing.view.document.on('keyup', (evt, data) => {
                            document.querySelector("#contact_email_content_holding").value = editor.getData();
                        });
                    } )
                    .catch( error => {
                        console.error( error );
                    } );
                }else{
                    console.log('cant create text editor: '+element+' does not exist');
                }
            }
        }

        $('#send_email_btn').on('click', function(){
            @this.set('contact_email_content', $('#contact_email_content_holding').val());
            @this.send_email();
        });

    </script>
@endpush
