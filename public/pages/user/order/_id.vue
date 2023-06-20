<template>
  <client-only>
    <account-layout
      active-route="orders"
      class="mb-20 mb-sm-15"
    >
      <template v-slot:rightArea>
        <div
          class="spinner-wrapper flex"
          v-if="fetchingOrderData"
        >
          <spinner
            :radius="100"
          />
        </div>
        <p
          v-if="orderCancelled"
          class="info-msg danger-msg order-wrapper mb-15"
        >
          {{ $t('order.orderCancelled') }}
        </p>
        <p
          v-if="refunded"
          class="info-msg success-msg order-wrapper mb-15"
        >
          {{ $t('order.orderRefunded') }}
        </p>

        <div
          v-if="Object.keys(ordered).length"
          class="card"
        >
          <div class="p-20 p-sm-15 pt-20">
            <div class="flex f-reverse sided block-md mb-30 mb-sm-15">
              <ul class="mx-w-400x order-details mb-md-15">
                <li>
                <span>
                  {{ $t('order.order') }}
                </span>
                  <span>#{{ ordered.order }}</span>
                </li>
                <li>
                <span>
                  {{ $t('order.deliveryStatus') }}
                </span>
                  <span>{{ orderStatus[ordered.status].title }}</span>
                </li>
                <li>
                <span>
                  {{ $t('order.orderMethod') }}
                </span>
                  <span>{{ orderMethodsIn[ordered.order_method] }}</span>
                </li>
                <li>
                <span>
                  {{ $t('order.orderDate') }}
                </span>
                  <span>{{ ordered.created }}</span>
                </li>
                <li>
                <span>
                  {{ $t('order.orderAmount') }}
                </span>
                  <span>
                    <price-format
                      :price="totalPrice"
                    />
                  </span>
                </li>
                <li>
                  <span>
                    {{ $t('order.paymentStatus') }}
                  </span>
                  <span>
                  {{ paymentStatus[ordered.payment_done] }}
                  <pay-button
                    v-if="!orderCancelled && parseInt(ordered.payment_done) === paymentStatusIn.UNPAID
                      && parseInt(ordered.order_method) !== orderMethods.CASH_ON_DELIVERY"
                    class="block mt-10"
                    :order="ordered"
                  />
                </span>
                </li>
              </ul>
              <p
                class="mx-w-400x lh-2 mr-15"
              >
                <b>{{ dataFromObject(ordered.address, 'name') }}</b>
                <span class="block">{{ generateAddress(ordered.address) }}</span>
                <span class="block">{{ $t('addressPopup.email') }}: {{ ordered.user.email }}</span>
                <span
                  class="block">{{ $t('addressPopup.phone') }}: {{ dataFromObject(ordered.address, 'phone', 'n/a') }}</span>
              </p>
            </div>

            <div class="mb-15">
              <ordered-status
                :status-of-order="ordered.status"
              />

            </div>
            <div class="flow-auto mtb-15">
              <table class="mn-w-600x no-bg w-100 mtb-0">
                <tr class="lite-bold">
                  <th>{{ $t('order.image') }}</th>
                  <th>{{ $t('orderCancelPopup.title') }}</th>
                  <th>{{ $t('order.shipTo') }}</th>
                  <th>{{ $t('order.deliveryFee') }}</th>
                  <th>{{ $t('detailRight.quantity') }}</th>
                  <th>{{ $t('cartProductTile.bundleOffer') }}</th>
                  <th>{{ $t('detailRight.price') }}</th>
                  <th>{{ $t('checkoutRight.total') }}</th>
                </tr>
                <tr
                  v-for="(value, index) in ordered.ordered_products"
                  :key="index"
                >
                  <td>
                    <nuxt-link
                      :to="productLink(value.product)"
                      class="img-wrap"
                      :title="value.product.title"
                    >
                      <img
                        :src="thumbImageURL(value.product)"
                        height="50"
                        width="50"
                        :alt="value.product.title"
                      >
                    </nuxt-link>
                  </td>
                  <td class="mn-w-200x-md">
                    <nuxt-link
                      :to="productLink(value.product)"
                      :title="value.product.title"
                      class="mb-5 block"
                    >
                      {{ value.product.title }}
                    </nuxt-link>
                    <span
                      class="block"
                    >
                     <span class="mr-15" v-for="i in generatingAttribute(value)">
                        <b class="mr-10">{{i[0]}}</b> : {{i[1]}}
                    </span>
                  </span>
                    <button
                      v-if="!!!ordered.cancelled"
                      aria-label="submit"
                      class="link-color"
                      @click.prevent="rateProductId = value.product.id">
                      {{ $t('ratePopup.rateNow') }}
                    </button>
                  </td>
                  <td>{{ shippingTypes[value.shipping_type] }}</td>
                  <td>
                    <price-format
                      :price="shippingPrice"
                    />
                  </td>
                  <td>{{ value.quantity }}</td>
                  <td>
                    <price-format
                      :price="value.selling * dataFromObject(value, 'bundle_offer', 0)"
                    />
                  </td>
                  <td>
                    <price-format
                      :price="value.selling"
                    />
                  </td>
                  <td>
                    <price-format
                      :price="value.selling * value.quantity"
                    />
                  </td>
                </tr>
              </table><!--table-->
            </div>

            <div class="flex right no-space">
              <ul
                class="mx-w-400x order-details order-price"
              >
                <li>
                <span>
                  {{ $t('order.subtotal') }}
                </span>
                  <span class="semi-bold">
                  <price-format
                    :price="subtotalPrice"
                  />
                </span>
                </li>
                <li>
                <span>
                  {{ $t('order.shippingCost') }}
                </span>
                  <span class="semi-bold">
                  <price-format
                    :price="shippingPrice"
                  />
                </span>
                </li>
                <li v-if="bundleOffer">
                <span>
                  {{ $t('cartProductTile.bundleOffer') }}
                </span>
                  <span class="semi-bold">
                  <price-format
                    :price="bundleOffer"
                  />
                </span>
                </li>
                <li v-if="voucherPrice">
                <span>
                   {{ $t('checkoutRight.voucher') }}
                </span>
                  <span class="semi-bold">
                  <price-format
                    :price="voucherPrice"
                  />
                </span>
                </li>
                <li v-if="taxPrice">
                <span>
                   {{ $t('cart.tax') }}
                </span>
                  <span class="semi-bold">
                  <price-format
                    :price="taxPrice"
                  />
                </span>
                </li>
                <li
                  class="mb-0"
                >
                <span>
                  {{ $t('checkoutRight.total') }}
                </span>
                  <span class="semi-bold f-11">
                  <price-format
                    :price="totalPrice"
                  />
                </span>
                </li>
                <li
                  v-if="!isDelivered"
                  class="pb-0 mb-0 j-end mt-15 mt-sm"
                >
                  <button
                    aria-label="submit"
                    class="outline-btn plr-30 plr-sm-15"
                    @click="cancelPopup = true"
                  >
                    {{ cancellationBtnText }}
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <transition name="fade" mode="out-in">
          <order-cancel-popup
            v-if="cancelPopup"
            :order-id="orderId"
            @success="orderCancelling"
            @close="cancelPopup = false"
          />
        </transition>
        <transition name="fade" mode="out-in">
          <rate-popup
            v-if="rateProductId"
            :order-id="orderId"
            :product-id="rateProductId"
            @close="rateProductId = 0"
          />
        </transition>
      </template>
    </account-layout>
  </client-only>

