<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalCache extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'portal_cache';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'account_id', 'cache_key', 'data', 'expires_at',
    ];

    public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }
}
