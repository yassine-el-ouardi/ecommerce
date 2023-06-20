<template>
  <div
    class="pos-rel"
  >

    <stripe-payment
      v-if="showStripe"
      :stripe-key="paymentGateway.stripe_key"
      :order-id="orderId"
      :amount="parseFloat(amount)"
      :currency="currencyData"
      :name="userName"
      :site-name="site_setting.siteName"
      :email="userEmail"
      @success="orderPlaced('success', $event)"
      @closed="orderPlaced('closed', $event)"
    />

    <razorpay-payment
      v-else-if="showRazorpay"
      :order-id="orderId"
      :razorpay-key="paymentGateway.razorpay_key"
      :razorpay-payment-token="razorpayPaymentToken"
      :amount="parseFloat(amount)"
      :site-name="site_setting.siteName"
      :currency="currencyData"
      :name="userName"
      :email="userEmail"
      @success="orderPlaced('success', $event)"
      @closed="orderPlaced('closed', $event)"
      @error="orderPlaced('error', $event)"
    />

    <transition
      name="fade"
      mode="out-in"
    >
      <div
        class="spinner-wrapper flex layer-white"
        v-if="loading"
      >
        <spinner
          :radius="100"
        />
      </div>
    </transition>

    <p
      v-if="orderError"
      class="f-13 error mb-15"
    >
      <span v-for="i in orderError" class="block">{{ i }}</span>
    </p>
    <form
      v-if="paymentGateway"
    >
      <p
        v-if="noPaymentMethod"
        class="info mt-15"
      >
        {{ $t('checkout.noPayment') }}
      </p>

      <div
        class="heading-tab-wrapper"
      >
        <div
          class="tab-heading"
        >

          <label
            v-if="parseInt(paymentGateway.stripe) === status.PUBLIC"
            :class="{active: paymentType === orderMethods.STRIPE}"
          >
            <input
              type="radio"
              :value="orderMethods.STRIPE"
              v-model="paymentType"
            >
            <i class="icon stripe-icon"/>
            <span>{{ $t('payment.stripe') }}</span>
          </label>


          <label
            v-if="parseInt(paymentGateway.flutterwave) === status.PUBLIC"
            :class="{active: paymentType === orderMethods.FLUTTERWAVE}"
          >
            <input
              type="radio"
              :value="orderMethods.FLUTTERWAVE"
              v-model="paymentType"
            >
            <i class="icon flutterwave-icon"/>
            <span>{{ $t('payment.flutterwave') }}</span>
          </label>


          <label
            v-if="parseInt(paymentGateway.razorpay) === status.PUBLIC"
            :class="{active: paymentType === orderMethods.RAZORPAY}"
          >
            <input
              type="radio"
              :value="orderMethods.RAZORPAY"
              v-model="paymentType"
            >
            <i class="icon razorpay-icon"/>
            <span>{{ $t('payment.razorpay') }}</span>
          </label>


          <label
            v-if="parseInt(paymentGateway.paypal) === status.PUBLIC"
            :class="{active: paymentType === orderMethods.PAYPAL}"
          >
            <span
              class="spinner-wrapper flex layer-white"
              v-if="!paypaLoaded && paymentType === orderMethods.PAYPAL"
            >
              <spinner
                :radius="50"
              />
            </span>

            <input
              type="radio"
              :value="orderMethods.PAYPAL"
              v-model="paymentType"
            >
            <i class="icon paypal-icon"/>
            <span>{{$t('payment.paypal')}}</span>
          </label>


          <label
            v-if="parseInt(paymentGateway.cash_on_delivery) === status.PUBLIC"
            :class="{active: paymentType === orderMethods.CASH_ON_DELIVERY}"
          >
            <input
              type="radio"
              :value="orderMethods.CASH_ON_DELIVERY"
              v-model="paymentType"
            >
            <i class="icon cod-icon"/>
            <span>{{ $t('orderTabbing.cod') }}</span>
          </label>
        </div>

        <div
          class="tab-content"
        >
          <ajax-button
            v-if="paymentType === orderMethods.STRIPE"
            class="primary-btn  plr-30 plr-sm-15"
            type="button"
            :fetching-data="placingOrder"
            :disabled="!totalPrice || noPaymentMethod"
            :text="paymentBtnText"
            @clicked="initStripe"
          />
          <ajax-button
            v-else-if="paymentType === orderMethods.RAZORPAY"
            class="primary-btn  plr-30 plr-sm-15"
            type="button"
            :fetching-data="placingOrder"
            :disabled="!totalPrice || noPaymentMethod"
            :text="paymentBtnText"
            @clicked="initRazorpay"
          />
          <div
            v-else-if="paymentType === orderMethods.CASH_ON_DELIVERY"
          >
            <ajax-button
              class="primary-btn  plr-30 plr-sm-15"
              type="button"
              :fetching-data="placingOrder"
              :disabled="!totalPrice || noPaymentMethod"
              :text="$t('checkout.confirmOrder')"
              @clicked="confirmOrder"
            />
          </div>
          <div
            v-else-if="paymentType === orderMethods.FLUTTERWAVE"
          >
            <flutterwave-pay-btn
              ref="flutterWave"
              :order="orderData"
              public-key="FLWPUBK_TEST-7ceb4852c77efb1c4193180c82c5729d-X"
              :amount="amount"
              :currency="currency"
              :btn-text="paymentBtnText"
              :name="userName"
              :loading="!flutterwaveLoaded"
              :user-id="`${userId}`"
              :email="userEmail"
              :site-name="siteName"
              :header-logo="headerLogo"
              @clicked="payWithFlutterWave"
              @success="orderPlaced('success', $event)"
              @closed="orderPlaced('closed', $event)"
              @error="orderPlaced('error', $event)"
            />
          </div>

          <div
            v-if="parseInt(paymentGateway.paypal) === status.PUBLIC"
            class="paypal-tab"
            :class="{'paypal-active': paymentType === orderMethods.PAYPAL}"
          >
            <div
              ref="paypal"
            />
          </div>

        </div>

      </div>
    </form>
  </div>
