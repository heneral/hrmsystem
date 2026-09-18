<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->string('guest_consent')->nullable()->after('task_type');
            $table->timestamp('guest_contacted_at')->nullable()->after('guest_consent');
        });
    }

    public function down(): void
    {
        Schema::table('housekeeping_tasks', function (Blueprint $table) {
            $table->dropColumn(['guest_consent', 'guest_contacted_at']);
        });
    }
};