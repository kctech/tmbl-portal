<div class="card mb-3">
    <div class="card-header">
        {{ __('Quote Information') }}
    </div>
    <div class="card-body">

        <div class="form-group row">
            <label for="purchase_val" class="col-md-3 col-form-label text-md-right">{{ __('Purchase Price/Property Value') }}</label>

            <div class="col-md-8">
                <input id="purchase_val" type="number" step="1" class="form-control @error('purchase_val') is-invalid @enderror" name="purchase_val" value="{{ old('purchase_val', $quote->purchase_val) }}" placeholder='500000' required>

                @error('purchase_val')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="loan_amnt" class="col-md-3 col-form-label text-md-right">{{ __('Loan Amount') }}</label>

            <div class="col-md-8">
                <input id="loan_amnt" type="number" step="1" class="form-control @error('loan_amnt') is-invalid @enderror" name="loan_amnt" value="{{ old('loan_amnt', $quote->loan_amnt) }}" placeholder='150000' required>

                @error('loan_amnt')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="loan_interest" class="col-md-3 col-form-label text-md-right">{{ __('Amount of Loan on Interest Only') }}</label>

            <div class="col-md-8">
                <input id="loan_interest" type="number" step="1" class="form-control @error('loan_interest') is-invalid @enderror" name="loan_interest" value="{{ old('loan_interest', $quote->loan_interest) }}" placeholder='0' required>

                @error('loan_interest')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="term_yrs" class="col-md-3 col-form-label text-md-right">{{ __('Term of Mortgage') }}</label>
            <div class="col-md-4">
                <select name="term_yrs" id="term_yrs"  class="svr-period-calc form-control select2--off @error('term_yrs') is-invalid @enderror" required>
                    <option value="" {{selected('', old('term_yrs'))}}>Years</option>    
                    @for ($i = 1; $i <= 40; $i++)
                        The current value is 
                        <option value="{{ $i }}" {{selected($i, old('term_yrs', $quote->term_yrs))}}>{{ $i }}</option>
                    @endfor
                </select>

                @error('term_yrs')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-md-4">
                <select name="term_mnth" id="term_mnth"  class="svr-period-calc form-control select2--off @error('term_mnth') is-invalid @enderror" required>
                    <option value="" {{selected('', old('term_mnth'))}}>Months</option>    
                    @for ($i = 0; $i <= 12; $i++)
                        The current value is 
                        <option value="{{ $i }}" {{selected($i, old('term_mnth', $quote->term_mnth))}}>{{ $i }}</option>
                    @endfor
                </select>
                @error('term_mnth')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        @if(session('account_id') == 4)

            <div class="form-group d-flex align-items-center row">
                <label for="fee" class="col-md-3 col-form-label text-md-right">{{ __('Fee 1') }}</label>

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="fee_type" class="form-label">Type</label>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_type_no_fee" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_type') ? ' is-invalid' : '' }}" name="fee_type" {{checked('No Fee', old('fee_type'))}} value="No Fee" required /> 
                                <label class="custom-control-label" for="fee_type_no_fee">No Fee</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_type_fixed" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_type') ? ' is-invalid' : '' }}" name="fee_type" {{checked('Fixed Fee', old('fee_type', 'Fixed Fee'))}} value="Fixed Fee" required /> 
                                <label class="custom-control-label" for="fee_type_fixed">Fixed Fee</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="fee_type_percentage" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_type') ? ' is-invalid' : '' }}" name="fee_type" {{checked('Percentage', old('fee_type'))}} value="Percentage" required /> 
                                <label class="custom-control-label" for="fee_type_percentage">Percentage</label>
                            </div>

                            @error('fee_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            
                            <label for="fee" class="form-label">Fee</label>
                            <input id="fee" type="number" step="0.01" class="fee-field form-control @error('fee') is-invalid @enderror" name="fee" value="{{ old('fee', $quote->fee) }}" placeholder='Enter fee' required>

                            @error('fee')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div class="mt-2">
                                <button type="button" class="badge badge-pill border-0 fee-btn no-fee @if($quote->fee == 0.00) badge-primary @else badge-secondary @endif" data-value="0.00">No Fee</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 195.00) badge-primary @else badge-secondary @endif" data-value="195.00">&pound;195</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 295.00) badge-primary @else badge-secondary @endif" data-value="295.00">&pound;295</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 395.00) badge-primary @else badge-secondary @endif" data-value="395.00">&pound;395</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 495.00) badge-primary @else badge-secondary @endif" data-value="495.00">&pound;495</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 595.00) badge-primary @else badge-secondary @endif" data-value="595.00">&pound;595</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 695.00) badge-primary @else badge-secondary @endif" data-value="695.00">&pound;695</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 795.00) badge-primary @else badge-secondary @endif" data-value="795.00">&pound;795</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 895.00) badge-primary @else badge-secondary @endif" data-value="895.00">&pound;895</button>
                                <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 995.00) badge-primary @else badge-secondary @endif" data-value="995.00">&pound;995</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="fee_timing" class="form-label">Timing</label>

                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_timing_na" type="radio" class="custom-control-input{{ $errors->has('fee_timing') ? ' is-invalid' : '' }}" name="fee_timing" {{checked('NA', old('fee_type'))}} value="NA" required /> 
                                <label class="custom-control-label" for="fee_type_no_fee">NA</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_timing_a" type="radio" class="custom-control-input{{ $errors->has('fee_timing') ? ' is-invalid' : '' }}" name="fee_timing" {{checked('Application', old('fee_type', 'Application'))}} value="Application" required /> 
                                <label class="custom-control-label" for="fee_timing_a">Application</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="fee_timing_c" type="radio" class="custom-control-input{{ $errors->has('fee_timing') ? ' is-invalid' : '' }}" name="fee_timing" {{checked('Completion', old('fee_type'))}} value="Completion" required /> 
                                <label class="custom-control-label" for="fee_timing_c">Completion</label>
                            </div>

                            @error('fee_timing')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group d-flex align-items-center row">
                <label for="fee_2" class="col-md-3 col-form-label text-md-right">{{ __('Fee 2') }}</label>

                <div class="col-md-8">

                    <hr />

                    <div class="row">
                        <div class="col-md-3">
                            <label for="fee_2_type" class="form-label">Type</label>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_2_type_na" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_2_type') ? ' is-invalid' : '' }}" name="fee_2_type" {{checked('NA', old('fee_2_type', 'NA'))}} value="NA" required /> 
                                <label class="custom-control-label" for="fee_2_type_na">NA</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_2_type_fixed" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_2_type') ? ' is-invalid' : '' }}" name="fee_2_type" {{checked('Fixed Fee', old('fee_2_type'))}} value="Fixed Fee" required /> 
                                <label class="custom-control-label" for="fee_2_type_fixed">Fixed Fee</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="fee_2_type_percentage" type="radio" class="custom-control-input fee-field{{ $errors->has('fee_2_type') ? ' is-invalid' : '' }}" name="fee_2_type" {{checked('Percentage', old('fee_2_type'))}} value="Percentage" required /> 
                                <label class="custom-control-label" for="fee_2_type_percentage">Percentage</label>
                            </div>

                            @error('fee_2_type')
                                <span class="invalid-fee_2dback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            
                            <label for="fee_2" class="form-label">Fee</label>
                            <input id="fee_2" type="number" step="0.01" class="fee-field form-control @error('fee_2') is-invalid @enderror" name="fee_2" value="{{ old('fee_2', $quote->fee_2) }}" placeholder='Enter Fee' required>

                            @error('fee_2')
                                <span class="invalid-fee_2dback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <div class="mt-2">
                                <button type="button" class="badge badge-pill border-0 fee-field fee_2-btn no-fee_2 @if($quote->fee_2 == 0.00) badge-primary @else badge-secondary @endif" data-value="0.00">No Fee</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 195.00) badge-primary @else badge-secondary @endif" data-value="195.00">&pound;195</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 295.00) badge-primary @else badge-secondary @endif" data-value="295.00">&pound;295</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 395.00) badge-primary @else badge-secondary @endif" data-value="395.00">&pound;395</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 495.00) badge-primary @else badge-secondary @endif" data-value="495.00">&pound;495</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 595.00) badge-primary @else badge-secondary @endif" data-value="595.00">&pound;595</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 695.00) badge-primary @else badge-secondary @endif" data-value="695.00">&pound;695</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 795.00) badge-primary @else badge-secondary @endif" data-value="795.00">&pound;795</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 895.00) badge-primary @else badge-secondary @endif" data-value="895.00">&pound;895</button>
                                <button type="button" class="badge badge-pill border-0 fee_2-btn @if($quote->fee_2 == 995.00) badge-primary @else badge-secondary @endif" data-value="995.00">&pound;995</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="fee_2_timing" class="form-label">Timing</label>

                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_2_timing_na" type="radio" class="custom-control-input{{ $errors->has('fee_2_timing') ? ' is-invalid' : '' }}" name="fee_2_timing" {{checked('NA', old('fee_2_type', 'NA'))}} value="NA" required /> 
                                <label class="custom-control-label" for="fee_2_type_no_fee_2">NA</label>
                            </div>
                            <div class="custom-control custom-radio mb-2">
                                <input id="fee_2_timing_a" type="radio" class="custom-control-input{{ $errors->has('fee_2_timing') ? ' is-invalid' : '' }}" name="fee_2_timing" {{checked('Application', old('fee_2_type'))}} value="Application" required /> 
                                <label class="custom-control-label" for="fee_2_timing_a">Application</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input id="fee_2_timing_c" type="radio" class="custom-control-input{{ $errors->has('fee_2_timing') ? ' is-invalid' : '' }}" name="fee_2_timing" {{checked('Completion', old('fee_2_type'))}} value="Completion" required /> 
                                <label class="custom-control-label" for="fee_2_timing_c">Completion</label>
                            </div>

                            @error('fee_2_timing')
                                <span class="invalid-fee_2dback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

        @else

            <div class="form-group d-flex align-items-center row">
                <label for="fee" class="col-md-3 col-form-label text-md-right">{{ __('Fee Amount') }}</label>

                <div class="col-md-8">

                    <div class="mb-2">
                        <small>Select Fee:</small>
                        <button type="button" class="badge badge-pill border-0 fee-btn no-fee @if($quote->fee == 0.00) badge-primary @else badge-secondary @endif" data-value="0.00">No Fee</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 195.00) badge-primary @else badge-secondary @endif" data-value="195.00">&pound;195</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 295.00) badge-primary @else badge-secondary @endif" data-value="295.00">&pound;295</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 395.00) badge-primary @else badge-secondary @endif" data-value="395.00">&pound;395</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 495.00) badge-primary @else badge-secondary @endif" data-value="495.00">&pound;495</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 595.00) badge-primary @else badge-secondary @endif" data-value="595.00">&pound;595</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 695.00) badge-primary @else badge-secondary @endif" data-value="695.00">&pound;695</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 795.00) badge-primary @else badge-secondary @endif" data-value="795.00">&pound;795</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 895.00) badge-primary @else badge-secondary @endif" data-value="895.00">&pound;895</button>
                        <button type="button" class="badge badge-pill border-0 fee-btn @if($quote->fee == 995.00) badge-primary @else badge-secondary @endif" data-value="995.00">&pound;995</button>
                    </div>

                    <input id="fee" type="number" step="0.01" class="fee-field form-control @error('fee') is-invalid @enderror" name="fee" value="{{ old('fee', $quote->fee) }}" placeholder='0.00' required>

                    @error('fee')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <input name="fee_type" type="hidden" value="Fixed Fee" />
            <input name="fee_timing" type="hidden" value="Application" />

            <input name="fee_2_type" type="hidden" value="NA" />
            <input name="fee_2" type="hidden" value="0.00" />
            <input name="fee_2_timing" type="hidden" value="NA" />

        @endif

        <div class="form-group row">
            <label for="email_intro" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Email Intro') }}</label>
            <div class="col-md-8">
                <select name="email_intro" id="email_intro" type="text" class="form-control @error('email_intro') is-invalid @enderror" required>
                    <option value="" {{selected('', old('email_intro', $quote->email_intro))}} >-- Select Email Intro --</option>    
                    <option value="Thank you for your mortgage enquiry earlier" data-val="2" {{selected('Thank you for your mortgage enquiry earlier', old('email_intro', $quote->email_intro))}} >Thank you for your mortgage enquiry earlier</option>
                    <option value="Thank you for your mortgage enquiry" data-val="2" {{selected('Thank you for your mortgage enquiry', old('email_intro', $quote->email_intro))}} >Thank you for your mortgage enquiry</option>
                    <option value="Thank you for discussing your situation with me today" data-val="2" {{selected('Thank you for discussing your situation with me today', old('email_intro', $quote->email_intro))}} >Thank you for discussing your situation with me today</option>
                    <option value="Thank you for your time earlier" data-val="3" {{selected('Thank you for your time earlier', old('email_intro', $quote->email_intro))}} >Thank you for your time earlier</option>
                </select>
                @error('email_intro')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="message" class="col-md-3 col-form-label text-md-right">{{ __('General Message To Client(s)') }}</label>

            <div class="col-md-8">
                <textarea id="message" rows="5" class="form-control @error('message') is-invalid @enderror" name="message" placeholder="Optional.">{{ old('message', $quote->message) }}</textarea>

                @error('message')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

    </div>
</div>