</template>
<script>
  import StripePayment from '~/components/StripePayment'
  import RazorpayPayment from '~/components/RazorpayPayment'
  import util from '~/mixin/util'
  import {mapGetters, mapActions} from 'vuex'
  import productHelper from "~/mixin/productHelper"
  import productPriceHelper from "~/mixin/productPriceHelper"
  import paymentHelper from '~/mixin/paymentHelper'
  import Spinner from "./Spinner";
  import AjaxButton from "./AjaxButton";
  import FlutterwavePayBtn from "./FlutterwavePayBtn";

  export default {
    middleware: ['auth'],
    data() {
      return {
        loading: false,
        flutterwaveLoaded: false,
        paypaLoaded: false,
        showRazorpay: false,
        showStripe: false,
        paymentType: 1,
        orderData: null,
        orderError: null,
        submitting: false,
        placingOrder: false,
        checkedProductQty: 0
      }
    },
    props: {
      voucher: {
        type: Object,
        default() {
          return null
        }
      },
      order: {
        type: Object,
        default() {
          return null
        }
      },
      page: {
        type: String,
        default: 'checkout'
      },
      totalPrice: {
        type: Number,
        default: 0
      }
    },
    watch: {},
    components: {
      FlutterwavePayBtn,
      AjaxButton,
      Spinner,
      RazorpayPayment,
      StripePayment
    },
    mixins: [util, productHelper, paymentHelper, productPriceHelper],
    computed: {
      paymentBtnText() {
        return this.$t('checkout.confirmOrderAnd', {amount: this.formattedPrice})
      },
      formattedPrice() {
        if (parseInt(this.currencyPosition) === this.currencyPositionsIn.PRE) {
          return this.currencyIcon + this.totalPrice
        }
        return this.totalPrice + this.currencyIcon
      },
      voucherResult() {
        return this.voucher
      },
      isCheckout() {
        return this.page === 'checkout'
      },
      userEmail() {
        return this.orderData?.userEmail || this.$auth?.user?.email
      },
      headerLogo() {
        return this.imageURL({'image': this.site_setting.header_logo})
      },
      siteName() {
        return this.site_setting?.site_name
      },
      currencyData() {
        return this.orderData?.currency || this.currency
      },
      userId() {
        return this.$auth?.user?.id
      },
      userName() {
        return this.orderData?.userName || this.$auth?.user?.name
      },
      razorpayPaymentToken() {
        return this.orderData?.payment_token || null
      },
      amount() {
        return parseFloat(this.orderData?.total_amount).toFixed(2) || 0
      },
      orderId() {
        return this.orderData?.id || null
      },
      noPaymentMethod() {
        return parseInt(this.paymentGateway?.stripe) !== this.status.PUBLIC &&
          parseInt(this.paymentGateway?.razorpay) !== this.status.PUBLIC &&
          parseInt(this.paymentGateway?.paypal) !== this.status.PUBLIC &&
          parseInt(this.paymentGateway?.cash_on_delivery) !== this.status.PUBLIC
      },
      checkedProduct() {
        return this.cartProducts.filter(obj => {
          return parseInt(obj.selected) === 1
        })
      },
      ...mapGetters('common', ['currencyIcon', 'currency', 'currencyPosition', 'paymentGateway', 'site_setting']),
      ...mapGetters('cart', ['cartProducts']),
    },
    methods: {
      async payWithFlutterWave(){
        await this.confirmOrder()
        this.$refs.flutterWave.makePayment()
      },
      setLoaded(resp) {
        window.paypal
          .Buttons({
            style: {
              label: 'pay'
            },
            createOrder: async (data, actions) => {
              if (this.isCheckout) {
                return this.confirmOrder()
                  .then(() => {
                    return actions.order.create({
                      purchase_units: [
                        {
                          description: '',
                          amount: {
                            currency_code: this.currency,
                            value: this.amount
                          }
                        }
                      ]
                    });
                  })
              } else {
                this.orderData = this.order
                return actions.order.create({
                  purchase_units: [
                    {
                      description: '',
                      amount: {
                        currency_code: this.currency,
                        value: this.amount
                      }
                    }
                  ]
                });
              }
            },
            onApprove: async () => {
              await this.paymentDoneFn(this.orderId, this.orderId, this.orderMethods.PAYPAL)
              this.orderPlaced('success', this.orderId)
            },
            onError: err => {
              this.orderPlaced('closed', err)
            }
          })
          .render(this.$refs.paypal)
      },
      async initRazorpay(){
        try{
          await this.confirmOrder()
          this.showRazorpay = true
        }catch(e){
          console.log(e)
        }
      },
      async initStripe(){
        try{
          await this.confirmOrder()
          this.showStripe = true
        }catch(e){
          console.log(e)
        }
      },
      confirmOrder() {
        return new Promise(async resolve => {
          if (this.isCheckout) {
            this.orderError = ''
            this.placeOrder()
              .then(result => {
                const data = result?.data
                if (parseInt(data.order_method) !== this.orderMethods.CASH_ON_DELIVERY) {
                  data['total_amount'] = data.amount
                }
                this.orderData = data
                resolve(data)
              })
          } else {
            this.orderData = this.order

            if (parseInt(this.paymentType) === this.orderMethods.CASH_ON_DELIVERY) {
              await this.paymentDoneFn(this.order.id, this.order.id, this.orderMethods.CASH_ON_DELIVERY)
              this.orderPlaced('success', this.order.id)
            }

            resolve()
          }
        })
      },
      orderPlaced(type = 'success', event) {
        if (type === 'success') {
          this.setToastMessage(this.$t('payButton.placedSuccess'))
          this.$router.push({path: '/user/order/' + event})
          this.$emit('order-status', true)

        } else if (type === 'error') {

          this.$router.push({path: '/user/order/' + this.orderId})
          this.setToastError(event)

        } else if (type === 'closed') {

          // this.setToastMessage(this.$t('payButton.placedSuccess'))
          this.$router.push({path: '/user/order/' + this.orderId})
          this.$emit('order-status', false)
        }
      },
      async placeOrder() {
        return new Promise(((resolve, reject) => {
          if (this.checkedProduct.length) {
            const params = []
            this.checkedProduct.forEach(obj => {

              let shippingPrice = 0
              if (parseInt(obj.shipping_type) === 1) {
                shippingPrice = parseInt(obj?.shipping_place?.price)
              } else if (parseInt(obj.shipping_type) === 2) {
                shippingPrice = parseInt(obj?.shipping_place?.pickup_price)
              }
              const currentInventoryPrice = this.currentInventoryPriceCalc(obj.inventory, obj.flash_product)
              const currentPrice = parseInt(obj?.quantity) * currentInventoryPrice
              const currentOffer = currentInventoryPrice * parseInt(obj?.offered)
              this.checkedProductQty += parseInt(obj?.quantity)

              params.push({
                cart: obj.id,
                bundle_offer: currentOffer,
                shipping_price: shippingPrice,
                selling: currentPrice,
              })
            })
            this.loading = true

            this.orderAction({
              data: this.phpEncryption({
                order_method: this.paymentType,
                voucher: this.voucherResult?.voucher || '',
                time_zone: this.timeZone
              })
            })
              .then(res => {
                this.loading = false
                if (res.status === 200) {

                  setTimeout(() => {
                    this.sendOrderEmail({
                      id: res.data.id,
                      time_zone: this.timeZone
                    })
                  }, 5000)

                  this.subtractCartProductCount({
                    qty: this.checkedProductQty,
                    status: this.status
                  })

                  if (parseInt(res.data.order_method) === this.orderMethods.CASH_ON_DELIVERY) {
                    this.orderPlaced('success', res.data.id)
                  }
                  resolve(res)

                } else if (res.status === 201) {

                  if (res?.data?.form) {
                    this.orderError = res?.data?.form

                  } else if (res?.data?.product) {
                    const productError = []
                    Object.values(res?.data?.product[0]).forEach((obj) => {
                      obj.forEach(o => {
                        productError.push(o)
                      })
                    })

                    this.orderError = productError
                  }
                  reject()
                }
              })
          } else {
            this.setToastError(this.$t('listingLayout.noProductFound'))
            this.$router.push('cart')
          }
        }))
      },
      ...mapActions('common', ['setToastMessage', 'setToastError']),
      ...mapActions('order', ['orderAction', 'voucherValidity', 'sendOrderEmail', 'paymentDone']),
      ...mapActions('cart', ['getCartByUser', 'subtractCartProductCount', 'emptyCartProduct']),
    },
    async mounted() {
        if (parseInt(this.paymentGateway?.paypal) === this.status.PUBLIC) {
          const recaptchaScript = document.createElement('script')
          recaptchaScript.setAttribute('src',
            `https://www.paypal.com/sdk/js?client-id=${this.paymentGateway?.paypal_key}&components=buttons,marks&disable-funding=paylater,card`
          )
          recaptchaScript.setAttribute('async', true)
          document.head.appendChild(recaptchaScript)
          recaptchaScript.addEventListener("load", () => {
            this.setLoaded()
            this.paypaLoaded = true
          });
        } else {
          this.paypaLoaded = true
        }


      if (parseInt(this.paymentGateway?.flutterwave) === this.status.PUBLIC) {

        const recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src',
          `https://checkout.flutterwave.com/v3.js`
        )
        recaptchaScript.setAttribute('async', true)
        document.head.appendChild(recaptchaScript)
        recaptchaScript.addEventListener("load", () => {
          this.flutterwaveLoaded = true
        });

      } else {
        this.flutterwaveLoaded = true
      }



      if (parseInt(this.paymentGateway?.stripe) === this.status.PUBLIC) {
        this.paymentType = this.orderMethods.STRIPE

      } else if (parseInt(this.paymentGateway?.rezorpay) === this.status.PUBLIC) {
        this.paymentType = this.orderMethods.RAZORPAY

      } else if (parseInt(this.paymentGateway?.paypal) === this.status.PUBLIC) {
        this.paymentType = this.orderMethods.PAYPAL

      } else if (parseInt(this.paymentGateway?.cash_on_delivery) === this.status.PUBLIC) {
        this.paymentType = this.orderMethods.CASH_ON_DELIVERY
      }
    },
  }
</script>
