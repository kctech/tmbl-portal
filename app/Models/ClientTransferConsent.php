<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

use App\Models\Client;

class ClientTransferConsent extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'consent', 'notes'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('client_transfer_consents.updated_at', 'desc');
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

            if (empty($data['notes'])) {
                $data['notes'] = "";
            }

            $clientTransferConsent = new ClientTransferConsent();
            $clientTransferConsent->user_id = $data['user_id'];
            $clientTransferConsent->notes = $data['notes'];
            $clientTransferConsent->client_id = $clientId;

            if($clientTransferConsent->save()) {
                return array('client_id'=>$clientId,'consent_id'=>$clientTransferConsent->id);
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

        $clientTransferConsent = ClientTransferConsent::findOrFail($data['id']);
        $clientTransferConsent->notes = $data['notes'];
        $clientTransferConsent->consent = $data['consent'];

        if($client->save() && $clientTransferConsent->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$clientTransferConsent->id);
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
        $clientTransferConsent = ClientTransferConsent::findOrFail($id);
        if ($clientTransferConsent->delete()) {
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

        $clientTransferConsent = ClientTransferConsent::findOrFail($data['id']);
        $clientTransferConsent->consent = $data['consent'];

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

        if($client->save() && $clientTransferConsent->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$clientTransferConsent->id);
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
    public static function filter($consent_status, $consent_type, $client_surname, $sort)
    {
        $query = self::select('client_transfer_consents.*')->where('client_transfer_consents.deleted_at', null)->where('client_transfer_consents.user_id', session('user_id', auth()->id()));
        $query = $query->join('clients', 'clients.id', 'client_transfer_consents.client_id');

        if (!empty($consent_status)) {
            $query = $query->where('client_transfer_consents.consent', $consent_status);
        }
        if (!empty($consent_type)) {
            $query = $query->where('client_transfer_consents.consent_type', $consent_type);
        }
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'like', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('client_transfer_consents.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('client_transfer_consents.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('client_transfer_consents.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('client_transfer_consents.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('client_transfer_consents.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }
}
