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
        Schema::create('work_timings', function (Blueprint $table) {
            $table->id('workTimingId')->autoIncrement();
            $table->integer('workTimingUserId');
            $table->integer('workTimingRelatorId')->nullable();
            $table->integer('workTimingRelatorParentId')->nullable();
            $table->string('workTimingRoleSlug', 64)->nullable();
            $table->enum('workTimingType', ["estimated", "real", "worktime", "complex"]);
            $table->bigInteger('workTimingStart')->nullable();
            $table->bigInteger('workTimingEnd')->nullable();
            $table->integer('workTimingFinal')->nullable();
            $table->string('workTimingMeta', 256)->nullable();
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
        Schema::dropIfExists('work_timings');
    }
};
