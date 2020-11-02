<template>
  <div class="bg-white my-3 flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <a
        href="#"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
      >
        Previous
      </a>
      <a
        href="#"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
      >
        Next
      </a>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm leading-5 text-gray-700">
          <span class="font-medium">Flight count per page: {{ perPage }}</span>
          <br />
          <span class="font-medium">Total results: {{ total }}</span>
        </p>
      </div>
      <div>
        <Select @change="(event) => changePage(event.target.value)">
          <option
            v-for="page in lastPage"
            :key="page"
            :selected="page === currentPage"
          >
            Page {{ page }}
          </option>
        </Select>
      </div>
    </div>
  </div>
</template>

<script>
import Select from "@/Components/Select";
export default {
  props: ["total", "currentPage", "lastPage", "perPage"],
  components: {
    Select,
  },
  methods: {
    changePage(page) {
      const trimmedPage = page.replace("Page ", "");
      const params = new URLSearchParams(window.location.search);
      let paramObj = {};
      for (var value of params.keys()) {
        paramObj[value] = params.get(value);
      }
      paramObj["page"] = page;
      this.$inertia.get(`/dashboard/flights`, paramObj);
    },
  },
};
</script>

<style>
</style>