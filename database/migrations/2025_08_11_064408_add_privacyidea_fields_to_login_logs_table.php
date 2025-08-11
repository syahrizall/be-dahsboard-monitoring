<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->string('realm')->nullable()->after('ip_address');
            $table->string('resolver')->nullable()->after('realm');
            $table->string('token_type')->nullable()->after('resolver');
            $table->string('serial')->nullable()->after('token_type');
            $table->string('action')->nullable()->after('serial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropColumn(['realm', 'resolver', 'token_type', 'serial', 'action']);
        });
    }
};
