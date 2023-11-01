<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountModule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'module', 'access'
    ];

}
