<div class="col-md-12 product_fields" id="option_num{{$loop->iteration}}" data-counter="{{$loop->iteration}}">
    <div class="card mb-3">
        <div class="card-header">
            {{ __('Product:') }} <span class="product-name">{{$loop->iteration}}</span>
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-warning action-btn clear-fields @if($loop->index == 0) d-none @endif">Clear Fields</button>
                &nbsp;
                <button type="button" class="btn btn-sm btn-danger action-btn remove-option @if($loop->index == 0) d-none @endif">Remove Product</button>
            </div>
        </div>
        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="provider_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Provider') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][provider]" id="provider_num{{$loop->iteration}}" type="text" class="form-control form-control-sm @error('options.'.$loop->index.'.provider') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.provider', $option->provider) }}" placeholder='e.g. HSBC' required>

                            @error('options.'.$loop->index.'.provider')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="product_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Product') }}</label>
                        <div class="col-md-9">
                            <select name="options[{{$loop->index}}][product]" id="product_num{{$loop->iteration}}" type="text" class="form-control NOselect2--product form-control-sm @error('options.'.$loop->index.'.product') is-invalid @enderror" required>
                                <option value="" {{selected('', old('options.'.$loop->index.'.product', $option->product))}} >-- Select Product--</option>    
                                <option value="2 Year Fixed Rate" data-val="2" {{selected('2 Year Fixed Rate', old('options.'.$loop->index.'.product', $option->product))}} >2 Year Fixed Rate</option>
                                <option value="2 Year Tracker Rate" data-val="2" {{selected('2 Year Tracker Rate', old('options.'.$loop->index.'.product', $option->product))}} >2 Year Tracker Rate</option>
                                <option value="3 Year Fixed Rate" data-val="3" {{selected('3 Year Fixed Rate', old('options.'.$loop->index.'.product', $option->product))}} >3 Year Fixed Rate</option>
                                <option value="5 Year Fixed Rate" {{selected('5 Year Fixed Rate', old('options.'.$loop->index.'.product', $option->product))}} >5 Year Fixed Rate</option>
                                <option value="7 Year Fixed Rate" data-val="7" {{selected('7 Year Fixed Rate', old('options.'.$loop->index.'.product', $option->product))}} >7 Year Fixed Rate</option>
                                <option value="10 Year Fixed Rate" data-val="10" {{selected('10 Year Fixed Rate', old('options.'.$loop->index.'.product', $option->product))}} >10 Year Fixed Rate</option>
                                <option value="Discounted Variable Rate" {{selected('Discounted Variable Rate', old('options.'.$loop->index.'.product', $option->product))}} >Discounted Variable Rate</option>
                            </select>
                            @error('options.'.$loop->index.'.product')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="end_date_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('End Date') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][end_date]" id="end_date_num{{$loop->iteration}}" type="text" class="component_future_datepicker form-control form-control-sm @error('options.'.$loop->index.'.end_date') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.end_date', $option->end_date) }}" placeholder='dd/mm/yyyy' required>

                            @error('options.'.$loop->index.'.end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="initial_rate_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Initial Rate') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][initial_rate]" id="initial_rate_num{{$loop->iteration}}" type="number" step="0.01" data-for="options_num{{$loop->iteration}}" class="total-cost-calc initial-calc form-control form-control-sm @error('options.'.$loop->index.'.initial_rate') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.initial_rate', $option->initial_rate) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.initial_rate')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="monthly_payment_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Monthly Payment') }}</label>
                        <div class="col-md-9">

                        <div class="input-group">

                            <input name="options[{{$loop->index}}][monthly_payment]" id="monthly_payment_num{{$loop->iteration}}" type="number" step="0.01" aria-describedby="payment_options_num{{$loop->iteration}}" class="total-cost-calc form-control form-control-sm @error('options.'.$loop->index.'.monthly_payment') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.monthly_payment', $option->monthly_payment) }}" placeholder='' required>
                                <div class="input-group-append" id="payment_options_num{{$loop->iteration}}">
                                    <button id="int_options_num{{$loop->iteration}}" class="btn btn-secondary btn-sm opt_sel" type="button" data-val="0" data-for="monthly_payment_num{{$loop->iteration}}"><i class="fa fa-percentage"></i></button>
                                    <button id="repay_options_num{{$loop->iteration}}" class="btn btn-secondary btn-sm opt_sel" type="button" data-val="0" data-for="monthly_payment_num{{$loop->iteration}}"><i class="fa fa-pound-sign"></i></button>
                                </div>
                            </div>

                            @error('options.'.$loop->index.'.monthly_payment')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lender_prod_fee_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Lender Product Fee') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][lender_prod_fee]" id="lender_prod_fee_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc form-control form-control-sm @error('options.'.$loop->index.'.lender_prod_fee') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.lender_prod_fee', $option->lender_prod_fee) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.lender_prod_fee')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lender_val_fee_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Lender Valuation Fee') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][lender_val_fee]" id="lender_val_fee_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc form-control form-control-sm @error('options.'.$loop->index.'.lender_val_fee') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.lender_val_fee', $option->lender_val_fee) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.lender_val_fee')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="lender_exit_fee_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Lender Exit Fee') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][lender_exit_fee]" id="lender_exit_fee_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc form-control form-control-sm @error('options.'.$loop->index.'.lender_exit_fee') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.lender_exit_fee', $option->lender_exit_fee) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.lender_exit_fee')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="other_fees_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Other Fees (e.g. CHAPS)') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][other_fees]" id="other_fees_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc form-control form-control-sm @error('options.'.$loop->index.'.other_fees') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.other_fees', $option->other_fees) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.other_fees')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="incl_std_legal_fees_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Incl. Standard Legal Costs?') }}</label>
                        <div class="col-md-9">
                            <div class="form-control w-auto h-auto p-0 btn-group btn-group-sm btn-group-toggle @error('options.'.$loop->index.'.incl_std_legal_fees') is-invalid @enderror" data-toggle="buttons">
                                <label class="btn btn-light active">
                                    <input type="radio" name="options[{{$loop->index}}][incl_std_legal_fees]" id="incl_std_legal_fees_num{{$loop->iteration}}" autocomplete="off" value="" {{checked('', old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees))}} readonly> select one
                                </label>
                                <label class="btn btn-secondary @if(old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees) == 'Yes') active @endif">
                                    <input type="radio" name="options[{{$loop->index}}][incl_std_legal_fees]" id="incl_std_legal_fees_Y_num{{$loop->iteration}}" autocomplete="off" value="Yes" {{checked('Yes', old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees))}}> Yes
                                </label>
                                <label class="btn btn-secondary @if(old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees) == 'No') active @endif">
                                    <input type="radio" name="options[{{$loop->index}}][incl_std_legal_fees]" id="incl_std_legal_fees_N_num{{$loop->iteration}}" autocomplete="off" value="No" {{checked('No', old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees))}}> No
                                </label>
                                <label class="btn btn-secondary @if(old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees) == 'NA') active @endif">
                                    <input type="radio" name="options[{{$loop->index}}][incl_std_legal_fees]" id="incl_std_legal_fees_NA_num{{$loop->iteration}}" autocomplete="off" value="NA" {{checked('NA', old('options.'.$loop->index.'.incl_std_legal_fees', $option->incl_std_legal_fees))}}> N/A
                                </label>
                            </div>
                            @error('options.'.$loop->index.'.incl_std_legal_fees')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="other_lender_incentives_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Other Lender Incentives') }}</label>
                        <div class="col-md-9">
                            <textarea name="options[{{$loop->index}}][other_lender_incentives]" id="other_lender_incentives_num{{$loop->iteration}}" rows="2" class="form-control form-control-sm @error('options.'.$loop->index.'.other_lender_incentives') is-invalid @enderror" required>{{ old('options.'.$loop->index.'.other_lender_incentives', $option->other_lender_incentives) }}</textarea>

                            @error('options.'.$loop->index.'.other_lender_incentives')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group row">
                        <label for="tmbl_fee_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Our Fee') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][tmbl_fee]" id="tmbl_fee_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc tmbl-fee form-control form-control-sm @error('options.'.$loop->index.'.tmbl_fee') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.tmbl_fee', $option->tmbl_fee) }}" placeholder='auto populated from above' required>

                            @error('options.'.$loop->index.'.tmbl_fee')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="details_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Details') }}</label>
                        <div class="col-md-9">

                            <div class="list-group list-group-horizontal mb-3">
                                <button type="button" class="list-group-item list-group-item-action" disabled>Select a desc:</button>
                                <button type="button" class="list-group-item list-group-item-action desc-btn" data-value="F" data-for="details_num{{$loop->iteration}}">Fixed</button>
                                <button type="button" class="list-group-item list-group-item-action desc-btn" data-value="LT" data-for="details_num{{$loop->iteration}}">Limted Tracker</button>
                                <button type="button" class="list-group-item list-group-item-action desc-btn" data-value="T" data-for="details_num{{$loop->iteration}}">Tracker</button>
                                <button type="button" class="list-group-item list-group-item-action desc-btn" data-value="D" data-for="details_num{{$loop->iteration}}">Discounted</button>
                            </div>

                            <textarea name="options[{{$loop->index}}][details]" id="details_num{{$loop->iteration}}" rows="4" class="form-control form-control-sm @error('options.'.$loop->index.'.details') is-invalid @enderror" required>{{ old('options.'.$loop->index.'.details', $option->details) }}</textarea>

                            @error('options.'.$loop->index.'.details')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="initial_period_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Initial Period (in months)') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][initial_period]" id="initial_period_num{{$loop->iteration}}" type="number" step="1" class="total-cost-calc svr-period-calc initial-period form-control form-control-sm @error('options.'.$loop->index.'.initial_period') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.initial_period', $option->initial_period) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.initial_period')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="svr_period_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('SVR Period (in months)') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][svr_period]" id="svr_period_num{{$loop->iteration}}" type="number" step="1" class="total-cost-calc svr-period form-control form-control-sm @error('options.'.$loop->index.'.svr_period') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.svr_period', $option->svr_period) }}" placeholder='auto calculated' required readonly>

                            @error('options.'.$loop->index.'.svr_period')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="svr_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Standard Variable Rate') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][svr]" id="svr_num{{$loop->iteration}}" type="number" step="0.01"data-for="svr_options_num{{$loop->iteration}}" class="total-cost-calc svr-calc form-control form-control-sm @error('options.'.$loop->index.'.svr') is-invalid @enderror}" value="{{ old('options.'.$loop->index.'.svr', $option->svr) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.svr')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="svr_monthly_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('SVR Monthly Payments') }}</label>
                        <div class="col-md-9">
                            
                            <div class="input-group">
                                <input name="options[{{$loop->index}}][svr_monthly]" id="svr_monthly_num{{$loop->iteration}}" aria-describedby="svr_options_num{{$loop->iteration}}" type="number" step="0.01" class="total-cost-calc svr-monthly form-control form-control-sm @error('options.'.$loop->index.'.svr_monthly') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.svr_monthly', $option->svr_monthly) }}" placeholder='' required>
                                {{--<div class="input-group-append" id="svr_options_num{{$loop->iteration}}">
                                    <button id="int_svr_options_num{{$loop->iteration}}" class="btn btn-secondary btn-sm opt_sel" type="button" data-val="0" data-for="svr_monthly_num{{$loop->iteration}}"><i class="fa fa-percentage"></i></button>
                                    <button id="repay_svr_options_num{{$loop->iteration}}" class="btn btn-secondary btn-sm opt_sel" type="button" data-val="0" data-for="svr_monthly_num{{$loop->iteration}}"><i class="fa fa-pound-sign"></i></button>
                                </div>--}}
                            </div>
                            
                            @error('options.'.$loop->index.'.svr_monthly')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="total_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('Total Cost of Credit (includes interest, fees & sum borrowed)') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][total]" id="total_num{{$loop->iteration}}" type="text" class="total-cost form-control form-control-sm @error('options.'.$loop->index.'.total') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.total', $option->total) }}" placeholder='auto calculated' required readonly>

                            @error('options.'.$loop->index.'.total')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="aprc_num{{$loop->iteration}}" class="col-md-3 col-form-label col-form-label-sm text-md-right">{{ __('APRC') }}</label>
                        <div class="col-md-9">
                            <input name="options[{{$loop->index}}][aprc]" id="aprc_num{{$loop->iteration}}" type="number" step="0.01" class="form-control form-control-sm @error('options.'.$loop->index.'.aprc') is-invalid @enderror" value="{{ old('options.'.$loop->index.'.aprc', $option->aprc) }}" placeholder='' required>

                            @error('options.'.$loop->index.'.aprc')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>