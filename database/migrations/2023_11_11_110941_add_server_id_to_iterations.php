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
        Schema::table('iterations', function (Blueprint $table) {
            $table->timestamp('process_at')->nullable();
            $table->string('server_name')->nullable();
            $table->time('time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iterations', function (Blueprint $table) {
            $table->dropColumn(['process_at','server_name','time']);
        });
    }
};
