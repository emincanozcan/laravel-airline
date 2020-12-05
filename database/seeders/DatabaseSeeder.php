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
        \App\Models\Airport::factory(50)->create();
        \App\Models\Flight::factory(30000)->create();
    }
}
