<?php

namespace App\Console\Commands\Azure;

use Illuminate\Console\Command;
use App\Libraries\Azure\GraphConnector;
use App\Libraries\SSO\AzureProvider; //Uses application level single sign on

class RefreshUserObjectIDs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'azure:refreshUserObjectIDs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This pulls the user information from the client azure active directory and adds or updates them in microsoft_object_ids';

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
        $azure = new AzureProvider(account_id:3);
        $graph = new GraphConnector($azure);
        echo $graph->initialise();
        return 0;
    }
}
