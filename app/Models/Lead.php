<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use \Carbon\Carbon;

class Lead extends Model
{

    const PROSPECT = 0;
    const CLAIMED = 1;
    const CONTACTED = 2;
    const TRANSFERRED = 3;
    const COLD = 4;
    const ARCHIVED = 5;

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
        'uuid','source_id','account_id','user_id','client_id','first_name','last_name','email_address','contact_number','data','options','status','allocated_at','transferred_at','contact_count','last_contacted_at'
    ];

    public function newQuery()
    {
        return parent::newQuery()->where('account_id', session('account_id'));
    }

    /**
     * Get the client that owns the consent is related to.
     */
    public function full_name()
    {
        return $this->first_name." ".$this->last_name;
    }

    /**
     * Get the source that the lead is related to.
     */
    public function source()
    {
        return $this->belongsTo(\App\Models\ApiKey::class);
    }

    /**
     * Get the allocated user that the lead is related to.
     */
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class,'user_id','id');
    }

    //date query filter maker
    public static function date_filter_prev_period($limits, $currentYear=true){
        $date = null;
        if ($limits != 'ALL') {
            switch ($limits) {
                case 'YTD':
                    $date = "PREV_YTD";
                    break;
                case 'LAST_YEAR':
                    $date = "LAST_YEAR_2";
                    break;
                case 'THIS_YEAR':
                    $date = "LAST_YEAR";
                    break;
                case 'NEXT_YEAR':
                    $date = 'THIS_YEAR';
                    break;
                case 'LAST_6_MONTHS':
                    $date = "LAST_MONTH_6";
                    break;
                case 'LAST_3_MONTHS':
                    $date = "LAST_MONTH_3";
                    break;
                case 'LAST_MONTH':
                    $date = "LAST_MONTH_2";
                    break;
                case 'THIS_MONTH':
                    $date = "LAST_MONTH";
                    break;
                case 'NEXT_MONTH':
                    $date = 'THIS_MONTH';
                    break;
                case 'LAST_WEEK':
                    $date = "LAST_WEEK_2";
                    break;
                case 'THIS_WEEK':
                    $date = "LAST_WEEK";
                    break;
                case 'NEXT_WEEK':
                    $date = 'THIS_WEEK';
                    break;
                case 'JANUARY':
                    $date = 'LAST_JANUARY';
                    break;
                case 'FEBRUARY':
                    $date = 'LAST_FEBRUARY';
                    break;
                case 'MARCH':
                    $date = 'LAST_MARCH';
                    break;
                case 'APRIL':
                    $date = 'LAST_APRIL';
                    break;
                case 'MAY':
                    $date = 'LAST_MAY';
                    break;
                case 'JUNE':
                    $date = 'LAST_JUNE';
                    break;
                case 'JULY':
                    $date = 'LAST_JULY';
                    break;
                case 'AUGUST':
                    $date = 'LAST_AUGUST';
                    break;
                case 'SEPTEMBER':
                    $date = 'LAST_SEPTEMBER';
                    break;
                case 'OCTOBER':
                    $date = 'LAST_OCTOBER';
                    break;
                case 'NOVEMBER':
                    $date = 'LAST_NOVEMBER';
                    break;
                case 'DECEMBER':
                    $date = 'LAST_DECEMBER';
                    break;
            }
        }

        return $date;
    }

    //date query filter maker
    public static function date_filter_query($column, $limits){
        $date = null;
        if ($limits != 'ALL') {
            switch ($limits) {
                case 'FUTURE':
                    $date = " and " . $column . " >= '" . Carbon::now()->toDateTimeString() . "'";
                    break;
                case 'PAST':
                    $date = " and " . $column . " < '" . Carbon::now()->toDateTimeString() . "'";
                    break;
                case 'TODAY':
                    $date = " and " . $column . " >= '" . Carbon::now()->startOfDay()->toDateTimeString() . "' and " . $column . " <= '" . Carbon::now()->endOfDay()->toDateTimeString() . "'";
                    break;
                case 'YESTERDAY':
                    $date = " and " . $column . " >= '" . Carbon::now()->subDay()->startOfDay()->toDateTimeString() . "' and " . $column . " <= '" . Carbon::now()->subDay()->endOfDay()->toDateTimeString() . "'";
                    break;
                case 'YTD':
                    $date = " and ". $column ." > '" . Carbon::now()->startofYear()->subYear()->endOfYear()->toDateTimeString() . "'";
                    break;
                case 'PREV_YTD':
                    $date = " and ". $column ." > '" . Carbon::now()->startofYear()->subYear()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->subYear()->toDateTimeString() . "'";
                    break;
                case 'LAST_YEAR':
                    $date = " and ". $column ." > '" . Carbon::now()->startofYear()->subYear()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofYear()->subYear()->endOfYear()->toDateTimeString() . "'";
                    break;
                case 'LAST_YEAR_2':
                    $date = " and ". $column ." > '" . Carbon::now()->startofYear()->subYear()->subYear()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofYear()->subYear()->subYear()->endOfYear()->toDateTimeString() . "'";
                    break;
                case 'LAST_MONTH':
                    $date = " and ". $column ." > '" . Carbon::now()->startofMonth()->subMonth()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofMonth()->subMonth()->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_MONTH_2':
                    $date = " and ". $column ." > '" . Carbon::now()->startofMonth()->subMonth()->subMonth()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofMonth()->subMonth()->subMonth()->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_MONTH_3':
                    $date = " and " . $column . " > '" . Carbon::now()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofMonth()->subMonth()->subMonth()->subMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_MONTH_6':
                    $date = " and " . $column . " > '" . Carbon::now()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofMonth()->subMonth()->subMonth()->subMonth()->subMonth()->subMonth()->subMonth()->toDateTimeString() . "'";
                    break;
                case 'THIS_MONTH':
                    $date = " and " . $column . " > '" . Carbon::now()->startofMonth()->subMonth()->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'NEXT_MONTH':
                    $date = " and " . $column . " > '" . Carbon::now()->endOfMonth()->addMonth()->startofMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_WEEK':
                    $date = " and ". $column ." > '" . Carbon::now()->startofWeek()->subWeek()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofWeek()->subWeek()->endOfWeek()->toDateTimeString() . "'";
                    break;
                case 'LAST_WEEK_2':
                    $date = " and ". $column ." > '" . Carbon::now()->startofWeek()->subWeek()->subWeek()->toDateTimeString() . "' and ". $column ." < '" . Carbon::now()->startofWeek()->subWeek()->subWeek()->endOfWeek()->toDateTimeString() . "'";
                    break;
                case 'THIS_WEEK':
                    $date = " and ". $column ." > '" . Carbon::now()->startofWeek()->subWeek()->endOfWeek()->toDateTimeString() . "'";
                    break;
                case 'NEXT_WEEK':
                    $date = " and " . $column . " > '" . Carbon::now()->endOfWeek()->addWeek()->startofWeek()->toDateTimeString() . "'";
                    break;
                case 'JANUARY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(1)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(1)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_JANUARY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(1)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(1)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'FEBRUARY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(2)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(2)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_FEBRUARY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(2)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(2)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'MARCH':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(3)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(3)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_MARCH':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(3)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(3)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'APRIL':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(4)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(4)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_APRIL':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(4)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(4)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'MAY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(5)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(5)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_MAY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(5)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(5)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'JUNE':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(6)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(6)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_JUNE':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(6)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(6)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'JULY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(7)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(7)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_JULY':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(7)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(7)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'AUGUST':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(8)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(8)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_AUGUST':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(8)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(8)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'SEPTEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(9)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(9)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_SEPTEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(9)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(9)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'OCTOBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(10)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(10)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_OCTOBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(10)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(10)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'NOVEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(11)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(11)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_NOVEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(11)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(11)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'DECEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->month(12)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->month(12)->endOfMonth()->toDateTimeString() . "'";
                    break;
                case 'LAST_DECEMBER':
                    $date = " and " . $column . " > '" . Carbon::now()->startofYear()->subYear()->month(12)->startofMonth()->toDateTimeString() . "' and " . $column . " < '" . Carbon::now()->startofYear()->subYear()->month(12)->endOfMonth()->toDateTimeString() . "'";
                    break;
            }
        }

        return $date;
    }
}
