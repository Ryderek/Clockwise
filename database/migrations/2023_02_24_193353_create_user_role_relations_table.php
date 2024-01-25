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
        Schema::create('user_role_relations', function (Blueprint $table) {
            $table->id('userRoleRelationId')->autoIncrement();
            $table->integer('userRoleUserId');
            $table->integer('userRoleRoleId');
            $table->unique(array("userRoleUserId", "userRoleRoleId"));
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
        Schema::dropIfExists('user_role_relations');
    }
};
