<template>
  <table class="table-auto w-full">
    <thead class="block lg:hidden">
      <tr class="block">
        <th class="px-4 py-2 text-center block text-xl">Flights</th>
      </tr>
    </thead>
    <thead class="hidden lg:table-header-group">
      <tr>
        <th class="px-4 py-2">From</th>
        <th class="px-4 py-2">To</th>
        <th class="px-4 py-2">Flight Time</th>
        <th class="px-4 py-2">Price</th>
        <th class="px-4 py-2">Capacity</th>
      </tr>
    </thead>
    <tbody class="block lg:hidden w-full">
      <tr v-for="(flight, key) in flights.data" :key="flight.id" class="w-full min-w-full block" :class="[key % 2 == 0 ? 'bg-gray-50' : '']">
        <td class="border px-4 py-2 text-center w-full min-w-full block">
          {{ flight.departure_airport.full_name }}
          <span class="text-gray-400"> ({{ flight.departure_airport.code_name }}) </span>
          <br />
          <span>
            {{ flight.departure_time }}
          </span>
          {{ flight.arrival_airport.full_name }}
          <span class="text-gray-400"> ({{ flight.arrival_airport.code_name }}) </span>
          <br />
          <span>
            {{ flight.arrival_time }}
          </span>
          {{ Math.floor((new Date(flight.arrival_time) - new Date(flight.departure_time)) / 3.6e6) }}
          hours
          {{ Math.floor((new Date(flight.arrival_time) - new Date(flight.departure_time)) % 36e5) / 6e4 }}
          minutes
          {{ flight.price }}
          {{ flight.sold_count }} / {{ flight.capacity }}
        </td>
      </tr>
    </tbody>
    <tbody class="hidden lg:table-row-group">
      <tr v-for="(flight, key) in flights.data" :key="flight.id" :class="[key % 2 == 0 ? 'bg-gray-50' : '']">
        <td class="border px-4 py-2 text-center">
          {{ flight.departure_airport.full_name }}
          <span class="text-gray-400"> ({{ flight.departure_airport.code_name }}) </span>
          <br />
          <span>
            {{ flight.departure_time }}
          </span>
        </td>
        <td class="border px-4 py-2 text-center">
          {{ flight.arrival_airport.full_name }}
          <span class="text-gray-400"> ({{ flight.arrival_airport.code_name }}) </span>
          <br />
          <span>
            {{ flight.arrival_time }}
          </span>
        </td>
        <td class="border px-4 py-2 text-center">
          {{ Math.floor((new Date(flight.arrival_time) - new Date(flight.departure_time)) / 3.6e6) }}
          hours
          {{ Math.floor((new Date(flight.arrival_time) - new Date(flight.departure_time)) % 36e5) / 6e4 }}
          minutes
        </td>
        <td class="border px-4 py-2 text-center">
          {{ flight.price }}
        </td>
        <td class="border px-4 py-2 text-center">{{ flight.sold_count }} / {{ flight.capacity }}</td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  name: 'FlightsTable',
  props: ['flights'],
};
</script>

<style>
</style>