<?php

namespace App\Services;

use Exception;
use App\Models\Flight;
use App\Models\Airport;
use Illuminate\Support\Carbon;

class FlightSearchService
{
  protected $nodeIdList;
  protected $timeList;
  protected $flightList;
  protected $maxDepth = 4;
  protected array $graph;
  protected $visited, $path;
  protected $result = [];
  protected $passengerCount;


  public function __construct($fromDate, $endDate, $passengerCount)
  {
    $this->passengerCount = $passengerCount;

    $maxArrivalTime = Carbon::parse($fromDate)->setHour(0)->setMinutes(0)->setSeconds(0)->addHours(48);
    $flightList = Flight::where("departure_time", ">", $fromDate)
      ->where("departure_time", ">", Carbon::now()->toDateTimeString())
      ->where("departure_time", "<", $endDate)
      ->where("arrival_time", "<", $maxArrivalTime)
      ->whereRaw('capacity > (sold_count + ? - 1)', [$passengerCount])
      ->with(['departureAirport', 'arrivalAirport'])
      ->get();

    foreach ($flightList as $flight) {
      $this->flightList[$flight->id] = $flight;
    }

    $this->nodeIdList = Airport::all()->map(fn ($a) => $a->id);
    foreach ($flightList as $flight) {
      $this->timeList[$flight->id] = [
        "departure" => $flight->departure_time,
        "arrival" => $flight->arrival_time
      ];
    }
    $this->graph = [];
    foreach ($this->nodeIdList as $nodeId) {
      $this->graph[$nodeId] = [];
    }
    foreach ($flightList as $flight) {
      $this->addEdge($flight->departure_airport, $flight->arrival_airport, $flight->id);
    }
  }
  public function setMaxDepth($depth)
  {
    $this->maxDepth = $depth;
  }
  protected function addEdge($from, $to, $flightId)
  {
    array_push($this->graph[$from], ['to' => $to, 'flightId' => $flightId]);
  }
  protected function getAllPathsRecursively($from, $to, $flightId = null)
  {
    $this->visited[$from] = true;
    array_push($this->path, ['airport' => $from, 'flight' => $flightId !== null ? $this->flightList[$flightId] : null]);
    if ($from == $to) {
      if (count($this->path) > 1) {
        array_push($this->result, $this->path);
      }
    } else if (count($this->path) < $this->maxDepth) {
      foreach ($this->graph[$from] as $i) {
        if ($this->visited[$i['to']] == false) {
          $this->getAllPathsRecursively($i['to'], $to, $i['flightId']);
        }
      }
    }
    array_pop($this->path);
    $this->visited[$from] = false;
  }
  public function getAllPaths($from, $to)
  {
    $this->visited = [];
    foreach ($this->nodeIdList as $nodeId) {
      $this->visited[$nodeId] = false;
    }
    $this->path = [];
    $this->getAllPathsRecursively($from, $to);

    $tmp = [];
    foreach ($this->result as $flights) {
      $flag = true;
      for ($i = 1; $i < count($flights) - 1; $i++) {

        if (
          $flights[$i]['flight']->arrival_time > $flights[$i + 1]['flight']->departure_time ||
          Carbon::parse($flights[$i]['flight']->arrival_time)->diffInMinutes(Carbon::parse($flights[$i + 1]['flight']->departure_time)) < 60
        ) {
          $flag = false;
        }
      }
      if ($flag) {
        array_push($tmp, $flights);
      }
    }
    $connectedFlights = [];
    foreach ($tmp as $k) {
      $flights = [];
      $price = 0;
      $flightTime = Carbon::parse($k[count($k) - 1]['flight']->arrival_time)->diffInMinutes(Carbon::parse($k[1]['flight']->departure_time));
      foreach ($k as $j) {
        if (isset($j['flight'])) {
          $flights[] = $j['flight'];
          $price += $j['flight']->price;
        }
      }
      $price *= $this->passengerCount;
      array_push($connectedFlights, [
        'price' => $price,
        'time' => $flightTime,
        'flights' => $flights
      ]);
    }
    return $connectedFlights;
  }
}
