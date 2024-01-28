<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    const NEW_LEAD = 0;
    const QUALIFIED_LEAD = 1;
    const ALLOCATED_LEAD = 2;
    const REMOVED_LEAD = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid','source_id','account_id','user_id','client_id','first_name','last_name','email_address','contact_number','data','options','status','allocated_at'
    ];

    public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }

    /**
     * Get the client that owns the consent is related to.
     */
    public function source()
    {
        return $this->belongsTo(\App\Models\ApiKey::class);
    }
}
