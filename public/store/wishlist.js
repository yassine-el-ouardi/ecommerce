import Service from '@/services/service.js'

const state = () => ({

})
const getters = {

}
const mutations = {
}

const actions = {
  async userWishlistAll ({ commit }, params) {
    const {data} = await Service.userWishlistAll(params, this.$auth.strategy.token.get())
    return data
  },
  async userWishlistAction ({ commit }, params) {
    const {data} = await Service.userWishlistAction(params, this.$auth.strategy.token.get())
    commit('detail/UPDATE_WISHLIST', data, {root: true})
    return data
  },
}

export {
  state,
  getters,
  mutations,
  actions
}
