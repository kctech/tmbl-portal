<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\User;

class Client extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'user_id', 'first_name', 'last_name', 'email', 'tel', 'link'
        ,'comm_email_consent', 'comm_phone_consent', 'comm_sms_consent', 'comm_face_consent', 'comm_thirdparty_consent', 'comm_other_consent'
        ,'mkt_post_consent', 'mkt_automatedcall_consent', 'mkt_web_consent', 'mkt_email_consent', 'mkt_phone_consent', 'mkt_sms_consent', 'mkt_face_consent', 'mkt_thirdparty_consent', 'mkt_other_consent'
    ];

    /**
     * Create a new client record
     *
     * @var array
     */
    public static function create($data) {

        $client = new Client();

        if (empty($data['link'])) {
            $data['link'] = md5(microtime(true).random_bytes(6));
        }

        //$client->account_id = User::where('id', $data['user_id'])->first()->account_id;
        $client->account_id = Session::get('account_id');
        $client->user_id = $data['user_id'];
        $client->first_name = $data['first_name'];
        $client->last_name = $data['last_name'];
        $client->email = $data['email'];
        $client->tel = $data['tel'];
        $client->link = $data['link'];
        $client->uid = md5($data['first_name'].$data['last_name'].$data['email'].$data['tel'].random_bytes(6));

        if ($client->save()) {
            return $client->id;
        } else {
            return false;
        }

    }

    /**
     * Update a client record
     *
     * @var array
     */
    public static function change($data) {

        $client = Client::findOrFail($data['client_id']);

        foreach ((new static)->getFillable() as $field) {
            if (isset($data[$field])) {
                if (!empty($data[$field])) {
                    $client->$field = $data[$field];
                }
            }
        }

        if ($client->save()) {
            return $client->id;
        } else {
            return false;
        }
    }

    /**
     * Get the user that consent was made by.
     */
    public static function filter($consent_status, $consent_type, $client_surname, $sort)
    {
        //allow admins access to all clients, unless impersonating
        if(!session()->has('impersonate') && in_array(auth()->user()->role->permissions, array('admin','sudo'))){
            $query = self::where('clients.deleted_at', null);
        }else{
            $query = self::where('clients.deleted_at', null)->where('clients.user_id', session('user_id', auth()->id()));
        }

        $query = $query->where('account_id', '=', auth()->user()->account_id);

        //$query = $query->join('clients', 'clients.id', 'gdpr_consents.client_id');

        /*if (!empty($consent_status)) {
            $query = $query->where('gdpr_consents.consent', $consent_status);
        }
        if (!empty($consent_type)) {
            $query = $query->where('gdpr_consents.consent_type', $consent_type);
        }*/
        if (!empty($client_surname)) {
            $query = $query->where('clients.last_name', 'LIKE', '%'.$client_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('clients.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('clients.id', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('clients.id', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('clients.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('clients.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('clients.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('clients.updated_at', 'DESC');
        }
        return $query->paginate(config('database.pagination_size'));
    }

    /**
     * Soft Delete request
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public static function remove($id)
    {
        $Client = Client::findOrFail($id);
        if ($Client->delete()) {
            //soft delete
            $Client->gdpr_consents()->delete();
            $Client->btl_consents()->delete();
            $Client->transfer_requests()->delete();
            $Client->terms_consents()->delete();
            $Client->quotes()->delete();
            $Client->sdlt_disclaimers()->delete();
            //$Client->eligibility_statements()->delete();

            //full delete
            $Client->gdpr_consents()->forceDelete();
            $Client->btl_consents()->forceDelete();
            $Client->transfer_requests()->forceDelete();
            $Client->terms_consents()->forceDelete();
            $Client->quotes()->forceDelete();
            $Client->sdlt_disclaimers()->forceDelete();
            //$Client->eligibility_statements()->forceDelete();

            $Client->forceDelete();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Return links for a client
     */
    protected function linked($id,$link)
    {

        return Client::where('link', $link)->where('id', '!=', $id)->get();
    }

    /**
     * Get linked clients
     */
    public function links()
    {
        return $this->hasMany('App\Client', 'link', 'link');
    }

    /**
     * Get the clients GDPR requests
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the clients SDLT Disclaimer requests
     */
    public function sdlt_disclaimers()
    {
        return $this->hasMany(SdltDisclaimer::class);
    }

    /**
     * Get the clients Eligibility Statement requests
     */
    public function eligibility_statements()
    {
        return $this->hasMany(EligibilityStatement::class);
    }

    /**
     * Get the clients GDPR requests
     */
    public function gdpr_consents()
    {
        return $this->hasMany(GdprConsent::class);
    }

    /**
     * Get the clients BTL requests
     */
    public function btl_consents()
    {
        return $this->hasMany(BtlConsent::class);
    }

    /**
     * Get the clients Transfer requests
     */
    public function transfer_requests()
    {
        return $this->hasMany(ClientTransferConsent::class);
    }

    /**
     * Get the clients Terms requests
     */
    public function terms_consents()
    {
        return $this->hasMany(TermsConsent::class);
    }
    /**
     * Return latest Terms request for a client
     */
    public function terms_consent($id)
    {
        return TermsConsent::where('client_id', '=', $id)->latest()->first();
    }

    /**
     * Get the clients quotes
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

}
