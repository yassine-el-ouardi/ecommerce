<template>
  <div>
    <div v-if="product">
      <div
        class="detail-menu hide-sm"
        v-if="currentCategories && currentCategories.length"
      >
        <div class=" container-fluid">
          <div class="mlr--15">
            <nuxt-link
              v-for="(value, i) in currentCategories"
              :title="value.title"
              :to="subCategoryLink(value, category)"
              :key="i"
            >
              {{ value.title }}
            </nuxt-link>
          </div>
        </div>
      </div>
      <div class="container-fluid mtb-15 mt-sm-10 mn-h-400x">
        <div>
          <breadcrumb
            class="mb-20 mb-sm-15"
            :slugs="preparedSlug"
            :page="productTitle"
          />
          <div class="product-detail">
            <div class="detail-left pr-30 pr-sm-0">
              <div class="flex start align-start block-md">
                <div class="product-main">
                  <div class="detail-image-wrapper">
                    <div
                      class="detail-image-inner"
                      :class="{'z-2': imagePopup}"
                    >
                      <product-images
                        v-if="productImage || productImageList"
                        :title="productTitle"
                        :product="product"
                        :main-image="productImage"
                        :images="productImageList"
                        @image-popup="imagePopup = $event"
                        @add-to-wishlist="$refs.detailRight.wishListAction()"
                      />
                    </div>
                  </div>

                  <div class="pl-30 pl-md grow">
                    <h1 class="f-16">
                      {{ productTitle }}
                    </h1>
                    <div class="mt-10">
                      <rating-star
                        :rating="parseFloat(productRating)"
                      />
                      <span
                        class="f-10 ml-5 semi-bold color-lite">
                        {{ reviewCount }} {{ $t('productReview.reviews') }}
                      </span>
                    </div>

                    <div class="devider w-md-100 mtb-15">&nbsp;</div>

                    <div
                      v-if="endTime"
                      class="flex sided warning-msg ptb-10 plr-15 mb-15 wrap"
                    >
                      <h5 class="color-inherit mr-10">
                        {{ $t('product.shocking') }}
                      </h5>
                      <div class="flex">
                        <h5 class="mr-10 color-inherit">
                          {{ $t('product.endsIn') }}
                        </h5>
                        <b>
                          <countdown
                            :time-zone="product.time_zone"
                            :end-time="endTime"
                          />
                        </b>
                      </div>
                    </div>
                    <h4
                      class="mb-15 bold"
                      :class="[{'color-success': isInStock}, {'color-danger': !isInStock}]"
                    >
                      {{ inStock }}
                    </h4>
                    <div
                      v-if="vouchers && vouchers.length"
                      class="two-sided mb-15 ">
                      <h6 class="left">
                        {{ $t('accountLayout.vouchers') }}
                      </h6>
                      <div class="pos-rel ">
                        <div
                          class="right mlr--2-5 cp"
                          data-ignore="voucher-pop-over"
                          @click.passive="voucherPopOver = !voucherPopOver"
                        >
                          <span
                            v-for="(value, index) in vouchers"
                            :key="index"
                            class="no-click info-msg ptb-5 mlr-2-5 mb-5"
                          >
                            {{ getPriceType(value) }} {{$t('detailRight.off')}}
                          </span>
                        </div>
                        <pop-over
                          v-if="voucherPopOver"
                          title="Shop Vouchers"
                          @close="closeVoucherPopOver"
                          elem-id="voucher-pop-over"
                          :layer="false"
                        >
                          <template v-slot:content>
                            <vouchers
                              ref="voucherPagination"
                              :changing-route="false"
                            />
                          </template>
                        </pop-over>
                      </div>
                    </div>

                    <div
                      v-if="bundleDeal"
                      class="two-sided mb-15">
                      <h6 class="left">
                        {{ $t('product.bundleDeal') }}
                      </h6>
                      <div class="right bundle-deal">
                        {{ bundleDeal.title }}
                      </div>
                    </div>

                    <div
                      v-if="brand"
                      class="two-sided mb-15">
                      <h6 class="left">
                        {{ $t('product.brand') }}
                      </h6>
                      <div class="right">
                        <nuxt-link
                          class="link"
                          :to="brandLink(product.brand)"
                        >
                          {{ brand }}
                        </nuxt-link>
                      </div>
                    </div>

                    <div
                      class="two-sided mb-15 align-start">
                      <h6 class="left">
                        {{ $t('product.refundWarranty') }}
                      </h6>
                      <div class="right">
                        <div class="mb-5">

                          <template v-if="refundable(product)">
                            <div>{{ $t('productHelper.refundable') }}</div>
                            <div class="mb-10 mt-5 block color-lite">{{ $t('productHelper.mindChange') }}</div>
                          </template>
                          <template v-else>
                            {{ this.$t('productHelper.notRefundable') }}
                          </template>
                        </div>

                        <div v-if="product.warranty">{{ warranty(product) }}</div>
                        <div class="mt-5">
                          {{ $t('product.authentic') }}
                        </div>
                      </div>
                    </div>

                    <div
                      class="editor mt-30 mt-sm-15"
                      v-dompurify-html="overview"
                    />
                  </div><!-- plr-30 grow -->
                </div><!-- flex -->
              </div>
              <client-only>
                <div
                  class="ellipsis-para editor mt-30 mt-sm-15"
                  :class="{'expanded': descriptionExpand}"
                  v-dompurify-html="description"
                />
                <button
                  @click.prevent="descriptionToggle"
                  aria-label="Read less"
                  class="link mt-15 mb-5"
                >
                  {{ descriptionExpand ? $t('product.readLess') : $t('product.readMore') }}
                </button>
              </client-only>
            </div>
            <!-- product-detail -->

            <detail-right
              ref="detailRight"
              :disabled="!statusPublic"
              :product="product"
              @option-changed="optionChanged"
            />
          </div><!-- product-detail -->
        </div>

      </div><!-- container-fluid mtb-15 -->

      <client-only>
        <div
          :class="{'mx-h-0': !hasReview, 'review-loaded': !reviewLoaded}"
          class="container-fluid suggested-container mn-h-400x"
        >
          <lazy-area
            v-slot:default="{renderArea}"
          >
            <product-review
              v-if="renderArea"
              :id="product.id"
              class="b-t pt-20 pt-sm-15  "
              @has-review="fetchedReview"
            />
          </lazy-area>
        </div>


        <div
          class="container-fluid suggested-container mn-h-400x"
        >
          <lazy-area
            v-slot:default="{renderArea}"
          >
            <suggested-products
              v-if="renderArea"
              :product-id="productId"
            />
          </lazy-area>
        </div>
      </client-only>


    </div>
  </div>
