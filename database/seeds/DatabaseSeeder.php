<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => bcrypt('secret'),
        ]);*/

        //roles
        DB::table('roles')->insert([
            'name' => 'Adviser',
            'permissions' => 'adviser',
            'level' => '2',
        ]);
        DB::table('roles')->insert([
            'name' => 'Client',
            'permissions' => 'Client',
            'level' => '99',
        ]);
        DB::table('roles')->insert([
            'name' => 'Admin',
            'permissions' => 'admin',
            'level' => '1',
        ]);
        DB::table('roles')->insert([
            'name' => 'Super Admin',
            'permissions' => 'sudo',
            'level' => '0',
        ]);

        //accounts
        //todo

        //modules
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'clients',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'btlconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'sdltdisclaimers',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'gdprconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'transferrequests',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'businessterms',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'quotes',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'calculators',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'businesstermsprotection',
            'access' => '1',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'leads',
            'access' => '3',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '1',
            'module' => 'lead_admin',
            'access' => '1',
        ]);

        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'clients',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'btlconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'sdltdisclaimers',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'gdprconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'transferrequests',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'businessterms',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'quotes',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'calculators',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'businesstermsprotection',
            'access' => '1',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'leads',
            'access' => '3',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '2',
            'module' => 'lead_admin',
            'access' => '1',
        ]);

        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'clients',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'btlconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'sdltdisclaimers',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'gdprconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'transferrequests',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'businessterms',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'quotes',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'calculators',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'businesstermsprotection',
            'access' => '1',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'leads',
            'access' => '3',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '3',
            'module' => 'lead_admin',
            'access' => '1',
        ]);

        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'clients',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'btlconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'sdltdisclaimers',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'gdprconsents',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'transferrequests',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'businessterms',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'quotes',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'calculators',
            'access' => '2',
        ]);
        DB::table('account_modules')->insert([
            'account_id' => '4',
            'module' => 'businesstermsprotection',
            'access' => '1',
        ]);

    }
}
