<?php
namespace App\Libraries;

class Slack {

	public static function send($channel,$title='New Message',$message,$icon=":information_source:"){
		
		$time = time();
		$url = config('app.slack_hook_url');
        $fields = ['payload'=>"{\"channel\": \"$channel\", \"username\": \"reachbot\", \"text\": \"$title\", \"icon_emoji\": \"$icon\",\"attachments\": [
            {
                \"color\": \"#7eca30\",
                \"text\": \"$message\",
                \"footer\": \"Reach ATS\",
                \"footer_icon\": \"https://candidate.reach-ats.com/images/reach-icon.png\",
                \"ts\": $time
            }
        ]}"];
        $fields_string="";
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);
        curl_close($ch);

	}
}