<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_setting_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->timestamps();

            $table->unique(['seo_setting_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_translations');
    }
};
