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
        Schema::table('active_users', function (Blueprint $table) {
            $table->text('about_en')->nullable()->after('fullname_ru');
            $table->text('about_hy')->nullable()->after('about_en');
            $table->text('about_ge')->nullable()->after('about_hy');
            $table->text('about_ru')->nullable()->after('about_ge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('active_users', function (Blueprint $table) {
            $table->dropColumn(['about_en', 'about_hy', 'about_ge', 'about_ru']);
        });
    }
};

