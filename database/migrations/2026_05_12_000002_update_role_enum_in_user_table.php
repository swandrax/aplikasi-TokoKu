<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE `user` MODIFY `role` ENUM('0', '1', '2') NOT NULL DEFAULT '2'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('user')->where('role', '2')->update(['role' => '0']);
        DB::statement("ALTER TABLE `user` MODIFY `role` ENUM('0', '1') NOT NULL DEFAULT '0'");
    }
};
