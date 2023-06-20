<template>
  <div :class="routeName">
    <transition
      name="fade"
      mode="out-in"
    >
      <div
        v-if="popupWrapperVisible"
        class="popup-banner-wrapper"
      >
        <banner
          class="popup-banner"
          :banner="popupBanner"
          v-outside-click="bannerClosed"
          @close="closedPermanently"
          @clicked="closedPermanently"
        />
      </div>
    </transition>
    <Header
      @going-next="goingNext"
    />
    <main>
      <Nuxt
        :nuxt-child-key="componentId"
      />
    </main>
    <Footer/>

    <transition name="fade" mode="out-in">
      <toast-message
        v-if="toastMessageStatus"
        :is-error="toastError"
        @hide="hideToast"
        :message="toastMessage"
      />
    </transition>
  </div>
</template>

<script>
  import Header from '~/layouts/partials/Header.vue'
  import Footer from '~/layouts/partials/Footer.vue'
  import ToastMessage from '~/components/ToastMessage.vue'
  import {mapState, mapGetters, mapActions} from 'vuex'
  import outsideClick from '~/directives/outside-click'
  import Banner from "~/components/Banner";
  import metaHelper from "~/mixin/metaHelper";

  export default {
    head() {
      return this.commonMeta(this.site_setting)
    },
    mixins: [metaHelper],
    data() {
      return {

        componentId: null,
        loading: false,
        scrollPosition: 0,
        isPopupWrapperVisible: false
      }
    },
    components: {
      Banner,
      Header,
      Footer,
      ToastMessage
    },
    directives: {
      outsideClick
    },
    watch: {},
    computed: {
      popupWrapperVisible() {
        return this.isPopupWrapperVisible && parseInt(this.popupBanner?.status) === 1
      },
      routeName() {
        return this.$route?.name?.split('___')[0] || 'error'
      },
      ...mapState('common', ['site_setting']),
      ...mapGetters('user', ['profile']),
      ...mapGetters('error', ['error']),
      ...mapGetters('common', ['popupBanner', 'toastMessage', 'toastError', 'toastMessageStatus']),
    },
    methods: {
      goingNext({id, url}) {
        this.componentId = id + '-' + Date.now()
        this.$router.push({path: url})
      },
      closedPermanently() {
        localStorage.setItem('popupBannerClosed', true)
        this.bannerClosed()
      },
      bannerClosed() {
        this.isPopupWrapperVisible = false
        document.body.classList.remove('no-scroll')
      },
      ...mapActions('common', ['hideToast', 'getRequest']),
      ...mapActions('error', ['emptyError']),
      ...mapActions('user', ['setProfile']),
    },
    async mounted() {
      if (this.site_setting?.primary_color) {
        document.documentElement.style.setProperty('--primary-color', this.site_setting.primary_color)
        document.documentElement.style.setProperty('--primary-hover-color', this.site_setting.primary_hover_color)
      }
      if (!this.profile) {
        if(!process.env.apiBase.trim()){
          this.$axios.defaults.baseURL = window.location.origin + '/'
        }

        try {
          this.loading = true
          const data = await this.getRequest({params: {}, api: 'profile', requiredToken: true})

          if (data?.status === 200) {
            await this.$auth.setUser(data.data)
            this.setProfile(data)
          }

          this.loading = false
        } catch (e) {
          await this.$auth.logout()
          this.loading = false
          return this.$nuxt.error(e)
        }
      }

      if (!localStorage.getItem('popupBannerClosed', false) && this.popupBanner) {
        document.body.classList.add('no-scroll')
        this.isPopupWrapperVisible = true
      }
    }
  }
</script>
<style lang="stylus">
  main
    min-height 500px

  .error
    main
      min-height 300px


  @media only screen and (max-width: 768px)
    main
      min-height 400px
</style>
