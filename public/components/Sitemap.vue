<template>
  <div class="container-fluid">

   <breadcrumb
    :page="pageTitle"
   />


    <h1>{{ $t('sitemap.find') }}</h1>
    <p class="mtb-10 f-12">{{ $t('sitemap.stock') }}</p>

    <div class="site-map">
      <sitemap-item
        :categories="categoriesLeft"
      />
      <sitemap-item
        :categories="categoriesRight"
      />
    </div>
</div>

</template>

<script>
  import SitemapItem from "~/components/SitemapItem";
  import Breadcrumb from "~/components/Breadcrumb";

  export default {
    name: 'Sitemap',
    components: {Breadcrumb, SitemapItem},
    data() {
      return {

      }
    },
    mixins: [],
    watch: {},
    props: {
      pageTitle: {
        type: String,
        default: ''
      },
      categories: {
        type: Array,
        default() {
          return []
        }
      }
    },
    computed: {
      middleIndex(){
        return this.categories.length/2 - 2
      },
      categoriesRight(){
        return  this.categories.slice(this.middleIndex + 1);
      },
      categoriesLeft(){
        return this.categories.slice(0, this.middleIndex);

      }

    },
    methods: {
    }
  }
</script>
<style lang="stylus">
</style>
