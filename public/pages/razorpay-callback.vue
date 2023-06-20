<template>
  <client-only>
  <div class="container">
    <section class="home-section">
      <h3>{{ $t('razorpayCallback.wait') }}...</h3>
    </section>
  </div>
  </client-only>
</template>
<script>
  export default {
    middleware: ['auth'],
    data() {
      return {
      }
    },
    components: {

    },
    mixins: [],
    computed: {

    },
    methods: {
    },
    mounted(){
    },
  }
</script>

<style>

</style>
