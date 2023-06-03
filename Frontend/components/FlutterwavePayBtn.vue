<template>
  <div>

    <ajax-button
      class="primary-btn  plr-30 plr-sm-15"
      type="button"
      :fetching-data="placingOrder"
      :disabled="!amount"
      :text="btnText"
      @clicked="confirmOrder"
    />

    <p class="error">
      {{ errorText }}
    </p>

    <span
      class="spinner-wrapper flex layer-white"
      v-if="loading || submitting"
    >
      <spinner
        :radius="50"
      />
    </span>
  </div>

</template>
<script>
  import {mapActions} from 'vuex'
  import Spinner from "./Spinner";
  import util from "~/mixin/util";
  import paymentHelper from "~/mixin/paymentHelper";
  import AjaxButton from "./AjaxButton";

  export default {
    name: 'FlutterwavePayBtn',
    data() {
      return {
        errorText: '',
        submitting: false,
        placingOrder: false
      }
    },
    watch: {},
    props: {
      order: {
        type: Object,
        default() {
          return null
        }
      },
      loading: {
        type: Boolean,
        default: false
      },
      razorpayKey: {
        type: String,
        default: ''
      },
      currency: {
        type: String,
        default: 'USD'
      },
      amount: {
        type: Number,
        default: 0
      },
      name: {
        type: String,
        default: ''
      },
      userId: {
        type: String,
        default: ''
      },
      email: {
        type: String,
        default: ''
      },
      publicKey: {
        type: String,
        default: ''
      },
      siteName: {
        type: String,
        default: ''
      },
      headerLogo: {
        type: String,
        default: ''
      },
      btnText: {
        type: String,
        default: ''
      },
    },
    components: {
      AjaxButton,
      Spinner
    },
    computed: {

    },
    mixins: [util, paymentHelper],
    methods: {
      confirmOrder(){
        this.placingOrder = true
        this.$emit('clicked')
      },

      makePayment() {
        this.placingOrder = false
        const self = this

        const fw = FlutterwaveCheckout({
          public_key: this.publicKey,
          tx_ref: "ref_" + this.order.order,
          amount: this.amount,
          currency: this.currency,
          payment_options: "card, banktransfer, ussd",

          meta: {
            consumer_id: this.userId,
          },
          customer: {
            email: this.email,
            user_id:  this.userId,
            name: this.name,
          },
          callback: async (data) => {
            this.submitting = true
            const res = await this.paymentDoneFn(this.order.id, data.transaction_id, this.orderMethods.FLUTTERWAVE)
            console.log(res)
            fw.close();
          },
          onclose: function() {
            self.$emit('closed', self.order.id)
          },
          customizations: {
            title: this.siteName,
            description: "",
            logo: this.headerLogo
          },
        });
      },
      ...mapActions('order', ['paymentDone']),
    },
    created() {
    },
    async mounted() {


    }
  }
</script>

