$(document).ready(function(){
    
    var optLimit = 4;
    var desc = {
        F: "This deal offers fixed monthly payments for INITIAL_PERIOD years; then you would transfer to the Lender’s Standard Variable Rate (SVR).",
        LT: "This deal offers monthly payments that track the base rate for INITIAL_PERIOD years; then you would transfer to the Lender’s Standard Variable Rate (SVR).",
        T: "This deal offers monthly payments at a rate of RATE_DISCOUNT discount from the lenders current variable rate for INITIAL_PERIOD years; then you would transfer to the Lender’s Standard Variable Rate (SVR).",
        D: "This deal offers monthly payments that track the base rate for the life of the mortgage"
    };

    var total_fee = 0;
    $(".fee-field").on('change', function(){
        var total_fee_1 = total_fee_2 = 0;
        var loan_amnt = parseInt($("#loan_amnt").val());
        var fee_type = $('input[name="fee_type"]:checked').val();
        var fee_1 = parseFloat($('input[name="fee"]').val());
        var fee_2_type = $('input[name="fee_2_type"]:checked').val();
        var fee_2 = parseFloat($('input[name="fee_2"]').val());

        if (typeof fee_type === 'undefined') {
            var fee_type = $('input[name="fee_type"]').val();
        }
        if (typeof fee_2_type === 'undefined') {
            var fee_2_type = $('input[name="fee_2_type"]').val();
        }

        console.log("fee calc: " + loan_amnt + " | " + fee_type + " | " + fee_1 + " | " + fee_2_type + " | " + fee_2);

        if (fee_type == "Fixed Fee"){
            total_fee_1 = fee_1;
        } else if (fee_type == "Percentage"){
            total_fee_1 = loan_amnt * (fee_1 / 100);
        }

        if (fee_2_type == "Fixed Fee") {
            total_fee_2 =  fee_2;
        } else if (fee_2_type == "Percentage") {
            total_fee_2 = loan_amnt * (fee_2 / 100);
        }

        total_fee = total_fee_1 + total_fee_2;

        $('.tmbl-fee').val(parseFloat(total_fee).toFixed(2));
    });

    //page plugins
    function initProductPlugins() {
        $('.component_future_datepicker').datepicker({format: 'dd/mm/yyyy',clearBtn: true,startDate: "now",autoclose: true});
        $(".select2--product").select2({
            tags: true,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            }
        });
    }

    //page calculations
    function updateCalcs() {
        $(".total-cost-calc").trigger('change');
        $(".initial-calc").trigger('change');
        $(".svr-calc").trigger('change');
        $(".svr-period-calc").trigger('change');
    }

    //quick fee select
    $('.fee-btn').click(function(){
        $('#fee').val(parseFloat($(this).data("value")).toFixed(2)).trigger('change');
        $('.fee-btn').removeClass('badge-primary').addClass('badge-secondary');
        $(this).removeClass('badge-secondary').addClass('badge-primary');
    });
    $('.fee_2-btn').click(function(){
        $('#fee_2').val(parseFloat($(this).data("value")).toFixed(2)).trigger('change');
        $('.fee_2-btn').removeClass('badge-primary').addClass('badge-secondary');
        $(this).removeClass('badge-secondary').addClass('badge-primary');
    });

    //fee type change prevent silly percentages
    $('input[name="fee_type"]').change(function () {
        if (this.value == "Percentage") {
            $('input[name="fee"]').val(1).trigger('change');
        }
    });
    $('input[name="fee_2_type"]').change(function () {
        if (this.value == "Percentage") {
            $('input[name="fee_2"]').val(1).trigger('change');
        }
    });

    //initial period text
    $('#product_container').on('click','.desc-btn', function(){
        var descKey = $(this).data("value");
        var descVal = desc[descKey];
        //var initialRate = ($(this).closest('.product_fields').find('.initial-period').val() / 12);
        //$('#'+$(this).data("for")).vl(descVal.replace('INITIAL_PERIOD',initialRate));
        $('#'+$(this).data("for")).val(descVal);
    });

    //add new prodcut option
    $('.add-option').click(function(){
        if(currCount < optLimit){
            currCount++;
            $("#options_count").val(currCount);
            optCount++;
            var product_master = $('#option_num1').clone();
            //show remove button
            product_master.find('.action-btn').removeClass('d-none');
            //change header
            product_master.find('.product-name').html(optCount);
            //change counter
            product_master.data('counter', optCount);
            //remove select2
            product_master.find('.select2-container').remove();
            //replace id, name, aria-describedby
            var eleId = product_master.attr('id');
            product_master.attr('id', eleId.replace('_num1', '_num'+optCount));
            product_master.find('input,select,textarea').each(function(){
                if($(this).hasClass('select2') || $(this).hasClass('select2--product')){
                    $(this).removeAttr('data-select2-id');
                    $(this).removeClass('select2-hidden-accessible');
                }
                eleId = $(this).attr('id');
                $(this).attr('id', eleId.replace('_num1', '_num'+optCount));
                eleName = $(this).attr('name');
                $(this).attr('name', eleName.replace('[0]', '[' + optCount + ']'));
                var eleDescBy = $(this).attr('aria-describedby');
                if(typeof eleDescBy !== "undefined"){
                    $(this).attr('aria-describedby', eleDescBy.replace('_num1', '_num'+optCount));
                }
            });
            //replace for
            product_master.find('label,.initial-calc,.svr-calc,.opt_sel').each(function(){
                var eleFor = $(this).data('for');
                if(typeof eleFor !== "undefined"){
                    $(this).data('for', eleFor.replace('_num1', '_num'+optCount));
                }
            });
            //replace id
            product_master.find('.input-group-append,.opt_sel').each(function(){
                eleId = $(this).attr('id');
                $(this).attr('id', eleId.replace('_num1', '_num'+optCount));
            });
            //replace desc button for
            product_master.find('.desc-btn').each(function(){
                var eleFor = $(this).attr('data-for');
                if(typeof eleFor !== "undefined"){
                    $(this).attr('data-for', eleFor.replace('_num1', '_num'+optCount));
                }
            });
            
            $('#product_container').append(product_master);

            //reinit plugins
            initProductPlugins();
        }else{
            app.alerts.notice("Limit Reached","Sorry, you can only add 4 products per quote.");
        }
    });

    //remove product option
    $('#product_container').on('click','.remove-option',function(){
        //optCount--; //dont bother - this way id's are always unique, even when removing "middle" elements
        currCount--;
        $(this).closest('.product_fields').remove();
        $("#options_count").val(currCount);
    });

    //clear fields from copied option
    $('#product_container').on('click','.clear-fields',function(){
        var product_fields = $(this).closest('.product_fields');
        product_fields.find('input,select,textarea').each(function(){
            $(this).val('');
        });
        product_fields.find('textarea').each(function(){
            $(this).html('');
        });
        //refill auto stuff
        var productOpt = product_fields.data('counter');
        //$("#tmbl_fee_num"+productOpt).val($("#fee").val());
        $("#tmbl_fee_num" + productOpt).val(total_fee);
        $("#details_num"+productOpt).val("This deal offers fixed monthly payments for X years; then you would transfer to the Lender’s Standard Variable Rate (SVR).");
    });

    //svr period auto calc
    $('#quoteForm').on('change','.svr-period-calc',function(){
        //get counter
        var productOpt = $(this).closest('.product_fields').data('counter');
        if(typeof productOpt == "undefined"){
            productOpt = 1;
        }
        //get fields
        var term_yrs = parseInt($("#term_yrs").val());
        var term_mnth = parseInt($("#term_mnth").val());
        var initial_period = parseInt($("#initial_period_num"+productOpt).val());
        var svrPeriod = ( ( term_yrs * 12 ) + term_mnth ) - initial_period;

        //console.log(term_yrs+"|"+term_mnth+"|"+initial_period);

        if(isNaN(svrPeriod)){
            svrPeriod = "Missing/Inforrectly formatted variables";
        }
        $('#svr_period_num'+productOpt).val(svrPeriod);
    });

    /*initial period
    $('#product_container').on('change','.select2--product',function(){
        //get counter
        var productOpt = $(this).closest('.product_fields').data('counter');
        var termYrs = parseInt($(this).find(':selected').data('val'));
        if(typeof termYrs !== "undefined"){
            var termMonths = termYrs * 12;
            $("#initial_period_num"+productOpt).val(termMonths);
            $(".svr-period-calc").trigger('change');
        }
    });*/

    //total cost auto calc
    $('#quoteForm').on('change','.total-cost-calc',function(){
        //get counter
        var productOpt = $(this).closest('.product_fields').data('counter');
        //get fields
        var initial_period = parseFloat($("#initial_period_num"+productOpt).val());
        var monthly_payment = parseFloat($("#monthly_payment_num"+productOpt).val());
        var svr_period = parseFloat($("#svr_period_num"+productOpt).val());
        var svr_monthly = parseFloat($("#svr_monthly_num"+productOpt).val());
        var lender_prod_fee = parseFloat($("#lender_prod_fee_num"+productOpt).val());
        var lender_val_fee = parseFloat($("#lender_val_fee_num"+productOpt).val());
        var lender_exit_fee = parseFloat($("#lender_exit_fee_num"+productOpt).val());
        var other_fees = parseFloat($("#other_fees_num"+productOpt).val());
        var tmbl_fee = parseFloat($("#tmbl_fee_num" + productOpt).val());
        
        var loan_interest = parseFloat($("#loan_interest").val());

        //console.log(initial_period+"|"+monthly_payment+"|"+svr_period+"|"+svr_monthly+"|"+lender_prod_fee+"|"+lender_val_fee+"|"+lender_exit_fee+"|"+other_fees+"|"+tmbl_fee+"|"+loan_interest);

        var totalCost = ( initial_period * monthly_payment ) + ( svr_period * svr_monthly ) + ( lender_prod_fee + lender_val_fee + lender_exit_fee + other_fees + tmbl_fee ) + loan_interest;

        if(isNaN(totalCost)){
            var totalCostFormatted = "Missing/Inforrectly formatted variables";
        }else{
            var totalCostFormatted = totalCost.toFixed(2);
        }
        
        $('#total_num'+productOpt).val(totalCostFormatted);
    });

    //intital rate calc options
    $('#quoteForm').on('change','.initial-calc',function(){
        //get fields
        var btnKey = $(this).data("for");
        var term_yrs = parseInt($("#term_yrs").val());
        var term_mnth = parseInt($("#term_mnth").val());

        var $borrowed = parseInt($("#loan_amnt").val());
        var $interestRate = parseFloat($(this).val());
        var $term = (term_yrs * 12) + term_mnth;

        var interestOnlyMort = ($borrowed * ($interestRate / 100)) / 12;
        var repaymentMort = ( $borrowed * (($interestRate/12) / 100) / ( 1 - Math.pow( 1 + (($interestRate/12) / 100), -$term)) );
                            
        var interestOnlyMortFormatted = interestOnlyMort.toFixed(2);
        var repaymentMortFormatted = repaymentMort.toFixed(2);

        $('#int_'+btnKey).data('val', interestOnlyMortFormatted);
        $('#repay_'+btnKey).data('val', repaymentMortFormatted);
    });

    //svr calc options
    $('#quoteForm').on('change','.svr-calc',function(){
        //get counter
        var productOpt = $(this).closest('.product_fields').data('counter');
        //get fields
        var btnKey = $(this).data("for");
        var term_yrs = parseInt($("#term_yrs").val());
        var term_mnth = parseInt($("#term_mnth").val());
        var initial_period = parseInt($("#initial_period_num"+productOpt).val());                                                                                                       

        var $borrowed = parseInt($("#loan_amnt").val());
        var $repaidMo = parseInt($("#monthly_payment_num"+productOpt).val());
        var $outstanding = $borrowed - ($repaidMo * initial_period);
        var $interestRate = parseFloat($(this).val());
        var $term = ( ( term_yrs * 12 ) + term_mnth ) - initial_period;

        var interestOnlyMort = ($outstanding * ($interestRate / 100)) / 12;
        var repaymentMort = ( $outstanding * (($interestRate/12) / 100) / ( 1 - Math.pow( 1 + (($interestRate/12) / 100), -$term)) );
                            
        var interestOnlyMortFormatted = interestOnlyMort.toFixed(2);
        var repaymentMortFormatted = repaymentMort.toFixed(2);

        $('#int_'+btnKey).data('val', interestOnlyMortFormatted);
        $('#repay_'+btnKey).data('val', repaymentMortFormatted);
    });

    //rate select
    $('#quoteForm').on('click','.opt_sel', function(){
        var value = parseFloat($(this).data("val"));
        if (value != 0 && !isNaN(value)){
            $('#'+$(this).data("for")).val(value);
            updateCalcs();
        }else{
            app.alerts.notice("Oops","Looks like there's been a problem calculating, auto calc needs 'Loan Amount', 'Term of Mortgage (both year and month) and the related 'Rate' otherwise it'll return 0.");
        }
    });

    //btn toggle
    $('#quoteForm').on('click','.btn-group-toggle > .btn', function(){
        $(this).parent().find('.btn').removeClass('active');
        $(this).addClass('active');
    });

    initProductPlugins();
    updateCalcs();
});