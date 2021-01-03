<template>
  <div class="flex flex-col lg:flex-row justify-between items-center">
    <div class="mb-4 lg:mb-0">
      <Button class="block my-1 sm:my-0 sm:inline-flex" @click="() => filterResults('filter', '')" :disabled="!(activeFilter && activeFilter.length > 0)"> All </Button>
      <Button class="block my-1 sm:my-0 sm:inline-flex" @click="() => filterResults('filter', 'fly')" :disabled="activeFilter === 'fly'"> Flying </Button>
      <Button class="block my-1 sm:my-0 sm:inline-flex" @click="() => filterResults('filter', 'future')" :disabled="activeFilter === 'future'"> Future </Button>
      <Button class="block my-1 sm:my-0 sm:inline-flex" @click="() => filterResults('filter', 'past')" :disabled="activeFilter === 'past'"> Past </Button>
    </div>
    <div class="flex justify-center">
      <div class="flex items-center">
        <span class="font-medium text-lg mr-2">From:</span>
        <Select class="mr-6" @change="(event) => filterResults('from', event.target.value)">
          <option value="">All</option>
          <option :selected="airport.id == activeFrom" :value="airport.id" :key="airport.id" v-for="airport in airports">{{ airport.full_name }} ({{ airport.code_name }})</option>
        </Select>
      </div>
      <div class="flex items-center">
        <span class="font-medium text-lg mr-2">To:</span>
        <Select @change="(event) => filterResults('to', event.target.value)">
          <option value="">All</option>
          <option :selected="airport.id == activeTo" :value="airport.id" :key="airport.id" v-for="airport in airports">{{ airport.full_name }} ({{ airport.code_name }})</option>
        </Select>
      </div>
    </div>
  </div>
</template>

<script>
import Button from '@/Jetstream/Button';
import Select from '@/Components/Select';
export default {
  name: 'FlightsFilterButtons',
  components: {
    Button,
    Select,
  },
  props: ['airports'],
  data() {
    return {
      activeFilter: '',
      activeFrom: '',
      activeTo: '',
    };
  },
  methods: {
    filterResults(k, v) {
      const params = new URLSearchParams(window.location.search);
      let paramObj = {};
      for (var value of params.keys()) {
        paramObj[value] = params.get(value);
      }
      paramObj[k] = v;
      paramObj['page'] = '';
      this.$inertia.get(`/dashboard/flights`, paramObj);
    },
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    this.activeFilter = urlParams.get('filter');
    this.activeFrom = urlParams.get('from');
    this.activeTo = urlParams.get('to');
  },
};
</script>

<style>
</style>