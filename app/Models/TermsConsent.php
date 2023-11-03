<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

use App\Models\Client;

class TermsConsent extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'service', 'description', 'type', 'amount', 'timing', 'type_2', 'amount_2', 'timing_2', 'privacy_consent', 'terms_consent', 'signature'
    ];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('terms_consents.updated_at', 'desc');
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

            $termsConsent = new TermsConsent();
            $termsConsent->user_id = $data['user_id'];
            $termsConsent->service = $data['service'];
            $termsConsent->description = $data['description'];
            $termsConsent->type = $data['type'];
            $termsConsent->amount = $data['amount'];
            $termsConsent->timing = $data['timing'];
            $termsConsent->type_2 = $data['type_2'];
            $termsConsent->amount_2 = $data['amount_2'];
            $termsConsent->timing_2 = $data['timing_2'];
            $termsConsent->client_id = $clientId;

            if($termsConsent->save()) {
                return array('client_id'=>$clientId,'consent_id'=>$termsConsent->id);
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

        $termsConsent = TermsConsent::findOrFail($data['id']);
        $termsConsent->description = $data['description'];
        $termsConsent->type = $data['type'];
        $termsConsent->amount = $data['amount'];
        $termsConsent->timing = $data['timing'];
        $termsConsent->type_2 = $data['type_2'];
        $termsConsent->amount_2 = $data['amount_2'];
        $termsConsent->timing_2 = $data['timing_2'];
        $termsConsent->privacy_consent = $data['privacy_consent'];
        $termsConsent->terms_consent = $data['terms_consent'];
        $termsConsent->signature = $data['signature'];

        if($client->save() && $termsConsent->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$termsConsent->id);
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
        $termsConsent = TermsConsent::findOrFail($id);
        if ($termsConsent->delete()) {
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

        $termsConsent = TermsConsent::findOrFail($data['id']);
        $termsConsent->privacy_consent = $data['privacy_consent'];
        $termsConsent->terms_consent = $data['terms_consent'];
        $termsConsent->signature = $data['signature'];

        $client = Client::findOrFail($data['client_id']);
        //reset prefs
        $client->comm_email_consent = "N";
        $client->comm_phone_consent = "N";
        $client->comm_sms_consent = "N";
        $client->comm_face_consent = "N";
        $client->comm_thirdparty_consent = "N";
        $client->comm_other_consent = "N";
        $client->mkt_post_consent = "N";
        $client->mkt_automatedcall_consent = "N";
        $client->mkt_web_consent = "N";
        $client->mkt_email_consent = "N";
        $client->mkt_phone_consent = "N";
        $client->mkt_sms_consent = "N";
        $client->mkt_face_consent = "N";
        $client->mkt_thirdparty_consent = "N";
        $client->mkt_other_consent = "N";
        //enable prefs from submission
        foreach($data as $key=>$value){
            if((strrpos($key,'mkt_') !== false || strrpos($key,'comm_') !== false) && strrpos($key,'_2') === false) {
                if(!empty($value)){
                    $client->$key = $value;
                }
            }
        }

        if (isset($data['client_id_2']) && !empty($data['client_id_2'])) {
            $client2 = Client::findOrFail($data['client_id_2']);
            //reset prefs
            $client2->comm_email_consent = "N";
            $client2->comm_phone_consent = "N";
            $client2->comm_sms_consent = "N";
            $client2->comm_face_consent = "N";
            $client2->comm_thirdparty_consent = "N";
            $client2->comm_other_consent = "N";
            $client2->mkt_post_consent = "N";
            $client2->mkt_automatedcall_consent = "N";
            $client2->mkt_web_consent = "N";
            $client2->mkt_email_consent = "N";
            $client2->mkt_phone_consent = "N";
            $client2->mkt_sms_consent = "N";
            $client2->mkt_face_consent = "N";
            $client2->mkt_thirdparty_consent = "N";
            $client2->mkt_other_consent = "N";
            //enable prefs from submission
            foreach($data as $key=>$value){
                if((strrpos($key,'mkt_') !== false || strrpos($key,'comm_') !== false) && strrpos($key,'_2') !== false) {
                    if(!empty($value)){
                        $dbKey = str_replace('_2', '', $key);
                        $client2->$dbKey = $value;
                    }
                }
            }
            $client2->save();
        }

        if($client->save() && $termsConsent->save()) {
            return array('client_id'=>$client->id,'consent_id'=>$termsConsent->id);
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
    public static function filter($consent_status, $timing, $service, $client_surname, $sort)
    {
        $query = self::select('terms_consents.*')->where('terms_consents.deleted_at', null)->where('terms_consents.user_id', session('user_id', auth()->id()));
        $query = $query->join('clients', 'clients.id', 'terms_consents.client_id');

        if (!empty($consent_status)) {
            $query = $query->where('terms_consents.privacy_consent', $consent_status)
                            ->orWhere('terms_consents.privacy_consent', $consent_status);
        }
        if (!empty($timing)) {
            $query = $query->where('terms_consents.timing', $timing)->orWhere('terms_consents.timing_2', $timing);
        }
        if (!empty($service)) {
            $query = $query->where('terms_consents.service', $service);
        }
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'like', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('terms_consents.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('terms_consents.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('terms_consents.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('terms_consents.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('terms_consents.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }
}
