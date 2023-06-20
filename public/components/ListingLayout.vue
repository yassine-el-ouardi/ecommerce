<template>
  <product-list
    ref="productList"
    :products="products"
    :result-title="resultTitle"
    :product-params="productParams"
    :fetching-product-data="fetchingProductData"
    @fetch-data="fetchingData"
  />

</template>

<script>
  import listingParams from '~/mixin/listingParams'
  import {mapGetters, mapActions} from 'vuex'

  import ProductList from "./ProductList";

  export default {
    name: 'ListingLayout',
    data() {
      return {
        products: null,
        fetchingProductData: false,
      }
    },
    mixins: [listingParams],
    watch: {},
    props: {
      productParams: {
        type: Object,
        default() {
          return {}
        }
      },
      resultTitle: {
        type: String,
        default: ''
      },
    },
    components: {
      ProductList
    },
    computed: {
      ...mapGetters('listing', ['brands', 'shippingRules', 'collections']),
    },
    methods: {
      clearQuery() {
        this.$refs.productList.clearQuery()
      },
      async fetchingData() {
        this.fetchingProductData = true

          setTimeout(async () => {
            try {


              const data = await this.getRequest({
                params: {
                  ...this.productParams,
                  ...{
                    sortby: this.sortByData,
                    shipping: this.shippingFromRoute,
                    brand: this.brandFromRoute,
                    collection: this.collectionFromRoute,
                    rating: this.ratingFromRoute,
                    max: this.maxPriceFromRoute,
                    min: this.minPriceFromRoute,
                    q: this.searchedKeyword,
                    page: this.pageData,
                    sidebar_data: !this.brands || !this.shippingRules || !this.collections
                  }
                },
                api: 'products'
              })


              this.setProducts(data)

              this.products = data.data.result
              this.fetchingProductData = false


            } catch (e) {

              return this.$nuxt.error(e)
            }

          }, 100)


      },
      ...mapActions('listing', ['setProducts', 'emptyProducts']),
      ...mapActions('common', ['getRequest']),
    },
    async mounted() {
      this.emptyProducts()
      await this.fetchingData()
    }
  }
</script>
