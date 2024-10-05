<?php

use App\Models\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameXpathStockToStockQueryAndAddStockQueryTypeToTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->renameColumn('xpath_stock', 'stock_query');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->string('stock_query_type')->after('stock_query')->default(Watcher::QUERY_TYPE_XPATH);
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
            $table->renameColumn('stock_query', 'xpath_stock');
            $table->dropColumn('stock_query_type');
        });
    }
}
