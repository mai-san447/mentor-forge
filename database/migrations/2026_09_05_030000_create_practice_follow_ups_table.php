<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('case_reflection_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weeks_after');
            $table->boolean('practiced');
            $table->text('counterpart_reaction')->nullable();
            $table->string('consultation_change', 30)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['case_reflection_id', 'weeks_after']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_follow_ups');
    }
};
