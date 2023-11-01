@extends('layouts.app')

@section('title') {{ __('Add New Client') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('clients') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf
        <input name="user_id" type="hidden" value="{{ Session::get('user_id', Auth::user()->id) }}" />
        <input name="link" type="hidden" value="{{$link}}" />

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif
        
        <div class="row">
            <div class="col-md-4 text-md-right"><strong>{{ __('Field') }}</strong></div>
            <div class="col-md-3"><strong>{{ __('Client 1 (required)') }}</strong></div>
            <div class="col-md-3"><strong>{{ __('Client 2 (optional)') }}</strong></div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

            <div class="col-md-3">
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" tabindex="2">

                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-md-3">
                <input id="email_2" type="email_2" class="form-control{{ $errors->has('email_2') ? ' is-invalid' : '' }}" name="email_2" value="{{ old('email_2') }}" tabindex="6" placeholder="(optional, but required if 2 clients)">

                @if ($errors->has('email_2'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email_2') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

            <div class="col-md-3">
                <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" tabindex="3">

                @if ($errors->has('first_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('first_name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-md-3">
                <input id="first_name_2" type="text" class="form-control{{ $errors->has('first_name_2') ? ' is-invalid' : '' }}" name="first_name_2" value="{{ old('first_name_2') }}" tabindex="7" placeholder="(optional, but required if 2 clients)" >

                @if ($errors->has('first_name_2'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('first_name_2') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

            <div class="col-md-3">
                <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" tabindex="4">

                @if ($errors->has('last_name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </span>
                @endif
            </div>
            <div class="col-md-3">
                <input id="last_name_2" type="text" class="form-control{{ $errors->has('last_name_2') ? ' is-invalid' : '' }}" name="last_name_2" value="{{ old('last_name_2') }}" tabindex="8" placeholder="(optional, but required if 2 clients)" >

                @if ($errors->has('last_name_2'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('last_name_2') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Phone Number') }}</label>

            <div class="col-md-3">
                <input id="tel" type="tel" class="form-control{{ $errors->has('tel') ? ' is-invalid' : '' }}" name="tel" value="{{ old('tel') }}" placeholder="(optional)" tabindex="5">

                @if ($errors->has('tel'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tel') }}</strong>
                    </span>
                @endif
            </div>

            <div class="col-md-3">
                <input id="tel_2" type="tel_2" class="form-control{{ $errors->has('tel_2') ? ' is-invalid' : '' }}" name="tel_2" value="{{ old('tel_2') }}" placeholder="(optional)" tabindex="9">

                @if ($errors->has('tel_2'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tel_2') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Add Client') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@push('js')

<script>
    $(document).ready(function() {

        $('#email,#email_2').on('change',function(){
            $('input[type="submit"],button[type="submit"]').attr('disabled',true);
            //check for duplicate clients
            var data = {};
            data._token = "{{ csrf_token() }}";
            data.email = $(this).val();
            $.ajax({
                url: "{{route('clients.duplicate')}}",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(data),
                cache: false,
                success: function(response) {
                    if(response.count > 0) {
                        app.alerts.response('Duplicate Client','This client alreay exists, please use the search from the dropdown box to find them','error');
                    }else{
                        $('input[type="submit"],button[type="submit"]').removeAttr('disabled');
                    }
                },
                error: function (jqXHR, response, errorThrown) {
                    console.log(jqXHR);
                    $('input[type="submit"],button[type="submit"]').removeAttr('disabled');
                }
            });
        });

    });
</script>

@endpush