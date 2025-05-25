<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // ID_płatności
            $table->foreignId('rental_id')->constrained()->onDelete('cascade'); // ID_rezerwacji
            $table->decimal('amount', 10, 2); // Kwota
            $table->date('payment_date')->nullable(); // Data płatności
            $table->enum('status', ['oczekujące', 'oplacone', 'anulowane'])->default('oczekujące');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
