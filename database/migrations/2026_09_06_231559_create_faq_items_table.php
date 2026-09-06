<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FAQ entries belong to the section that renders them, so any page can host
     * a FAQ list without a table of its own. The wording lives in the
     * translations; everything shared sits here.
     */
    public function up(): void
    {
        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();

            /* Shows the additional-usage cost breakdown under the answer. The
               figures in it come from membership_settings, never from here. */
            $table->boolean('shows_usage_breakdown')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['page_section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_items');
    }
};
