import Service from '@/services/service.js'

const state = () => ({
  orderedList: [],
  ordered: {}
})
const getters = {
  orderedList: ({ orderedList }) => orderedList,
  ordered: ({ ordered }) => ordered
}
const mutations = {
  SET_ORDERED_LIST (state, order) {
    state.orderedList = order
  },
  SET_ORDERED (state, order) {
    state.ordered = order
  },
  EMPTY_ORDERED (state) {
    state.ordered = {}
  },
  ORDER_ACTION (state, order) {
    state.orderedList.push(order)
  }
}

const actions = {
  async voucherValidity ({ commit }, payload) {
    const {data} = await Service.voucherValidity(payload, this.$auth.strategy.token.get())
    return data
  },
  async getOrderByUser ({ commit }, payload) {
    commit('EMPTY_ORDERED')
    const {data} = await Service.orderByUser(payload, this.$auth.strategy.token.get())
    if(data?.status === 200){
      if(!payload.order_id){
        commit('SET_ORDERED_LIST', data.data)
      }else {
        commit('SET_ORDERED', data.data)
      }
    }
    return data
  },
  async orderAction ({ commit, dispatch }, payload) {
    const {data} = await Service.orderAction(payload, this.$auth.strategy.token.get())
    return data
  },
  async paymentDone ({ commit, dispatch }, payload) {
    const {data} = await Service.paymentDone(payload, this.$auth.strategy.token.get())
    return data
  },
  async cancelOrder ({ commit, dispatch }, payload) {
    const {data} = await Service.cancelOrder(payload, this.$auth.strategy.token.get())
    return data
  },
  async cancellationFind({ commit, dispatch }, payload) {
    const {data} = await Service.cancellationFind(payload, this.$auth.strategy.token.get())
    return data
  },
  async ratingReviewAction ({ commit, dispatch }, payload) {
    const {data} = await Service.ratingReviewAction(payload, this.$auth.strategy.token.get())
    return data
  },
  async ratingReviewFind({ commit, dispatch }, payload) {
    const {data} = await Service.ratingReviewFind(payload, this.$auth.strategy.token.get())
    return data
  },
  async sendOrderEmail({ commit, dispatch }, payload) {
    const {data} = await Service.sendOrderEmail(payload, this.$auth.strategy.token.get())
    return data
  }

}

export {
  state,
  getters,
  mutations,
  actions
}
