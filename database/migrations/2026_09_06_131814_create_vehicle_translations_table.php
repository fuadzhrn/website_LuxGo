<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->json('content');
            $table->timestamps();

            $table->unique(['vehicle_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_translations');
    }
};
