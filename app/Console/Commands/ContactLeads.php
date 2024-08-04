<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\ChaseEmail;

use App\Models\Lead;
use App\Models\LeadChaseStep;
use App\Models\LeadChaseStepContactMethod;
use App\Models\LeadEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        $chase_steps = LeadChaseStep::where('status',LeadChaseStep::ACTIVE)->get();
        $prospects = Lead::whereIn('status',[Lead::PROSPECT,Lead::CONTACT_ATTEMPTED,Lead::CLAIMED])->get();

        $live = false;
        if($this->option('live') == 'true'){
            $live = true;
        }

        foreach($prospects as $prospect){
            $this->info($prospect->id."|".$prospect->email_address."|".$prospect->created_at);

            $created_at = Carbon::parse($prospect->created_at);
            $prospect_auto_comms = $prospect->events()->where('event_id',LeadEvent::AUTO_CONTACT_ATTEMPTED)->pluck('information')->toArray(); //avoid re-sending the same thing

            foreach($chase_steps as $step){
                $this->info("> checking step ".$step->id);
                foreach($step->contact_methods()->where(function($q){ $q->where('method','email')->where('auto_contact',LeadChaseStepContactMethod::AUTO_CONTACT); })->get() as $contact){
                    $this->info("> checking contact method ".$contact->id);
                    if(!in_array($contact->id, $prospect_auto_comms)){
                        $this->info("check time for ".$contact->id." - ".$contact->chase_duration);
                        if($created_at->copy()->add($contact->chase_duration)->isPast()){
                            $this->info("> send ".$contact->id);

                            try{
                                $merge_data_compiled = ChaseEmail::createAndSend($prospect,$contact,$live);
                            }catch(\Exception $e){
                                Log::error($merge_data_compiled);
                                Log::error($e->getMessage());
                            }

                            if($live){
                                //record event
                                //$prospect->status = Lead::AUTO_CONTACT_ATTEMPTED;
                                $prospect->last_contacted_at = date("Y-m-d H:i:s");
                                if($step->auto_progress == LeadChaseStep::AUTO_PROGRESS){
                                    $prospect->strategy_position_id = LeadChaseStep::getNextStep(1,$prospect->strategy_position_id)->id ?? $prospect->strategy_position_id;
                                }
                                ++$prospect->contact_count;
                                $prospect->save();
                                $prospect->events()->create([
                                    'account_id' => $prospect->account_id,
                                    'user_id' => 0,
                                    'event_id' => LeadEvent::AUTO_CONTACT_ATTEMPTED,
                                    'information' => $contact->id
                                ]);
                            }else{
                                //dump($adviser->email ?? $adviser['email']);
                                dump($merge_data_compiled);
                                //dump(Render::merge_data($merge_data_compiled,$contact->body));
                            }
                        }
                    }
                }
            }
        }
    }
}
