<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

use App\Models\Client;

class SdltDisclaimer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'consent', 'consent_additional', 'consent_type'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('sdlt_disclaimers.updated_at', 'desc');
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

            $sdltDisclaimer = new SdltDisclaimer();
            $sdltDisclaimer->user_id = $data['user_id'];
            $sdltDisclaimer->consent_type = $data['consent_type'];
            $sdltDisclaimer->client_id = $clientId;

            if($sdltDisclaimer->save()) {
                return array('client_id'=>$clientId,'consent_id'=>$sdltDisclaimer->id);
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

        $sdltDisclaimer = SdltDisclaimer::findOrFail($data['id']);
        $sdltDisclaimer->consent_type = $data['consent_type'];
        $sdltDisclaimer->consent = $data['consent'];

        if($client->save() && $sdltDisclaimer->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$sdltDisclaimer->id);
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
        $sdltDisclaimer = SdltDisclaimer::findOrFail($id);
        if ($sdltDisclaimer->delete()) {
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

        $sdltDisclaimer = SdltDisclaimer::findOrFail($data['id']);
        $sdltDisclaimer->consent = $data['consent'];
        $sdltDisclaimer->consent_additional = $data['consent_additional'];

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

        if($client->save() && $sdltDisclaimer->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$sdltDisclaimer->id);
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
    public static function filter($consent_status, $consent_additional, $client_surname, $sort)
    {
        $query = self::select('sdlt_disclaimers.*')->where('sdlt_disclaimers.deleted_at', null)->where('sdlt_disclaimers.user_id', Session::get('user_id', auth()->id()));
        $query = $query->join('clients', 'clients.id', 'sdlt_disclaimers.client_id');

        if (!empty($consent_status)) {
            $query = $query->where('sdlt_disclaimers.consent', $consent_status);
        }
        if (!empty($consent_additional)) {
            $query = $query->where('sdlt_disclaimers.consent_additional', $consent_additional);
        }
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'like', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('sdlt_disclaimers.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('sdlt_disclaimers.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('sdlt_disclaimers.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('sdlt_disclaimers.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('sdlt_disclaimers.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }
}
