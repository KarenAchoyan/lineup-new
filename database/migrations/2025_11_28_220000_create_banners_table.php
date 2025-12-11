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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('about_dancing_en')->nullable();
            $table->string('dancing_en')->nullable();
            $table->string('about_dancing_hy')->nullable();
            $table->string('dancing_hy')->nullable();
            $table->string('about_dancing_ge')->nullable();
            $table->string('dancing_ge')->nullable();
            $table->string('about_dancing_ru')->nullable();
            $table->string('dancing_ru')->nullable();
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
        Schema::dropIfExists('banners');
    }
};











