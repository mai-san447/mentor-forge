<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_reflections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scenario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_response_id')->nullable()->constrained('drill_responses')->nullOnDelete();
            $table->text('selected_response_content');
            $table->text('selection_reason');
            $table->text('difference');
            $table->text('next_action');
            $table->timestamps();

            $table->unique(['user_id', 'scenario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_reflections');
    }
};
