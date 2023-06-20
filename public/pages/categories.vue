<template>
  <category-listing-layout
    :sub-categories-map="subCategoriesMap"
    type="subCategory"
  />
</template>
<script>
  import { mapGetters, mapActions } from 'vuex'
  import CategoryListingLayout from "../components/CategoryListingLayout";
  export default {
    components: {
      CategoryListingLayout
    },
    head(){
      return {
        title: 'Categories',
        description: 'description'
      }
    },
    data() {
      return {
      }
    },
    mixins: [],
    computed: {
      ...mapGetters('common', ['subCategoriesMap', 'categories'])
    },
    methods: {
      ...mapActions('common', ['setSubCatMap', 'getRequest'])
    },
    async mounted() {
      if(!this.subCategoriesMap){
        this.setSubCatMap(this.categories)
      }
    }
  }
</script>
