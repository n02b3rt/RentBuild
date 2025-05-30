<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostsAndOperatorToRentalsTable extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (! Schema::hasColumn('rentals', 'with_operator')) {
                $table->boolean('with_operator')->default(false)->after('end_date');
            }

            if (! Schema::hasColumn('rentals', 'days')) {
                $table->integer('days')->unsigned()->nullable()->after('with_operator');
            }

            if (! Schema::hasColumn('rentals', 'equipment_daily_rate')) {
                $table->decimal('equipment_daily_rate', 8, 2)
                    ->nullable()
                    ->after('days');
            }

            if (! Schema::hasColumn('rentals', 'equipment_cost')) {
                $table->decimal('equipment_cost', 10, 2)
                    ->nullable()
                    ->after('equipment_daily_rate');
            }

            if (! Schema::hasColumn('rentals', 'operator_daily_rate')) {
                $table->decimal('operator_daily_rate', 8, 2)
                    ->nullable()
                    ->after('equipment_cost');
            }

            if (! Schema::hasColumn('rentals', 'operator_cost')) {
                $table->decimal('operator_cost', 10, 2)
                    ->nullable()
                    ->after('operator_daily_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'operator_cost')) {
                $table->dropColumn('operator_cost');
            }
            if (Schema::hasColumn('rentals', 'operator_daily_rate')) {
                $table->dropColumn('operator_daily_rate');
            }
            if (Schema::hasColumn('rentals', 'equipment_cost')) {
                $table->dropColumn('equipment_cost');
            }
            if (Schema::hasColumn('rentals', 'equipment_daily_rate')) {
                $table->dropColumn('equipment_daily_rate');
            }
            if (Schema::hasColumn('rentals', 'days')) {
                $table->dropColumn('days');
            }
            if (Schema::hasColumn('rentals', 'with_operator')) {
                $table->dropColumn('with_operator');
            }
        });
    }
}
