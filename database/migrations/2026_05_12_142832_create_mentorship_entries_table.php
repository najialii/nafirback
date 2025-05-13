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
            $table->dateTime('date'); 
            $table->integer('duration'); 
            $table->unsignedBigInteger('bookedBy'); 
            $table->unsignedBigInteger('mentorship_id');  
            $table->timestamps();

            $table->foreign('bookedBy')->references('id')->on('users')->onDelete('cascade');
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