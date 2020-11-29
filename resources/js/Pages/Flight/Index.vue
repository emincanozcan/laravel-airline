<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Flights</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div
            class="my-4 pb-4 px-8 font-semibold text-center border-b border-gray-200 text-center"
          >
            <h1 class="text-3xl">Flight List</h1>
          </div>
          <div>
            <flights-filters class="px-8 mb-4" :airports="airports" />
          </div>

          <div class="mx-8 mb-6">
            <flights-table :flights="flights" />
            <div>
              <flights-pagination
                :total="flights.total"
                :currentPage="flights.current_page"
                :lastPage="flights.last_page"
                :perPage="flights.per_page"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import FlightsTable from "@/Components/FlightsTable";
import FlightsFilters from "@/Components/FlightsFilters";
import FlightsPagination from "@/Components/FlightsPagination";
export default {
  props: ["flights", "airports"],
  components: {
    FlightsTable,
    AppLayout,
    FlightsPagination,
    FlightsFilters,
  },
};
</script>
