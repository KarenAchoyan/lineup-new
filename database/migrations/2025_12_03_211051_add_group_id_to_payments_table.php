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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('student_id')->constrained()->onDelete('cascade');
            $table->index(['group_id', 'status']);
            $table->index(['group_id', 'paid_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropIndex(['group_id', 'status']);
            $table->dropIndex(['group_id', 'paid_at']);
            $table->dropColumn('group_id');
        });
    }
};
