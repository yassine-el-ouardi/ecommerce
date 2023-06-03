const configJson = require('./config.json')

let apiBase = !process.env.API_BASE.trim() ? '/' : process.env.API_BASE
apiBase += configJson.api.url

export default {
  ssr: process.env.SSR === 'true',
  env: {
    apiBase: process.env.API_BASE
  },
  css: [
    '~/assets/styles/styles.styl',
    '@glidejs/glide/dist/css/glide.core.min.css'
  ],
  //Progress bar color
  loading: {color: '#39b982'},

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: [
    {src: '~/plugins/vue-product-zoomer.js', mode: 'client'},
    {src: '~/plugins/vue-social-sharing.js', mode: 'client'},
    {src: '~/plugins/v-drag.js', mode: 'client'},
    {src: '~/plugins/cryptojs.js', mode: 'client'},
    {src: '~/plugins/nuxt-client-init.js', ssr: false},
    {src: '~/plugins/dompurify.js'}
  ],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    '@nuxtjs/moment'
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: [
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios',
    '@nuxtjs/auth-next',
    '@nuxtjs/i18n'
  ],
  auth: {
    cookie:{
      prefix: 'frontend_'
    },
    localStorage: false,
    strategies: {
      local: {
        token: {
          property: 'data.token',
          // required: true,
          // type: 'Bearer'
        },
        user: {
          property: 'data',
          // autoFetch: true
        },
        endpoints: {
          login: {url: apiBase + configJson.api.login, method: 'post'},
          logout: {url: apiBase + configJson.api.logout, method: 'get'},
          user: false
          /*user: {url: apiBase + configJson.api.profile, method: 'get'}*/
        }
      }
    }
  },
  axios: {},

  build: {
    transpile: [
      'defu',
    ]
  },
  i18n: {
    locales: [
      {
        code: 'en',
        file: 'en.json'
      }
    ],
    lazy: true,
    langDir: 'lang/',
    defaultLocale: 'en'
  }
}
