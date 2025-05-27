<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalsTable extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE TYPE rental_status AS ENUM ('oczekujace', 'nadchodzace', 'aktualne', 'przeszle')");

        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status'); // zaplanowane, aktywne, zakończone, anulowane, reklamacja
            $table->text('notes')->nullable(); // warunki, uwagi, reklamacje
            $table->string('payment_reference')->nullable(); // np. tokenizacja
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
        DB::statement("DROP TYPE IF EXISTS rental_status");
    }
}
