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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name_en');
            $table->string('name_hy');
            $table->string('name_ge');
            $table->string('name_ru');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('months')->default(12);
            $table->string('background_image')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_hy')->nullable();
            $table->text('description_ge')->nullable();
            $table->text('description_ru')->nullable();
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
        Schema::dropIfExists('courses');
    }
};
