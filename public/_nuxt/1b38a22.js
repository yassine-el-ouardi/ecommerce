(window.webpackJsonp=window.webpackJsonp||[]).push([[20,13],{466:function(t,e,r){"use strict";r.r(e);r(288);var n={name:"Breadcrumb",data:function(){return{}},props:{page:{type:String,required:!0},slugs:{type:Array,default:function(){return[]}}},computed:{},mounted:function(){},destroyed:function(){}},o=r(13),component=Object(o.a)(n,(function(){var t=this,e=t._self._c;return e("nav",{attrs:{"aria-label":"breadcrumb"}},[e("ol",{staticClass:"breadcrumb",attrs:{itemscope:"",itemtype:"https://schema.org/BreadcrumbList"}},[e("li",{attrs:{itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem"}},[e("nuxt-link",{attrs:{to:"/",itemprop:"item"}},[e("span",{attrs:{itemprop:"name"}},[t._v(t._s(t.$t("product.home")))])]),t._v(" "),e("meta",{attrs:{itemprop:"position",content:"1"}})],1),t._v(" "),t._l(t.slugs,(function(r,i){return e("li",{key:i,attrs:{itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem"}},[e("nuxt-link",{attrs:{title:r.title,to:r.link,itemprop:"item"}},[e("span",{attrs:{itemprop:"name"}},[t._v(t._s(r.title))])]),t._v(" "),e("meta",{attrs:{itemprop:"position",content:i+2}})],1)})),t._v(" "),e("li",{staticClass:"breadcrumb-item",attrs:{itemprop:"itemListElement",itemscope:"",itemtype:"https://schema.org/ListItem"}},[e("span",{attrs:{itemprop:"name"}},[t._v(t._s(t.page))]),t._v(" "),e("meta",{attrs:{itemprop:"position",content:t.slugs.length+2}})])],2)])}),[],!1,null,null,null);e.default=component.exports},485:function(t,e,r){"use strict";var n=r(0);r(12),r(35),r(102),r(24),r(6),r(81),r(22),r(80);e.a={data:function(){return{fetchingAddressData:!1}},methods:{addressAction:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){var data,r,n;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t.addressData.city&&t.addressData.phone&&t.addressData.name&&t.addressData.zip&&t.addressData.country&&t.addressData.address_1)){e.next=9;break}return t.submittingAddressData=!0,e.next=4,t.userAddressAction(t.addressData);case 4:200===(null==(data=e.sent)?void 0:data.status)?(t.hasAddressErrors=!1,t.setToastMessage(data.message)):201===(null==data?void 0:data.status)?t.setToastError(null===(r=data.data)||void 0===r||null===(n=r.form)||void 0===n?void 0:n.join(", ")):t.hasError(data),t.submittingAddressData=!1,e.next=10;break;case 9:t.hasAddressErrors=!0;case 10:case"end":return e.stop()}}),e)})))()},deleting:function(address){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){var data;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!confirm(t.$t("cartProductTile.deleteAlert"))){e.next=13;break}return t.ajaxDeleting=address.id,e.next=4,t.userAddressDelete(address.id);case 4:if(200!==(null==(data=e.sent)?void 0:data.status)){e.next=11;break}return t.setToastMessage(data.message),e.next=9,t.fetchingData();case 9:e.next=12;break;case 11:t.setToastError(data.data.form.join(", "));case 12:t.ajaxDeleting=0;case 13:case"end":return e.stop()}}),e)})))()},formatAddress:function(t){var e=[];if(arguments.length>1&&void 0!==arguments[1]&&arguments[1]||(e.push(t.name),e.push("tel: ".concat(t.phone))),e.push(t.address_1),t.address_2&&e.push(t.address_2),e.push(t.city+"-"+t.zip),this.countryList[t.country]){var r=this.countryList[t.country];r.states[t.state]&&e.push(r.states[t.state].name),e.push(r.name)}return e.filter((function(t){return null!=t})).join(", ")},fetchingData:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:t.fetchingAddressData=!0,setTimeout(Object(n.a)(regeneratorRuntime.mark((function e(){var data;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,t.settingRouteParam(),e.next=4,t.userAddressAll({time_zone:t.timeZone,order_by:t.orderBy,type:t.orderByType,page:t.page,q:t.search});case 4:200!==(null==(data=e.sent)?void 0:data.status)&&t.hasError(data),e.next=11;break;case 8:return e.prev=8,e.t0=e.catch(0),e.abrupt("return",t.$nuxt.error(e.t0));case 11:t.fetchingAddressData=!1;case 12:case"end":return e.stop()}}),e,null,[[0,8]])}))),100);case 2:case"end":return e.stop()}}),e)})))()}}}},574:function(t,e,r){"use strict";r.r(e);r(35),r(19),r(21),r(24),r(6),r(26),r(20),r(27);var n=r(0),o=r(9),c=(r(12),r(485)),l=r(34),m=r(204),d=r(10),v=r(493);function f(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}function _(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?f(Object(source),!0).forEach((function(e){Object(o.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):f(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}var h={name:"Contact",components:{Breadcrumb:r(466).default},props:{pageTitle:{type:String,default:""}},data:function(){return{countryList:v,name:"",email:"",subject:"",errors:[],message:"",formSubmitting:!1,hasFormError:!1,messageSent:!1}},mixins:[l.a,m.a,c.a],watch:{},computed:_({invalidEmail:function(){return!this.isValidEmail(this.email)},emailValid:function(){return this.email&&!this.invalidEmail},contactEmail:function(){var t;return null===(t=this.setting)||void 0===t?void 0:t.email},phone:function(){var t;return null===(t=this.setting)||void 0===t?void 0:t.phone}},Object(d.c)("common",["setting"])),methods:_({formSubmit:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){var data,r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t.email&&t.name&&t.subject&&t.message)){e.next=15;break}return t.formSubmitting=!0,e.prev=2,e.next=5,t.contactUs({name:t.name,email:t.email,subject:t.subject,message:t.message});case 5:200===(null==(data=e.sent)?void 0:data.status)?(t.messageSent=!0,t.hasFormError=!1,t.errors=[]):t.errors=null==data||null===(r=data.data)||void 0===r?void 0:r.form,e.next=12;break;case 9:return e.prev=9,e.t0=e.catch(2),e.abrupt("return",t.$nuxt.error(e.t0));case 12:t.formSubmitting=!1,e.next=16;break;case 15:t.hasFormError=!0;case 16:case"end":return e.stop()}}),e,null,[[2,9]])})))()}},Object(d.b)("contact",["contactUs"]))},y=r(13),component=Object(y.a)(h,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"container"},[e("div",{staticClass:"contact-wrapper"},[e("breadcrumb",{attrs:{page:t.pageTitle}}),t._v(" "),e("h1",{staticClass:"page-title"},[t._v(t._s(t.$t("contact.contactUs")))]),t._v(" "),e("p",{staticClass:"mt-10 mb-30 mb-sm-15 f-12"},[t._v(t._s(t.$t("contact.feelFree")))]),t._v(" "),e("div",{staticClass:"flex"},[e("div",{staticClass:"contact-form"},[t.messageSent?e("div",[e("h4",{staticClass:"mb-15 bold"},[t._v("\n          "+t._s(t.$t("contact.success"))+"!!!\n        ")]),t._v(" "),e("h5",{staticClass:"mb-15"},[t._v("\n          "+t._s(t.$t("contact.successMessage"))+"\n        ")]),t._v(" "),e("p",{staticClass:"mb-15"},[e("nuxt-link",{staticClass:"link bold color-primary",attrs:{to:"/"}},[t._v("\n            "+t._s(t.$t("contact.goToHome"))+"\n          ")]),t._v("\n          "+t._s(t.$t("contact.now"))+".\n        ")],1)]):e("form",{on:{submit:function(e){return e.preventDefault(),t.formSubmit.apply(null,arguments)}}},[t.errors.length?e("ul",{staticClass:"error-list mb-15"},[e("li",{staticClass:"mb-10"},[t._v("\n            "+t._s(t.$t("contact.errorOccurred"))+"\n          ")]),t._v(" "),t._l(t.errors,(function(r,n){return e("li",{key:n},[t._v("\n            "+t._s(r)+"\n          ")])}))],2):t._e(),t._v(" "),e("div",{staticClass:"flex"},[e("div",{staticClass:"input-wrap",class:{invalid:!t.name&&t.hasFormError}},[e("label",[t._v(t._s(t.$t("addressPopup.name")))]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.name,expression:"name"}],attrs:{type:"text",placeholder:t.$t("contact.your",{type:this.$t("contact.name")})},domProps:{value:t.name},on:{input:function(e){e.target.composing||(t.name=e.target.value)}}}),t._v(" "),!t.name&&t.hasFormError?e("span",{staticClass:"error"},[t._v("\n          "+t._s(t.$t("addressPopup.isRequired",{type:t.$t("addressPopup.name")}))+"\n        ")]):t._e()]),t._v(" "),e("div",{staticClass:"input-wrap",class:{invalid:!t.emailValid&&t.hasFormError}},[e("label",[t._v(t._s(t.$t("addressPopup.email")))]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.email,expression:"email"}],attrs:{type:"text",placeholder:t.$t("contact.your",{type:this.$t("contact.email")})},domProps:{value:t.email},on:{input:function(e){e.target.composing||(t.email=e.target.value)}}}),t._v(" "),!t.email&&t.hasFormError?e("span",{staticClass:"error"},[t._v("\n          "+t._s(t.$t("addressPopup.isRequired",{type:t.$t("addressPopup.email")}))+"\n        ")]):t.invalidEmail&&t.hasFormError?e("span",{staticClass:"error"},[t._v("\n          "+t._s(t.$t("contact.invalidEmail"))+"\n        ")]):t._e()])]),t._v(" "),e("div",{staticClass:"input-wrap",class:{invalid:!t.subject&&t.hasFormError}},[e("label",[t._v(t._s(t.$t("addressPopup.subject")))]),t._v(" "),e("input",{directives:[{name:"model",rawName:"v-model",value:t.subject,expression:"subject"}],attrs:{type:"text",placeholder:t.$t("addressPopup.subject")},domProps:{value:t.subject},on:{input:function(e){e.target.composing||(t.subject=e.target.value)}}}),t._v(" "),!t.subject&&t.hasFormError?e("span",{staticClass:"error"},[t._v("\n          "+t._s(t.$t("addressPopup.isRequired",{type:t.$t("addressPopup.subject")}))+"\n        ")]):t._e()]),t._v(" "),e("div",{staticClass:"input-wrap",class:{invalid:!t.message&&t.hasFormError}},[e("label",[t._v(t._s(t.$t("addressPopup.message")))]),t._v(" "),e("textarea",{directives:[{name:"model",rawName:"v-model",value:t.message,expression:"message"}],attrs:{placeholder:t.$t("addressPopup.message")},domProps:{value:t.message},on:{input:function(e){e.target.composing||(t.message=e.target.value)}}}),t._v(" "),!t.message&&t.hasFormError?e("span",{staticClass:"error"},[t._v("\n          "+t._s(t.$t("addressPopup.isRequired",{type:t.$t("addressPopup.message")}))+"\n        ")]):t._e()]),t._v(" "),e("div",{staticClass:"flex j-end m-0"},[e("ajax-button",{staticClass:"primary-btn plr-30 plr-sm-15",attrs:{"fetching-data":t.formSubmitting}})],1)])]),t._v(" "),e("div",{staticClass:"contact-info"},[e("div",{staticClass:"flex"},[t._m(0),t._v(" "),e("div",[e("h4",{staticClass:"mb-10 bold"},[t._v("Phone")]),t._v(" "),e("a",{attrs:{href:"tel:".concat(t.phone)}},[t._v("\n            "+t._s(t.$t("home.helpline",{phone:t.phone}))+"\n          ")])])]),t._v(" "),e("div",{staticClass:"flex"},[t._m(1),t._v(" "),e("div",[e("h4",{staticClass:"mb-10 bold"},[t._v("Email")]),t._v(" "),e("a",{attrs:{href:"mailto:".concat(t.contactEmail)}},[t._v("\n            "+t._s(t.$t("home.mail",{email:t.contactEmail}))+"\n          ")])])]),t._v(" "),e("div",{staticClass:"flex"},[t._m(2),t._v(" "),e("div",[e("h4",{staticClass:"mb-10 bold"},[t._v("Address")]),t._v(" "),e("p",[t._v("\n            "+t._s(t.formatAddress(t.setting,!0))+"\n          ")])])])])])],1)])}),[function(){var t=this._self._c;return t("span",[t("i",{staticClass:"icon phone-icon"})])},function(){var t=this._self._c;return t("span",[t("i",{staticClass:"icon email-icon"})])},function(){var t=this._self._c;return t("span",[t("i",{staticClass:"icon location-icon"})])}],!1,null,null,null);e.default=component.exports;installComponents(component,{Breadcrumb:r(466).default,AjaxButton:r(286).default})}}]);