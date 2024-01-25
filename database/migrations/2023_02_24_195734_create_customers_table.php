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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customerId')->autoIncrement();
            $table->string('customerName', 127);
            $table->string('customerTaxIdentityNumber', 31);
            $table->string('customerCountry', 127);
            $table->string('customerCity', 127);
            $table->string('customerPostal', 31);
            $table->string('customerAddress', 127);
            $table->string('customerDeliveryCountry', 127);
            $table->string('customerDeliveryCity', 127);
            $table->string('customerDeliveryPostal', 31);
            $table->string('customerDeliveryAddress', 127);
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
        Schema::dropIfExists('customers');
    }
};
