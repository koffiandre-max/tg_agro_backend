<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('features')->insertOrIgnore([
            'code'       => 'payment',
            'label'      => 'Payment',
            'type'       => 'paid',
            'amount'     => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('features')->where('code', 'payment')->delete();
    }
};