</template>

<script>

  import {mapGetters, mapActions} from 'vuex'
  import util from '~/mixin/util'
  import metaHelper from '~/mixin/metaHelper'
  import LazyImage from '~/components/LazyImage'
  import countryList from '~/resources/countries'
  import RatePopup from '~/components/RatePopup'
  import AccountLayout from '~/components/AccountLayout'
  import PayButton from "~/components/PayButton";
  import Spinner from "~/components/Spinner";
  import OrderCancelPopup from "~/components/OrderCancelPopup";
  import PriceFormat from "~/components/PriceFormat";
  import OrderedStatus from "../../../components/OrderedStatus";

  export default {
    middleware: ['auth'],
    head() {
      return {
        title: 'Order',
        meta: []
      }
    },
    data() {
      return {
        cancelPopup: false,
        fetchingOrderData: false,
        rateProductId: 0,
        countryList: countryList,
      }
    },
    components: {
      OrderedStatus,
      PriceFormat,
      OrderCancelPopup,
      Spinner,
      PayButton,
      LazyImage,
      RatePopup,
      AccountLayout
    },
    mixins: [util, metaHelper],
    computed: {
      cancellationBtnText() {
        return this.orderCancelled ? this.$t('order.cancellationMessage') : this.$t('order.cancelOrder')
      },
      isDelivered() {
        return parseInt(this.ordered?.status) === this.orderStatusIn.DELIVERED
      },
      refunded() {
        return parseInt(this.ordered?.cancellation?.refunded) === this.status.PUBLIC || false
      },
      orderCancelled() {
        return parseInt(this.ordered.cancelled) === this.status.PUBLIC
      },
      totalPrice() {
        return this.ordered.calculated.total_price
      },
      voucherPrice() {
        return this.ordered.calculated.voucher_price
      },
      bundleOffer() {
        return this.ordered.calculated.bundle_offer
      },
      shippingPrice() {
        return this.ordered.calculated.shipping_price
      },
      taxPrice() {
        return this.ordered.calculated.tax
      },
      subtotalPrice() {
        return this.ordered.calculated.subtotal
      },
      orderId() {
        return parseInt(this.$route.params.id)
      },
      ...mapGetters('order', ['ordered']),
      ...mapGetters('common', ['currencyIcon'])
    },
    methods: {
      orderCancelling() {
        this.cancelPopup = false
        this.fetchingData()
      },
      generateAddress(obj) {
        if (!obj) {
          return ''
        }

        let addArr = []
        addArr.push(obj?.address_1 || '')
        if (obj?.address_2) {
          addArr.push(obj?.address_2)
        }
        addArr.push(obj?.city + '-' + obj?.zip)

        if (this.countryList[obj?.country]) {
          const country = this.countryList[obj?.country]

          if (country.states[obj?.state]) {
            addArr.push(country?.states[obj?.state]?.name)
          }
          addArr.push(country?.name)
        }
        this.ordered['formatted_address'] = addArr.join(', ')
        return this.ordered['formatted_address']
      },
      generatingAttribute(attr) {
        return attr?.updated_inventory?.inventory_attributes?.map(i => {
          return [i?.attribute_value?.attribute?.title, i?.attribute_value?.title]
        })
      },
      async fetchingData() {
        this.fetchingOrderData = true
        try {
          const data = await this.getOrderByUser({order_id: this.orderId, time_zone: this.timeZone})
          if (data?.status !== 200) {
            this.hasError(data)
          }
        } catch (e) {
          return this.$nuxt.error(e)
        }
        this.fetchingOrderData = false
      },
      ...mapActions('common', ['setToastMessage', 'setToastError']),
      ...mapActions('order', ['getOrderByUser', 'cancelOrder']),
    },
    async mounted() {
      await this.fetchingData()
    },
    async asyncData({store, error}) {
      try {
        if(!store.state.common.paymentGateway){
          const data = await store.dispatch('common/getRequest', {
            params: {},
            api: 'paymentGateway'
          })

          store.commit('common/SET_PAYMENT_GATEWAY', data.data)
        }
      } catch (e) {
        error(e)
      }
    },
  }
</script>

<style>

</style>
