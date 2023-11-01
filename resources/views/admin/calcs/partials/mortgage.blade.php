<!-- MORTGAGE CALC -->
<div id="mortgage_calculator">
    <div class="row">
        <div class="form-group col-6 col-md-7">
            <label for="borrowedValue">Amount to borrow &#40;&pound;&#41;</label>
            <input type="number" min="1000" step="1" class="form-control mortgage-input" id="borrowedValue" name="borrow" maxlength="7" value="200000" />
        </div>
        <div class="form-group col-6 col-md-5">
            <label for="termValue">Term &#40;Years&#41;</label>
            <input type="number" min="1" step="1" class="form-control mortgage-input" id="termValue" name="term" maxlength="2" value="32" />
        </div>
        <div class="form-group col-6 col-md-7">
            <label for="interestRateValue">Interest &#40;&#37;&#41;</label>
            <input type="number" min="0" step="0.01" class="form-control mortgage-input" id="interestRateValue" name="interest_rate" maxlength="5" value="2.98" />
        </div>
        <div class="form-group col-6 col-md-5">
            <label for="mortgageCalc">&nbsp;</label>
            <button type="button" id="mortgageCalc" class="btn btn-block btn-primary">Calculate</button>
        </div>

        <div class="col-sm-12">
            <div class="alert alert-success" id="mortgageValue" role="alert">&pound; - /mo</div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function(){
            accounting.settings.currency.symbol  = "£";
            /* Mortgage CALC */
            $("#mortgageCalc").click(function(){$(".mortgage-input").trigger('change');});
            $(".mortgage-input").on('change',function(){
                var $borrowed = parseInt($("#borrowedValue").val(),10);
                var $interestRate = parseFloat($("#interestRateValue").val(),10);
                var $term = parseInt($("#termValue").val(),10);
                var $months = ( $term * 12 );
                var interestOnlyMort = ($borrowed * ($interestRate / 100)) / 12;
                var repaymentMort = ( $borrowed * (($interestRate/12) / 100) / ( 1 - Math.pow( 1 + (($interestRate/12) / 100), -$months)) );
                                    
                //var interestOnlyMortFormatted = accounting.formatMoney(interestOnlyMort, "£", 2, ",", ".");
                //var repaymentMortFormatted = accounting.formatMoney(repaymentMort, "£", 2, ",", ".");
                var interestOnlyMortFormatted = accounting.formatNumber(interestOnlyMort, 2, ",", ".");
                var repaymentMortFormatted = accounting.formatNumber(repaymentMort, 2, ",", ".");
                
                var outputHTML = "<strong>" + interestOnlyMortFormatted + "/mo</strong> (Interest Only)<br />" + "<strong>" + repaymentMortFormatted + "/mo</strong> (Repayment)";

                $("#mortgageValue").html(outputHTML);
            });
        });
    </script>
@endpush