<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use \Carbon\Carbon;

class LeadEvent extends Model
{

    const AUTO_CONTACT_ATTEMPTED = 0;
    const MANUAL_CONTACT_ATTEMPTED = 1;
    const BOOKED_TEAMS_MEETING = 2;
    const TRANSFERRED_TO_MAB = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id','user_id','lead_id','event_id','information'
    ];

    public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }

    /**
     * Get the user that the event is related to.
     */
    public function owner()
    {
        return $this->belongsTo(\App\Models\Lead::class,'user_id','id');
    }

    /**
     * Get the lead that the event is related to.
     */
    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class,'lead_id','id');
    }

}
