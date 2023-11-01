<!-- SDLT CALC -->
<div id="sdlt_calculator">
    <div class="row">
        <div class="form-group col-6 col-md-7">
            <label for="propValue">Property Value</label>
            <input type="number" min="1000" step="1" class="form-control sdlt-input" id="propValue" placeholder="e.g. 375000">
        </div>
        <div class="col-6 col-md-5">
            <label for="sdltCalc">&nbsp;</label>
            <button type="button" id="sdltCalc" class="btn btn-block btn-primary">Calculate</button>
        </div>
        <div class="col-12">
            <p>Must be numeric, so &pound;375,000 is 375000</p>
            <div class="alert alert-success" id="sdltValue" role="alert">&pound; -</div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function(){
            accounting.settings.currency.symbol  = "£";
            /* SDLT CALC */
            $("#sdltCalc").click(function(){$(".sdlt-input").trigger('change');});
            $(".sdlt-input").on('change',function(){
                var propVal = parseInt($("#propValue").val(),10);
                var sdlt = 0;

                /*nothing on the first £125,000 of the property price*/
                if(propVal <= 125000){
                sdlt = 0;
                }
                /*2% on the next £125,000 (£125,000 - £250,000)*/
                if(propVal > 125000 && propVal <= 250000){
                sdlt += (propVal - 125000)*0.02;
                }
                /*5% on the next £675,000 (£250,001 - £925,000)*/
                if(propVal > 250000 && propVal <= 925000){
                sdlt += 125000*0.02;
                sdlt += (propVal - 250000)*0.05;
                }
                /*10% on the next £575,000 (£925,001 - £1.5 million)*/
                if (propVal > 925000 && propVal <= 1500000){
                sdlt += 125000*0.02;
                sdlt += 675000*0.05;
                sdlt += (propVal - 925000)*0.1;
                }
                /*12% on the rest (above £1.5 million)*/
                if(propVal > 1500000){
                sdlt += 125000*0.02;
                sdlt += 675000*0.05;
                sdlt += 575000*0.1;
                sdlt += (propVal - 1500000)*0.12;
                }

                //var formatted = accounting.formatMoney(sdlt, "£", 2, ",", "."); 
                var formatted = accounting.formatNumber(sdlt, 2, ",", "."); 

                $("#sdltValue").html(formatted);

            });
        });
    </script>
@endpush