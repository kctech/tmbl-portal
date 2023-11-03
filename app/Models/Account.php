<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'acronym'
    ];

    /**
     * Get the modules
     */
    public function modules()
    {
        return $this->hasMany(AccountModule::class);
        //return AccountModule::where('account_id', $account_id)->where('status',0)->get();
    }
}
