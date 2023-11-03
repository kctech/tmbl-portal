<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

use App\Models\Client;

class Quote extends Model
{
    use SoftDeletes;

    CONST VALIDATION_RULES = [
        'user_id' => 'required',
        'client_id' => 'required',
        'link' => '',
        'options_count' => '',
        'options.*.provider' => 'required',
        'options.*.product' => 'required',
        'options.*.end_date' => 'required|date_format:d/m/Y',
        'options.*.monthly_payment' => 'required|numeric',
        'options.*.initial_rate' => 'required|numeric',
        'options.*.lender_prod_fee' => 'required|numeric',
        'options.*.lender_val_fee' => 'required|numeric',
        'options.*.lender_exit_fee' => 'required|numeric',
        'options.*.other_fees' => 'required|numeric',
        'options.*.incl_std_legal_fees' => 'required',
        'options.*.other_lender_incentives' => '',
        'options.*.tmbl_fee' => 'required|numeric',
        'options.*.details' => 'required',
        'options.*.initial_period' => 'required|numeric',
        'options.*.svr_period' => 'required|numeric',
        'options.*.svr' => 'required|numeric',
        'options.*.svr_monthly' => 'required|numeric',
        'options.*.total' => 'required|numeric',
        'options.*.aprc' => 'required',
        'purchase_val' => 'required|numeric',
        'loan_amnt' => 'required|numeric',
        'loan_interest' => 'required|numeric',
        'term_yrs' => 'required|numeric',
        'term_mnth' => 'required|numeric',
        'fee_type' => 'required',
        'fee' => 'required|numeric',
        'fee_timing' => 'required',
        'fee_2_type' => '',
        'fee_2' => 'sometimes|numeric',
        'fee_2_timing' => '',
        'message' => '',
        'email_intro' => 'required',
        'first_name' => 'requiredif:client_id,0',
        'last_name' => 'requiredif:client_id,0',
        'tel' => '',
        'email_2' => 'sometimes|nullable|email',
        'tel_2' => '',
        'linked' => ''
    ];
    CONST VALIDATION_LABELS = [
        'first_name.requiredif' => 'Need client First Name for new client',
        'last_name.requiredif' => 'Need client Last Name for new client',
        'email.required' => 'Need client Email',
        'first_name.required' => 'Need client First Name',
        'last_name.required' => 'Need client Last Name',
        'first_name_2.required' => 'Need client First Name for new client 2',
        'last_name_2.required' => 'Need client Last Name for new client 2',
        'options.*.provider.required' => 'Provider is required',
        'options.*.product.required' => 'Product is required',
        'options.*.end_date.required' => 'End date is required',
        'options.*.end_date.date_format' => 'End date must be in format dd/mm/yyyy',
        'options.*.monthly_payment.required' => 'Monthly payment is required',
        'options.*.initial_rate.required' => 'Initial Rate is required',
        'options.*.lender_prod_fee.required' => 'Lender Product Fee is required, enter 0 if none.',
        'options.*.lender_val_fee.required' => 'Lender Valuation Fee is required, enter 0 if none.',
        'options.*.lender_exit_fee.required' => 'Lender Exit Fee is required, enter 0 if none.',
        'options.*.other_fees.required' => 'Other Fees is required, enter 0 if none.',
        'options.*.tmbl_fee.required' => 'Fee is required',
        'options.*.details.required' => 'Details field is required',
        'options.*.initial_period.required' => 'Initial Period is required',
        'options.*.svr_period.required' => 'SVR Period is required',
        'options.*.svr.required' => 'SVR is required',
        'options.*.svr_monthly.required' => 'SVR Monthly Payments is required',
        'options.*.total.required' => 'Total is required',
        'options.*.aprc.required' => 'APRC is required',
        'options.*.incl_std_legal_fees.required' => 'Incl. Standard Legal Costs is required, please click an option.'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'options', 'purchase_val', 'loan_amnt', 'loan_interest', 'term_yrs', 'term_mnth',
        'fee', 'fee_type', 'fee_timing', 'fee_2', 'fee_2_type', 'fee_2_timing', 'email_intro', 'message', 'accepted', 'signature'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('quotes.updated_at', 'desc');
        });
    }

    /**
     * Create a new request
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if (empty($data['client_id'])) {
            if (empty($data['link'])) {
                $data['link'] = md5(microtime(true).random_bytes(6));
            }

            $client = new Client();
            //$client->account_id = User::where('id', $data['user_id'])->first()->account_id;
            $client->account_id = Session::get('account_id');
            $client->user_id = $data['user_id'];
            $client->first_name = $data['first_name'];
            $client->last_name = $data['last_name'];
            $client->email = $data['email'];
            $client->tel = $data['tel'];
            $client->link = $data['link'];
            $client->uid = md5($data['first_name'].$data['last_name'].$data['email'].$data['tel'].random_bytes(6));
            $client->save();

            $clientId = $client->id;
        }else{
            $clientId = $data['client_id'];

        }

        if (!empty($clientId)) {
            $quote = new Quote();
            $quote->user_id = $data['user_id'];
            $quote->client_id = $clientId;
            $quote->options = $data['options'];
            $quote->purchase_val = $data['purchase_val'];
            $quote->loan_amnt = $data['loan_amnt'];
            $quote->loan_interest = $data['loan_interest'];
            $quote->term_yrs = $data['term_yrs'];
            $quote->term_mnth = $data['term_mnth'];
            $quote->fee_type = $data['fee_type'];
            $quote->fee = $data['fee'];
            $quote->fee_timing = $data['fee_timing'];
            $quote->fee_2_type = $data['fee_2_type'];
            $quote->fee_2 = $data['fee_2'];
            $quote->fee_2_timing = $data['fee_2_timing'];
            $quote->message = $data['message'];
            $quote->email_intro = $data['email_intro'];

            if($quote->save()) {
                return array('client_id'=>$clientId,'quote_id'=>$quote->id);
            }else{
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Edit request
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function edit(array $data)
    {

        $client = Client::findOrFail($data['client_id']);
        $client->first_name = $data['first_name'];
        $client->last_name = $data['last_name'];
        $client->email = $data['email'];
        $client->tel = $data['tel'];

        $quote = Quote::findOrFail($data['id']);
        $quote->options = $data['options'];
        $quote->purchase_val = $data['purchase_val'];
        $quote->loan_amnt = $data['loan_amnt'];
        $quote->loan_interest = $data['loan_interest'];
        $quote->term_yrs = $data['term_yrs'];
        $quote->term_mnth = $data['term_mnth'];
        $quote->fee_type = $data['fee_type'];
        $quote->fee = $data['fee'];
        $quote->fee_timing = $data['fee_timing'];
        $quote->fee_2_type = $data['fee_2_type'];
        $quote->fee_2 = $data['fee_2'];
        $quote->fee_2_timing = $data['fee_2_timing'];
        $quote->message = $data['message'];
        $quote->email_intro = $data['email_intro'];

        if($client->save() && $quote->save()) {
            return array('client_id'=>$client->id,'quote_id'=>$quote->id);
        }else{
            return false;
        }
    }

    /**
     * Soft Delete request
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public static function remove($id)
    {
        $quote = Quote::findOrFail($id);
        if ($quote->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Client response to request
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function respond(array $data)
    {

        $quote = Quote::findOrFail($data['id']);
        $quote->accepted = $data['accepted'];
        $quote->signature = $data['signature'];

        if($client->save() && $quote->save()) {
            return array('client_id'=>$client->id,'quote_id'=>$quote->id);
        }else{
            return false;
        }
    }

    /**
     * Get the client that owns the quote is related to.
     */
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    /**
     * Get the user that quote was made by.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the user that quote was made by.
     */
    public static function filter($accepted, $client_surname, $sort)
    {

        $query = self::select('quotes.*')->where('quotes.deleted_at', null)->where('quotes.user_id', session('user_id', auth()->id()));
        $query = $query->join('clients', 'clients.id', 'quotes.client_id');

        if (!empty($accepted)) {
            $query = $query->where('quotes.accepted', $accepted);
        }
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'like', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('quotes.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('quotes.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('quotes.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('quotes.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('quotes.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }
}
