<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

use App\Models\Client;

class EligibilityStatement extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'type', 'options', 'message', 'responded'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('eligibilitystatements.updated_at', 'desc');
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

            $statement = new EligibilityStatement();
            $statement->user_id = $data['user_id'];
            $statement->options = $data['options'];
            $statement->message = $data['message'];
            $statement->statement_type = $data['statement_type'];
            $statement->client_id = $clientId;

            if($statement->save()) {
                return array('client_id'=>$clientId,'statement_id'=>$statement->id);
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

        $statement = EligibilityStatement::findOrFail($data['id']);
        $statement->options = $data['options'];
        $statement->statement_type = $data['statement_type'];
        $statement->message = $data['message'];
        $statement->responded = 'N';

        if($client->save() && $statement->save()) {
            return array('client_id'=>$client->id,'statement_id'=>$statement->id);
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
        $statement = EligibilityStatement::findOrFail($id);
        if ($statement->delete()) {
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

        $statement = EligibilityStatement::findOrFail($data['id']);
        $statement->options = $data['options'];
        $statement->responded = 'Y';

        $client = Client::findOrFail($data['client_id']);
        if(!empty($data['mkt_email_consent'])){
            $client->mkt_email_consent = $data['mkt_email_consent'];
        }
        if(!empty($data['mkt_phone_consent'])){
            $client->mkt_phone_consent = $data['mkt_phone_consent'];
        }
        if(!empty($data['mkt_sms_consent'])){
            $client->mkt_sms_consent = $data['mkt_sms_consent'];
        }
        if(!empty($data['mkt_post_consent'])){
            $client->mkt_post_consent = $data['mkt_post_consent'];
        }

        if($client->save() && $statement->save()) {
            return array('client_id'=>$client->id,'statement_id'=>$statement->id);
        }else{
            return false;
        }
    }

    /**
     * Get the client that owns the consent is related to.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user that consent was made by.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that consent was made by.
     */
    public static function filter($statement_status, $statement_type, $client_surname, $sort)
    {
        $query = self::select('eligibilitystatements.*')->where('eligibilitystatements.deleted_at', null)->where('eligibilitystatements.user_id', session('user_id', auth()->id()));
        $query = $query->join('clients', 'clients.id', 'eligibilitystatements.client_id');

        if (!empty($statement_status)) {
            $query = $query->where('eligibilitystatements.responded', $statement_status);
        }
        if (!empty($statement_type)) {
            $query = $query->where('eligibilitystatements.statement_type', $statement_type);
        }
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'like', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('eligibilitystatements.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('eligibilitystatements.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('eligibilitystatements.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('eligibilitystatements.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('eligibilitystatements.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }
}
