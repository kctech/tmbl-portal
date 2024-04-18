<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Blade;
use App\Jobs\QueueRenderedEmail;
use App\Libraries\Render;

class ChaseEmail {

	public static function version() {
		return "1.0";
	}

    //TEAMS GRAPH CREATION ERROR
    public static function createAndSend($prospect, $chaser, $send_email=false)
    {

        if(!empty($prospect->owner)){
            $adviser = $prospect->owner;
        }else{
            /*$adviser = collect((object)[
                "first_name" => "The Mortgage Broker",
                "last_name" => "",
                "tel" => "0800 0320 316",
                "email" => "enquiries@tmblgroup.co.uk"
            ]);*/
            $adviser = new \App\Models\User;
            $adviser->first_name = "The Mortgage Broker";
            $adviser->last_name = "";
            $adviser->tel = "0800 0320 316";
            $adviser->email = "enquiries@tmblgroup.co.uk";
        }

        $merge_data = ['prospect'=>json_decode($prospect->data), 'adviser'=>$adviser->toArray(), 'chaser'=>$chaser->toArray()];
        $merge_data_compiled = [];
        foreach($merge_data as $datatype => $values){
            foreach($values as $key => $value){
                $merge_data_compiled[$datatype.":".$key] = $value;
            }
        }

        if($send_email){
            //build email
            $email['ident'] = $chaser->name;
            $email['subject'] = $chaser->subject;
            $email['body'] = Blade::render(Render::merge_data($merge_data_compiled,$chaser->body));
            $email['to'] = $prospect->email_address;
            $email['from'] = $adviser->email;
            $email['fromName'] = $adviser->first_name ." ". $adviser->last_name;
            $email['replyTo'] = $adviser->email;

            dispatch(new QueueRenderedEmail($email))->onQueue('lead_chasers');
        }

        return $merge_data_compiled;
    }
}
