<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
class LeadChaseStepContactMethod extends Model
{
    use SoftDeletes;

    const ACTIVE = 0;
    const INACTIVE = 1;

    const AUTO_CONTACT = 0;
    const MANUAL_CONTACT = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead_chase_step_contact_methods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'strategy_id', 'step_id', 'chase_order', 'chase_duration', 'method', 'auto_contact', 'name', 'template_ids', 'default_template_id', 'status'
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
