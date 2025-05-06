<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cvs', function (Blueprint $table) {
            $table->id();
            $table->string('fname');
            $table->string('lname');
            $table->string('img')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->json('skills')->nullable();
            $table->integer('exp_years')->nullable();
            $table->string('country')->nullable();
            $table->json('expertise')->nullable();
            $table->json('education')->nullable();
            $table->json('certificates')->nullable();
            $table->string('linkden_profile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cvs');
    }
};
