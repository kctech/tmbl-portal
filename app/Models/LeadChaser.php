<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
class LeadChaser extends Model
{
    use SoftDeletes;

    const ACTIVE = 0;
    const INACTIVE = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead_chasers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'method', 'title', 'chase_status', 'subject', 'body', 'attachments', 'status'
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
}
