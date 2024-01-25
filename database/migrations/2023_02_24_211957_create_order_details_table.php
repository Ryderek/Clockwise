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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('orderDetailId')->autoIncrement();
            $table->string('orderDetailUniqueId', 16)->unique();
            $table->integer('orderDetailOrderId');
            $table->integer('orderDetailOrderNumber');
            $table->string('orderDetailName', 127);
            $table->double('orderDetailUnitProductionCost')->default(0);
            $table->double('orderDetailUnitSellValue')->default(0);
            $table->integer('orderDetailItemsDone')->default(0);
            $table->integer('orderDetailItemsDeployed')->default(0);
            $table->integer('orderDetailItemsTotal');
            $table->string('orderDetailPainting', 127)->nullable();
            $table->integer('orderDetailCooperation')->default(0);
            $table->binary('orderDetailDescriptor')->nullable();
            $table->integer('orderDetailIsDeleted')->default(0);
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
        Schema::dropIfExists('order_details');
    }
};
