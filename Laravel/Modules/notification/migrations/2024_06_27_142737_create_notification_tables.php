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
        Schema::create(
            'notification__notification',
            function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('type');
                $table->string('message');
                $table->jsonb('channel');
                $table->string('reserve_channels');
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification__notification');
    }
};
