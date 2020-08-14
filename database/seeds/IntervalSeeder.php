<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntervalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('intervals')->insert([
            'name' => 'None',
            'minutes' => null,
        ]);
        DB::table('intervals')->insert([
            'name' => '1 min',
            'minutes' => 1,
        ]);
        DB::table('intervals')->insert([
            'name' => '3 mins',
            'minutes' => 3,
        ]);
        DB::table('intervals')->insert([
            'name' => '5 mins',
            'minutes' => 5,
        ]);
        DB::table('intervals')->insert([
            'name' => '10 mins',
            'minutes' => 10,
        ]);
        DB::table('intervals')->insert([
            'name' => '15 mins',
            'minutes' => 15,
        ]);
        DB::table('intervals')->insert([
            'name' => '30 mins',
            'minutes' => 30,
        ]);
        DB::table('intervals')->insert([
            'name' => '1 hour',
            'minutes' => 60,
        ]);
        DB::table('intervals')->insert([
            'name' => '3 hour',
            'minutes' => 180,
        ]);
        DB::table('intervals')->insert([
            'name' => '6 hours',
            'minutes' => 320,
        ]);
        DB::table('intervals')->insert([
            'name' => '12 hours',
            'minutes' => 12 * 60,
        ]);
        DB::table('intervals')->insert([
            'name' => '1 day',
            'minutes' => 24 * 60,
        ]);
    }
}
