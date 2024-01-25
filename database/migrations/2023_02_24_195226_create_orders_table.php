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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('orderId')->autoIncrement();
            $table->string('orderName', 127);
            $table->enum('orderStatus', ["created", "confirmed", "in-production", "done"])->default('created');
            $table->integer('orderCustomer')->index('orderCustomer');
            $table->dateTime('orderDeadline');
            $table->integer('orderStatusLight')->default(0);
            $table->double('orderValue', 10, 2)->default(0);
            $table->integer('orderCooperated')->default(0);
            $table->string('orderAdditionalField', 127)->nullable();
            $table->integer('orderCreatedBy');
            $table->dateTime('orderCreatedTime');
            $table->integer('orderConfirmedBy')->nullable();
            $table->dateTime('orderConfirmedTime')->nullable();
            $table->integer('orderPublishedBy')->nullable();
            $table->dateTime('orderPublishedTime')->nullable();
            $table->integer('orderDoneBy')->nullable();
            $table->dateTime('orderDoneTime')->nullable();
            $table->integer('orderIsDeleted')->default(0);
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
        Schema::dropIfExists('orders');
    }
};
