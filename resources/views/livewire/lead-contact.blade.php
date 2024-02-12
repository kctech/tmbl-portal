<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <div id="topbar_title"><h1 class="h2">Arrange a meeting</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <a href="{{ route('leads.manage') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Leads</a>
    </div>
</div>

<div class="flash-message py-2">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

<div wire:init="loadData" wire:loading.remove>

    @if($isLoaded)
        {{--
            @foreach($data as $adviser => $ad)
                @if(in_array($adviser,$advisers))
                    <pre>{{print_r($ad)}}</pre>
                @endif
            @endforeach
        --}}

        <div class="card mb-3 p-3">
            <div class="row">
                <div class="col-2 d-flex align-items-center justify-content-center"><strong>Adviser(s)</strong></div>
                <div class="col-10">
                    <select wire:model="advisers" id="advisers" class="form-controlX select2-tags">
                        @foreach($adviser_list as $adviser)
                            @php $adviser = (object) $adviser @endphp
                            @if($adviser->email)
                                <option value="{{$adviser->email}} ">
                                    {{$adviser->first_name}} {{$adviser->last_name}}
                                    <span class="tip" title="{{$adviser->presence->activity ?? 'unknown'}}">
                                        @switch(($adviser->presence->availability ?? 'unknown'))
                                            @case('Available')
                                                <i class="fas fa-check-circle text-success"></i>
                                                @break
                                            @case('Busy')
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
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <pre>{{print_r($advisers)}}</pre>

        @foreach($calendar as $week_number => $week)
            <div class="card mb-3 p-3">
                <div class="row">
                    <div class="col-2 d-flex align-items-center justify-content-center"><strong>{{$week_number}}</strong></div>
                    @foreach($week as $day => $hour)
                        <div class="col">
                            <div class="mb-1">
                                <h3 class="mb-0">{{$day}}</h3>
                                <span class="badge badge-primary">{{$hour['date']}}</span>
                            </div>
                            @if(!$hour['is_past'])
                                @foreach($hour['hours'] as $hour_number => $availability)
                                    @if($availability['availability'] > 0)
                                        <div class="card bg-light px-1 mb-1 w-100 @if($availability['is_past']) text-muted @else cursor-pointer @endif" @if(!$availability['is_past']) wire:click="select('{{$hour['date']}}','{{$hour_number}}')" @endif>
                                            <div class="row">
                                                <div class="col-6">{{ str_pad($hour_number,  2, "0", STR_PAD_LEFT) }}:00</div>
                                                <div class="col-6 text-right">{{$availability['availability']}}</div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
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
            console.log("message");
            if(typeof data.message != 'undefined'){
                app.alerts.toast(data.message,'error');
            }else{
                app.alerts.toast('An error occurred','error');
            }
        });

    </script>
@endpush
