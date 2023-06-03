<template>
  <div>
    <transition name="fade" mode="out-in">
      <div
        class="spinner-wrapper flex"
        v-if="fetchingAddressData"
      >
        <spinner
          :radius="100"
        />
      </div>

    <div
      v-else-if="currentAddresses && !currentAddresses.length"
      class="info-msg"
    >
      {{ $t('userAddress.noAddress') }}
    </div>

    </transition>

    <div v-if="hasRadio">
      <div
        v-for="(value, key) in currentAddresses"
        :key="key"
        class="mb-20 mb-sm-15"
      >
        <label
          class="card ptb-15 pr-15"
          :class="{active: selectedAddress === key}"
        >
          <input
            type="radio"
            name="user_address"
            :value="key"
            v-model="selectedAddress"
          />
          <p>
            {{ formatAddress(value) }}
          </p>
        </label>
        <div class="flex mt-15 mb-5 start">
          <ajax-button
            class="outline-btn plr-20"
            :type="'button'"
            :text="$t('userAddress.edit')"
            color="primary"
            @clicked="$emit('editing', value)"
          />
          <ajax-button
            class="outline-btn plr-20 mlr-10"
            :type="'button'"
            :fetching-data="ajaxDeleting === value.id"
            :loading-text="$t('userAddress.deleting')"
            :text="$t('userAddress.delete')"
            color="primary"
            @clicked="deleting(value)"
          />
        </div>
      </div>
    </div>

    <div v-else class="flex wrap start align-start m--7-5">
      <div
        class="card plr-20 ptb-15 pb-sm-10 plr-sm-15 m-7-5 mx-w-400x"
        v-for="(value, index) in currentAddresses"
        :key="index"
      >
        <p>{{ formatAddress(value) }}</p>
        <div class="flex mt-15 mb-5 start">
          <ajax-button
            class="outline-btn plr-20"
            :type="'button'"
            :text="$t('userAddress.edit')"
            color="primary"
            @clicked="$emit('editing', value)"
          />
          <ajax-button
            class="outline-btn plr-20 mlr-10"
            :type="'button'"
            :fetching-data="ajaxDeleting === value.id"
            :loading-text="$t('userAddress.deleting')"
            :text="$t('userAddress.delete')"
            color="primary"
            @clicked="deleting(value)"
          />
        </div>
      </div>
    </div>
    <pagination
      ref="addressPagination"
      :total-page="totalPage"
      @fetching-data="fetchingData"
    />
  </div>
</template>

<script>
  import util from '~/mixin/util'
  import Pagination from '~/components/Pagination'
  import addressHelper from '~/mixin/addressHelper'
  import routeParamHelper from '~/mixin/routeParamHelper'
  import {mapGetters, mapActions} from 'vuex'
  import countries from '~/resources/countries'
  import AjaxButton from "./AjaxButton";

  export default {
    name: 'UserAddress',
    data() {
      return {
        ajaxDeleting: 0,
        countryList: countries,
        selectedAddress: -1,
        selectedAddressObj: null
      }
    },
    props: {
      hasRadio: {
        type: Boolean,
        default: false
      }
    },
    watch: {
      selectedAddressObj(value){
        if (this.currentAddresses.length){
          const countryName = this.countryList[value.country]?.name
          const stateName = value.state ? this.countryList[value.country].states[value.state]?.name : ''
          this.$emit('selected-address', {...value, ...{countryTitle: countryName, stateTitle: stateName}})
        }else{
          this.$emit('selected-address', null)
        }
      },
      currentAddresses(value){

        if(value.length){
          if(this.hasRadio){
            this.selectedAddress = 0
            this.selectedAddressObj = value[this.selectedAddress]
          }
        }else{

          this.selectedAddress = -1
          this.selectedAddressObj = null
        }
      },
      selectedAddress(value){
        this.selectedAddressObj = this.currentAddresses[value]
      }
    },
    directives: {},
    components: {AjaxButton, Pagination},
    mixins: [util, addressHelper, routeParamHelper],
    computed: {
      totalPage() {
        return this.allAddress?.last_page
      },
      currentAddresses() {
        return this.allAddress?.data || []
      },
      ...mapGetters('user', ['allAddress'])
    },
    methods: {
      async loadData() {
        this.$refs.addressPagination.routeParam()
      },
      ...mapActions('common', ['setToastMessage', 'setToastError']),
      ...mapActions('user', ['userAddressAll', 'userAddressDelete']),
    },
    destroyed() {
    },
    async mounted() {
      await this.fetchingData()
    }
  }
</script>
