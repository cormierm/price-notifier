<?php

use App\Utils\HtmlFetcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientColumnToWatchersAndTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->string('client')->default(HtmlFetcher::CLIENT_BROWERSHOT);
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->string('client')->default(HtmlFetcher::CLIENT_BROWERSHOT);
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
            $table->dropColumn('client');
        });

        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn('client');
        });
    }
}
