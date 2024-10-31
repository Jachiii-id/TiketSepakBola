<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add default value to role_id and registration_date
            $table->unsignedBigInteger('role_id')->default(2)->change();
            $table->date('registration_date')->default(now())->change();

            // Make nik, number, birth_date, and gender nullable
            $table->bigInteger('nik')->nullable()->change();
            $table->bigInteger('number')->nullable()->change();
            $table->date('birth_date')->nullable()->change();
            $table->string('gender')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert changes
            $table->unsignedBigInteger('role_id')->default(null)->change();
            $table->date('registration_date')->default(null)->change();

            $table->integer('nik')->nullable(false)->change();
            $table->integer('number')->nullable(false)->change();
            $table->date('birth_date')->nullable(false)->change();
            $table->string('gender')->nullable(false)->change();
        });
    }
};
