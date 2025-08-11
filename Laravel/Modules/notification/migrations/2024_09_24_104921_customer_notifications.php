<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('notification__notification');
        Schema::create('notification__notification', function (Blueprint $table): void {
            $table->id();
            $table->string('customer_id', 36);
            $table->timestamp('created_at')->useCurrent();
            $table->string('type', 16);
            $table->text('message');
            $table->string('status', 16);
            $table->json('provider_response')->nullable();
            $table->json('payload')->nullable();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification__notification');
    }
};
