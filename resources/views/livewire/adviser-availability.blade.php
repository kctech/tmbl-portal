<div wire:init="loadData">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

        <div id="topbar_title"><h1 class="h2">Adviser Availability</h1></div>

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

    <div class="card mb-3 p-3">
        <div class="row">
            <div class="col-2 d-flex align-items-center justify-content-center"><strong>Adviser(s)</strong></div>
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

            <h2>Live Availability</h2>
            <div class="card p-0 w-100 h-100 position-relative mb-3" style="min-height: @if(!empty($advisers)) 100px; @else 50vh; @endif">
                <div class="card-body p-0 position-absolute overflow-auto" style="top:0; bottom:0; left:0; right:0;">
                    <div class="list-group list-group-flush">
                        @foreach($adviser_list as $adviser)
                            @php
                                $adviser = (object) $adviser;
                                $adviser->presence = (object) $adviser->presence;
                            @endphp
                            @if(!in_array($adviser->email,$advisers) && !empty($advisers)) @continue @endif
                            <div class="list-group-item p-1">
                                <div class="row">
                                    <div class="col-1 text-right">
                                        <span class="tip" title="{{$adviser->presence->activity ?? 'unknown'}}">
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
                                    <div class="col-5 text-truncate">
                                        {{$adviser->first_name ?? '?'}} {{$adviser->last_name ?? '?'}}
                                    </div>
                                    <div class="col-1 text-right">
                                        @if(!is_null($adviser->mab_id))
                                            MAB <i class="fas fa-check"></i>
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="col-1 text-right">
                                        @if(!is_null($adviser->azure_id))
                                            Azure <i class="fas fa-check"></i>
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="col-2 text-right">
                                        {{\App\Libraries\Interpret::AzureStatus(($adviser->presence->activity ?? 'Unknown'))}}
                                        @if(!is_null(($adviser->presence->statusMessage->message->content ?? null)))
                                            - {{$adviser->presence->statusMessage->message->content ?? 'Blank Message'}}
                                        @endif
                                    </div>
                                    <div class="col-2 text-right">
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

            <h2>Weekly Availabilty <small class="float-right badge badge-primary">Updated {{$cache_date ?? 'Unknown'}}</small></h2>

            @foreach($calendar as $week_number => $week)
                <div class="card mb-3 p-3">
                    <div class="row">
                        <div class="col-2 d-flex flex-column align-items-center justify-content-center">
                            <strong>{{$week['title']}}</strong>
                            <div><span class="badge badge-primary">Starts {{$week['start_date']}}</span></div>
                        </div>
                        @foreach($week['days'] as $day => $hour)
                            <div class="col" @if($hour['is_past']) style="opacity:0.5;" @endif>
                                <div class="mb-1">
                                    <h3 class="mb-0">{{$day}}</h3>
                                    <span class="badge badge-primary">{{$hour['date']}}</span>
                                </div>

                                @foreach($hour['hours'] as $hour_number => $availability)
                                    @if($availability['availability'] > 0)
                                        <div class="card bg-light px-1 mb-1 w-100 @if($availability['is_past']) text-muted @else cursor-pointer @endif" @if($availability['is_past']) style="opacity:0.5;" @endif @if(!$availability['is_past']) wire:click="select_slot('{{$hour['date']}}','{{$hour_number}}')" @endif>
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
            @endforeach
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
                app.alerts.toast(data.message);
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
