<?php

namespace App\Http\Controllers\Front\Api;

use Exception;
use App\Models\User;
use App\Models\Flight;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Notifications\NotifyPurchaserOnPurchase;
use Illuminate\Support\Facades\Notification;

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
        $paymentMethod = request()->get('paymentMethod');
        $validatedData = request()->validate([
            "name" => "required|string|min:2|max:50",
            'email' => "required|email",
            "passengerCount" => "required|numeric",
            'flightIds' => "required",
            "flightIds.*" => "required|numeric",
        ]);
        $name = $validatedData['name'];
        $passengerCount = $validatedData['passengerCount'];
        $email = $validatedData['email'];
        $flightIds = $validatedData['flightIds'];
        $implodedFlightIds = implode("','", $flightIds);

        $flights = Flight::with(['departureAirport', 'arrivalAirport'])
            ->whereIn('id', $flightIds)
            ->orderByRaw(DB::raw("FIELD(id, '$implodedFlightIds')"))
            ->get();

        $totalPrice = $flights->reduce(fn ($carry, $item) => $carry + $item->price) * 100 * $passengerCount;

        try {
            (new User)->charge($totalPrice, $paymentMethod['id']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
        Flight::whereIn('id', $flightIds)->increment('sold_count', $passengerCount);
        Notification::route('mail', $email)->notify(new NotifyPurchaserOnPurchase(['name' => $name, 'email' => $email, 'flights' => $flights, 'passengerCount' => $passengerCount]));
        return response()->json(['status' => 'ok', 'message' => "success"]);
    }
}
