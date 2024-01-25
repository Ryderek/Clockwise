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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('birthDate')->nullable();
            $table->string('address', 127)->nullable();
            $table->string('email')->unique();
            $table->integer('groupId');
            $table->timestamp('email_verified_at')->nullable();
            $table->double('partTimeJob')->default(1);
            $table->string('password');
            $table->integer('isActive')->default(1);
            $table->double('employeeDefaultWage')->default(0);
            $table->double('employeeOvertimeWage')->default(0);
            $table->double('employeeSpecialWage')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
        DB::table('users')->insert(
            array(
                'name' => 'Clockwise',
                'email' => 'clockwise',
                'groupId' => 1,
                'password' => '$2y$10$bvdJpNa2JgAw2qLWD.3lBuXcoUcNjtydqkAxFd524gsc1BTM2Pdoq',
            )
        );
        DB::table('users')->insert(
            array(
                'name' => 'Rafał Mieszczak',
                'email' => 'rafal.egwo@o2.pl',
                'groupId' => 2,
                'password' => '$2y$10$GDup1JOtc5Nq8p84ffz9m.CUqVla/j9CbRZctEZ9FEWUEw2RLv9JG',
            )
        );
        DB::table('users')->insert(
            array(
                'name' => 'Rafał Mieszczak',
                'email' => 'rafal.egwo@o2.pl',
                'groupId' => 2,
                'password' => '$2y$10$xzAX.tcrFj3dAm4qHB1qSer6dqH7165991Bx/WxrzcAG/W8Mmyrqu',
            )
        );
        DB::table('users')->insert(
            array(
                'name' => 'Demo',
                'email' => 'demo@clockwise.online',
                'groupId' => 2,
                'password' => '$2y$10$b0cbPiXCNSJ2x/6Ra8hhIu71sRIJFxDisxwH8Z.DfATiiOSQX0v7C',
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
        Schema::dropIfExists('users');
    }
};
