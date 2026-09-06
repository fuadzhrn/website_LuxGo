<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_settings', function (Blueprint $table) {
            $table->id();
            /* Typed and shared: these numbers are identical in both locales, so
               they never belong in a translation row.

               There is deliberately no additional_lot_price column — that price
               has not been published, and inventing one would be a fabrication. */
            $table->unsignedBigInteger('regular_membership_price');
            $table->unsignedBigInteger('promo_membership_price');
            $table->unsignedInteger('promo_member_limit');
            $table->unsignedSmallInteger('membership_period_years');
            $table->unsignedSmallInteger('base_usage_rights_per_year');
            $table->unsignedSmallInteger('additional_lot_rights_per_year');
            $table->unsignedBigInteger('member_usage_fee');
            $table->unsignedBigInteger('additional_usage_fee');
            $table->unsignedSmallInteger('usage_duration_hours');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_settings');
    }
};
