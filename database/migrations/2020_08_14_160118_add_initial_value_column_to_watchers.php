<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInitialValueColumnToWatchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->string('initial_value')->after('value')->nullable();
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
            $table->dropColumn('initial_value');
        });
    }
}
