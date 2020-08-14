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
            $table->unsignedInteger('watcher_id');
            $table->string('formatted_value')->nullable();
            $table->string('raw_value')->nullable();
            $table->unsignedInteger('duration');
            $table->text('error')->nullable();
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
