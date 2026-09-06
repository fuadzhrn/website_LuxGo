<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            /* The share image is shared by both locales; only the wording differs. */
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_indexable')->default(true);
            $table->timestamps();

            /* One SEO record per page. */
            $table->unique('page_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
