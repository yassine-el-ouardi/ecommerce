import Service from '@/services/service.js'

const state = () => ({
  profile: null,
  vouchers: null,
  allWishlist: null,
  allAddress: []
})
const getters = {
  profile: ({ profile }) => profile,
  vouchers: ({ vouchers }) => vouchers,
  allWishlist: ({ allWishlist }) => allWishlist,
  allAddress: ({ allAddress }) => allAddress
}
const mutations = {
  EMPTY_VOUCHER (state) {
    state.vouchers = null
  },
  SET_PROFILE(state, profile) {
    state.profile = profile
  },
  UPDATE_VOUCHERS (state, vouchers) {
    state.vouchers = vouchers
  },
  ALL_WISHLIST (state, allWishlist) {
    state.allWishlist = allWishlist
  },
  ALL_ADDRESS (state, allAddress) {
    state.allAddress = allAddress
  },
  UPDATE_ADDRESS (state, address) {
    const index = state.allAddress.data.findIndex(obj=>{
      return parseInt(obj.id) === parseInt(address.id)
    })
    if(index > -1){
      state.allAddress.data.splice(index, 1, address)
    }else{
      state.allAddress.data.unshift(address)
    }
  }
}

const actions = {
  async login ({ commit }, params) {
    const {data} = await Service.userLogin(params)
    return data
  },
  async registration ({ commit }, params) {
    const {data} = await Service.userRegistration(params)
    return data
  },
  async verify ({ commit }, params) {
    const {data} = await Service.userVerification(params)
    return data
  },
  async forgotPassword ({ commit }, params) {
    const {data} = await Service.userForgotPassword(params)
    return data
  },
  async updatePassword ({ commit }, params) {
    const {data} = await Service.userUpdatePassword(params)
    return data
  },
  setProfile({ commit }, params) {
    commit('SET_PROFILE', params?.user)
  },
  async getProfile ({ commit }) {
    const {data} = await Service.getProfile(this.$auth.strategy.token.get())
    if(data?.status === 200){
      commit('SET_PROFILE', data.data)
    }
    return data
  },
  async userVouchers ({ commit }, params) {
    const {data} = await Service.userVouchers(params, this.$auth.strategy.token.get())
    if(data?.status === 200){
      commit('UPDATE_VOUCHERS', data.data)
    }
    return data
  },
  async userWishlistAll ({ commit }, params) {
    const {data} = await Service.userWishlistAll(params, this.$auth.strategy.token.get())
    if(data?.status === 200){
      commit('ALL_WISHLIST', data.data)
    }
    return data
  },
  async userAddressAll ({ commit }, params) {
    const {data} = await Service.userAddressAll(params, this.$auth.strategy.token.get())
    if(data?.status === 200){
      commit('ALL_ADDRESS', data.data)
    }
    return data
  },
  async userAddressAction ({ commit }, params) {
    const {data} = await Service.userAddressAction(params, this.$auth.strategy.token.get())
    if(data?.status === 200){
      commit('UPDATE_ADDRESS', data.data)
    }
    return data
  },
  async userAddressDelete ({ commit }, id) {
    const {data} = await Service.userAddressDelete(id, this.$auth.strategy.token.get())
    return data
  },
  async updateUserPassword ({ commit }, params) {
    const {data} = await Service.updateUserPassword(params, this.$auth.strategy.token.get())
    return data
  },
  async updateProfile ({ commit }, params) {
    const {data} = await Service.updateProfile(params, this.$auth.strategy.token.get())
    return data
  },
  emptyVoucher ({ commit }) {
    commit('EMPTY_VOUCHER')
  },
}

export {
  state,
  getters,
  mutations,
  actions
}
