<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockColumnsToWatchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->string('xpath_stock')->nullable();
            $table->string('stock_text')->nullable();
        });

        Schema::table('watchers', function (Blueprint $table) {
            $table->string('xpath_stock')->nullable();
            $table->string('stock_text')->nullable();
            $table->boolean('stock_alert')->default(false);
            $table->boolean('has_stock')->default(false);
        });

        Schema::table('watcher_logs', function (Blueprint $table) {
            $table->boolean('has_stock')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('xpath_stock');
            $table->dropColumn('stock_text');
        });

        Schema::table('watchers', function (Blueprint $table) {
            $table->dropColumn('xpath_stock');
            $table->dropColumn('stock_text');
            $table->dropColumn('stock_alert');
            $table->dropColumn('has_stock');
        });

        Schema::table('watcher_logs', function (Blueprint $table) {
            $table->dropColumn('has_stock');
        });
    }
}
