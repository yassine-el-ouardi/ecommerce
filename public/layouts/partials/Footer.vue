<template>
  <div>
    <subscription />
    <footer class="link-hover">
      <div class="top-area section pb-0">
        <div class="container">
          <div class="row">
            <div
              v-for="(cat, i) in categories"
              class="col-lg-3 col-md-6 mb-15"
              :key="i"
            >
              <h4 class="mb-5 mb-xs">
                <nuxt-link
                  :to="categoryLink(cat)" class="bold"
                  :title="cat.title"
                >
                  {{cat.title}}
                </nuxt-link>
              </h4>
              <div>
                <nuxt-link
                  :title="subCat.title"
                  :to="subCategoryLink(subCat, cat)"
                  v-for="(subCat, i) in cat.public_sub_categories"
                  :key="i"
                >
                  {{ subCat.title }}
                </nuxt-link>
              </div>
            </div>
          </div><!--row-->

          <div class="ptb-15 mt-20 mt-sm-15 b-t center-text">
            <nuxt-link
              to="/" class="logo"
            >
              <img
                :src="imageURL({'image': site_setting.footer_logo})"
                :alt="$t('footer.siteLogo')"
                height="50"
                width="50"
              >
            </nuxt-link>
          </div>
        </div><!--container-->
      </div>

      <div class="bottom-area section pb-0">
        <div class="container">
          <div class="row">
            <div class="col-lg-3 col-md-6 mb-15">
              <h4 class="bold mb-15">
                {{ $t('footer.services') }}
              </h4>
              <nuxt-link
                :to="pageLink(item)"
                v-for="(item, i) in services"
                :key="i"
              >
                {{item.title}}
              </nuxt-link>
            </div>

            <div class="col-lg-3 col-md-6 mb-15">
              <h4 class="bold mb-15">
                {{ $t('footer.about') }}
              </h4>
              <nuxt-link
                :to="pageLink(item)"
                v-for="(item, i) in about"
                :key="i"
              >
                {{item.title}}
              </nuxt-link>
            </div>

            <div class="col-lg-3 col-md-6 payment mb-20 mb-sm-15">
              <h4 class="bold mb-15">
                {{ $t('footer.payment') }}
              </h4>
              <a :href="item.link"
                 target="_blank"
                 v-for="(item, i) in payment"
                 :key="i"
              >
                <lazy-image
                  :data-src="imageURL(item)"
                  :alt="item.title"
                  :title="item.title"
                />
              </a>
            </div>

            <div class="col-lg-3 col-md-6 mb-15 mb-xs">
              <h4 class="bold mb-15">
                {{ $t('footer.social') }}
              </h4>
              <a :href="item.link" target="_blank" v-for="(item, i) in social" :key="i">
                <lazy-image
                  :data-src="imageURL(item)"
                  :alt="item.title"
                  :title="item.title"
                />
                {{item.title}}
              </a>
            </div>
          </div><!--row-->
          <p class="ptb-15 mt-10 b-t center-text">
            © {{ getYear }} - {{ site_setting.copyright_text }}
          </p>

        </div><!--container-->
      </div>

    </footer>
  </div>

</template>

<script>
  import util from '~/mixin/util'
  import {mapGetters} from 'vuex'
  import LazyImage from "../../components/LazyImage";
  import Subscription from "../../components/Subscription";
  export default {
    data() {
      return {
      }
    },
    mixins: [util],
    components: {Subscription, LazyImage},
    computed: {
      ...mapGetters('common', ['site_setting', 'categories', 'services', 'about', 'payment', 'social'])
    },
  }
</script>
