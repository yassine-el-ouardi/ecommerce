<template>
  <div
    class="flex wrap static-container home-section"
  >
    <div class="static-area flex">
      <i
        class="icon shipping-icon "
      />
      <div>
        <h4 class="bold mb-5">{{ $t('home.rapid_shipping') }}</h4>
        <span class="color-lite ">{{ $t('home.shortPeriod') }}</span>
      </div>
    </div>

    <div class="static-area flex">
      <i
        class="icon card-icon"
      />
      <div>
        <h4 class="bold mb-5">{{ $t('home.secureTransaction') }}</h4>
        <span class="color-lite">{{ $t('home.checkoutSecurely') }}</span>
      </div>
    </div>

    <div class="static-area flex">
      <i
        class="icon support-icon "
      />
      <div>
        <h4 class="bold mb-5">{{ $t('home.support') }}</h4>
        <span class="color-lite">{{ $t('home.pickupCalls') }}</span>
      </div>
    </div>

    <div class="static-area flex">
      <i
        class="icon bundle-icon "
      />
      <div>
        <h4 class="bold mb-5">{{ $t('home.bundleOffer') }}</h4>
        <span class="color-lite">{{ $t('home.onProducts') }}</span>
      </div>
    </div>

  </div>

</template>

<script>
  export default {
    name: 'StaticSection',
    data() {
      return {

      }
    },
    watch: {


    },
    props: {

    },
    components: {

    },
    computed: {

    },
    mixins: [],
    methods: {

    },
    created() {
    },
    async mounted() {
    }


  }
</script>

