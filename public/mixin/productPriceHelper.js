export default {
  data() {
    return {
    }
  },
  computed: {
    reducedPercent() {
      return 100 - parseInt(((this.currentPricing / this.prevPrice) * 100).toString())
    },
    flashPrice(){
      return this.product?.price !== null ? parseFloat(this.product?.price) : null
    },
    sellPrice(){
      return parseFloat(this.product?.selling || 0)
    },
    offeredPrice(){
      return parseFloat(this.product?.offered || 0)
    },
    prevPrice(){
      return parseFloat(this.offeredPrice > 0 ? this.sellPrice : this.flashPrice ? this.sellPrice : 0)
    },
    currentPricing(){
      return parseFloat(this.flashPrice !== null ? this.flashPrice
        : this.offeredPrice > 0 ? this.offeredPrice
          : this.sellPrice)
    },
    inventoryPrice(){
      return parseFloat(this.inventory?.price) || 0
    },
    currentInventoryPrice(){
      return parseFloat(this.inventoryPrice > 0 ? this.inventoryPrice : this.currentPricing)
    },
  },
  methods:{
    flashPriceCalc(product){
      return product?.end_time ? product?.price ?? null : null
    },
    offeredPriceCalc(product){
      return parseFloat(product?.offered || 0)
    },
    currentPricingCalc(product){
      const flashPrice = this.flashPriceCalc(product)

      return flashPrice !== null
        ? flashPrice : this.offeredPriceCalc(product) > 0
          ? this.offeredPriceCalc(product) : parseFloat(product?.selling || 0)
    },
    currentInventoryPriceCalc(inventory, product){
      return parseFloat(inventory?.price || 0) > 0 ? (inventory?.price || 0) : this.currentPricingCalc(product)
    },
  }
}
