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
        if (!Schema::hasTable('otp_verifikasi')) {
            Schema::create('otp_verifikasi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('email');
                $table->string('otp_code', 6);
                $table->timestamp('expired_at');
                $table->boolean('is_used')->default(false);
                $table->timestamps();

                $table->index(['email', 'is_used']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifikasi');
    }
};
