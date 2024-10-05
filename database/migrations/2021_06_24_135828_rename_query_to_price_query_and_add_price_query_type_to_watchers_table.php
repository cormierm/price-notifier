<?php

use App\Models\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameQueryToPriceQueryAndAddPriceQueryTypeToWatchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->renameColumn('query', 'price_query');
        });

        Schema::table('watchers', function (Blueprint $table) {
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
        Schema::table('watchers', function (Blueprint $table) {
            $table->renameColumn('price_query', 'query');
            $table->dropColumn('price_query_type');
        });
    }
}
