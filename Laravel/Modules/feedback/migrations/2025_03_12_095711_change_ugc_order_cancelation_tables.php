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
        Schema::table('feedback_order_cancelations', function (Blueprint $table): void {
            $table->string('reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_order_cancelations', function (Blueprint $table): void {
            $table->string('reason')->change();
        });
    }
};
