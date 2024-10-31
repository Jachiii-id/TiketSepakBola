<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add ip_address and device_id columns
            $table->string('ip_address')->nullable()->after('payment_channel');
            $table->string('device_id')->nullable()->after('ip_address');
            $table->string('platform')->nullable()->after('device_id');
            $table->string('browser')->nullable()->after('platform');
            $table->string('language')->nullable()->after('browser');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn('ip_address');
            $table->dropColumn('device_id');
            $table->dropColumn('platform');
            $table->dropColumn('browser');
            $table->dropColumn('language');
        });
    }
};