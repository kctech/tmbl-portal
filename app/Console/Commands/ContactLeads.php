<?php

namespace App\Console\Commands;

use App\Jobs\QueueRenderedEmail;
use App\Libraries\Render;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Blade;

use App\Models\Lead;
use App\Models\LeadChaser;
use App\Models\LeadEvent;
use Carbon\Carbon;
use stdClass;

class ContactLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:contact {--account_id=1} {--live=true}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the lead contact schedule';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        session()->put('account_id',$this->option('account_id'));
        $contact_schedule = LeadChaser::where('method','email')->where('status',LeadChaser::ACTIVE)->get();
        $prospects = Lead::whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED,Lead::CLAIMED])->get();

        foreach($prospects as $prospect){
            $this->info($prospect->id."|".$prospect->email_address."|".$prospect->created_at);

            $created_at = Carbon::parse($prospect->created_at);
            $prospect_auto_comms = $prospect->events()->where('event_id',LeadEvent::AUTO_CONTACT_ATTEMPTED)->pluck('information')->toArray();

            foreach($contact_schedule as $chaser){
                if(!in_array($chaser->id, $prospect_auto_comms)){
                    $this->info("check time for ".$chaser->id." - ".$chaser->chase_duration);
                    if($created_at->copy()->add($chaser->chase_duration)->isPast()){
                        $this->info("> send ".$chaser->id);

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

                        if($this->option('live') == 'true'){
                            //build email
                            $email['ident'] = $chaser->name;
                            $email['subject'] = $chaser->subject;
                            $email['body'] = Blade::render(Render::merge_data($merge_data_compiled,$chaser->body));
                            $email['to'] = $prospect->email_address;
                            $email['from'] = $adviser->email;
                            $email['fromName'] = $adviser->first_name ." ". $adviser->last_name;
                            $email['replyTo'] = $adviser->email;

                            dispatch(new QueueRenderedEmail($email))->onQueue('lead_chasers');

                            //record event
                            //$prospect->status = Lead::AUTO_CONTACT_ATTEMPTED;
                            $prospect->last_contacted_at = date("Y-m-d H:i:s");
                            ++$prospect->contact_count;
                            $prospect->save();
                            $prospect->events()->create([
                                'account_id' => $prospect->account_id,
                                'user_id' => 0,
                                'event_id' => LeadEvent::AUTO_CONTACT_ATTEMPTED,
                                'information' => $chaser->id
                            ]);
                        }else{
                            //dump($adviser->email ?? $adviser['email']);
                            dump($merge_data_compiled);
                            //dump(Render::merge_data($merge_data_compiled,$chaser->body));
                        }
                    }
                }
            }
        }
    }
}
