<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Images a section shows, addressed by slot ("hero_image", "business_image").
     * The slot is what the page asks for, so a section can carry several images
     * without the page needing to know which media row is behind each one, and
     * the same relation serves every page that comes after Home.
     */
    public function up(): void
    {
        Schema::create('page_section_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('slot');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            /* One image per slot, which is what a fixed layout expects. */
            $table->unique(['page_section_id', 'slot']);
            $table->index('media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_media');
    }
};
