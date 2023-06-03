<template>
  <client-only>
    <listing-layout
      :result-title="slug"
      :product-params="productParams"
    />
  </client-only>
</template>

<script>
  import ListingLayout from "~/components/ListingLayout"
  import util from '~/mixin/util'
  import metaHelper from '~/mixin/metaHelper'
  export default {
    head(){
      return {
        title: this.slugToText(this.slug),
        description: 'description'
      }
    },
    components: {
      ListingLayout
    },
    mixins: [util, metaHelper],
    computed: {
      slug() {
        return this.slugToText(this.$route?.params?.main)
      },
      productParams(){
        return this.$route.query
      }
    },
    mounted() {
    }
  }
</script>
