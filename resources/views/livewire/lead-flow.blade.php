<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-leads-center pt-3 pb-2 mb-3 border-bottom">

    <div id="topbar_title"><h1 class="h2">Leads Flow</h1></div>

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

<div wire:loading.remove>

    <div class="row mb-4">
        @forelse($data as $strategy)
            @php $strategy->info = (object) ($strategy->info ?? $strategy['info']); @endphp
            <div class="col">
                <div class="card">
                    <div class="card-header text-white bg-dark p-2">
                        @if($strategy->info->auto_contact == 0)
                            <span class="badge badge-light tip" title="Automatic - Stage {{$strategy->info->chase_order}}"><i class="fal fa-sync"></i> {{$strategy->info->chase_order}}</span>
                        @else
                            <span class="badge badge-light tip" title="Manual - Stage {{$strategy->info->chase_order}}"><i class="fal fa-user"></i> {{$strategy->info->chase_order}}</span>
                        @endif
                        <strong>{{$strategy->info->name}}</strong><br />
                        <small>
                            @if($strategy->info->auto_contact == 0)
                                {{ __('Automatically') }}
                            @else
                                {{ __('Manually') }}
                            @endif
                            here {{$strategy->info->chase_duration}} after creation
                        </small>
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($strategy->data as $lead)
                            @php $lead = (object) $lead; @endphp
                            <li class="list-group-item p-1">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="badge badge-dark tip" data-title="From source: {{$lead->source['source'] ?? 'Default'}}"><i class="fa fa-fw {{$lead->source['icon'] ?? 'fa-star'}}"></i> {{$lead->id}}</span> {{$lead->first_name}} {{$lead->last_name}}
                                        <br />
                                        <span class="badge badge-info text-white tip" data-title="Created {{\Carbon\Carbon::parse($lead->created_at)->format('d/m/Y H:i')}}"><i class="fa fa-plus"></i>{{\Carbon\Carbon::parse($lead->created_at)->diffForHumans()}}</span>
                                        @if(!empty($lead->last_contacted_at))
                                            <span class="badge badge-success tip" title="Contacted {{ $lead->contact_count }} times, last at {{$lead->last_contacted_at}}"><i class="fas fa-phone"></i> {{\Carbon\Carbon::parse($lead->last_contacted_at)->diffForHumans()}}</span>
                                        @endif
                                    </div>
                                    <div class="col d-flex align-items-center justify-content-end">
                                        <a class="btn btn-sm btn-primary" href="{{route('leads.manager-contact', [$lead->id, 'leads.flow'])}}"><i class="fa fa-chevron-right"></i></a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-1 text-muted">No leads in this step</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <div class="col">No Chase steps in strategy</div>
        @endforelse
    </div>

</div>

<div class="w-100" wire:loading>
    <div class="card mt-4 border-0 shadow">
        <div class="card-body p-1">
            <div class="p-3 d-flex align-leads-center justify-content-center text-center">
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
