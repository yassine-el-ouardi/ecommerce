<template>
  <listing-layout
    :product-params="productParams"
  />
</template>

<script>
  import ListingLayout from "~/components/ListingLayout"
  import util from '~/mixin/util'
  import metaHelper from '~/mixin/metaHelper'
  export default {
    head(){
      return {
        title: this.slugToText(this.$route?.params?.main || ''),
        description: 'description'
      }
    },
    mixins: [util, metaHelper],
    components: {
      ListingLayout
    },
    computed: {
      productParams(){
        return {'brand': this.$route?.params?.id}
      }
    },
    mounted() {
    }
  }
</script>
