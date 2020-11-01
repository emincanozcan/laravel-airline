<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Flight;
use App\Models\Airport;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Flight::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $airport1 = Airport::inRandomOrder()->first();
        $airport2 = Airport::inRandomOrder()->first();
        if ($airport1->id === $airport2->id) {
            while ($airport1->id !== $airport2->id) {
                $airport2 = Airport::inRandomOrder()->first();
            }
        }
        $date1 = Carbon::now()->addHours(rand(4, 24))->toDateTimeString();
        $date2 = Carbon::parse($date1)->addHours(rand(4, 36))->toDateTimeString();
        return [
            'capacity' => rand(120, 240),
            'departure_airport' => $airport1->id,
            'arrival_airport' => $airport2->id,
            'departure_time' => $date1,
            'arrival_time' => $date2,
        ];
    }
}
