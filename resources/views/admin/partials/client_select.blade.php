<div class="form-group row">
    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Client') }}</label>

    <div class="col-md-6">

        <select name="client_id" id="client_id"  class="form-control select2 {{ $errors->has('client_id') ? ' is-invalid' : '' }}" required autofocus tabindex="1">
            <option value="" {!! selected('', old('client_id')) !!}>- Select -</option>
            <option value="0" {!! selected('0', old('client_id')) !!}>- New Client(s) -</option>

            @foreach($clients as $client)

                <option value="{{ $client->id }}" {!! selected($client->id, old('client_id', @$_GET['client'])) !!}>{{ $client->first_name }} {{ $client->last_name }} ({{ $client->email }})</option>

            @endforeach

        </select>

        <span class="help-text text-muted">{{ __('Search for an exisitng client or selecting \'New Client\' will reveal the relevant fields below') }}</span>

        @if ($errors->has('client_id'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('client_id') }}</strong>
            </span>
        @endif
    </div>
</div>

<div id="client_linked" class="@if (old('client_id_2') === null) d-none @endif">
    <div class="form-group row">
        <div class="col-md-4 text-md-right">
            Linked Client(s)
        </div>
        <div class="col-md-6" id="linked_clients">
            {{--<div class="custom-control custom-radio mb-2">
                <input id="client_id_2" type="checkbox" class="custom-control-input{{ $errors->has('client_id_2') ? ' is-invalid' : '' }}" name="client_id_2" value="old('client_id_2')" /> 
                <label class="custom-control-label" for="client_id_2">Also send to linked client <span id="linked_name"></span></label>
            </div>--}}
        </div>
    </div>
</div>

<div id="client_fields" class="@if (!$errors->any() || old('client_id') != 0) d-none @endif">
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
            <input id="email_2" type="email" class="form-control{{ $errors->has('email_2') ? ' is-invalid' : '' }}" name="email_2" value="{{ old('email_2') }}" tabindex="6" placeholder="(optional, but required if 2 clients)">

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
            <input id="tel_2" type="tel" class="form-control{{ $errors->has('tel_2') ? ' is-invalid' : '' }}" name="tel_2" value="{{ old('tel_2') }}" placeholder="(optional)" tabindex="9">

            @if ($errors->has('tel_2'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('tel_2') }}</strong>
                </span>
            @endif
        </div>
    </div>
    
</div>

@push('js')
    <script>
        $(document).ready(function(){

            $('#client_id').change(function(){
                //reset form
                $("#client_linked").addClass("d-none");
                $('#linked_clients').html('');
                $('input[type="submit"],button[type="submit"]').removeAttr('disabled');

                if($(this).val() == 0){
                    $('#client_fields').removeClass('d-none');
                }else{
                    $('#client_fields').addClass('d-none');

                    //check for linked clients
                    var data = {};
                    data._token = "{{ csrf_token() }}";
                    data.client_id = $(this).val();
                    $.ajax({
                        url: "{{route('clients.linked')}}",
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify(data),
                        cache: false,
                        success: function(response) {
                            if (response.status == 0 && response.links > 0) {
                                var links = JSON.parse(response.clients);
                                if(links.length > 0) {
                                    app.alerts.response('Linked Client','This client is linked to another, if you\'d like to send this request to both clients, tick the box','info');
                                    $("#client_linked").removeClass("d-none");

                                    links.forEach(function(link, index) {
                                        $("#linked_clients").append('<div class="custom-control custom-radio mb-2"><input type="checkbox" class="custom-control-input" name="linked[]" value="' + link.id + '" id="linked_client_' + index + '" /><label class="custom-control-label" for="linked_client_' + index + '">Also send to linked client ' + link.first_name + ' ' + link.last_name + '</label></div>');
                                    });
                                }
                            }
                        },
                        error: function (jqXHR, response, errorThrown) {
                            console.log(jqXHR);
                        }
                    });

                }
            });

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