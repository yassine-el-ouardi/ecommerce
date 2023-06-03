(window.webpackJsonp=window.webpackJsonp||[]).push([[74,15,17,21],{343:function(t,e,r){"use strict";r.r(e);var n={name:"AjaxButton",components:{Spinner:r(44).default},props:{color:{type:String,default:"white"},text:{type:String,default:"Submit"},onlyIcon:{type:String,default:null},loadingText:{type:String,default:"Getting Response"},fetchingData:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},type:{type:String,default:"Submit"}},computed:{buttonText:function(){return this.fetchingData?this.loadingText:this.text},disable:function(){return this.fetchingData}},methods:{btnClicked:function(){"Submit"!==this.type&&this.$emit("clicked")}}},o=(r(350),r(14)),component=Object(o.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("button",{staticClass:"ajax-btn",attrs:{disabled:t.disable||t.disabled,type:t.type},on:{"&click":function(e){return t.btnClicked.apply(null,arguments)}}},[t.fetchingData?r("spinner",{class:{"mr-15":!t.onlyIcon},attrs:{color:t.color}}):t._e(),t._v(" "),t.onlyIcon&&!t.fetchingData?r("i",{staticClass:"icon",class:t.onlyIcon}):t._e(),t._v(" "),t.onlyIcon?t._e():r("span",[t._v("\n    "+t._s(t.buttonText)+"\n  ")])],1)}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{Spinner:r(44).default})},345:function(t,e,r){var content=r(351);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[t.i,content,""]]),content.locals&&(t.exports=content.locals);(0,r(52).default)("91a8990a",content,!0,{sourceMap:!1})},346:function(t,e,r){"use strict";r.r(e);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(6),o=r(11);function l(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}var c={name:"ErrorFormatter",props:{type:{type:String,default:"form"}},computed:function(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?l(Object(source),!0).forEach((function(e){Object(n.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):l(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}({errorData:function(){return this.errors&&void 0!==this.errors[this.type]?this.errors[this.type]:null}},Object(o.c)("ui",["errors"]))},m=c,d=r(14),component=Object(d.a)(m,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return t.errorData?r("ul",{staticClass:"error-list mb-15"},[r("li",{staticClass:"mb-10"},[t._v("Following error occurred")]),t._v(" "),t._l(t.errorData,(function(e,n){return r("li",{key:n},[t._v("\n    "+t._s(e)+"\n  ")])}))],2):t._e()}),[],!1,null,null,null);e.default=component.exports},350:function(t,e,r){"use strict";r(345)},351:function(t,e,r){var n=r(51)(!1);n.push([t.i,".ajax-btn{display:flex;justify-content:center;align-items:center}button:disabled,button[disabled]{opacity:.6;cursor:no-drop}",""]),t.exports=n},352:function(t,e,r){"use strict";r(29),r(40);e.a={data:function(){return{allowedImageExtensions:/(\.jpg|\.jpeg|\.png|\.svg|\.webp|\.gif)$/i,allowedVideoExtensions:/(\.mp4)$/i,passwordLength:6,maxImageSize:1,maxVideoSize:2,reg:/^(([^<>()\]\\.,;:\s@"]+(\.[^<>()\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/}},methods:{isValidImage:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.maxImageSize,r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2];return t.size>1024*e*1024?"File size must be less than ".concat(e,"MB"):r&&!this.allowedImageExtensions.exec(t.name)?"Invalid File Type":r||this.allowedVideoExtensions.exec(t.name)?null:"Invalid File Type"},isValidEmail:function(t){return this.reg.test(t)},isValidLength:function(t){return t&&t.length>=this.passwordLength||!1}}}},724:function(t,e,r){"use strict";r.r(e);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(3),o=r(6),l=(r(23),r(11)),c=r(352),m=r(343),d=r(346);function f(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}function h(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?f(Object(source),!0).forEach((function(e){Object(o.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):f(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}var v={name:"login",layout:"login-layout",middleware:["non-logged-in"],data:function(){return{email:"",hasError:!1,formSubmitting:!1}},mixins:[c.a],components:{AjaxButton:m.default,ErrorFormatter:d.default},watch:{},computed:{isInvalidEmail:function(){return this.email&&!this.isValidEmail(this.email)},isLengthInvalid:function(){return this.password&&!this.isValidLength(this.password)}},methods:h(h({checkForm:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t.hasError=!1,t.email&&!t.isInvalidEmail){e.next=4;break}return t.hasError=!0,e.abrupt("return",!1);case 4:return t.formSubmitting=!0,e.prev=5,e.next=8,t.setRequest({params:{email:t.email},api:"forgotPassword"});case 8:e.sent&&t.$router.push("/verify-code?email=".concat(t.email)),e.next=15;break;case 12:return e.prev=12,e.t0=e.catch(5),e.abrupt("return",t.$nuxt.error(e.t0));case 15:t.formSubmitting=!1;case 16:case"end":return e.stop()}}),e,null,[[5,12]])})))()}},Object(l.b)("ui",["setErrors"])),Object(l.b)("common",["setRequest"])),mounted:function(){this.setErrors()}},y=r(14),component=Object(y.a)(v,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("form",{staticClass:"login-form",class:{"has-error":t.hasError},on:{submit:function(e){return e.preventDefault(),t.checkForm()}}},[r("h4",{staticClass:"mb-30 mb-sm-15"},[t._v("\n    We are going to send you an email containing a code\n  ")]),t._v(" "),r("error-formatter"),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("div",{staticClass:"icon-input"},[r("i",{staticClass:"icon email-icon"},[t._v(" ")]),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:t.email,expression:"email",modifiers:{trim:!0}}],class:{invalid:!t.email||t.isInvalidEmail},attrs:{type:"text",placeholder:"Your Email"},domProps:{value:t.email},on:{input:function(e){e.target.composing||(t.email=e.target.value.trim())},blur:function(e){return t.$forceUpdate()}}})]),t._v(" "),!t.email&&t.hasError?r("span",{staticClass:"error"},[t._v("\n      Email is required\n    ")]):t.isInvalidEmail&&t.hasError?r("span",{staticClass:"error"},[t._v("\n      Email is invalid\n    ")]):t._e()]),t._v(" "),r("div",{staticClass:"dply-felx j-right mt-15"},[r("nuxt-link",{staticClass:"link",attrs:{to:"/login"}},[t._v("\n      Login to your account\n    ")])],1),t._v(" "),r("ajax-button",{staticClass:"mt-20 primary-btn",attrs:{"fetching-data":t.formSubmitting,"loading-text":"Sending email",text:"Send verification code"}})],1)}),[],!1,null,"167d8c14",null);e.default=component.exports;installComponents(component,{ErrorFormatter:r(346).default,AjaxButton:r(343).default})}}]);