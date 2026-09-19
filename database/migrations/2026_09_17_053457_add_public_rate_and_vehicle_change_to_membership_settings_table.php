<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Three figures the revised programme introduced:
 *
 *   the public rate a non-member pays, which the member price is discounted
 *   from; the discount itself, stated as a percentage; and how often the
 *   vehicle is replaced during the membership.
 *
 * The member rate already had a column — what it was a discount *from* did not.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('public_usage_fee')->default(0)->after('member_usage_fee');
            $table->unsignedSmallInteger('usage_discount_percent')->default(0)->after('public_usage_fee');
            $table->unsignedSmallInteger('vehicle_change_years')->default(0)->after('membership_period_years');
        });
    }

    public function down(): void
    {
        Schema::table('membership_settings', function (Blueprint $table) {
            $table->dropColumn(['public_usage_fee', 'usage_discount_percent', 'vehicle_change_years']);
        });
    }
};
