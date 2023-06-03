<template>
  <header
    :class="{'no-banner': (topBannerLoaded && isTopBannerClosed) || !isPublic}"
  >
      <banner
        v-if="!isTopBannerClosed"
        class="top-banner"
        :banner="topBanner"
        @close="topBannerClosed"
      />

    <div class="top-wrapper">
      <div class="container-fluid">

        <div class="wrap flex sided">
          <div class="left wrap flex">

            <a
              :href="`mailto:${email}`"
              class="flex"
            >
              <i
                class="icon email-icon mr-5"
              />
              <span><span>{{ $t('home.mail') }}</span> {{ email }}</span>
            </a>
            <span>|</span>
            <a
              :href="`tel:${phone}`"
              class="flex"
            >
              <i
                class="icon phone-icon mr-5"
              />
              <span><span>{{ $t('home.helpline') }}</span> {{ phone }}</span>
            </a>
          </div>

          <div class="flex right text-upper">

            <div class="flex" v-if="!isLoggedIn">
              <nuxt-link
                to="/login"
                class="flex"
              >
                <i
                  class="icon login-icon mr-5"
                />
                {{ $t('header.login') }}
              </nuxt-link>
              <span>|</span>
              <nuxt-link
                to="/register"
                class="flex"
              >
                <i
                  class="icon register-icon mr-5"
                />
                {{ $t('header.register') }}
              </nuxt-link>
            </div>

            <nuxt-link
              v-else
              to="/user/profile"
              class="flex"
            >
              <i
                class="icon user-icon mr-5"
              />
              {{ $t('header.profile') }}
            </nuxt-link>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid flex pos-rel">
      <div class="left-area">
        <nuxt-link
          to="/"
          class="logo"
        >
          <img
            :src="imageURL({'image': site_setting.header_logo})"
            :alt="$t('footer.siteLogo')"
            height="40"
            width="139"
          >
        </nuxt-link>
      </div>

      <form
        class="search-input grow"
        @submit.prevent="search"
      >
        <input
          @focus="openSearchPopup"
          @blur="blurSearchInput"
          type="text"
          :placeholder="$t('header.searchHere')"
          v-model="searchedText"
        >
        <button
          aria-label="submit"
          type="submit"
          class="flex"
        >
          <i
            class="icon search-icon"
          />
        </button>

        <search-popup
          v-if="searchPopup"
          :searched-text="searchedText"
          @close="closeSearchPopup"
        />
      </form>

      <div class="right-area flex right">
        <div
          class="pos-rel"
          v-outside-click="closeDropdown"
        >
          <button
            aria-label="submit"
            class="flex mr-20 mr-sm-10"
            @click="dropdown = !dropdown"
          >
            {{ $t('header.account') }}
            <i
              class="ml-10 icon arrow-down black"
            />
          </button>
          <div
            class="dropdown"
            :class="{active: dropdown}"
          >
            <nuxt-link
              to="/user/orders"
            >
              {{ $t('header.orders') }}
            </nuxt-link>
            <nuxt-link
              to="/user/wishlists"
            >
              {{ $t('header.wishList') }}
            </nuxt-link>
            <nuxt-link
              to="/user/compared"
            >
              {{ $t('header.comparedList') }}
            </nuxt-link>
            <nuxt-link
              to="/user/vouchers"
            >
              {{ $t('header.vouchers') }}
            </nuxt-link>
            <button
              aria-label="Logout"
              v-show="isLoggedIn"
              class="clear-btn"
              @click.prevent="loggingOut"
            >
              {{ $t('header.logout') }}
            </button>
          </div>
        </div>
        <nuxt-link
          to="/cart"
          class="cart-btn flex pos-rel h-40x"
        >
          <span
            v-if="cartCount"
            class="cart-badge">
            {{ cartCount }}
          </span>
          <i
            class="icon cart-icon black mr-5"
          />
          <span class="title">{{ $t('header.cart') }}</span>
        </nuxt-link>
      </div>
    </div>
    <div class="bottom-area text-nowrap">
      <div class="container-fluid">
        <div class="flex sided">
          <div>

            <nuxt-link
              to="/discover/products"
              :custom="true"
            >
              <span
                @click.prevent="$emit('going-next', {
                  id: _uid.toString(),
                  url: '/discover/products'
                })"
              >
                {{ $t('header.products') }}
              </span>
            </nuxt-link>


            <nuxt-link
              to="/categories"
              :custom="true"
            >
              <span
                @click.prevent="$emit('going-next', {
                  id: _uid.toString(),
                  url: '/categories'
                })"
              >
                {{ $t('header.categories') }}
              </span>
            </nuxt-link>


            <nuxt-link
              to="/brands"
              :custom="true"
            >
              <span
                @click.prevent="$emit('going-next', {
                  id: _uid.toString(),
                  url: '/brands'
                })"
              >
                {{ $t('header.brands') }}
              </span>
            </nuxt-link>


            <nuxt-link
              to="/flash-sale"
              :custom="true"
            >
              <span
                @click.prevent="$emit('going-next', {
                  id: _uid.toString(),
                  url: '/flash-sale'
                })"
              >
                {{ $t('header.hotDeals') }}
              </span>
            </nuxt-link>
          </div>

          <div>

            <nuxt-link
              to="/track-order"
            >
              <span>
                {{ $t('header.trackOrder') }}
              </span>
            </nuxt-link>


            <nuxt-link
              to="/page/faq"
            >
              <span>
                {{ $t('header.faq') }}
              </span>
            </nuxt-link>
            <nuxt-link
              to="/page/help"
            >
              <span>
                 {{ $t('header.help') }}
              </span>
            </nuxt-link>

            <nuxt-link
              to="/page/contact"
            >
              <span>
                {{ $t('header.contact') }}
              </span>
            </nuxt-link>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
