<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('background');
            $table->text('challenge');
            $table->string('tone');
            $table->string('accent_color')->default('#0f766e');
            $table->timestamps();
        });

        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('situation');
            $table->text('goal');
            $table->string('difficulty')->default('初級');
            $table->timestamps();
        });

        Schema::create('roleplay_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('persona_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scenario_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mode');
            $table->string('status')->default('active');
            $table->string('room_code', 8)->nullable()->unique();
            $table->string('mentor_name')->nullable();
            $table->string('mentee_name')->nullable();
            $table->string('observer_name')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('reflection')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('roleplay_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roleplay_session_id')->constrained()->cascadeOnDelete();
            $table->string('speaker');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('roleplay_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roleplay_session_id')->constrained()->cascadeOnDelete();
            $table->string('reviewer_role');
            $table->unsignedTinyInteger('listening_score');
            $table->unsignedTinyInteger('empathy_score');
            $table->unsignedTinyInteger('question_score');
            $table->text('strengths');
            $table->text('improvements');
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->text('question');
            $table->json('choices');
            $table->unsignedTinyInteger('correct_index');
            $table->text('explanation');
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('score');
            $table->unsignedTinyInteger('total');
            $table->json('answers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('roleplay_feedback');
        Schema::dropIfExists('roleplay_messages');
        Schema::dropIfExists('roleplay_sessions');
        Schema::dropIfExists('scenarios');
        Schema::dropIfExists('personas');
    }
};
