@extends('layouts.app')

@section('title') {{ __('Add New Business Terms Consent Request') }} @endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('terms-consent') }}
@endsection

@section('content')
    <form method="POST" action="{{ route('terms-consent.store') }}">
        @csrf
        <input name="user_id" type="hidden" value="{{ Session::get('user_id', Auth::user()->id) }}" />
        <input name="link" type="hidden" value="{{$link}}" />

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
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee Type') }}</label>

                    <div class="col-md-8">
                        <div class="custom-control custom-radio mb-2">
                            <input id="type_no_fee" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('No Fee', old('type'))}} value="No Fee" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_no_fee">No Fee</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input id="type_fixed" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Fixed Fee', old('type'))}} value="Fixed Fee" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_fixed">Fixed Fee</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input id="type_percentage" type="radio" class="custom-control-input{{ $errors->has('type') ? ' is-invalid' : '' }}" name="type" {{checked('Percentage', old('type'))}} value="Percentage" data-fee-num="" required /> 
                            <label class="custom-control-label" for="type_percentage">Percentage</label>
                        </div>

                        @if ($errors->has('type'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group d-flex align-items-center row fees ">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee Amount') }}</label>

                    <div class="col-md-8">

                        <div class="list-group list-group-horizontal mb-3">
                            <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn no-fee" data-value="0.00" data-fee-num="">No Fee</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="195.00" data-fee-num="">&pound;195</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="295.00" data-fee-num="">&pound;295</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="395.00" data-fee-num="">&pound;395</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="495.00" data-fee-num="">&pound;495</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="595.00" data-fee-num="">&pound;595</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="695.00" data-fee-num="">&pound;695</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="795.00" data-fee-num="">&pound;795</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="895.00" data-fee-num="">&pound;895</button>
                            <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="995.00" data-fee-num="">&pound;995</button>
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
                    <label for="timing_na" class="col-md-2 col-form-label text-md-right">{{ __('Fee Timing') }}</label>

                    <div class="col-md-8">
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_na" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Not applicable', old('timing'))}} value="Not applicable" required /> 
                            <label class="custom-control-label" for="timing_na">Not applicable</label>
                        </div>    
                        <div class="custom-control custom-radio mb-2">
                            <input id="timing_application" type="radio" class="custom-control-input{{ $errors->has('timing') ? ' is-invalid' : '' }}" name="timing" {{checked('Application', old('timing'))}} value="Application" required /> 
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

                @if(session('account_id') == 4)

                    <hr />

                    <div class="form-group d-flex align-items-center row">
                        <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2 Type') }}</label>

                        <div class="col-md-8">
                            <div class="custom-control custom-radio mb-2">
                                <input id="type_2_no_fee" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('No Fee', old('type_2'))}} value="No Fee" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_no_fee">No Fee</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="type_2_fixed" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('Fixed Fee', old('type_2'))}} value="Fixed Fee" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_fixed">Fixed Fee</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="type_2_percentage" type="radio" class="custom-control-input{{ $errors->has('type_2') ? ' is-invalid' : '' }}" name="type_2" {{checked('Percentage', old('type_2'))}} value="Percentage" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="type_2_percentage">Percentage</label>
                            </div>

                            @if ($errors->has('type_2'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('type_2') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group d-flex align-items-center row fees">
                        <label for="amount_2" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2 Amount') }}</label>

                        <div class="col-md-8">

                            <div class="list-group list-group-horizontal mb-3">
                                <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn no-fee" data-value="0.00" data-fee-num="_2">No Fee</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="195.00" data-fee-num="_2">&pound;195</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="295.00" data-fee-num="_2">&pound;295</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="395.00" data-fee-num="_2">&pound;395</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="495.00" data-fee-num="_2">&pound;495</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="595.00" data-fee-num="_2">&pound;595</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="695.00" data-fee-num="_2">&pound;695</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="795.00" data-fee-num="_2">&pound;795</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="895.00" data-fee-num="_2">&pound;895</button>
                                <button type="button" class="list-group-item list-group-item-action fee-btn" data-value="995.00" data-fee-num="_2">&pound;995</button>
                            </div>

                            <input id="amount_2" type="number" step="0.01" class="tmbl-fee form-control @error('amount_2') is-invalid @enderror" name="amount_2" value="{{ old('amount_2', '') }}" placeholder='0.00' required>

                            @error('amount_2')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group d-flex align-items-center row">
                        <label for="timing_2_na" class="col-md-2 col-form-label text-md-right">{{ __('Fee 2 Timing') }}</label>

                        <div class="col-md-8">
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_na" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Not applicable', old('timing_2'))}} value="Not applicable" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_na">Not applicable</label>
                            </div>    
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_application" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Application', old('timing_2'))}} value="Application" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_application">Payable on application</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="timing_2_offer" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Offer', old('timing_2'))}} value="Offer" data-fee-num="_2" required /> 
                                <label class="custom-control-label" for="timing_2_offer">Payable at offer</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="timing_2_completion" type="radio" class="custom-control-input{{ $errors->has('timing_2') ? ' is-invalid' : '' }}" name="timing_2" {{checked('Completion', old('timing_2'))}} value="Completion" data-fee-num="_2" required /> 
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
                    <label for="service" class="col-md-2 col-form-label text-md-right">{{ __('Service') }}</label>

                    <div class="col-md-8">

                        <div class="list-group list-group-horizontal mb-3">
                            <button type="button" class="list-group-item list-group-item-action desc-btn @if(old('service') == 'MR') active @endif" data-desc="m_desc" data-value="MR">Adviser Referring Protection</button>
                            <button type="button" class="list-group-item list-group-item-action desc-btn @if(old('service') == 'MP') active @endif" data-desc="mp_desc" data-value="MP">Adviser Doing Protection</button>
                        </div>

                        <input id="service" name="service" type="hidden" value="{{old('service')}}" />
                    </div>
                </div>

                <div class="form-group d-flex align-items-center row">
                    <label for="description" class="col-md-2 col-form-label text-md-right">{{ __('Service Description') }}</label>

                    <div class="col-md-8">

                        <textarea id="description" name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description') }}</textarea>

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

    <span id="m_desc" class="d-none">Mortgage Advice with referred Protection Advice</span>
    <span id="mp_desc" class="d-none">Mortgage &amp; Protection Advice</span>
@endsection

@push('js')

<script>
    $(document).ready(function() {

        $('input[name="type"],input[name="type_2"]').change(function(){
            if($(this).val() == "Percentage"){
                $('button[data-fee-num="'+ $(this).data("fee-num") +'"].fee-btn').removeClass('active');
                $('#amount' + $(this).data("fee-num")).val(1);
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

        $('.desc-btn').click(function(){
            $('#description').val($('#'+$(this).data("desc")).text());
            $('#service').val($(this).data("value"));
            $('.desc-btn').removeClass('active');
            $(this).addClass('active');
        });

    });
</script>

@endpush