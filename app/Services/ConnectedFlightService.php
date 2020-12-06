<?php

namespace App\Services;

use App\Models\Flight;
use Illuminate\Support\Carbon;

class ConnectedFlightService
{

  public static function find($from, $to, $startDate, $endDate, $passengerCount)
  {
    $connectedFlights = [];
    $startPointFlights = Flight::from($from, $startDate, $endDate, $passengerCount)->with(['departureAirport', 'arrivalAirport'])->get();

    $endPointFlights = Flight::query();
    $endPointFlights->with(['departureAirport', 'arrivalAirport']);

    $startPointFlights->each(function ($flight) use ($endPointFlights, $to, $passengerCount) {
      $maxDepTime = (Carbon::parse($flight->arrival_time))->addHours(12);
      $minDepTime = (Carbon::parse($flight->arrival_time))->addHours(1);
      $endPointFlights->orWhere(function ($q) use ($flight, $to, $passengerCount, $maxDepTime, $minDepTime) {
        $q->direct($flight->arrival_airport, $to, $minDepTime, $maxDepTime, $passengerCount);
      });
    });

    $endPointFlights = $endPointFlights->get();

    $startPointFlights->each(function ($startFlight) use ($endPointFlights, &$connectedFlights, $passengerCount) {
      $endPointFlights->each(function ($endFlight) use ($startFlight, &$connectedFlights, $passengerCount) {
        if ($startFlight->arrival_airport == $endFlight->departure_airport) {
          $price = ($startFlight->price + $endFlight->price) * $passengerCount;
          $flightTime = Carbon::parse($endFlight->arrival_time)->diffInMinutes(Carbon::parse($startFlight->departure_time));
          $connectedFlights[] = [
            'price' => $price,
            'time' => $flightTime,
            'flights' => [$startFlight, $endFlight]
          ];
        }
      });
    });
    return $connectedFlights;
  }
}
