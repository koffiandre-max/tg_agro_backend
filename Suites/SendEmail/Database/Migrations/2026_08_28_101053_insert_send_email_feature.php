<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('features')->insertOrIgnore([
            'code'       => 'send_email',
            'label'      => 'SendEmail',
            'type'       => 'paid',
            'amount'     => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('features')->where('code', 'send_email')->delete();
    }
};
