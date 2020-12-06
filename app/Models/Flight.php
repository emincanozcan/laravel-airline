<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $appends = ['time'];
    public function departureAirport()
    {
        return $this->belongsTo('App\Models\Airport', 'departure_airport', 'id');
    }
    public function arrivalAirport()
    {
        return $this->belongsTo('App\Models\Airport', 'arrival_airport', 'id');
    }
    public function getTimeAttribute()
    {
        return Carbon::parse($this->arrival_time)->diffInMinutes(Carbon::parse($this->departure_time));
    }
    public function scopeFuture($query)
    {
        return $query->where('departure_time', '>', Carbon::now()->toDateTimeString());
    }
    public function scopePast($query)
    {
        return $query->where('arrival_time', '<', Carbon::now()->toDateTimeString());
    }
    public function scopeFlying($query)
    {
        return $query->where('departure_time', '<', Carbon::now()->toDateTimeString())
            ->where('arrival_time', '>', Carbon::now()->toDateTimeString());
    }
    public function scopeDirect($query, $from, $to, $fromMinDate, $fromMaxDate, $passengerCount)
    {
        return $query->where("departure_time", "<", $fromMaxDate)
            ->where("departure_time", ">", $fromMinDate)
            ->where("departure_time", ">", Carbon::now()->toDateTimeString())
            ->where('departure_airport', $from)
            ->where('arrival_airport', $to)
            ->whereRaw('capacity > (sold_count + ? - 1)', [$passengerCount]);
    }
    public function scopeFrom($query, $from, $fromMinDate, $fromMaxDate, $passengerCount)
    {
        return $query->where("departure_time", "<", $fromMaxDate)
            ->where("departure_time", ">", $fromMinDate)
            ->where("departure_time", ">", Carbon::now()->toDateTimeString())
            ->whereRaw('capacity > (sold_count + ? - 1)', [$passengerCount])
            ->where('departure_airport', $from);
    }
}
