<template>
  <account-layout
    active-route="wishlists"
    @clicked-wishlists="loadData"
    class="flow-hidden"
  >
    <template v-slot:rightArea>

      <transition name="fade" mode="out-in">
        <div
          class="spinner-wrapper flex"
          v-if="fetchingWishlistData"
        >
          <spinner
            :radius="100"
          />
        </div>
      </transition>

      <div
        v-if="currentWishlists && !currentWishlists.length"
        class="info-msg"
      >
        {{ $t('wishlist.noWishlist') }}
      </div>

      <div
        v-else
        class="area"
      >
        <div class="area-content">
          <div class="tile-container">
            <div class="shimmer-wrapper pt-15" v-if="fetchingWishlistData">
              <tile-shimmer
                v-for="index in 8"
                :key="index"
              />
            </div>
            <template v-else>
              <product-tile
                v-for="(value, index) in currentWishlists"
                :key="index"
                :product="value.product"
              />
            </template>
          </div>
        </div>
      </div>

      <pagination
        class="mt-20 mt-sm-15"
        ref="wishlistPagination"
        :total-page="totalPage"
        @fetching-data="fetchingData"
      />

    </template>
  </account-layout>
</template>

<script>

  import {mapGetters, mapActions} from 'vuex'
  import util from '~/mixin/util'
  import LazyImage from '~/components/LazyImage'
  import RatePopup from '~/components/RatePopup'
  import AccountLayout from '~/components/AccountLayout'
  import ProductTile from '~/components/ProductTile'
  import routeParamHelper from '~/mixin/routeParamHelper'
  import Pagination from '~/components/Pagination'
  import productHelper from '~/mixin/productHelper'
  import Spinner from "../../components/Spinner";
  import TileShimmer from "../../components/TileShimmer";

  export default {
    middleware: ['auth'],
    head() {
      return {
        title: 'Wishlists',
        meta: []
      }
    },
    data() {
      return {

        fetchingWishlistData: false
      }
    },
    watch: {},
    components: {
      TileShimmer,
      Spinner,
      LazyImage,
      RatePopup,
      AccountLayout,
      Pagination,
      ProductTile
    },
    mixins: [util, routeParamHelper, productHelper],
    computed: {
      totalPage() {
        return this.allWishlist?.last_page
      },
      currentWishlists() {
        return this.allWishlist?.data
      },
      ...mapGetters('user', ['allWishlist']),
      ...mapGetters('common', ['currencyIcon'])
    },
    methods: {
      async loadData() {
        this.$refs.wishlistPagination.routeParam()
      },
      async fetchingData() {
        this.fetchingWishlistData = true
        setTimeout(async () => {

          try {
            this.settingRouteParam()

            const data = await this.userWishlistAll({
              time_zone: this.timeZone,
              order_by: this.orderBy,
              type: this.orderByType,
              page: this.page,
              q: this.search
            })
            if (data?.status !== 200) {
              this.hasError(data)
            }
          } catch (e) {
            return this.$nuxt.error(e)
          }
          this.fetchingWishlistData = false
        }, 100)

      },
      ...mapActions('user', ['userWishlistAll']),
    },

    async mounted() {
      await this.fetchingData()
    },
  }
</script>

<style>

</style>
