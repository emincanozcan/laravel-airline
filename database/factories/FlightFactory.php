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
        $date1 = Carbon::now()->addDays(rand(-5, 5))->addHours(rand(4, 24))->addMinutes(rand(0, 60))->second(0)->toDateTimeString();
        $date2 = Carbon::parse($date1)->addHours(rand(4, 36))->addMinutes(rand(0, 60))->toDateTimeString();
        $capacity = rand(120, 240);
        return [
            'capacity' => $capacity,
            'sold_count' => rand(0, $capacity),
            'departure_airport' => $airport1->id,
            'arrival_airport' => $airport2->id,
            'departure_time' => $date1,
            'arrival_time' => $date2,
            'price' => rand(10000, 99999) / 100
        ];
    }
}
