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
        Schema::table('otp_verifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('otp_verifikasi', 'attempts')) {
                $table->unsignedTinyInteger('attempts')->default(0)->after('is_used');
            }
            if (!Schema::hasColumn('otp_verifikasi', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('attempts');
            }
            if (!Schema::hasColumn('otp_verifikasi', 'user_agent')) {
                $table->string('user_agent', 255)->nullable()->after('ip_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verifikasi', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('otp_verifikasi', 'attempts')) {
                $cols[] = 'attempts';
            }
            if (Schema::hasColumn('otp_verifikasi', 'ip_address')) {
                $cols[] = 'ip_address';
            }
            if (Schema::hasColumn('otp_verifikasi', 'user_agent')) {
                $cols[] = 'user_agent';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
