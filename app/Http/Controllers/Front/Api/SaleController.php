<?php

namespace App\Http\Controllers\Front\Api;

use Exception;
use App\Models\User;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{
    public function getPurchaseInformationByFlightIds()
    {
        $flightIds = request()->get('flightIds');
        $implodedFlightIds = implode("','", $flightIds);
        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->whereIn('id', $flightIds)
            ->orderByRaw(DB::raw("FIELD(id, '$implodedFlightIds')"))
            ->get();

        $totalPrice = $flights->reduce(fn ($carry, $item) => $carry + $item->price);

        $route = [];
        $flights->each(function ($flight) use (&$route) {
            array_push($route, $flight->departureAirport);
        });
        array_push($route, $flights->last()->arrivalAirport);

        return response()->json([
            'flights' => $flights,
            'totalPrice' => $totalPrice,
            'route' => $route
        ]);
    }
    public function purchase()
    {
        /* #todo send email  */
        $paymentMethod = request()->get('paymentMethod');
        $name = request()->get('name');
        $email = request()->get('email');
        $flightIds = request()->get('flightIds');
        $flights =  Flight::findMany($flightIds);

        $totalPrice = $flights->reduce(fn ($carry, $item) => $carry + $item->price) * 100;

        try {
            $stripeCharge = (new User)->charge($totalPrice, $paymentMethod['id']);
        } catch (Exception $e) {
            return $e;
        }
        return response()->json(['charge' => $stripeCharge]);
    }
}
