<!-- BORROWING CALC -->
<div id="borrowing_calculator">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="form-group col-4">
                            <label for="cc_adjust">CC %</label>
                            <input type="number" min="1000" step="0.01" class="form-control borrowing-input" id="cc_adjust" name="cc_adjust" maxlength="6" value="0.04" />
                        </div>
                        <div class="form-group col-4">
                            <label for="upper_adjust">Upper Limit %</label>
                            <input type="number" min="0" step="0.01" class="form-control borrowing-input" id="upper_adjust" name="upper_adjust" maxlength="6" value="4.5" />
                        </div>
                        <div class="form-group col-4">
                            <label for="lower_adjust">Lower Limit %</label>
                            <input type="number" min="0" step="0.01" class="form-control borrowing-input" id="lower_adjust" name="lower_adjust" maxlength="6" value="3.75" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="borrowing_income1">Gross Income</label>
                            <input type="number" min="1000" step="500" class="form-control borrowing-input" id="borrowing_income1" name="borrowing_income1" maxlength="6" value="25000" />
                        </div>
                        <div class="form-group col-12">
                            <label for="borrowing_income2">Partners Gross Income</label>
                            <input type="number" min="0" step="500" class="form-control borrowing-input" id="borrowing_income2" name="borrowing_income2" maxlength="6" value="0" />
                        </div>
                        <div class="form-group col-12">
                            <label for="borrowing_allowances">Any Allowances</label>
                            <input type="number" min="0" step="1" class="form-control borrowing-input" id="borrowing_allowances" name="borrowing_allowances" maxlength="6" value="0" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="borrowing_loans">Monthly cost of loans</label>
                            <input type="number" min="0" step="1" class="form-control borrowing-input" id="borrowing_loans" name="borrowing_loans" maxlength="6" value="0" />
                            <small>(Leave zero if these loans will be cleared with the mortgage)</small>
                        </div>
                        <div class="form-group col-12">
                            <label for="borrowing_cc">Outstanding credit card balance</label>
                            <input type="number" min="0" step="1" class="form-control borrowing-input" id="borrowing_cc" name="borrowing_cc" maxlength="6" value="0" />
                            <small>(Leave zero if these balances will be cleared with the mortgage)</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-6">
                <p style="color:#fff;"><small>All fields Must be numeric, so &pound;35,000 is 35000</small></p>
        </div>
        <div class="form-group col-12 col-sm-6">
            <button type="button" id="borrowingCalc" class="btn btn-block btn-primary">Calculate</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success" id="borrowingValueborrowing" role="alert">
                An indication of your borrowing levels will be between
                <br />
                <strong>&pound;<span id="borrowing_lower">187,500</span> and &pound;<span id="borrowing_upper">225,000</span></strong>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function(){
            accounting.settings.currency.symbol  = "£";
            /* Borrowing CALC */
            $("#borrowingCalc").click(function(){$(".borrowing-input").trigger('change');});
            $(".borrowing-input").on('change',function(){
            
                if($(this).val() == ""){$(this).val(0);}
                var $cc_adjust = parseFloat($("#cc_adjust").val());
                var $upper_adjust = parseFloat($("#upper_adjust").val());
                var $lower_adjust = parseFloat($("#lower_adjust").val());
                var $income1 = parseInt($("#borrowing_income1").val());
                var $income2 = parseInt($("#borrowing_income2").val());
                var $allowances = parseInt($("#borrowing_allowances").val());
                
                var $loans = parseInt($("#borrowing_loans").val());
                var $cc = parseInt($("#borrowing_cc").val());
                
                var $total_in = $income1 + $income2 + $allowances;
                var $total_out = ($loans*12) + (($cc * $cc_adjust) * 12);
                
                var $borrowing = $total_in - $total_out;
                
                console.log($borrowing);
                
                var upper = $borrowing * $upper_adjust;
                var lower = $borrowing * $lower_adjust;
                
                $("#borrowing_upper").html(accounting.formatNumber(upper, 2, ",", "."));
                $("#borrowing_lower").html(accounting.formatNumber(lower, 2, ",", "."));
            });
        });
    </script>
@endpush