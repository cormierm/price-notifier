<?php

use App\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameXpathValueToPriceQueryAndAddPriceQueryTypeToTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->renameColumn('xpath_value', 'price_query');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->string('price_query_type')->after('price_query')->default(Watcher::QUERY_TYPE_XPATH);
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
            $table->renameColumn('price_query', 'xpath_value');
            $table->dropColumn('price_query_type');
        });
    }
}
