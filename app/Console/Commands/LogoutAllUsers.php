<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class LogoutAllUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs out all users';

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
     * @return mixed
     */
    public function handle()
    {
        DB::table('sessions')->truncate();

        $users = User::all();
        foreach ($users as $user) {
            $user->update(['remember_token'=>null]);
        }

        exec('rm -rf storage/framework/sessions/*');

    }
}
