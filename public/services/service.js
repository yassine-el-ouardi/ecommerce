import axios from 'axios'
import json from '~/config.json'

const apiBase = !process.env.apiBase.trim() ? window.location.origin + '/' : process.env.apiBase

const apiClient = axios.create({
  baseURL: apiBase + json.api.url,
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type':  'application/json'
  }
})

export default {
  common(){
    return apiClient.get(json.api.common)
  },
  home(params){
    return apiClient.get(json.api.home, {
      params: params
    })
  },
  categories(params){
    return apiClient.get(json.api.categories, {
      params: params
    })
  },
  brands(params){
    return apiClient.get(json.api.brands, {
      params: params
    })
  },
  products(params){
    return apiClient.get(json.api.products, {
      params: params
    })
  },
  flashProducts(params){
    return apiClient.get(`${json.api.flashProducts}/${params.id}`, {
      params: params
    })
  },
  flashSales(){
    return apiClient.get(`${json.api.flashSales}`)
  },
  search(params){
    return apiClient.get(json.api.search, {
      params: params
    })
  },
  product(params){
    return apiClient.get(`${json.api.product}/${params.id}`, {
      params: params
    })
  },
  page(params){
    return apiClient.get(`${json.api.page}/${params.slug}`)
  },
  reviews(params){
    return apiClient.get(`${json.api.reviews}/${params.id}`, {
      params: params
    })
  },
  suggestedProducts(id, page){
    return apiClient.get(`${json.api.suggested_products}/${id}?page=${page}`)
  },
  userLogin(params){
    return apiClient.post(json.api.login, params)
  },
  userRegistration(params){
    return apiClient.post(json.api.register, params)
  },
  userVerification(params){
    return apiClient.post(json.api.verify, params)
  },
  userForgotPassword(params){
    return apiClient.post(json.api.forgotPassword, params)
  },
  userUpdatePassword(params){
    return apiClient.post(json.api.updatePassword, params)
  },
  getProfile(bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(json.api.profile)
  },
  userAddressAll(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(json.api.userAddressAll, { params: params})
  },
  userAddressAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.userAddressAction, params)
  },
  userAddressDelete(id, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.delete(`${json.api.userAddressDelete}/${id}`)
  },
  userWishlistAll(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(json.api.userWishlistAll, { params: params})
  },
  userWishlistAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.userWishlistAction, params)
  },
  userVouchers(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(json.api.userVouchers, { params: params})
  },
  updateProfile(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.updateProfile, params)
  },
  updateUserPassword(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.updateUserPassword, params)
  },
  userLogout(){
    return apiClient.get(json.api.logout)
  },
  cartByUser(bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(`${json.api.cartByUser}`)
  },
  cartAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.cartAction, params)
  },
  buyNow(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer

    return apiClient.post(json.api.buyNow, params)
  },
  cartDelete(cartId, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.delete(`${json.api.cartDelete}/${cartId}`)
  },
  cartChanged(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.cartChanged, params)
  },
  updateCartShipping(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(json.api.updateCartShipping, params)
  },
  addressByUser(userId, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(`${json.api.addressByUser}/${userId}`)
  },
  addressAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.addressAction}/${params.id}`, params)
  },
  addressDelete(addressId, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.delete(`${json.api.addressDelete}/${addressId}`)
  },
  orderByUser(params, bearer){
    if (bearer) {
      apiClient.defaults.headers.common['Authorization'] = bearer
    }
    return apiClient.post(`${json.api.orderByUser}`, params)
  },
  orderAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.orderAction}`, params)
  },
  paymentDone(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.paymentDone}`, params)
  },
  cancelOrder(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.cancelOrder}`, params)
  },
  cancellationFind(id, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(`${json.api.cancellationFind}/${id}`)
  },
  ratingReviewAction(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.ratingReviewAction}`, params)
  },
  ratingReviewFind(id, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(`${json.api.ratingReviewFind}/${id}`)
  },
  voucherValidity(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.post(`${json.api.voucherValidity}`, params)
  },
  contactUs(params){
    return apiClient.post(json.api.contact, params)
  },
  sendOrderEmail(params, bearer){
    apiClient.defaults.headers.common['Authorization'] = bearer
    return apiClient.get(`${json.api.sendOrderEmail}/${params.id}`, {params: params})
  },
  ssrGetRequest(params, api, bearer = null){
    if(bearer){
      apiClient.defaults.headers['Authorization'] = bearer
    }
    return apiClient.get(json.api[api], { params: params})
  },
  getRequest(params, api, bearer = null){
    if(bearer){
      apiClient.defaults.headers.common['Authorization'] = bearer
    }
    return apiClient.get(json.api[api], { params: params})
  },
  postRequest(params, api, bearer = null) {
    if(bearer){
      apiClient.defaults.headers.common['Authorization'] = bearer
    }
    return apiClient.post(json.api[api], params)
  },
}
