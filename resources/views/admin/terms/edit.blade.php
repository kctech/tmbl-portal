@extends('layouts.app')

@section('title') {{ __('Edit Business Terms Consent Request') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('terms-consent') }}
@endsection

@section('content')
    <form id="edit" method="POST" action="{{ route('terms-consent.update',$consent->id) }}">
        @csrf
        @method('PATCH')
        <input name="user_id" type="hidden" value="{{ $consent->user_id }}" />
        <input name="client_id" type="hidden" value="{{ $consent->client_id }}" />

        @if ($errors->any())
            <div class="alert alert-danger">
                <h3>There's a probelm with your submission:</h3>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!Session::has('impersonate'))
            @include('admin.partials.adviser')
        @else
            @include('admin.partials.adviser_impersonating')
        @endif

        <div class="card mb-3">
            <div class="card-header">
                {{ __('Client Information') }}
            </div>
            <div class="card-body">

                @include('admin.partials.client_edit',['client' => $consent->client])

            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header">
                {{ __('Quote Information') }}
            </div>
            <div class="card-body">
                <div class="form-group d-flex align-items-center row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Type') }}</label>

                    <div class="col-md-8">
                        <div class="custom-control custom-radio mb-2">
                            <input id="type_no_fee" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('No Fee', old('type', $consent->type))}} value="No Fee" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_no_fee">No Fee</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="type_fixed" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Fixed Fee', old('type', $consent->type))}} value="Fixed Fee" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_fixed">Fixed Fee</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="type_percentage" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Percentage', old('type', $consent->type))}} value="Percentage" data-fee-num="" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_percentage">Percentage</label>
                        </div>

                        @if ($errors->has('type'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group d-flex align-items-center row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee') }}</label>

                    <div class="col-md-8">

                        <div class="list-group list-group-horizontal mb-3">
                            <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn no-fee @if($consent->amount == 0.00) active @endif" data-value="0.00" data-fee-num="">No Fee</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 195.00) active @endif" data-value="195.00" data-fee-num="">&pound;195</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 295.00) active @endif" data-value="295.00" data-fee-num="">&pound;295</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 395.00) active @endif" data-value="395.00" data-fee-num="">&pound;395</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 495.00) active @endif" data-value="495.00" data-fee-num="">&pound;495</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 595.00) active @endif" data-value="595.00" data-fee-num="">&pound;595</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 695.00) active @endif" data-value="695.00" data-fee-num="">&pound;695</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 795.00) active @endif" data-value="795.00" data-fee-num="">&pound;795</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 895.00) active @endif" data-value="895.00" data-fee-num="">&pound;895</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount == 995.00) active @endif" data-value="995.00" data-fee-num="">&pound;995</button>
                        </div>

                        <input id="amount" type="number" step="0.01" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" name="amount" value="{{ old('amount', $consent->amount) }}" placeholder='0.00' required>

                        @if ($errors->has('amount'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('amount') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group d-flex align-items-center row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee Timing') }}</label>

                    <div class="col-md-8">
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_na" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Not applicable', old('timing', $consent->timing))}} value="Not applicable" data-fee-num="" required /> 
                            <label class="custom-control-label" for="timing_na">Not applicable</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_application" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Application', old('timing', $consent->timing))}} value="Application" data-fee-num="" required /> 
                            <label class="custom-control-label" for="timing_application">Payable on application</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_offer" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Offer', old('timing', $consent->timing))}} value="Offer" data-fee-num="" required /> 
                            <label class="custom-control-label" for="timing_offer">Payable at offer</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="timing_completion" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Completion', old('timing', $consent->timing))}} value="Completion" data-fee-num="" required /> 
                            <label class="custom-control-label" for="timing_completion">Payable at completion stage</label>
                        </div>

                        @if ($errors->has('timing'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('timing') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                @if(session('account_id') == 4)

                    <hr />

                    <div class="form-group d-flex align-items-center row">
                        <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2 Type') }}</label>

                        <div class="col-md-8">
                            <div class="custom-control custom-radio mb-2">
                                <input id="type_2_no_fee" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('No Fee', old('type_2', $consent->type_2))}} value="No Fee" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_no_fee">No Fee</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="type_2_fixed" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('Fixed Fee', old('type_2', $consent->type_2))}} value="Fixed Fee" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_fixed">Fixed Fee</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="type_2_percentage" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('Percentage', old('type_2', $consent->type_2))}} value="Percentage" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_percentage">Percentage</label>
                            </div>

                            @if ($errors->has('type_2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('type_2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group d-flex align-items-center row">
                        <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2') }}</label>

                        <div class="col-md-8">

                            <div class="list-group list-group-horizontal mb-3">
                                <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn no-fee @if($consent->amount_2 == 0.00) active @endif" data-value="0.00" data-fee-num="_2">No Fee</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 195.00) active @endif" data-value="195.00" data-fee-num="_2">&pound;195</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 295.00) active @endif" data-value="295.00" data-fee-num="_2">&pound;295</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 395.00) active @endif" data-value="395.00" data-fee-num="_2">&pound;395</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 495.00) active @endif" data-value="495.00" data-fee-num="_2">&pound;495</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 595.00) active @endif" data-value="595.00" data-fee-num="_2">&pound;595</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 695.00) active @endif" data-value="695.00" data-fee-num="_2">&pound;695</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 795.00) active @endif" data-value="795.00" data-fee-num="_2">&pound;795</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 895.00) active @endif" data-value="895.00" data-fee-num="_2">&pound;895</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn @if($consent->amount_2 == 995.00) active @endif" data-value="995.00" data-fee-num="_2">&pound;995</button>
                            </div>

                            <input id="amount_2" type="number" step="0.01" class="form-control{{ $errors->has('amount_2') ? ' is-invalid' : '' }}" name="amount_2" value="{{ old('amount_2', $consent->amount_2) }}" placeholder='0.00' required>

                            @if ($errors->has('amount_2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('amount_2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group d-flex align-items-center row">
                        <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2 Timing') }}</label>

                        <div class="col-md-8">
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_na" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Not applicable', old('timing_2', $consent->timing_2))}} value="Not applicable" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_na">Not applicable</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_application" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Application', old('timing_2', $consent->timing_2))}} value="Application" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_application">Payable on application</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_offer" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Offer', old('timing_2', $consent->timing_2))}} value="Offer" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_offer">Payable at offer</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="timing_2_completion" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Completion', old('timing_2', $consent->timing_2))}} value="Completion" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_completion">Payable at completion stage</label>
                            </div>

                            @if ($errors->has('timing_2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('timing_2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <hr />

                @else

                    <input name="type_2" type="hidden" value="NA" />
                    <input name="amount_2" type="hidden" value="0.00" />
                    <input name="timing_2" type="hidden" value="NA" />

                @endif

                <div class="form-group d-flex align-items-center row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Service Description') }}</label>

                    <div class="col-md-8">
                        <textarea id="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description">{{ old('description', $consent->description) }}</textarea>

                        @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="form-group row mb-3">
            <div class="col-md-8 offset-md-2 d-flex">
                <button type="submit" class="btn btn-primary">
                    {{ __('Edit Request') }}
                </button>
                <button type="button" class="btn btn-danger ml-auto" onclick="app.alerts.confirmDelete('delete','Business Terms Request ID {{$consent->id}}')">
                    {{ __('Delete Request') }}
                </button>
            </div>
        </div>
    </form>

    <form id="delete" method="POST" action="{{ route('terms-consent.destroy',$consent->id) }}">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')

<script>

    $(document).ready(function() {
        $('input[name="type"],input[name="type_2"]').change(function(){
            if($(this).val() == "Percentage"){
                $('button[data-fee-num="'+ $(this).data("fee-num") +'"].fee-btn').removeClass('active');
                $('#amount' + $(this).data("fee-num")).val(2);
            }
            if($(this).val() == "No Fee"){
                $('button[data-fee-num="'+ $(this).data("fee-num") +'"].fee-btn').removeClass('active');
                $('button[data-fee-num="'+ $(this).data("fee-num") +'"].fee-btn.no-fee').addClass('active');
                $('#amount').val(0);
                $('input#timing' + $(this).data("fee-num") + '_na').prop( "checked", true );
            }
        });

        $('.fee-btn').click(function(){
            $('#amount' + $(this).data("fee-num")).val(parseFloat($(this).data("value")).toFixed(2));
            $('button[data-fee-num="'+ $(this).data("fee-num") +'"].fee-btn').removeClass('active');
            $(this).addClass('active');
        });
    });

</script>

@endpush