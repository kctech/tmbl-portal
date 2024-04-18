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
        'account_id', 'strategy_id', 'chase_order', 'method', 'name', 'chase_duration', 'subject', 'body', 'attachments', 'status', 'auto_contact', 'auto_progress'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }

    /**
     * Get the parent strategy
     */
    public function strategy()
    {
        return $this->belongsTo(\App\Models\LeadChaseStrategy::class);
    }

    public static function getNextStep($strategy_id, $curren_position_id){
        $steps = self::where('strategy_id', $strategy_id)->orderBy('chase_order','asc')->get();
        foreach($steps as $k => $step){
            if($step->id == $curren_position_id){
                $next_step = $steps[$k+1] ?? false;
                if($next_step){
                    return $next_step;
                }else{
                    return false;
                }
            }
        }
    }
}
