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
        Schema::create('archive_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('image')->nullable(); // For image type
            $table->string('youtube_link')->nullable(); // For video type
            $table->string('link')->nullable(); // Optional link for image type
            $table->string('title_en')->nullable();
            $table->string('title_hy')->nullable();
            $table->string('title_ge')->nullable();
            $table->string('title_ru')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('archive_items');
    }
};
