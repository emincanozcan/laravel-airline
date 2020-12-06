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
                <option value="" disabled :selected="searchFlightsForm.departureAirport == ''">Departure Airport</option>
                <option :key="airport.id" :selected="airport.id == searchFlightsForm.departureAirport" :value="airport.id" v-for="airport in airports">
                  {{ airport.full_name }}
                </option>
              </Select>
            </div>
            <div class="flex flex-1 items-center mr-12">
              <label for="to" class="mr-4"> To: </label>
              <Select class="w-full" id="to" name="to" @change="(event) => (searchFlightsForm.arrivalAirport = event.target.value)">
                <option value="" disabled :selected="searchFlightsForm.arrivalAirport == ''">Arrival Airport</option>
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
          <div class="flex py-3 px-8 bg-gray-50 cursor-pointer border-b border-gray-200" :key="key" v-for="(flight, key) in flightList">
            <div class="flex items-center" style="flex: 2">
              <div class="flex flex-col justify-start items-start w-24">
                <span class="text-sm text-gray-700 font-medium">{{ flight.from.airport.code_name }}</span>
                <span class="text-2xl text-gray-900 font-bold tracking-tighter">{{ flight.from.time }}</span>
              </div>
              <div class="mx-auto">
                <div v-if="flight.via !== false">
                  <p class="text-sm text-gray-500 text-center">Via {{ flight.via.airport.full_name }}</p>
                  <svg class="w-20 my-2 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 12" id="icon-arrow-way-stop">
                    <path
                      fill="#C8CACC"
                      fill-rule="evenodd"
                      d="M39.952 6.623a4.001 4.001 0 11-.037-1.448h29.172l-3.89-3.768a.796.796 0 010-1.163.863.863 0 011.202 0l5.35 5.17.036.04.031.038c.04.052.063.083.08.113l.028.06a.42.42 0 01.04.105l.023.087.009.074.004.047v.04l-.004.051-.01.074-.017.074a.776.776 0 01-.045.122l-.027.056c-.018.03-.04.06-.063.096l-.04.043-.045.052-5.35 5.17a.863.863 0 01-1.202 0 .796.796 0 010-1.163l3.89-3.768H39.915a4.49 4.49 0 00.037-.202zM7.915 5.175h24.17a4.017 4.017 0 000 1.65H7.915A4.001 4.001 0 010 6a4 4 0 017.915-.825zM36 9a3 3 0 100-6 3 3 0 000 6z"
                    ></path>
                  </svg>
                  <p class="text-sm text-gray-500 text-center">{{ Math.floor(flight.time / 60) }} hours {{ flight.time % 60 }} minutes</p>
                </div>
                <div v-else>
                  <p class="text-sm text-gray-500 text-center">Direct Flight</p>
                  <svg class="w-20 mx-auto my-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 12" id="icon-arrow-way">
                    <path
                      fill="#C8CACC"
                      fill-rule="evenodd"
                      d="M7.915 5.175h61.172l-3.89-3.768a.796.796 0 010-1.163.863.863 0 011.202 0l5.35 5.17.036.04.031.038c.04.052.063.083.08.113l.028.06a.42.42 0 01.04.105l.023.087.009.074.004.047v.04l-.004.051-.01.074-.017.074a.776.776 0 01-.045.122l-.027.056c-.018.03-.04.06-.063.096l-.04.043-.045.052-5.35 5.17a.863.863 0 01-1.202 0 .796.796 0 010-1.163l3.89-3.768H7.915A4.001 4.001 0 010 6a4 4 0 017.915-.825z"
                    ></path>
                  </svg>
                  <p class="text-sm text-gray-500 text-center">{{ Math.floor(flight.time / 60) }} hours {{ flight.time % 60 }} minutes</p>
                </div>
              </div>
              <div class="flex flex-col justify-start items-start w-24">
                <span class="text-sm text-gray-700 font-medium">{{ flight.to.airport.code_name }}</span>
                <span class="text-2xl text-gray-900 font-bold tracking-tighter">{{ flight.to.time }}</span>
              </div>
            </div>
            <div class="flex-1 flex items-center justify-end">
              <span class="text-2xl font-bold text-gray-900"> ${{ flight.price }} </span>
            </div>
          </div>
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
    this.searchFlightsForm.departureAirport = params.has('departureAirport') ? params.get('departureAirport') : '';
    this.searchFlightsForm.arrivalAirport = params.has('arrivalAirport') ? params.get('arrivalAirport') : '';
    this.searchFlightsForm.departureDate = params.has('departureDate') ? params.get('departureDate') : moment().add(1, 'days').format('Y-M-D');
    this.searchFlightsForm.passengerCount = params.has('passengerCount') ? params.get('passengerCount') : 1;
    this.flights && this.organizeFlights();
  },
  data() {
    return {
      searchFlightsForm: {
        departureDate: null,
        departureAirport: '',
        arrivalAirport: '',
        passengerCount: 1,
      },
      flatPickrConfig: {
        enableTime: false,
        minDate: moment().format('Y-M-D'),
        dateFormat: 'Y-m-d',
      },
      flightList: [],
    };
  },
  methods: {
    organizeFlights() {
      let flightListData = [];
      this.flights.direct.forEach((flight) =>
        flightListData.push({
          from: {
            time: moment(flight['departure_time']).format('HH:mm'),
            airport: flight['departure_airport'],
          },
          to: {
            time: moment(flight['arrival_time']).format('HH:mm'),
            airport: flight['arrival_airport'],
          },
          via: false,
          price: flight.price.toFixed(2),
          time: flight.time,
        })
      );
      this.flights.connected.forEach((connectedFlight) => {
        flightListData.push({
          from: {
            time: moment(connectedFlight.flights[0]['departure_time']).format('HH:mm'),
            airport: connectedFlight.flights[0]['departure_airport'],
          },
          to: {
            time: moment(connectedFlight.flights[1]['arrival_time']).format('HH:mm'),
            airport: connectedFlight.flights[1]['arrival_airport'],
          },
          via: {
            airport: connectedFlight.flights[0]['arrival_airport'],
          },
          price: connectedFlight.price.toFixed(2),
          time: connectedFlight.time,
        });
      });
      this.flightList = flightListData;
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