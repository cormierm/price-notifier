<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntervalIdColumnToWatchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->unsignedInteger('interval_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->dropColumn('interval_id');
        });
    }
}
