<template>
  <div class="bg-gray-100 min-h-screen">
    <header class="w-full py-6 bg-blue-700 px-8">
      <div class="max-w-6xl w-full mx-auto">
        <h1 class="font-semibold text-3xl text-white">Evolution Airline</h1>
      </div>
    </header>

    <main class="mt-8 px-8">
      <div class="mx-auto max-w-6xl w-full bg-white px-8 py-8 shadow-md">
        <h2 class="font-semibold text-2xl border-b border-cool-gray-300 pb-2">Search Flights</h2>
        <form @submit.prevent="searchFlights">
          <div class="flex mt-8 justify-between">
            <div class="flex flex-1 items-center mr-12">
              <label for="departure-date" class="mr-4">Date:</label>
              <flat-pickr class="border w-full border-gray-200 py-2 px-4 bg-gray-100 rounded-md" id="departure-date" v-model="searchFlightsForm.departureDate" :config="flatPickrConfig"></flat-pickr>
            </div>
            <div class="flex flex-1 items-center mr-12">
              <label for="from" class="mr-4"> From: </label>
              <Select class="w-full" id="from" name="from" @change="(event) => (searchFlightsForm.departureAirport = event.target.value)">
                <option value="" disabled :selected="searchFlightsForm.departureAirport == ''">Select Departure Airport</option>
                <option :key="airport.id" :selected="airport.id == searchFlightsForm.departureAirport" :value="airport.id" v-for="airport in airports">
                  {{ airport.full_name }}
                </option>
              </Select>
            </div>
            <div class="flex flex-1 items-center mr-12">
              <label for="to" class="mr-4"> To: </label>
              <Select class="w-full" id="to" name="to" @change="(event) => (searchFlightsForm.arrivalAirport = event.target.value)">
                <option value="" disabled :selected="searchFlightsForm.arrivalAirport == ''">Select Arrival Airport</option>
                <option :key="airport.id" :selected="airport.id == searchFlightsForm.arrivalAirport" :value="airport.id" v-for="airport in airports">
                  {{ airport.full_name }}
                </option>
              </Select>
            </div>
            <div class="flex flex-1 items-center">
              <label for="passengers-count" class="mr-4"> Passengers: </label>
              <div class="flex bg-gray-100 w-full justify-between rounded-md overflow-hidden">
                <button type="button" @click="(event) => (searchFlightsForm.passengerCount > 1 ? (searchFlightsForm.passengerCount -= 1) : '')" class="bg-gray-600 text-white text-2xl leading-8 h-8 w-6 mr-2 font-semibold flex-1">-</button>
                <p class="flex-1 flex items-center justify-center">{{ searchFlightsForm.passengerCount }}</p>
                <button type="button" @click="(event) => (searchFlightsForm.passengerCount += 1)" class="bg-gray-600 text-white text-2xl leading-8 h-8 w-6 ml-2 font-semibold flex-1">+</button>
              </div>
            </div>
          </div>
          <div class="text-right mt-4">
            <Button> Search </Button>
          </div>
        </form>
      </div>
      <div class="mt-8 mx-auto max-w-6xl w-full bg-white px-8 py-8 shadow-md" v-if="flights">
        <h2 class="font-semibold text-2xl border-b border-cool-gray-300 pb-2">Flight List</h2>
        <div class="mt-4">
          <table class="table-auto w-full">
            <thead>
              <tr>
                <th class="px-4 py-2">Route</th>
                <th class="px-4 py-2">Flight Time</th>
                <th class="px-4 py-2">Price</th>
                <th class="px-4 py-2">Type</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="flight in flights.direct" :key="flight.id">
                <td>{{ flight.departure_airport.full_name }} -> {{ flight.arrival_airport.full_name }}</td>
                <td>{{ flight.time }} minutes</td>
                <td>{{ flight.price }}</td>
                <td>Direct</td>
              </tr>
              <tr v-for="(connectedFlight, key) in flights.connected" :key="'c' + key">
                <td>
                  {{ getRouteForConnectedFlight(connectedFlight.flights) }}
                  <!-- <span v-for="flight in connectedFlight.flights" :key="flight.id"> {{ flight.departure_airport.full_name }} -> {{ flight.arrival_airport.full_name }} </span> -->
                  <!-- {{ flight.departure_airport.full_name }} -> {{ flight.arrival_airport.full_name }} -->
                </td>
                <td>{{ connectedFlight.time }} minutes</td>
                <td>{{ connectedFlight.price }}</td>
                <td>Connected</td>
              </tr>
            </tbody>
          </table>

          <!-- {{ flights }} -->
          <!-- <table class="table-auto w-full">
            <thead>
              <tr>
                <th class="px-4 py-2">From</th>
                <th class="px-4 py-2">To</th>
                <th class="px-4 py-2">Flight Time</th>
                <th class="px-4 py-2">Price</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(flight, key) in flights" :key="flight.id" :class="[key % 2 == 0 ? 'bg-gray-50' : '']">
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
              </tr>
            </tbody>
          </table> -->
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import Select from '@/Components/Select';
import Button from '@/Jetstream/Button';
import flatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/airbnb.css';
import FlightsTable from '@/Components/FlightsTable.vue';

export default {
  name: 'Home',
  props: ['airports', 'flights'],
  components: { Select, flatPickr, Button, FlightsTable },
  mounted() {
    const params = new URLSearchParams(window.location.search);
    if (params.has('departureAirport')) {
      this.searchFlightsForm.departureAirport = params.get('departureAirport');
    }
    if (params.has('arrivalAirport')) {
      this.searchFlightsForm.arrivalAirport = params.get('arrivalAirport');
    }
    if (params.has('departureDate')) {
      this.searchFlightsForm.departureDate = params.get('departureDate');
    }
    if (params.has('passengerCount')) {
      this.searchFlightsForm.passengerCount = parseInt(params.get('passengerCount'));
    }
    console.log(this.flights);
  },
  data() {
    return {
      searchFlightsForm: {
        departureDate: moment().add(1, 'days').format('Y-M-D'),
        departureAirport: '',
        arrivalAirport: '',
        passengerCount: 1,
      },
      flatPickrConfig: {
        enableTime: false,
        minDate: moment().format('Y-M-D'),
        dateFormat: 'Y-m-d',
      },
    };
  },
  methods: {
    getRouteForConnectedFlight(connectedFlights) {
      let routeQueue = [];
      const data = Object.values(connectedFlights);
      data.forEach((flight, key) => {
        routeQueue.push(flight.departure_airport.full_name);
        if (!data[key + 1]) {
          routeQueue.push(flight.arrival_airport.full_name);
        }
      });
      let str = '';
      routeQueue.forEach((item, key) => (str += item + (routeQueue[key + 1] ? ' -> ' : '')));
      return str;
      // {{ flight.departure_airport.full_name }} -> {{ flight.arrival_airport.full_name }}
    },
    async searchFlights() {
      await this.$inertia.get('/', {
        departureDate: this.searchFlightsForm.departureDate,
        departureAirport: this.searchFlightsForm.departureAirport,
        passengerCount: this.searchFlightsForm.passengerCount,
        arrivalAirport: this.searchFlightsForm.arrivalAirport,
      });
      // this.$refs.createFlightForm.reset();
    },
  },
};
</script>

<style>
</style>