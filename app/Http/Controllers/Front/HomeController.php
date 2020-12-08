<?php

namespace App\Http\Controllers\Front;

use App\Helpers\ConnectedFlightDFS;
use App\Helpers\Graph;
use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Services\ConnectedFlightService;
use PDO;

class HomeController extends Controller
{
    public function index()
    {
        $airports = Airport::all();
        $data = ['airports' => $airports, 'flights' => []];
        if (request()->get('departureDate') && request()->get('departureAirport') && request()->get('arrivalAirport') && request()->get('passengerCount')) {
            $data['flights'] = $this->findFlights(request('departureAirport'), request('arrivalAirport'), request('departureDate'), request('passengerCount'));
        }
        return Inertia::render("Home/Index", $data);
    }
    private function findFlights($from, $to, $fromDate, $passengerCount)
    {
        $startDate = Carbon::createFromFormat("Y-m-d", $fromDate)->setHour(0)->setMinutes(0)->setSeconds(0);
        $endDate = clone ($startDate);
        $endDate->addDays(1);
        $c = new ConnectedFlightService($startDate, $endDate, $passengerCount);
        $flights =  $c->getAllPaths($from, $to);
        return $flights;
    }
}
