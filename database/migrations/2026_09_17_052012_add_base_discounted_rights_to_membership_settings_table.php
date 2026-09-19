<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Discounted Usage Rights became a benefit of the membership itself, not only
 * something an additional LOT buys. The base amount needs a figure of its own:
 * until now the only discounted figure was the per-LOT one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_settings', function (Blueprint $table) {
            $table->unsignedSmallInteger('base_discounted_rights_per_year')
                ->default(0)
                ->after('base_usage_rights_per_year');
        });
    }

    public function down(): void
    {
        Schema::table('membership_settings', function (Blueprint $table) {
            $table->dropColumn('base_discounted_rights_per_year');
        });
    }
};
