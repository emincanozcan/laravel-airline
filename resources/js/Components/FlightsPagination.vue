<template>
  <div class="bg-white my-3 flex items-center justify-between">
    <div class="flex-1 flex items-center justify-between">
      <div>
        <p class="text-sm leading-5 text-gray-700">
          <span class="font-medium">Flight count per page: {{ perPage }}</span>
          <br />
          <span class="font-medium">Total results: {{ total }}</span>
        </p>
      </div>
      <div>
        <Select @change="(event) => changePage(event.target.value)">
          <option v-for="page in lastPage" :key="page" :selected="page === currentPage">Page {{ page }}</option>
        </Select>
      </div>
    </div>
  </div>
</template>

<script>
import Select from '@/Components/Select';
export default {
  props: ['total', 'currentPage', 'lastPage', 'perPage'],
  components: {
    Select,
  },
  methods: {
    changePage(page) {
      const trimmedPage = page.replace('Page ', '');
      const params = new URLSearchParams(window.location.search);
      let paramObj = {};
      for (var value of params.keys()) {
        paramObj[value] = params.get(value);
      }
      paramObj['page'] = trimmedPage;
      this.$inertia.get(`/dashboard/flights`, paramObj);
    },
  },
};
</script>

<style>
</style>