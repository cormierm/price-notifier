<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockContainsColumnToWatchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->boolean('stock_contains')->default(1);
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->boolean('stock_contains')->default(1);
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
            $table->dropColumn('stock_contains');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('stock_contains');
        });
    }
}
