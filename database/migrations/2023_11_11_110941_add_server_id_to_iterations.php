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
            $table->unsignedBigInteger('server_id');
            $table->foreign('server_id')->references('id')->on('servers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iterations', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Server::class);
            $table->dropColumn('server_id');
        });
    }
};
