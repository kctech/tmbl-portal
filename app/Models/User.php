<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;

    const VALIDATION_RULES = [
        'account_id' => 'required',
        'role_id' => 'required',
        'tel' => ['required', 'string', 'max:191'],
        'first_name' => ['required', 'string', 'max:191'],
        'last_name' => ['required', 'string', 'max:191'],
        'email' => ['required', 'string', 'email', 'max:191', 'unique:users', 'regex:/@(tmblgroup\.co\.uk|gatewaymortgagesuk\.com)$/'],
        'password' => ['required', 'string', 'min:8', 'confirmed']
    ];
    //Rule::unique('users')->ignore($user->id),

    const VALIDATION_LABELS = [
        'first_name.required' => 'User first name required',
        'last_name.required' => 'User last name required',
        'email.required' => 'User email required',
        'email.regex' => 'Only TMBL or Gateway email addresses may sign up.',
        'tel.required' => 'User phone number required',
        'role_id.required' => 'User must have a role',
        'account_id.required' => 'User must belong to an account'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'role_id', 'first_name', 'last_name', 'email', 'password', 'tel', 'mab_id', 'azure_id', 'remember_token', 'last_login_at', 'last_login_ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function full_name(){
        return $this->first_name." ".$this->last_name;
    }

    /**
     * Create a new User record
     *
     * @var array
     */
    public static function create($data) {

        $user = new User();

        $user->account_id = $data['account_id'];
        $user->role_id = $data['role_id'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->tel = $data['tel'];
        $user->password = Hash::make($data['password']);
        $user->email_verified_at = now();

        if ($user->save()) {
            return $user->id;
        } else {
            return false;
        }

    }

    /**
     * Update a User record
     *
     * @var array
     */
    public static function change($data) {

        $user = User::findOrFail($data['user_id']);

        $user->account_id = $data['account_id'];
        $user->role_id = $data['role_id'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->tel = $data['tel'];

        if(isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        /*foreach ((new static)->getFillable() as $field) {
            if (isset($data[$field])) {
                if (!empty($data[$field])) {
                    if ($field == "password") { $data['password'] = Hash::make($data['password']); }
                    $user->$field = $data[$field];
                }
            }
        }*/

        if ($user->save()) {
            return $user->id;
        } else {
            return false;
        }
    }

    /**
     * Get the user that consent was made by.
     */
    public static function filter($user_role, $user_account, $user_email, $user_surname, $deleted_status, $sort)
    {
        $query = self::select('users.*')->join('roles', 'roles.id', 'users.role_id')->where('roles.level','>=', auth()->user()->role->level);

        if ($deleted_status == "Y") {
            $query = $query->withTrashed();
        } else {
            $query = $query->where('users.deleted_at', null);
        }
        if (!empty($user_role)) {
            $query = $query->where('users.role_id', $user_role);
        }
        if (!empty($user_account)) {
            $query = $query->where('users.account_id', $user_account);
        }
        if (!empty($user_email)) {
            $query = $query->where('users.email', 'LIKE', '%'.$user_email.'%');
        }
        if (!empty($user_surname)) {
            $query = $query->where('users.last_name', 'LIKE', '%'.$user_surname.'%');
        }
        if (!empty($sort)) {
            switch($sort) {
                case 'recent' :
                    $query = $query->orderby('users.updated_at', 'DESC');
                    break;
                case 'newest_first' :
                    $query = $query->orderby('users.last_login_at', 'DESC');
                    break;
                case 'oldest_first' :
                    $query = $query->orderby('users.last_login_at', 'ASC');
                    break;
                case 'surname_az' :
                    $query = $query->orderby('users.last_name', 'ASC');
                    break;
                case 'surname_za' :
                    $query = $query->orderby('users.last_name', 'DESC');
                    break;
                default :
                    $query = $query->orderby('users.updated_at', 'DESC');
            }
        } else {
            $query = $query->orderby('users.updated_at', 'DESC');
        }
        return $query->paginate(999);
        //return $query->paginate(config('database.pagination_size'));
    }

    /**
     * Soft Delete user
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public static function remove($id)
    {
        $user = User::findOrFail($id);
        if ($user->delete()) {
            //soft delete
            $user->gdpr_consents()->delete();
            $user->btl_consents()->delete();
            $user->transfer_requests()->delete();
            $user->terms_consents()->delete();
            $user->quotes()->delete();
            $user->clients()->delete();

            /*full delete
            $user->gdpr_consents()->forceDelete();
            $user->btl_consents()->forceDelete();
            $user->transfer_requests()->forceDelete();
            $user->terms_consents()->forceDelete();
            $user->quotes()->forceDelete();
            $user->forceDelete();*/

            return true;
        } else {
            return false;
        }
    }

    /**
     * Restore user
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public static function reinstate($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        //dd($user);
        if ($user->restore()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the users account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the users role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the users GDPR requests
     */
    public function clients()
    {
        return $this->hasMany(\App\Models\Client::class);
    }

    /**
     * Get the users GDPR requests
     */
    public function gdpr_consents()
    {
        return $this->hasMany(\App\Models\GdprConsent::class);
    }

    /**
     * Get the users BTL requests
     */
    public function btl_consents()
    {
        return $this->hasMany(\App\Models\BtlConsent::class);
    }

    /**
     * Get the users Transfer requests
     */
    public function transfer_requests()
    {
        return $this->hasMany(\App\Models\ClientTransferConsent::class);
    }

    /**
     * Get the users Terms requests
     */
    public function terms_consents()
    {
        return $this->hasMany(\App\Models\TermsConsent::class);
    }

    /**
     * Get the users quotes
     */
    public function quotes()
    {
        return $this->hasMany(\App\Models\Quote::class);
    }

    /**
     * Get the users quotes
     */
    public function leads()
    {
        return $this->hasMany(\App\Models\Lead::class, 'user_id', 'id');
    }

    /**
     * Get the users quotes
     */
    public function leads_this_month()
    {
        return $this->hasMany(\App\Models\Lead::class, 'user_id', 'id')->where('allocated_at','>', \Carbon\Carbon::now()->startofMonth()->subMonth()->endOfMonth()->toDateTimeString());
    }
}
