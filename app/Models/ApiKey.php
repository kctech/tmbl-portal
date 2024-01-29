<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
class ApiKey extends Model
{
    use SoftDeletes;

    const ACTIVE = 0;
    const INACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'source', 'api_token', 'last_login_at', 'last_login_ip', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    /*public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }*/

    /**
     * Get the users quotes
     */
    public function leads()
    {
        return $this->hasMany(\App\Models\Lead::class, 'source_id', 'id');
    }
}
