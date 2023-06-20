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
    components: {
      ListingLayout
    },
    mixins: [util, metaHelper],
    computed: {
      productParams(){
        return {'collection': this.$route?.params?.id}
      }
    },
    mounted() {
    }
  }
</script>
