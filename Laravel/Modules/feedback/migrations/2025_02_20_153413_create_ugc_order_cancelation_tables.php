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
        Schema::create('feedback_order_cancelation_variants', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('reason');
            $table->timestamps();
        });

        Schema::create('feedback_order_cancelations', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('customer_id', 36);
            $table->string('order_id', 36);
            $table->string('order_status', 16);
            $table->string('reason');
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_order_cancelations');
        Schema::dropIfExists('feedback_order_cancelation_variants');
    }
};
