<?php

use App\Models\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameQueryToStockQueryAndAddStockQueryTypeToWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->renameColumn('xpath_stock', 'stock_query');
        });

        Schema::table('watchers', function (Blueprint $table) {
            $table->string('stock_query_type')->after('stock_query')->default(Watcher::QUERY_TYPE_XPATH)->nullable();
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
            $table->renameColumn('stock_query', 'query');
            $table->dropColumn('stock_query_type');
        });
    }
}
