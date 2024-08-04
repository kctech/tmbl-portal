<?php
namespace App\Libraries;
use \DB;

class Postcode {

	public static function town($postcode) {
		$postcode = urlencode( strtoupper( str_replace(' ','',$postcode) ) );
		$url = "https://postcodes.io/postcodes/$postcode";
		$json_payload = @file_get_contents($url);

		if($json_payload!==false){
			$json = json_decode( $json_payload );
			if( $json->status==200 ){
				$town = explode(',',$json->result->parish);
				$town = trim($town[0]);
				return $town;
			}
		}else{
			return __('your area');
		}

	}

	/**
	 * Removes all spaces from a postcode and converts to uppercase to match stored database format
	 * in the postcodes table
	 *
	 * @return String
	 */
	public static function clean($postcode){
		return strtoupper(str_replace(' ','',$postcode));
	}
}
