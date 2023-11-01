<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('permissions');
            $table->bigInteger('level')->default(2);
            $table->timestamps();
        });

        // Insert some stuff
        DB::table('roles')->insert(
            array(
                'id' => 1,
                'name' => 'Adviser',
                'permissions' => 'adviser',
                'level' => 2
            )
        );

        DB::table('roles')->insert(
            array(
                'id' => 2,
                'name' => 'Client',
                'permissions' => 'client',
                'level' => 99
            )
        );

        DB::table('roles')->insert(
            array(
                'id' => 3,
                'name' => 'Admin',
                'permissions' => 'admin',
                'level' => 1
            )
        );

        DB::table('roles')->insert(
            array(
                'id' => 4,
                'name' => 'Super Admin',
                'permissions' => 'sudo',
                'level' => 0
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
