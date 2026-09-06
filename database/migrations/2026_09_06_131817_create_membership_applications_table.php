<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_applications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email');
            $table->unsignedSmallInteger('lots_interested')->default(1);
            $table->text('message')->nullable();
            /* Which language the applicant was reading when they applied. */
            $table->string('locale', 5);
            /* Plain string rather than a database enum, so adding a status later
               needs no schema change and stays portable across drivers. */
            $table->string('status')->default('new');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_applications');
    }
};
