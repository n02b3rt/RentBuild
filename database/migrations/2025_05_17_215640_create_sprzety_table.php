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
        Schema::create('sprzety', function (Blueprint $table) {
            $table->id();
            $table->string('nazwa', 255);
            $table->text('opis');
            $table->enum('dostepnosc', ['dostepny', 'niedostepny', 'rezerwacja']);
            $table->decimal('cena_wynajmu', 8, 2);
            $table->string('zdjecie_glowne');
            $table->string('folder_zdjec');
            $table->enum('stan_techniczny', ['nowy', 'uzywany', 'naprawa']);
            $table->string('kategoria');
            $table->integer('upust')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sprzety');
    }
};
