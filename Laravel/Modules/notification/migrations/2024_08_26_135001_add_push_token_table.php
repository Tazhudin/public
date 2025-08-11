<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification__push_token', function (Blueprint $table) {
            $table->string('token', 256)->primary();
            $table->string('user_id', 36);
            $table->string('device_id', 256)->nullable();
            $table->unique(['user_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification__push_token');
    }
};
