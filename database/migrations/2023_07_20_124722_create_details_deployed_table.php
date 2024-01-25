<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('details_deployed', function (Blueprint $table) {
            $table->id('deployedDetailId');
            $table->integer('deployedDetailOrderId');
            $table->integer('deployedDetailDetailId');
            $table->integer('deployedDetailOrderNumber');
            $table->integer('deployedDetailEAN');
            $table->integer('deployedDetailIsDeployed');
            $table->timestamps();
            $table->unique(array('deployedDetailDetailId', 'deployedDetailEAN'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('details_deployed');
    }
};
