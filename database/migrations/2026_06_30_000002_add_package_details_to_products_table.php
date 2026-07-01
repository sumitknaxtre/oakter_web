<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('package_weight_kg', 8, 3)
                ->nullable()
                ->after('sku')
                ->comment('Dead weight in kg for Shiprocket');

            $table->decimal('package_length_cm', 8, 2)
                ->nullable()
                ->after('package_weight_kg')
                ->comment('Package length in cm');

            $table->decimal('package_breadth_cm', 8, 2)
                ->nullable()
                ->after('package_length_cm')
                ->comment('Package breadth in cm');

            $table->decimal('package_height_cm', 8, 2)
                ->nullable()
                ->after('package_breadth_cm')
                ->comment('Package height in cm');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'package_weight_kg',
                'package_length_cm',
                'package_breadth_cm',
                'package_height_cm',
            ]);
        });
    }
};
