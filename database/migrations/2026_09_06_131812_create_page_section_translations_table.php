<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            /* One JSON document per locale rather than title_en / title_id
               columns, so a section can grow new fields without a migration. */
            $table->json('content');
            $table->timestamps();

            $table->unique(['page_section_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_translations');
    }
};
