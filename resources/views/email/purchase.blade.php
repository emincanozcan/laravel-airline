@component('mail::message')
# Hello, {{ $name }}

Flight details are below

@component('mail::table')
<table>
  <thead>
    <tr>
      <th>Departure</th>
      <th>Arrival</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($flights as $flight)
        <tr>
          <td><strong>{{ $flight->departureAirport->full_name}}</strong> <br> {{ $flight->departure_time }}</td>
          <td><strong>{{ $flight->arrivalAirport->full_name}}</strong> <br> {{ $flight->arrival_time }}</td>
        </tr>
    @endforeach
  </tbody>
</table>
@endcomponent
<br>
<strong>Passenger Count: {{ $passengerCount }}</strong>
<br>
<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
