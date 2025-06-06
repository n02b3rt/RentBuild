<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', [
                'oczekujace',
                'nadchodzace',
                'aktualne',
                'zrealizowane',
                'anulowane',
                'odrzucone',
                'reklamacja',
                'reklamacja_weryfikacja',
                'reklamacja_odrzucono',
                'reklamacja_przyjeto',
            ]);
            $table->text('notes')->nullable(); // warunki, uwagi, reklamacje
            $table->string('payment_reference')->nullable(); // np. tokenizacja
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
}
