<?php

namespace App\Helpers;
/* 
  This graph class is created for finding connected flights.
  This is not using anymore but it can be useful on next updates.
*/

class Graph
{
  protected $nodeIdList;
  protected array $graph;
  protected $visited, $path;
  protected $result = [];
  public function __construct($nodeIdList)
  {
    $this->nodeIdList = $nodeIdList;
    $this->graph = [];
    foreach ($this->nodeIdList as $nodeId) {
      $this->graph[$nodeId] = [];
    }
  }
  public function addEdge($from, $to)
  {
    array_push($this->graph[$from], $to);
  }
  protected function getAllPathsRecursively($from, $to)
  {
    $this->visited[$from] = true;
    array_push($this->path, $from);
    if ($from === $to) {
      array_push($this->result, $this->path);
    } else if (count($this->path) < 3) {
      foreach ($this->graph[$from] as $i) {
        if ($this->visited[$i] == false) {
          $this->getAllPathsRecursively($i, $to);
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
    return $this->result;
  }
}
