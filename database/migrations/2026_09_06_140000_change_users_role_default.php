<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The foundation stage defaulted this column to 'admin', which is not a role
     * the admin area recognises. Defaulting to 'user' instead means an account
     * created without an explicit role has no privileges at all — access has to
     * be granted deliberately rather than inherited by accident.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
        });
    }
};
