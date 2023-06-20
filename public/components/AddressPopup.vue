<template>
  <form @submit.prevent="savingAddressData">
    <pop-over
      v-if="addressData"
      title="User Address"
      @close="$emit('close')"
      elem-id="user-address-pop-over"
      :layer="true"
      class="address-popup popup-top-auto"
    >
      <template
        v-slot:content
      >
        <div class="">
          <div class="flex start mlr--5">
            <div class="input-wrap mlr-5">
              <label>
                {{ $t('addressPopup.country') }}
              </label>
              <dropdown
                :selected-key="addressData.country"
                :options="countries"
                key-name="name"
                :position-fixed="false"
                :searching="true"
                @clicked="selectedCountry"
              />
            </div>

            <div
              v-if="Object.keys(states).length"
              class="input-wrap mlr-5"
            >
              <label>
                {{ $t('addressPopup.state') }}
              </label>
              <dropdown
                :selected-key="addressData.state"
                :options="states"
                key-name="name"
                :position-fixed="false"
                @clicked="selectedState"
              />
            </div>
          </div>

          <div class="flex mlr--5 start">
            <div
              class="input-wrap mlr-5"
              :class="{invalid: !addressData.name && hasAddressErrors}"
            >
              <label>
                {{ $t('addressPopup.name') }}
              </label>
              <input
                type="text"
                v-model="addressData.name"
              />
              <span
                class="error"
                v-if="!addressData.name && hasAddressErrors"
              >
                {{ $t('addressPopup.isRequired', {type: $t('addressPopup.name')}) }}
              </span>
            </div>

            <div
              class="input-wrap mlr-5"
              :class="{invalid: !addressData.phone && hasAddressErrors}"
            >
              <label>
                {{ $t('addressPopup.phone') }}
              </label>
              <div class="input-text">
                <span>
                  {{ phoneCode }}
                </span>
                <input
                  type="text"
                  v-model="addressData.phone"
                />
              </div>

              <span
                class="error"
                v-if="!addressData.phone && hasAddressErrors"
              >
                 {{ $t('addressPopup.isRequired', {type: $t('addressPopup.phone')}) }}
              </span>
            </div>
          </div>

          <div
            class="input-wrap"
            :class="{invalid: !addressData.address_1 && hasAddressErrors}"
          >
            <label>
              {{ $t('addressPopup.address') }}
            </label>
            <input
              class="mb-10"
              type="text"
              v-model="addressData.address_1"
              :placeholder="$t('addressPopup.addressPlaceholder')"
            />
            <input
              type="text"
              v-model="addressData.address_2"
              :placeholder="$t('addressPopup.addressPlaceholder')"
            />
            <span
              class="error"
              v-if="!addressData.address_1 && hasAddressErrors"
            >
              {{ $t('addressPopup.isRequired', {type: $t('addressPopup.address')}) }}
            </span>
          </div>

          <div class="flex start mlr--5">
            <div
              class="input-wrap mlr-5"
              :class="{invalid: !addressData.city && hasAddressErrors}"
            >
              <label>
                {{ $t('addressPopup.city') }}
              </label>
              <input
                type="text"
                v-model="addressData.city"
              />
              <span
                class="error"
                v-if="!addressData.city && hasAddressErrors"
              >
                 {{ $t('addressPopup.isRequired', {type: $t('addressPopup.city')}) }}
              </span>
            </div>
            <div
              class="input-wrap mlr-5"
              :class="{invalid: !addressData.zip && hasAddressErrors}"
            >
              <label>
                {{ $t('addressPopup.zipCode') }}
              </label>
              <input
                type="text"
                v-model="addressData.zip"
              />
              <span
                class="error"
                v-if="!addressData.zip && hasAddressErrors"
              >
                {{ $t('addressPopup.isRequired', {type: $t('addressPopup.zipCode')}) }}
              </span>
            </div>
          </div>
        </div>
      </template>

      <template v-slot:pop-footer>
        <div class="flex j-end">
          <button
            class="outline-btn plr-30 plr-sm-15 mr-10"
            aria-label="Address cancel"
            @click.prevent="$emit('close')"
          >
            {{ $t('addressPopup.cancel') }}
          </button>
          <ajax-button
            class="primary-btn  plr-30 plr-sm-15"
            :fetching-data="submittingAddressData"
            :loading-text="$t('addressPopup.saving')"
            :text=" $t('addressPopup.thisAddress', {type: editing > 0 ? $t('addressPopup.update') : $t('addressPopup.save')})"
          />
        </div>
      </template>
    </pop-over>
  </form>

</template>

<script>
  import util from '~/mixin/util'
  import validation from '~/mixin/validation'
  import PopOver from '~/components/PopOver'
  import Dropdown from '~/components/Dropdown'
  import {mapGetters, mapActions} from 'vuex'
  import countries from '~/resources/countries'
  import phones from '~/resources/phones'
  import addressHelper from '~/mixin/addressHelper'
  import AjaxButton from "./AjaxButton";

  export default {
    name: 'AddressPopup',
    data() {
      return {
        states: {},
        phones: phones,
        countries: countries,
        addressData: null,
        hasAddressErrors: false,
        dropdownOpen: false,
        submittingAddressData: false
      }
    },
    watch: {
      location() {
        this.settingCountry()
      }

    },
    props: {
      address: {
        type: Object,
        default() {
          return null
        }
      }
    },
    components: {
      AjaxButton,
      PopOver,
      Dropdown
    },
    computed: {
      phoneCode() {
        return this.phones[this.addressData?.country]
      },
      editing() {
        return this.addressData && this.addressData.id
      },
      ...mapGetters('common', ['location'])
    },
    mixins: [util, validation, addressHelper],
    methods: {
      async savingAddressData() {
        await this.addressAction()

        if (!this.hasAddressErrors) {
          this.$emit('close')
        }
      },
      settingCountry() {
        this.addressData.country = this.addressData.country.trim() !== '' ? this.addressData.country.trim() : this.location.countryCode

        this.states = this.addressData?.country ? this.countries[this.addressData.country].states : ''
        this.addressData.state = this.addressData?.state?.trim() !== '' ? this.addressData?.state?.trim() : this.location.region
      },
      selectedCountry(evt) {
        this.addressData = {...this.addressData, ...{country: evt.value.code2}}
        this.states = evt.value.states
        this.addressData.state = Object.keys(evt.value.states).length ? Object.values(evt.value.states)[0]?.code : ''
      },
      selectedState(evt) {
        this.addressData.state = evt.value.code
      },
      ...mapActions('user', ['userAddressAction']),
      ...mapActions('common', ['fetchLocation', 'setToastMessage', 'setToastError']),
      ...mapActions('order', ['ratingReviewAction', 'ratingReviewFind']),
    },
    created() {
    },
    async mounted() {
      this.addressData = this.address ?? {
        id: '',
        city: '',
        name: '',
        phone: '',
        country: '',
        state: '',
        zip: '',
        address_1: '',
        address_2: ''
      }
      if (!this.addressData.country) {
        this.settingCountry()
      } else {
        this.states = this.addressData?.country ? this.countries[this.addressData.country].states : ''
        this.addressData.state = this.addressData?.state?.trim() !== '' ? this.addressData?.state?.trim() : this.location.region
      }
    }


  }
</script>

