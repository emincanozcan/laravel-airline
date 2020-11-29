<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Airport::factory(80)->create();
        \App\Models\Flight::factory(20000)->create();
    }
}
