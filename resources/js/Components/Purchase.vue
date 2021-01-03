<template>
  <div>
    <dialog-modal :show="show" @close="() => $emit('close')">
      <template #title> <h4 class="text-xl font-black tracking-wide border-b border-cool-gray-100 pb-2">Buy Ticket</h4> </template>
      <template #content>
        <h3 class="font-black tracking-wider mb-4 mt-4">Ticket Details</h3>
        <div v-if="loading">Loading...</div>
        <div v-else>
          <table class="table border-collapse table-auto w-full">
            <tr class="border-dashed border border-cool-gray-300">
              <td class="font-semibold border-r border-cool-gray-200 px-4 py-2">From</td>
              <td class="px-4 py-2 flex justify-between items-center">
                <span class="font-medium text-cool-gray-800"> {{ flightData.route[0].full_name }} </span>
                <span class="font-medium text-cool-gray-500 text-sm ml-8">{{ flightData.flights[0].departure_time }}</span>
              </td>
            </tr>

            <tr class="border-dashed border border-cool-gray-300">
              <td class="font-semibold border-r border-cool-gray-200 px-4 py-2">To</td>
              <td class="px-4 py-2 flex justify-between items-center">
                <span class="font-medium text-cool-gray-800"> {{ flightData.route[flightData.route.length - 1].full_name }} </span>
                <span class="font-medium text-cool-gray-500 text-sm ml-4">{{ flightData.flights[flightData.flights.length - 1].arrival_time }}</span>
              </td>
            </tr>

            <tr class="border-dashed border border-cool-gray-300" v-if="flightData.route.length > 2">
              <td class="font-semibold border-r border-cool-gray-200 px-4 py-2">Via</td>
              <td class="px-4 py-2">
                <span class="font-medium text-cool-gray-800 mr-2" v-for="i in flightData.route.length - 2" :key="i">
                  {{ flightData.route[i].full_name }}
                  <span v-if="i + 3 <= flightData.route.length" class="ml-2"> -> </span>
                </span>
              </td>
            </tr>

            <tr class="border-dashed border border-cool-gray-300">
              <td class="font-semibold border-r border-cool-gray-200 px-4 py-2">Passenger Count</td>
              <td class="px-4 py-2 flex justify-between items-center">
                <span class="font-medium text-cool-gray-800"> {{ passengerCount }} </span>
              </td>
            </tr>
            <tr class="border-dashed border border-cool-gray-300">
              <td class="font-semibold border-r border-cool-gray-200 px-4 py-2">Total Price</td>
              <td class="px-4 py-2 flex justify-between items-center">
                <span class="font-medium text-cool-gray-800"> ${{ flightData.totalPrice }} </span>
              </td>
            </tr>

            <!-- <td class="border-dashed border border-cool-gray-300 font-semibold px-4 py-2" v-if="">Via</td> -->
          </table>
        </div>
        <div v-show="!loading" class="mt-8 mb-6">
          <h3 class="font-black tracking-wider mb-4">Payment Details</h3>
          <div class="flex justify-between mb-4">
            <input class="form-input rounded-md shadow-sm flex-1 mr-8" type="text" v-model="cardEmail" placeholder="Email" />
            <input class="form-input rounded-md shadow-sm flex-1" type="text" v-model="cardName" placeholder="Name" />
          </div>
          <div id="card-element"></div>
        </div>
      </template>
      <template #footer>
        <Button @click="buyTicket" type="button">Buy Ticket</Button>
      </template>
    </dialog-modal>
  </div>
</template>

<script>
import DialogModal from '@/Jetstream/DialogModal.vue';
import Button from '@/Jetstream/Button';
export default {
  name: 'Purchase',
  data() {
    return {
      loading: true,
      flightData: [],
      stripeInstance: null,
      cardElement: null,
      cardName: '',
      cardEmail: '',
      cardStyle: {
        base: {
          color: '#32325d',
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: 'antialiased',
          fontSize: '16px',
          '::placeholder': {
            color: '#aab7c4',
          },
        },
        invalid: {
          color: '#fa755a',
          iconColor: '#fa755a',
        },
      },
    };
  },
  components: { DialogModal, Button },
  props: ['show', 'flightIds', 'passengerCount'],
  methods: {
    initialize() {
      const stripeKey = this.$page.stripePublicKey;
      this.stripeInstance = Stripe(stripeKey);
      const elements = this.stripeInstance.elements();
      this.cardElement = elements.create('card', { hidePostalCode: true, style: this.cardStyle });
      this.cardElement.mount(document.getElementById('card-element'));
    },
    async buyTicket() {
      const { paymentMethod, error } = await this.stripeInstance.createPaymentMethod('card', this.cardElement, {
        billing_details: { name: this.cardName, email: this.cardEmail },
      });
      if (error) {
        this.$swal('Error', error.message, 'error');
      } else {
        const response = await axios.post(route('sale.purchase').url(), {
          paymentMethod: paymentMethod,
          name: this.cardName,
          email: this.cardEmail,
          passengerCount: this.passengerCount,
          flightIds: this.flightIds,
        });
        if (response.data.status && response.data.status === 'ok') {
          await this.$swal('Success', 'We are sending an email to you about your ticket details.', 'success');
        } else {
          /* this is not acceptable error handling, it needs improve,emts but this is just a demo... */
          await this.$swal('Error', response.data.message ? response.data.message : 'An error happened. Please try again or contact us.', 'error');
        }
        window.location.reload();
      }
    },
  },
  mounted() {
    const self = this;
    axios.post(route('sale.information').url(), { flightIds: this.flightIds }).then(function (response) {
      self.flightData = response.data;
      self.loading = false;
      self.initialize();
    });
  },
};
</script>

<style>
.StripeElement {
  @apply shadow-md;
  @apply transition-all;
  @apply bg-white;
  @apply rounded-md;
  @apply w-full;
  @apply px-4;
  @apply py-3;
  @apply border;
  @apply border-gray-200;
}

.StripeElement--focus {
  box-shadow: 0 0 0 3px rgba(164, 202, 254, 0.45);
  border-color: #a4cafe;
}

.StripeElement--invalid {
  box-shadow: 0 0 0 3px rgba(219, 11, 11, 0.45);
}

.StripeElement--webkit-autofill {
  background-color: #fefde5 !important;
}
</style>