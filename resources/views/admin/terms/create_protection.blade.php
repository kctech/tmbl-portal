@extends('layouts.app')

@section('title') {{ __('Add New Protection Business Terms Consent Request') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('terms-consent') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('terms-consent.store') }}">
        @csrf
        <input id="service" name="service" type="hidden" value="P" />
        <input name="user_id" type="hidden" value="{{ Session::get('user_id', Auth::user()->id) }}" />
        <input name="link" type="hidden" value="{{$link}}" />

        <input name="type_2" type="hidden" value="NA" />
        <input name="amount_2" type="hidden" value="0.00" />
        <input name="timing_2" type="hidden" value="NA" />

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

                @include('admin.partials.client_select',['clients' => $clients])

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
                            <input id="type_no_fee" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('No Fee', old('type', 'No Fee'))}} value="No Fee" required /> 
                            <label class="custom-control-label" for="type_no_fee">No Fee</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="type_fixed" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Fixed Fee', old('type'))}} value="Fixed Fee" required /> 
                            <label class="custom-control-label" for="type_fixed">Fixed Fee</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="type_percentage" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Percentage', old('type'))}} value="Percentage" required /> 
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
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee Amount') }}</label>

                    <div class="col-md-8">

                        <div class="list-group list-group-horizontal mb-3">
                            <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn no-fee active" data-value="0.00">No Fee</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="195.00">&pound;195</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="295.00">&pound;295</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="395.00">&pound;395</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="495.00">&pound;495</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="595.00">&pound;595</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="695.00">&pound;695</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="795.00">&pound;795</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="895.00">&pound;895</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="995.00">&pound;995</button>
                        </div>

                        <input id="amount" type="number" step="0.01" class="form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}" name="amount" value="{{ old('amount', '0.00') }}" placeholder='0.00' required>

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
                            <input id="timing_na" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Not applicable', old('timing'))}} value="Not applicable" required /> 
                            <label class="custom-control-label" for="timing_na">Not applicable</label>
                        </div>    
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_application" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Application', old('timing', 'Application'))}} value="Application" required /> 
                            <label class="custom-control-label" for="timing_application">Payable on application</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_offer" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Offer', old('timing'))}} value="Offer" required /> 
                            <label class="custom-control-label" for="timing_offer">Payable at offer</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="timing_completion" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Completion', old('timing'))}} value="Completion" required /> 
                            <label class="custom-control-label" for="timing_completion">Payable at completion stage</label>
                        </div>

                        @if ($errors->has('timing'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('timing') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group d-flex align-items-center row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Service Description') }}</label>

                    <div class="col-md-8">

                        <textarea id="description" name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description','Protection Advice, no fee is charged to you.') }}</textarea>

                        @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="form-group d-flex align-items-center row mb-3">
            <div class="col-md-8 offset-md-2">
                <button type="submit" class="btn btn-primary">
                    {{ __('Send Request') }}
                </button>
            </div>
        </div>

    </form>

@endsection

@push('js')

<script>
    $(document).ready(function() {

        $('input[name="type"]').change(function(){
            if($(this).val() == "Percentage"){
                $('.fee-btn').removeClass('active');
                $('#amount').val(2);
            }
            if($(this).val() == "No Fee"){
                $('.fee-btn.no-fee').addClass('active');
                $('#amount').val(0);
                $('input#timing_na').prop( "checked", true );
            }
        });

        $('.fee-btn').click(function(){
            $('#amount').val(parseFloat($(this).data("value")).toFixed(2));
            $('.fee-btn').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>

@endpush