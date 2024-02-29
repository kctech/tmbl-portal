<?php

namespace App\Libraries;

class Render {

	public static function merge_data($data,$string){
        foreach($data as $key => $value){
            $string = preg_replace("/\[".$key."\]/i", $value, $string);
        }
        return $string;
    }
}
