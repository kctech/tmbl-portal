<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">

    <div id="topbar_title"><h1 class="h2">Leads Dashboard</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
        <a href="{{ route('leads.manager') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Leads</a>
        <a href="{{ route('leads.sources') }}" class="btn btn-lg btn-primary ml-3 mb-3"><i class="fa fa-inbox-in"></i> Lead Sources</a>
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

    @foreach($data as $section_title => $section)
        <div class="row mb-4">
            <div class="col-12"><h2>{{$section_title}}</h2></div>
            @foreach($section as $stat)
                @include('dashboard.'.($stat->tpl ?? 'total'), ['stat' => $stat])
            @endforeach
        </div>
    @endforeach

</div>

<div class="w-100" wire:loading>
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
