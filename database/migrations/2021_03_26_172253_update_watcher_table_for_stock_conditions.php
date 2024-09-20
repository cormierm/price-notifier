<?php

use App\Models\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWatcherTableForStockConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->string('stock_condition')->nullable()->default(Watcher::STOCK_CONDITION_CONTAINS_TEXT);
        });
        Schema::table('templates', function (Blueprint $table) {
            $table->string('stock_condition')->nullable()->default(Watcher::STOCK_CONDITION_CONTAINS_TEXT);
        });

        Watcher::query()->where('stock_contains', 1)->update(['stock_condition' => Watcher::STOCK_CONDITION_CONTAINS_TEXT]);
        Watcher::query()->where('stock_contains', 0)->update(['stock_condition'=> Watcher::STOCK_CONDITION_MISSING_TEXT]);

        Schema::table('watchers', function (Blueprint $table) {
            $table->dropColumn('stock_contains');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('stock_contains');
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
            $table->string('stock_contains')->default(1);
            $table->dropColumn('stock_condition');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->string('stock_contains')->default(1);
            $table->dropColumn('stock_condition');
        });
    }
}
