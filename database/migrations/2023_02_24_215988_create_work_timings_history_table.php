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
        Schema::create('work_timings_history', function (Blueprint $table) {
            $table->id('workTimingHistoryId')->autoIncrement();
            $table->integer('workTimingHistoryDetailId')->index('detailId');
            $table->integer('workTimingHistoryUserId')->index('userId');
            $table->integer('workTimingHistoryDetailsDone');
            $table->string('workTimingHistoryDescriptor', 512);
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
        Schema::dropIfExists('work_timings_history');
    }
};
