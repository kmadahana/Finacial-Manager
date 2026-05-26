<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('opening_balance', 14, 2)->default(0)->after('pay_cycle_day');
            $table->date('opening_balance_at')->nullable()->after('opening_balance');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['opening_balance', 'opening_balance_at']);
        });
    }
};