</template>

<script>

  import {mapGetters, mapActions} from 'vuex'
  import util from '~/mixin/util'
  import productPriceHelper from '~/mixin/productPriceHelper'
  import metaHelper from '~/mixin/metaHelper'
  import productHelper from '~/mixin/productHelper'
  import ProductImages from '~/components/ProductImages'
  import DetailRight from '~/components/DetailRight'
  import LazyArea from '~/components/LazyArea'
  import SuggestedProducts from '~/components/SuggestedProducts'
  import ProductReview from '~/components/ProductReview'
  import moment from 'moment-timezone'
  import DOMPurify from 'dompurify';
  import Vouchers from "../../../components/Vouchers";
  import PopOver from "../../../components/PopOver";
  import Countdown from "../../../components/Countdown";
  import RatingStar from "../../../components/RatingStar";
  import Breadcrumb from "../../../components/Breadcrumb";

  export default {
    head() {
      return {
        title: this.product?.meta_title,
        meta: [
          this.generatingMeta('description', this.product?.meta_description),
          this.generatingMeta('og:image', this.imageURL(this.product)),
          this.generatingMeta('og:title', this.product?.meta_title),
          this.generatingMeta('og:description', this.product?.meta_description)
        ],
        link: [
          {
            rel: 'preload',
            as: 'image',
            href: this.getThumbImageURL(this.productImage)
          },
        ],

      }
    },
    async asyncData({store, route, $auth, error}) {
      try {
        await store.dispatch('detail/fetchProduct', {
          id: route.params.id,
          user_id: $auth?.user?.id || ''
        })
      } catch (e) {
        error(e)
      }
    },
    data() {
      return {
        descriptionExpand: false,
        optionChange: false,
        productInventory: null,
        imagePopup: false,
        hasReview: true,
        reviewLoaded: true,
        activatedPage: false,
        voucherPopOver: false,
      }
    },
    components: {
      Breadcrumb,
      RatingStar,
      Countdown,
      PopOver,
      Vouchers,
      ProductImages,
      LazyArea,
      SuggestedProducts,
      DetailRight,
      ProductReview
    },
    mixins: [util, metaHelper, productHelper, productPriceHelper],
    computed: {
      description() {
        return this.product?.description || null
      },
      overview() {
        return this.product?.overview || null
      },
      reviewCount() {
        return this.product?.review_count || 0
      },
      productRating() {
        return this.product?.rating || 0
      },
      productImage() {
        return this.product?.image || null
      },
      productImageList() {
        return this.product?.images || null
      },
      timeDifference() {
        const len = this.product.id.toString()?.length
        let highest = ''
        for (let i = 1; i <= len; i++) {
          highest += '9'
        }
        return ((this.product.id / highest) * 100).toFixed(2)
      },
      endTime() {
        return this.product?.end_time || null
      },
      productId() {
        return this.$route.params.id
      },
      statusPublic() {
        return parseInt(this.product?.status) === 1
      },
      category() {
        return this.product?.category
      },
      currentCategories() {
        return this.product?.current_categories
      },
      productTitle() {
        return this.product?.title || ''
      },
      preparedSlug() {
        const slug = []
        this.productSlug.forEach(i => {
          if (i.category_id) {
            slug.push({
              title: i.title,
              link: this.subCategoryLink(i, i.category)
            })
          } else {
            slug.push({
              title: i.title,
              link: this.categoryLink(i)
            })
          }
        })
        return slug
      },
      productSlug() {
        return this.product?.slug
      },
      bundleDeal() {
        return this.product?.bundle_deal
      },
      productPrice() {
        if (this.productInventory?.price > 0) {
          return this.productInventory?.price
        }
        return this.product.price > 0
          ? this.product.price : this.product.offered > 0
            ? this.product.offered : this.product.selling
      },
      isInStock() {
        return this.optionChange ? this.productInventory?.quantity > 0 : this.product.in_stock
      },
      inStock() {
        return this.isInStock ? this.$t('detail.inStock') : this.$t('detail.outOfStock')
      },
      vouchers() {
        return this.product?.vouchers;
      },
      brand() {
        return this.product?.brand?.title || ''
      },
      ...mapGetters('common', ['currencyIcon', 'currencyPosition']),
      ...mapGetters('detail', ['product']),
    },
    methods: {
      descriptionToggle() {
        this.descriptionExpand = !this.descriptionExpand
      },
      closeVoucherPopOver() {
        this.voucherPopOver = false
      },
      fetchedReview(evt) {
        this.hasReview = !!evt
        this.reviewLoaded = !!!evt
      },
      optionChanged(evt) {
        this.optionChange = true
        this.productInventory = evt
      },
      qty(direction) {
        if (this.quantity + direction === 0) {
          return
        }
        this.quantity += direction
      },
      ...mapActions('common', ['fetchLocation']),
      ...mapActions('detail', ['emptySuggestedProducts', 'fetchProduct']),
      ...mapActions('user', ['emptyVoucher']),
    },
    beforeDestroy() {
      document.body.classList.remove('detail-page')
    },
    async mounted() {
      this.emptyVoucher()
      this.emptySuggestedProducts()

      document.body.classList.add('detail-page')
    }
  }
</script>

