<?php

namespace App\Http\Controllers\Back;

use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use \App\Http\Controllers\Controller;

class FlightController extends Controller
{

    public function index()
    {
        $airports = Airport::all();
        $flights = Flight::query();
        if ($filter = request()->get('filter')) {
            $filter === 'future' ? $flights = $flights->future() : null;
            $filter === 'past' ? $flights = $flights->past() : null;
            $filter === 'fly' ? $flights = $flights->flying() : null;
        }
        request()->get('from') ? $flights = $flights->whereDepartureAirport(request()->get('from')) : null;
        request()->get('to') ? $flights = $flights->whereArrivalAirport(request()->get('to')) : null;

        $flights = $flights->with(['departureAirport', 'arrivalAirport'])->paginate(10);
        return Inertia::render('Flight/Index', [
            'flights' => $flights,
            'airports' => $airports
        ]);
    }

    public function create()
    {
        $airports = Airport::all();
        return Inertia::render('Flight/Create', [
            'airports' => $airports
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validateWithBag('createFlight', [
            'departureDate' => 'required',
            'departureAirport' => 'required',
            'arrivalDate' => 'required',
            'arrivalAirport' => 'required',
            'price' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
            'capacity' => 'required|numeric',
        ]);

        $flight = new Flight();
        $flight->arrival_airport = $validatedData['arrivalAirport'];
        $flight->departure_airport = $validatedData['departureAirport'];
        $flight->price = $validatedData['price'];
        $flight->capacity = $validatedData['capacity'];
        $flight->departure_time = Carbon::createFromFormat('Y-m-d H:i', $validatedData['departureDate'])->toDateTimeString();
        $flight->arrival_time = Carbon::createFromFormat('Y-m-d H:i', $validatedData['arrivalDate'])->toDateTimeString();
        $flight->sold_count = 0;
        $flight->save();

        return Redirect::route('flights.create');
    }
}
