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
        Schema::create('auth_cards', function (Blueprint $table) {
            $table->id('authCardId')->autoIncrement();
            $table->integer('authCardUserId');
            $table->string('authCardCode', 127)->unique();
            $table->dateTime('authCardLastUsed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auth_cards');
    }
};
