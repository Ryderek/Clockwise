<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('roleId')->autoIncrement();
            $table->string('roleName', 64);
            $table->string('roleProcess', 64);
            $table->string('roleSlug', 64)->unique('roleSlug');
            $table->integer('roleStations')->default(1);
            $table->integer('roleIsActive')->default(1);
            $table->timestamps();
        });
        
        DB::table('roles')->insert(
            array(
                'roleName' => 'Operator obróbki ręcznej',
                'roleProcess' => 'Obróbka ręczna',
                'roleSlug' => 'manual',
            )
        );
        
        DB::table('roles')->insert(
            array(
                'roleName' => 'Krawędziarz',
                'roleProcess' => 'Krawędziowanie',
                'roleSlug' => 'edging',
            )
        );
        
        DB::table('roles')->insert(
            array(
                'roleName' => 'Spawacz',
                'roleProcess' => 'Spawanie',
                'roleSlug' => 'welding',
            )
        );
        
        DB::table('roles')->insert(
            array(
                'roleName' => 'Tokarz',
                'roleProcess' => 'Toczenie',
                'roleSlug' => 'turning',
            )
        );

        DB::table('roles')->insert(
            array(
                'roleName' => 'Operator CNC',
                'roleProcess' => 'Obróbka CNC',
                'roleSlug' => 'cnc',
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
};
