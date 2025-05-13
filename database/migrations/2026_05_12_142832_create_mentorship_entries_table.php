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
      Schema::create('mentorship_entries', function (Blueprint $table) {
    $table->id(); 
    $table->dateTime('session_date');
    $table->integer('duration');
    $table->unsignedBigInteger('mentorship_id')->nullable();
    $table->unsignedBigInteger('accepted_request_id')->nullable(); 
    $table->string('link')->nullable();
    $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
    $table->timestamps();
});

Schema::table('mentorship_entries', function (Blueprint $table) {
    $table->foreign('mentorship_id')->references('id')->on('mentorships')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentorship_entries');
    }
};