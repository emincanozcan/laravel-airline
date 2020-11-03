<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FlightController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flights = Flight::query();
        $airports = Airport::all();
        if ($filter = request()->get('filter')) {
            if ($filter === 'future') {
                $flights = $flights->where('departure_time', '>', Carbon::now()->toDateTimeString());
            }
            if ($filter === 'past') {
                $flights = $flights->where('arrival_time', '<', Carbon::now()->toDateTimeString());
            }
            if ($filter === 'fly') {
                $flights = $flights
                    ->where('departure_time', '<', Carbon::now()->toDateTimeString())
                    ->where('arrival_time', '>', Carbon::now()->toDateTimeString());
            }
        }
        if ($from = request()->get('from')) {
            $flights = $flights->where('departure_airport', $from);
        }
        if ($to = request()->get('to')) {
            $flights = $flights->where('arrival_airport', $to);
        }
        $flights = $flights->with(['departureAirport', 'arrivalAirport'])->paginate(10);
        return Inertia::render('Flight/Index', [
            'flights' => $flights,
            'airports' => $airports
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
