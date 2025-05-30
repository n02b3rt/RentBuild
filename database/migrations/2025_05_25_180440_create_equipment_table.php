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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description');
            $table->enum('availability', ['dostepny', 'niedostepny', 'rezerwacja']);
            $table->decimal('rental_price', 8, 2);
            $table->string('thumbnail');
            $table->string('folder_photos');
            $table->enum('technical_state', ['nowy', 'uzywany', 'naprawa']);
            $table->string('category');
            $table->enum('promotion_type', ['kategoria', 'pojedyncza'])->nullable(); // Typ promocji
            $table->unsignedInteger('discount')->nullable(); // Domyślny rabat dla produktu
            $table->dateTime('start_datetime')->nullable(); // Data rozpoczęcia promocji
            $table->dateTime('end_datetime')->nullable();   // Data zakończenia promocji
            $table->unsignedInteger('number_of_rentals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
