<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\Hash;
class LeadChaseStep extends Model
{
    use SoftDeletes;

    const ACTIVE = 0;
    const INACTIVE = 1;
    const AUTO_PROGRESS = 0;
    const MANUAL_PROGRESS = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lead_chase_steps';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'strategy_id', 'chase_order', 'name', 'auto_contact', 'auto_progress', 'status'
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

    public function contact_methods(){
        return $this->hasMany(LeadChaseStepContactMethod::class, 'step_id', 'id')->orderBy('chase_order','asc');
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

    public static function getCurrentStep($strategy_id, $curren_position_id){
        $steps = self::where('strategy_id', $strategy_id)->orderBy('chase_order','asc')->get();
        foreach($steps as $k => $step){
            if($step->id == $curren_position_id){
                $next_step = $steps[$k] ?? false;
                if($next_step){
                    return $next_step;
                }else{
                    return false;
                }
            }
        }
    }
}
