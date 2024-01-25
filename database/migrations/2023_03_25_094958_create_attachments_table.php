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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id('attachmentId')->autoIncrement();
            $table->string('attachmentTitle', 127);
            $table->string('attachmentPath', 255);
            $table->string('attachmentRelatorSlug', 64)->index('relatorSlugs');
            $table->integer('attachmentRelatorId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
