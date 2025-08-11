<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('feedback_wishes_products', function (Blueprint $table) {
            $table->id();
            $table->text('source');
            $table->text('store');
            $table->text('comment');
            $table->string('phone_number')->nullable()->default('-');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback_wishes_products');
    }
};
