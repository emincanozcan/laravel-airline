<?php

namespace App\Helpers;

use App\Models\Airport;

class ConnectedFlightDFS
{
  protected $nodeIdList;
  protected $timeList;
  protected $flightList;
  protected $maxDepth = 4;
  protected array $graph;
  protected $visited, $path;
  protected $result = [];
  public function __construct($flightList)
  {
    $this->flightList = [];
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
      array_push($this->result, $this->path);
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

    $finalPaths = [];
    foreach ($this->result as $flights) {
      $flag = true;
      for ($i = 1; $i < count($flights) - 1; $i++) {
        if ($flights[$i]['flight']->arrival_time > $flights[$i + 1]['flight']->departure_time) {
          $flag = false;
        }
      }
      if ($flag) {
        array_push($finalPaths, $flights);
      }
    }
    return $finalPaths;
  }
}
