<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_hy')->nullable();
            $table->string('title_ge')->nullable();
            $table->string('title_ru')->nullable();
            $table->text('content_en')->nullable();
            $table->text('content_hy')->nullable();
            $table->text('content_ge')->nullable();
            $table->text('content_ru')->nullable();
            $table->string('avatar')->nullable();
            $table->string('video')->nullable(); // YouTube link
            $table->string('slug')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
};
