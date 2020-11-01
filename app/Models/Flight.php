<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function departureAirport()
    {
        return $this->belongsTo('App\Models\Airport', 'departure_airport', 'id');
    }
    public function arrivalAirport()
    {
        return $this->belongsTo('App\Models\Airport', 'arrival_airport', 'id');
    }
}
