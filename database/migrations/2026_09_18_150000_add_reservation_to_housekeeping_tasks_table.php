<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->foreignId('reservation_id')->nullable()->after('room_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reservation_id');
        });
    }
};
