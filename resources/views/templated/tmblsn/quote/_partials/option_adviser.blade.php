<table class="productTable" width="100%" border="0" cellspacing="0" cellpadding="10">
    <tbody>
        <tr>
            <td colspan="2" valign="center" class="primaryBackground{{$loop->iteration}}">
                <strong>{{$option->provider}}</strong>
                <br />
                {{$option->product}}
                <br />
                ({{$option->end_date}})
            </td>
        </tr>
        <tr>
            <td>
                Monthly Payment
            </td>
            <td>
                &pound;{{number_format($option->monthly_payment,2)}}
            </td>
        </tr>
        <tr>
            <td class="oddRow">
                Initial Rate
            </td>
            <td class="oddRow">
                {{number_format($option->initial_rate,2)}}%
            </td>
        </tr>
        <tr>
            <td>
                Standard Variable Rate
            </td>
            <td>
                {{number_format($option->svr,2)}}%
            </td>
        </tr>
        <tr>
            <td class="oddRow">
                Lender Product Fee
            </td>
            <td class="oddRow">
                &pound;{{number_format($option->lender_prod_fee,2)}}
            </td>
        </tr>
        <tr>
            <td>
                Lender Valuation Fee
            </td>
            <td>
                &pound;{{number_format($option->lender_val_fee,2)}}
            </td>
        </tr>
        <tr>
            <td class="oddRow">
                Lender Exit Fee
            </td>
            <td class="oddRow">
                &pound;{{number_format($option->lender_exit_fee,2)}}
            </td>
        </tr>
        <tr>
            <td>
                Lender Incentives
            </td>
            <td>
                {{$option->other_lender_incentives}}
            </td>
        </tr>
        <tr>
            <td class="oddRow">
                Incl. Standard Legal Costs?
            </td>
            <td class="oddRow">
                {{$option->incl_std_legal_fees}}
            </td>
        </tr>
        <tr>
            <td>
                Other Fees
            </td>
            <td>
                &pound;{{number_format($option->other_fees,2)}}
            </td>
        </tr>
        <tr>
            <td class="oddRow">
                TMBL Fee
            </td>
            <td class="oddRow">
                &pound;{{number_format($fee,2)}}
            </td>
        </tr>
        <tr>
            <td>
                APRC
            </td>
            <td>
                {{number_format($option->aprc,2)}}%
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="center" style="detailsBlock">
                <span class="primaryColor{{$loop->iteration}}">Details:</span>
                <br />
                {!! nl2br(e($option->details)) !!}
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="center">
                <span class="primaryColor{{$loop->iteration}}">After your initial period:</span>
                <br />
                Initial period: {{$option->initial_period}} months<br />
                SVR period {{$option->svr_period}} months<br />
                SVR monthly payments &pound;{{number_format($option->svr_monthly,2)}}<br />
                Total cost of credit &pound;{{number_format($option->total,2)}} (inc interest &amp; fees)
            </td>
        </tr>
    </tbody>
</table>