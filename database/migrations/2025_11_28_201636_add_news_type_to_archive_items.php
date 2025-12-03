<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify enum to include 'news'
        DB::statement("ALTER TABLE archive_items MODIFY COLUMN type ENUM('image', 'video', 'news') DEFAULT 'image'");
        
        // Add description field for news items
        Schema::table('archive_items', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('title_ru');
            $table->text('description_hy')->nullable()->after('description_en');
            $table->text('description_ge')->nullable()->after('description_hy');
            $table->text('description_ru')->nullable()->after('description_ge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove description fields
        Schema::table('archive_items', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'description_hy', 'description_ge', 'description_ru']);
        });
        
        // Revert enum back to original
        DB::statement("ALTER TABLE archive_items MODIFY COLUMN type ENUM('image', 'video') DEFAULT 'image'");
    }
};
