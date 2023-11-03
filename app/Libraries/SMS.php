<?php
namespace App\Libraries;
use App\SMSLog;

class SMS {

	/**
	 * Send a text message to a UK number
	 * @param $recipient The mobile number
	 * @param $message String The message text to send
	 */
	public static function sendToNumber($recipient,$message) {
		$message = urlencode($message);
		$url = "https://platform.clickatell.com/messages/http/send?apiKey=VpS-bk4aSvOUjQ7e5QYQFg==&to=$recipient&content=$message";
		$response = file_get_contents($url);
		$json = json_decode($response);
		if($json){
			$status = "SENT";
			$log = new SMSLog;
			$log->recipient = $recipient;
			$log->message_id = $json->messages[0]->apiMessageId;
			$log->message_type = "TEST";
			if( $json->messages[0]->errorDescription != ''){
				$status = $json->messages[0]->errorDescription;
			}
			$log->status = $status;
			$log->accepted = $json->messages[0]->accepted;
			$log->send_date = \Carbon\Carbon::now();
			$log->save();
			dd($json);

		}
	}
}
