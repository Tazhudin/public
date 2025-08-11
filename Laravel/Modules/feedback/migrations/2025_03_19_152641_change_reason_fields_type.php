<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE feedback_order_cancelations ALTER COLUMN
            reason TYPE json USING (CASE WHEN reason IS NULL THEN '[]' ELSE CONCAT('[\"', reason, '\"]') END)::json");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_order_cancelations', function (Blueprint $table): void {
            $table->string('reason')->nullable()->change();
        });
    }
};
