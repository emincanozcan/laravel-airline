<?php

namespace App\Http\Controllers;

use App\Helpers\Graph;
use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $airports = Airport::all();
        $data = ['airports' => $airports, 'flights' => []];
        if (request()->get('departureDate') && request()->get('departureAirport') && request()->get('arrivalAirport') && request()->get('passengerCount')) {
            $startDate = Carbon::createFromFormat("Y-m-d", request()->get('departureDate'))->setHour(0)->setMinutes(0)->setSeconds(0);
            $endDate = clone ($startDate);
            $endDate->addDays(1);

            $directFlights = Flight::where("departure_time", "<", $endDate->toDateTimeString())
                ->where("departure_time", ">", $startDate->toDateTimeString())
                ->where('departure_airport', request()->get('departureAirport'))
                ->where('arrival_airport', request()->get('arrivalAirport'))
                ->whereRaw('capacity > (sold_count + ? - 1)', [request()->get('passengerCount')])
                ->with(['departureAirport', 'arrivalAirport'])
                ->get();
            foreach ($directFlights as $k => $v) {
                $flightTime = Carbon::parse($v->arrival_time)->diffInMinutes(Carbon::parse($v->departure_time));
                $directFlights[$k]['time'] = $flightTime;
            }


            $connectedFlights = [];
            $startPointFlights = Flight::with(['departureAirport', 'arrivalAirport'])
                ->where("departure_time", "<", $endDate->toDateTimeString())
                ->where("departure_time", ">", $startDate->toDateTimeString())
                ->where("departure_time", ">", Carbon::now()->toDateTimeString())
                ->where('departure_airport', request()->get('departureAirport'))
                ->get();

            $endPointFlights = Flight::query();
            $endPointFlights->with(['departureAirport', 'arrivalAirport']);

            $startPointFlights->each(function ($flight) use ($endPointFlights) {
                $maxDepTime = (Carbon::parse($flight->arrival_time))->addHours(12);
                $minDepTime = (Carbon::parse($flight->arrival_time))->addHours(1);

                $endPointFlights->orWhere(function ($q) use ($flight, $maxDepTime, $minDepTime) {
                    $q->where('departure_airport', $flight->arrival_airport)
                        ->where('arrival_airport', request()->get('arrivalAirport'))
                        ->where('departure_time', '>', $minDepTime->toDateTimeString())
                        ->where('departure_time', '<', $maxDepTime->toDateTimeString());
                });
            });

            $endPointFlights = $endPointFlights->get();

            $startPointFlights->each(function ($startFlight) use ($endPointFlights, &$connectedFlights) {
                $endPointFlights->each(function ($endFlight) use ($startFlight, &$connectedFlights) {
                    if ($startFlight->arrival_airport == $endFlight->departure_airport) {
                        $price = $startFlight->price + $endFlight->price;
                        $flightTime = Carbon::parse($endFlight->arrival_time)->diffInMinutes(Carbon::parse($startFlight->departure_time));
                        $connectedFlights[] = [
                            'price' => $price,
                            'time' => $flightTime,
                            'flights' => [$startFlight, $endFlight]
                        ];
                    }
                });
            });

            $data['flights'] = ['direct' => $directFlights, 'connected' => $connectedFlights];
            // dd($data);
            // dd($connectedFlights);
            // dd($endPointFlights->getQuery());

            // $endPointFlights->where("departure_time", "<", $endDate->toDateTimeString())
            // ->where("departure_time", ">", $startDate->toDateTimeString());




            // ->where('arrival_airport', request()->get('arrivalAirport'))
            // ->get();

            // $startPointFlights->each(function($sFlight){
            //     $endPointFlights->each(function($eFlight){
            //         if(
            //             $sFlight->arrival_airport === $eFlight->departure_airport &&
            //             $sFlight->arrival_time < $eFlight->departure_time
            //         ){

            //         }
            //     })
            // })

            // dd();





            // $airportIdList = [];
            // foreach ($airports as $airport) {
            //     array_push($airportIdList, $airport->id);
            // }

            // $graph = new Graph($airportIdList);
            // foreach ($flights as $flight) {
            //     $graph->addEdge($flight->departure_airport, $flight->arrival_airport);
            // }

            // $a = $graph->getAllPaths((int) request()->get('departureAirport'), (int) request()->get('arrivalAirport'));
            // $a = array_intersect_key($a, array_unique(array_map('serialize', $a)));

            // $flightData = [];


            // // tmp variable is reducing loop count and creates big performance difference.
            // $tmp = [];
            // foreach ($a as $k => $v) {
            //     for ($i = 0; $i < count($v) - 1; $i++) {
            //         $key = $v[$i] . '-' . $v[$i + 1];
            //         if (!isset($flightData[$k][$key])) {
            //             $flightData[$k][$key] = [];
            //         }
            //         if (isset($tmp[$key])) {
            //             $f = $tmp[$key];
            //         } else {
            //             $f = $flights->filter(function ($flight) use ($v, $i) {
            //                 return $flight->departure_airport === $v[$i] && $flight->arrival_airport === $v[$i + 1];
            //             });
            //             foreach ($f as $k1 => $v1) {
            //                 $departureDate = Carbon::parse($v1->departure_time);
            //                 $arrivalDate = Carbon::parse($v1->arrival_time);
            //                 $diff = $arrivalDate->diff($departureDate);
            //                 $f[$k1]->flight_time = $diff;
            //             }
            //             $tmp[$key] = $f;
            //         }
            //         array_push($flightData[$k][$key], $f);
            //     }
            // }
            // $data['flights'] = $flightData;

            // uasort($connectedFlights, function ($a, $b) {
            //     $time1 = Carbon::now();
            //     $time2 = Carbon::now();
            //     foreach ($a as $flights) {
            //         foreach ($flights as $flights2) {
            //             foreach ($flights2 as $flight) {
            //                 $time1->add($flight->flight_time);
            //             }
            //         }
            //     }
            //     foreach ($b as $flights) {
            //         foreach ($flights as $flights2) {
            //             foreach ($flights2 as $flight) {
            //                 $time2->add($flight->flight_time);
            //             }
            //         }
            //     }
            //     return ($time1 == $time2) ? 0 : (($time1 < $time2) ? -1 : 1);
            // });
            // dd($connectedFlights);
        }
        return Inertia::render("Home/Index", $data);
    }
}
