<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
          <div>
            <div class="px-6 py-12 sm:px-20 bg-white border-b border-gray-200">
              <div class="text-2xl">Evolution Airline Managament Panel</div>
              <div class="mt-6 text-gray-500 text-lg font-semibold">This dashboard is created to easily manage the app.</div>
              <div class="mt-6 text-red-600">
                @todo - Management panel usage instructions will be located here in the future.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';

export default {
  components: {
    AppLayout,
  },
};
</script>
