<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Shared, untranslated values for a section — a CTA destination, for
     * example. They belong here rather than in the translations, where they
     * would have to be entered once per locale and could drift apart.
     */
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('section_key');
        });
    }

    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropColumn('settings');
        });
    }
};
