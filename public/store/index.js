import Service from '@/services/service.js'

const state = () => ({
  imgSrcUrl: '',
  defaultImage: '',
  thumbPrefix: '',
})
const getters = {
  defaultImage: ({defaultImage}) => defaultImage,
  imgSrcUrl: ({imgSrcUrl}) => imgSrcUrl,
  thumbPrefix: ({thumbPrefix}) => thumbPrefix,
}

const mutations = {
  SET_DEFAULT_IMAGE(state, defaultImage) {
    state.defaultImage = defaultImage
  },
  SET_IMG_SRC_URL(state, imgSrcUrl) {
    state.imgSrcUrl = imgSrcUrl
  },
  SET_THUMB_PREFIX(state, thumbPrefix) {
    state.thumbPrefix = thumbPrefix
  },
}

const actions = {
  async nuxtServerInit ({ commit }, {error}) {
    try {
      const {data} = await Service.common()
      if(data?.status){

        if(data?.status === 200){
          commit('SET_DEFAULT_IMAGE', data.data.default_image)
          commit('SET_IMG_SRC_URL', data.data.img_src_url)
          commit('SET_THUMB_PREFIX', data.data.thumb_prefix)
          commit('common/SET_COMMON_DATA', data, {root: true})
        }else if(data?.status === 201){

          return error({
            statusCode: data?.status, message: data?.message
          })
        }


      } else {
        return error({
          message: "API is down."
        })
      }
    } catch (e) {
      return error({
        message: e.message
      })
    }
  },
  async nuxtClientInit ({ commit, state }, {error}) {
    if(!state?.common?.categories && !state?.common?.about){

      try {
        const {data} = await Service.common()


        if(data?.status){

          if(data?.status === 200){

            commit('common/SET_COMMON_DATA', data, {root: true})
            commit('SET_DEFAULT_IMAGE', data.data.default_image)
            commit('SET_IMG_SRC_URL', data.data.img_src_url)
            commit('SET_THUMB_PREFIX', data.data.thumb_prefix)

          }else if(data?.status === 201){

            return error({
              statusCode: data?.status, message: data?.message
            })
          }


        } else {

          return error({
            message: "API is down."
          })
        }
      }catch (e) {

        return error({
          message: e.message
        })
      }
    }
  }
}

export {
  state,
  getters,
  mutations,
  actions
}