<script>
  import outsideClick from '~/directives/outside-click'
  import util from '~/mixin/util'
  import { mapGetters, mapActions} from 'vuex'
  import SearchPopup from "../../components/SearchPopup";
  import Banner from "../../components/Banner";

  export default {
    data() {
      return {
        topBannerLoaded: false,
        isTopBannerClosed: true,
        dropdown: false,
        searchPopup: false,
        searchFocused: false,
        searchedText: ''
      }
    },
    computed: {
      isPublic(){
        return parseInt(this.topBanner?.status) === this.status.PUBLIC
      },
      isLoggedIn(){
        return this.$auth?.loggedIn || false
      },
      cartCountCom(){
        return this.$auth?.user?.cart_count
      },
      username(){
        return this.$auth?.user?.name?.split(' ')[0]
      },
      email(){
        return this.setting?.email
      },
      phone(){
        return this.setting?.phone
      },
      ...mapGetters('common', ['site_setting', 'setting', 'topBanner']),
      ...mapGetters('listing', ['searched']),
      ...mapGetters('cart', ['cartCount'])
    },
    watch: {
      cartCountCom(value){
        this.setCartCount(value)
      },
      '$route'() {
        this.setQFromRoute()
        this.closeDropdown()
      },
      searchedText(){
        if(!this.searchPopup && this.searchFocused){
          this.emptySearchedSuggestion()
          this.openSearchPopup()
        }
      }
    },
    directives: {outsideClick},
    components: {Banner, SearchPopup},
    mixins: [util],
    methods: {
      topBannerClosed(){
        localStorage.setItem('topBannerClosed', true)
        this.isTopBannerClosed = true
      },
      openSearchPopup(){
        if(this.searchedText.length > 0){
          this.searchPopup = true
        }
        this.searchFocused = true
      },
      blurSearchInput(){
        this.searchFocused = false
        this.closeSearchPopup()
      },
      closeSearchPopup(){
        setTimeout(() => {
          this.searchPopup = false
        }, 100)
      },
      setQFromRoute(){
        this.searchedText = this.$route?.query?.q || ''
      },
      search(){
        if(this.searchedText && (this.searchedText !== this.searched || this.$route.name !== 'search')){
          this.$router.push({ path: `/search?q=${this.searchedText}`})
          this.updateSearch(this.searchedText)
        }
      },
      async loggingOut(){
        try {
          this.$auth.logout()
          this.closeDropdown()
          this.emptyCartProduct()
        } catch (e) {
          return this.$nuxt.error(e)
        }
      },
      closeDropdown() {
        this.dropdown = false
      },
      ...mapActions('cart', ['emptyCartProduct', 'setCartCount']),
      ...mapActions('listing', ['updateSearch', 'emptySearchedSuggestion']),
    },
    deactivated() {
    },
    activated() {

    },
    mounted() {
      this.setQFromRoute()
      this.updateSearch(this.searchedText)
      if(this.cartCountCom){
        this.setCartCount(this.cartCountCom)
      }

      const self = this
      this.$nextTick(() => {
        if(localStorage.getItem('topBannerClosed') !== null){
          self.isTopBannerClosed = localStorage.getItem('topBannerClosed')
          self.topBannerLoaded = true
        } else {
          self.isTopBannerClosed = false
          self.topBannerLoaded = true
        }
      })
    }
  }
</script>
