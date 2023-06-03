<template>
  <category-listing-layout
    type="brand"
  />
</template>
<script>
  import CategoryListingLayout from "../components/CategoryListingLayout";
  export default {
    components: {
      CategoryListingLayout
    },
    head(){
      return {
        title: 'Brands',
        description: 'description'
      }
    },
    data() {
      return {
      }
    },
    mixins: [],
    computed: {
    },
    methods: {
    },
    async mounted() {
    }
  }
</script>
