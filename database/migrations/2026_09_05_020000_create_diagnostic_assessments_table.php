<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phase', 10);
            $table->json('responses');
            $table->timestamps();

            $table->unique(['user_id', 'phase']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_assessments');
    }
};
