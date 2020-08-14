<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWatcherLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('watcher_logs', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('raw_value');
            $table->unsignedInteger('status_code');
            $table->unsignedInteger('duration');
            $table->text('error');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('watcher_logs');
    }
}
