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

        <div class="form-group d-flex align-items-center row">
            <label for="fee" class="col-md-3 col-form-label text-md-right">{{ __('Fee Amount') }}</label>

            <div class="col-md-8">

                <div class="list-group list-group-horizontal mb-3">
                    <button type="button" class="list-group-item list-group-item-action" disabled>Select a Fee:</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn no-fee @if($quote->fee == 0) active @endif" data-value="0.00">No Fee</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 195.00) active @endif" data-value="195.00">&pound;195</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 295.00) active @endif" data-value="295.00">&pound;295</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 395.00) active @endif" data-value="395.00">&pound;395</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 495.00) active @endif" data-value="495.00">&pound;495</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 595.00) active @endif" data-value="595.00">&pound;595</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 695.00) active @endif" data-value="695.00">&pound;695</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 795.00) active @endif" data-value="795.00">&pound;795</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 895.00) active @endif" data-value="895.00">&pound;895</button>
                    <button type="button" class="list-group-item list-group-item-action fee-btn @if($quote->fee == 995.00) active @endif" data-value="995.00">&pound;995</button>
                </div>

                <input id="fee" type="number" step="0.01" class="tmbl-fee form-control @error('fee') is-invalid @enderror" name="fee" value="{{ old('fee', $quote->fee) }}" placeholder='0.00' required>

                @error('fee')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <input name="fee_2" type="hidden" value="0.00" />

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