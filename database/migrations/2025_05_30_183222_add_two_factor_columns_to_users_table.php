<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('two_factor_secret')
                ->nullable()
                ->after('remember_token');
            $table->boolean('two_factor_enabled')
                ->default(false)
                ->after('two_factor_secret');
            $table->text('two_factor_recovery_codes')->nullable();
            $table->unsignedInteger('two_factor_failed_attempts')
                ->default(0)
                ->after('two_factor_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_enabled',
                'two_factor_recovery_codes',
                'two_factor_failed_attempts',
            ]);
        });
    }
};
