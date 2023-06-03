<template>
   <router-link
     v-if="isPublic"
     @click.native="$emit('clicked')"
     :to="sourceUrl(banner, 'banner')"
     class="block banner-wrapper"
   >
     <img
       :src="imageURL(banner)"
       :alt="banner.title"
       class="block"
       height="200"
       width="10"
     >
     <button
       v-if="closable"
       aria-label="close"
       class="btn-banner-close"
       @click.prevent="$emit('close')"
     >
       <i class="icon close-icon"/>
     </button>
   </router-link>
</template>

<script>

  import util from '~/mixin/util'
  export default {
    name: 'Banner',
    data() {
      return {
      }
    },
    watch: {
    },
    props: {
      banner: {
        type: Object,
        default() {
          return null
        },
      },
    },
    components: {


    },
    computed: {
      isPublic(){
        return parseInt(this.banner?.status) === this.status.PUBLIC
      },
      closable(){
        return parseInt(this.banner?.closable) === this.status.PUBLIC
      }
    },
    mixins: [util],
    methods: {

    },
    created() {
    },
    async mounted() {
    }
  }
</script>

