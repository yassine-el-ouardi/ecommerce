(window.webpackJsonp=window.webpackJsonp||[]).push([[44],{520:function(e,n,r){"use strict";r.r(n);r(37),r(44),r(19),r(21),r(24),r(6),r(26),r(20),r(27);var t=r(9);function c(object,e){var n=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(object,e).enumerable}))),n.push.apply(n,r)}return n}function d(e){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?c(Object(source),!0).forEach((function(n){Object(t.a)(e,n,source[n])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(source)):c(Object(source)).forEach((function(n){Object.defineProperty(e,n,Object.getOwnPropertyDescriptor(source,n))}))}return e}var l={name:"OrderTabbing",components:{},directives:{},props:{},computed:{paidOrders:function(){var e,n;return(null===(e=this.$route)||void 0===e||null===(n=e.query)||void 0===n?void 0:n.paid)||!1},unpaidOrders:function(){var e,n;return(null===(e=this.$route)||void 0===e||null===(n=e.query)||void 0===n?void 0:n.unpaid)||!1},cardPaymentOrders:function(){var e,n;return(null===(e=this.$route)||void 0===e||null===(n=e.query)||void 0===n?void 0:n.cardPayment)||!1},cashOnDeliveryOrders:function(){var e,n;return(null===(e=this.$route)||void 0===e||null===(n=e.query)||void 0===n?void 0:n.cashOnDelivery)||!1},cancelledOrders:function(){var e,n;return(null===(e=this.$route)||void 0===e||null===(n=e.query)||void 0===n?void 0:n.cancelled)||!1}},data:function(){return{cashOnDelivery:!1,cardPayment:!1,cancelled:!1,paid:!1,unpaid:!1}},methods:{generateParam:function(){var param={};return this.cancelledOrders&&(param.cancelled=this.cancelledOrders?1:0),this.paidOrders&&(param.paid=this.paidOrders?1:0),this.unpaidOrders&&(param.unpaid=this.unpaidOrders?1:0),this.cardPaymentOrders&&(param.card_payment=this.cardPaymentOrders?1:0),this.cashOnDeliveryOrders&&(param.cash_on_delivery=this.cashOnDeliveryOrders?1:0),param},updateRoute:function(e){var n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;this.$router.push({query:d(d({},this.$route.query),{},Object(t.a)({page:1,orderBy:"created_at",orderByType:"desc"},e,n))}),this.$emit("fetch-data",this.generateParam())},unpaidChanged:function(){this.unpaid?this.updateRoute("unpaid",this.unpaid):this.updateRoute("unpaid")},cardPaymentChanged:function(){this.cardPayment?this.updateRoute("cardPayment",this.cardPayment):this.updateRoute("cardPayment")},codChanged:function(){this.cashOnDelivery?this.updateRoute("cashOnDelivery",this.cashOnDelivery):this.updateRoute("cashOnDelivery")},paidChanged:function(){this.paid?this.updateRoute("paid",this.paid):this.updateRoute("paid")},cancelledChanged:function(){this.cancelled?this.updateRoute("cancelled",this.cancelled):this.updateRoute("cancelled")},resetting:function(){this.cancelled=this.cancelledOrders,this.paid=this.paidOrders,this.unpaid=this.unpaidOrders,this.cardPayment=this.cardPaymentOrders,this.cashOnDelivery=this.cashOnDeliveryOrders}},mounted:function(){window.addEventListener("popstate",this.resetting),this.resetting()},destroyed:function(){window.removeEventListener("popstate",this.resetting)}},o=l,h=r(13),component=Object(h.a)(o,(function(){var e=this,n=e._self._c;return n("div",{staticClass:"mb-10 flex start wrap"},[n("div",{staticClass:"ck-button"},[n("label",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.cancelled,expression:"cancelled"}],attrs:{hidden:"",type:"checkbox",name:"orders-variation"},domProps:{checked:Array.isArray(e.cancelled)?e._i(e.cancelled,null)>-1:e.cancelled},on:{change:[function(n){var r=e.cancelled,t=n.target,c=!!t.checked;if(Array.isArray(r)){var d=e._i(r,null);t.checked?d<0&&(e.cancelled=r.concat([null])):d>-1&&(e.cancelled=r.slice(0,d).concat(r.slice(d+1)))}else e.cancelled=c},e.cancelledChanged]}}),e._v(" "),n("span",[e._v(e._s(e.$t("orderTabbing.cancelled")))])])]),e._v(" "),n("div",{staticClass:"ck-button"},[n("label",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.cashOnDelivery,expression:"cashOnDelivery"}],attrs:{hidden:"",type:"checkbox",name:"orders-variation"},domProps:{checked:Array.isArray(e.cashOnDelivery)?e._i(e.cashOnDelivery,null)>-1:e.cashOnDelivery},on:{change:[function(n){var r=e.cashOnDelivery,t=n.target,c=!!t.checked;if(Array.isArray(r)){var d=e._i(r,null);t.checked?d<0&&(e.cashOnDelivery=r.concat([null])):d>-1&&(e.cashOnDelivery=r.slice(0,d).concat(r.slice(d+1)))}else e.cashOnDelivery=c},e.codChanged]}}),e._v(" "),n("span",[e._v(e._s(e.$t("orderTabbing.cod")))])])]),e._v(" "),n("div",{staticClass:"ck-button"},[n("label",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.cardPayment,expression:"cardPayment"}],attrs:{hidden:"",type:"checkbox",name:"orders-variation"},domProps:{checked:Array.isArray(e.cardPayment)?e._i(e.cardPayment,null)>-1:e.cardPayment},on:{change:[function(n){var r=e.cardPayment,t=n.target,c=!!t.checked;if(Array.isArray(r)){var d=e._i(r,null);t.checked?d<0&&(e.cardPayment=r.concat([null])):d>-1&&(e.cardPayment=r.slice(0,d).concat(r.slice(d+1)))}else e.cardPayment=c},e.cardPaymentChanged]}}),e._v(" "),n("span",[e._v(e._s(e.$t("orderTabbing.cardPayment")))])])]),e._v(" "),n("div",{staticClass:"ck-button"},[n("label",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.paid,expression:"paid"}],attrs:{hidden:"",type:"checkbox",name:"orders-variation"},domProps:{checked:Array.isArray(e.paid)?e._i(e.paid,null)>-1:e.paid},on:{change:[function(n){var r=e.paid,t=n.target,c=!!t.checked;if(Array.isArray(r)){var d=e._i(r,null);t.checked?d<0&&(e.paid=r.concat([null])):d>-1&&(e.paid=r.slice(0,d).concat(r.slice(d+1)))}else e.paid=c},e.paidChanged]}}),e._v(" "),n("span",[e._v(e._s(e.$t("orderTabbing.paid")))])])]),e._v(" "),n("div",{staticClass:"ck-button"},[n("label",[n("input",{directives:[{name:"model",rawName:"v-model",value:e.unpaid,expression:"unpaid"}],attrs:{hidden:"",type:"checkbox",name:"orders-variation"},domProps:{checked:Array.isArray(e.unpaid)?e._i(e.unpaid,null)>-1:e.unpaid},on:{change:[function(n){var r=e.unpaid,t=n.target,c=!!t.checked;if(Array.isArray(r)){var d=e._i(r,null);t.checked?d<0&&(e.unpaid=r.concat([null])):d>-1&&(e.unpaid=r.slice(0,d).concat(r.slice(d+1)))}else e.unpaid=c},e.unpaidChanged]}}),e._v(" "),n("span",[e._v(e._s(e.$t("orderTabbing.unPaid")))])])])])}),[],!1,null,null,null);n.default=component.exports}}]);