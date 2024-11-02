<?php

use App\Models\Watcher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->string('alert_condition')->default(Watcher::ALERT_CONDITION_LESS_THAN)->after('alert_value');
        });
    }

    public function down(): void
    {
        Schema::table('watchers', function (Blueprint $table) {
            $table->dropColumn('alert_condition');
        });
    }
};
