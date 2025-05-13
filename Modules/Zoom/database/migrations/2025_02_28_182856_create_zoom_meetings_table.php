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
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->ulid('model_id');
            $table->foreignId('supervisor_id')->nullable();//->constrained('users')->nullOnDelete();
            $table->string('zoom_user_id');
            $table->string('zoom_meeting_id');
            $table->string('topic');
            $table->text('agenda')->nullable();
            $table->timestamp('preferred_start_time');
            $table->timestamp('start_time')->nullable();
            $table->integer('duration');
            $table->string('password')->nullable();
            $table->text('join_url');
            $table->text('start_url');
            $table->boolean('is_approved')->default(false);
            $table->json('settings');
            $table->string('status')->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('notifications_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
