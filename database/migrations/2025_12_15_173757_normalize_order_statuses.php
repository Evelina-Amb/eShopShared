<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('order')
            ->where('statusas', 'completed')
            ->update(['statusas' => 'paid']);
    }

    public function down(): void
    {
        DB::table('order')
            ->where('statusas', 'paid')
            ->update(['statusas' => 'completed']);
    }
};
