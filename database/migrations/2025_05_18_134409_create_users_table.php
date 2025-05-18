<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID_klienta
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->text('address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('payment_token')->nullable();
            $table->string('payment_provider')->nullable();
            $table->enum('role', ['administrator', 'klient'])->default('klient');
            $table->unsignedInteger('rentals_count')->default(0);
            $table->decimal('account_balance', 10, 2)->default(0.00);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
