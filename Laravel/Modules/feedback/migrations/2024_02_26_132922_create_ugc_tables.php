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
        Schema::create('feedback_order_evaluation_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('comment');
            $table->json('evaluations');
            $table->timestamps();
        });

        Schema::create('feedback_order_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('evaluation');
            $table->string('comment')->nullable();
            $table->json('comments')->nullable();
            $table->json('images')->nullable();
            $table->string('order_id', 36);
            $table->string('user_id', 36);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_order_evaluations');
        Schema::dropIfExists('feedback_order_evaluation_variants');
    }
};
