<?php

namespace App\Libraries;
use Config;

class Interpret {

	public static function version() {
		return "1.0";
	}

    //TEAMS GRAPH CREATION ERROR
    public static function TeamsGraphError($type, $returnType = "key")
    {
        $typesArray = array(
            null => 'Success',
            1 => 'User not found',
            3 => 'User fetch failed',
            2 => 'Meeting creation failed',
            4 => 'Missing data', //no venue
            5 => 'No interviewer found',
            6 => 'No application found',
            7 => 'No vacancy found',
            8 => 'No attendees',
            9 => 'Application email address blank',
        );

        if ($returnType != "key") return $typesArray;

        if (array_key_exists($type, $typesArray)) {
            return $typesArray[$type];
        } else {
            return "UNKNOWN (" . $type . ")";
        }
    }

        //TEAMS GRAPH CREATION ERROR
        public static function LeadStatus($type, $returnType = "key")
        {
            $typesArray = array(
                0 => 'Prospect',
                1 => 'Claimed',
                2 => 'Contacted',
                3 => 'Transferred',
                4 => 'Cold',
                5 => 'Dead',
            );

            if ($returnType != "key") return $typesArray;

            if (array_key_exists($type, $typesArray)) {
                return $typesArray[$type];
            } else {
                return "UNKNOWN (" . $type . ")";
            }
        }

}
