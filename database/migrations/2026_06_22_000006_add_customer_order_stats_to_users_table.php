<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('total_spent')
                ->default(0)
                ->after('admin_permissions')
                ->comment('Lifetime spend in paise');

            $table->unsignedInteger('total_orders')
                ->default(0)
                ->after('total_spent');
        });

        $stats = DB::table('orders')
            ->select('user_id')
            ->selectRaw('COUNT(*) as order_count')
            ->selectRaw('COALESCE(SUM(amount_paise), 0) as spent_paise')
            ->where('payment_status', 2)
            ->groupBy('user_id')
            ->get();

        foreach ($stats as $row) {
            DB::table('users')
                ->where('id', $row->user_id)
                ->update([
                    'total_orders' => (int) $row->order_count,
                    'total_spent' => (int) $row->spent_paise,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_spent', 'total_orders']);
        });
    }
};
