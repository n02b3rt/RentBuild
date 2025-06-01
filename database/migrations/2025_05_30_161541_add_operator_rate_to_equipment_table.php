<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorRateToEquipmentTable extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            // stawka operatora za dzień, zależna od kategorii sprzętu
            $table->decimal('operator_rate', 8, 2)
                ->default(0)
                ->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('operator_rate');
        });
    }
}
