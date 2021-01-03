<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Flights</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="my-4 pb-4 px-8 font-semibold text-center border-b border-gray-200 text-center">
            <h1 class="text-3xl">New Flight</h1>
          </div>
          <div class="bg-green-400 mx-8 text-center py-4 text-lg text-white text-center rounded-md" v-if="form.recentlySuccessful">Flight created successfully</div>
          <div class="mx-2 my-3 sm:mx-4 sm:my-4 md:mx-8 md:mb-6">
            <form @submit.prevent="createFlight" ref="createFlightForm">
              <h3 class="text-xl font-semibold border-b border-gray-100 pb-2 mb-6 mt-8">Departure Details</h3>
              <div class="lg:flex items-start">
                <div class="mb-4 lg:mb-0 lg:w-1/2">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="departure-airport"> Airport: </label>
                    <Select class="flex-1" id="departure-airport" @change="setDepartureAirport">
                      <option value="" disabled selected>Select an airport</option>
                      <option v-for="airport in airports" :key="airport.id" :value="airport.id">
                        {{ airport.full_name }}
                      </option>
                    </Select>
                  </div>
                  <input-error :message="form.error('departureAirport')" class="mt-2" />
                </div>
                <div class="lg:ml-16">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="departure-time"> Date & Time: </label>
                    <flat-pickr class="border border-gray-200 py-2 px-4 bg-gray-100 rounded-md w-full" id="departure-time" v-model="form.departureDate" :config="flatPickrConfig"></flat-pickr>
                  </div>
                  <input-error :message="form.error('departureDate')" class="mt-2" />
                </div>
              </div>

              <h3 class="text-xl font-semibold border-b border-gray-100 pb-2 mb-6 mt-12">Arrival Details</h3>
              <div class="lg:flex items-start">
                <div class="mb-4 lg:mb-0 lg:w-1/2">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="arrival-airport"> Airport: </label>
                    <Select class="flex-1" id="arrival-airport" @change="setArrivalAirport">
                      <option value="" ref="departureAirport" disabled selected>Select an airport</option>
                      <option v-for="airport in airports" :key="airport.id" :value="airport.id">
                        {{ airport.full_name }}
                      </option>
                    </Select>
                  </div>
                  <input-error :message="form.error('arrivalAirport')" class="mt-2" />
                </div>
                <div class="lg:ml-16">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="arrival-time"> Date & Time: </label>
                    <flat-pickr class="border border-gray-200 py-2 px-4 bg-gray-100 rounded-md w-full" id="arrival-time" v-model="form.arrivalDate" :config="flatPickrConfig"></flat-pickr>
                  </div>
                  <input-error :message="form.error('arrivalDate')" class="mt-2" />
                </div>
              </div>

              <h3 class="text-xl font-semibold border-b border-gray-100 pb-2 mb-6 mt-12">Additional Details</h3>
              <div class="lg:flex items-start">
                <div class="lg:w-1/2 mb-4 lg:mb-0">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="price"> Price: </label>
                    <Input name="price" type="number" step="0.01" v-model="form.price" />
                  </div>
                  <input-error :message="form.error('price')" class="mt-2" />
                </div>
                <div class="lg:ml-16">
                  <div class="flex items-center">
                    <label class="whitespace-no-wrap mr-4 w-24 font-semibold" for="capacity"> Capacity: </label>
                    <Input name="capacity" type="number" step="1" v-model="form.capacity" />
                  </div>
                  <input-error :message="form.error('capacity')" class="mt-2" />
                </div>
              </div>
              <div class="mt-8">
                <Button>Create</Button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import Select from '@/Components/Select';
import flatPickr from 'vue-flatpickr-component';
import Input from '@/Jetstream/Input';
import inputError from '@/Jetstream/InputError';
import Button from '@/Jetstream/Button';
import 'flatpickr/dist/flatpickr.css';
import 'flatpickr/dist/themes/airbnb.css';
export default {
  props: ['airports'],
  components: {
    AppLayout,
    Select,
    flatPickr,
    Input,
    Button,
    inputError,
  },
  data() {
    return {
      form: this.$inertia.form(
        {
          departureDate: null,
          departureAirport: '',
          arrivalDate: null,
          arrivalAirport: '',
          price: null,
          capacity: null,
        },
        {
          bag: 'createFlight',
        }
      ),
      flatPickrConfig: {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
      },
    };
  },
  methods: {
    async createFlight() {
      await this.form.post(route('flights.store'));
      this.$refs.createFlightForm.reset();
    },
    setDepartureAirport(event) {
      this.form.departureAirport = event.target.value;
    },
    setArrivalAirport(event) {
      this.form.arrivalAirport = event.target.value;
    },
  },
};
</script>
