<div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div id="topbar_title"><h1 class="h2">Edit Lead</h1></div>

    <div class="btn-toolbar mb-2 mb-md-0">
        {{ Breadcrumbs::render('leads') }}
    </div>
</div>

<div class="flash-message py-2">
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-{{ $msg }}">{!! Session::get('alert-' . $msg) !!} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>
        @endif
    @endforeach
</div> <!-- end .flash-message -->

<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>
    <div class="col-md-6">
        <input type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" wire:model.defer="first_name">
        @if ($errors->has('first_name'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('first_name') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>
    <div class="col-md-6">
        <input type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" wire:model.defer="last_name">
        @if ($errors->has('last_name'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('last_name') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>
    <div class="col-md-6">
        <input type="email" class="form-control{{ $errors->has('email_address') ? ' is-invalid' : '' }}" wire:model.defer="email_address">
        @if ($errors->has('email_address'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email_address') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Contact Number') }}</label>
    <div class="col-md-6">
        <input type="tel" class="form-control{{ $errors->has('contact_number') ? ' is-invalid' : '' }}" wire:model.defer="contact_number">
        @if ($errors->has('contact_number'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('contact_number') }}</strong>
            </span>
        @endif
    </div>
</div>
<div class="form-group row mb-0">
    <div class="col-md-6 offset-md-4">
        <button wire:click="save()" type="button" class="btn btn-primary">
            {{ __('Save') }}
        </button>
        <a href="{{route($redirect)}}" class="btn btn-danger ml-auto">
            {{ __('Cancel') }}
        </a>
    </div>
</div>

<div id="topbar_title_content" class="d-none">
    <h1 class="h2">Edit Lead: {{$lead->first_name}} {{$lead->last_name}}</h1>
    <span class="page-subtitle">ID: {{$lead->id}}</span>
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

        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', (message, component) => {
                $("#topbar_title").html($("#topbar_title_content").html());
            });

            $("#topbar_title").html($("#topbar_title_content").html());
        });

    </script>
@endpush
