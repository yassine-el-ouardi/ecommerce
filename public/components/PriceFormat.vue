<template>
  <span>{{formattedPrice}}</span>
</template>

<script>

  import {mapGetters} from "vuex";
  import util from "~/mixin/util"
  export default {
    name: 'PriceFormat',
    data() {
      return {

      }
    },
    props: {
      price: {
        default: 0,
        required: true
      }
    },
    mixins: [util],
    components: {},
    computed: {
      formattedPrice(){
        if(parseInt(this.currencyPosition) === this.currencyPositionsIn.PRE) {
          return this.currencyIcon + this.price
        }
        return this.price + this.currencyIcon
      },
      ...mapGetters('common', ['currencyIcon', 'currencyPosition', 'currency'])
    },
    methods: {

    },
    mounted() {
    }
  }
</script>

