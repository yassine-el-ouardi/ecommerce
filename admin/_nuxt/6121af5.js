(window.webpackJsonp=window.webpackJsonp||[]).push([[78,15,17,21,51],{343:function(e,t,r){"use strict";r.r(t);var n={name:"AjaxButton",components:{Spinner:r(44).default},props:{color:{type:String,default:"white"},text:{type:String,default:"Submit"},onlyIcon:{type:String,default:null},loadingText:{type:String,default:"Getting Response"},fetchingData:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},type:{type:String,default:"Submit"}},computed:{buttonText:function(){return this.fetchingData?this.loadingText:this.text},disable:function(){return this.fetchingData}},methods:{btnClicked:function(){"Submit"!==this.type&&this.$emit("clicked")}}},o=(r(350),r(14)),component=Object(o.a)(n,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("button",{staticClass:"ajax-btn",attrs:{disabled:e.disable||e.disabled,type:e.type},on:{"&click":function(t){return e.btnClicked.apply(null,arguments)}}},[e.fetchingData?r("spinner",{class:{"mr-15":!e.onlyIcon},attrs:{color:e.color}}):e._e(),e._v(" "),e.onlyIcon&&!e.fetchingData?r("i",{staticClass:"icon",class:e.onlyIcon}):e._e(),e._v(" "),e.onlyIcon?e._e():r("span",[e._v("\n    "+e._s(e.buttonText)+"\n  ")])],1)}),[],!1,null,null,null);t.default=component.exports;installComponents(component,{Spinner:r(44).default})},345:function(e,t,r){var content=r(351);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[e.i,content,""]]),content.locals&&(e.exports=content.locals);(0,r(52).default)("91a8990a",content,!0,{sourceMap:!1})},346:function(e,t,r){"use strict";r.r(t);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(6),o=r(11);function l(object,e){var t=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(object,e).enumerable}))),t.push.apply(t,r)}return t}var c={name:"ErrorFormatter",props:{type:{type:String,default:"form"}},computed:function(e){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?l(Object(source),!0).forEach((function(t){Object(n.a)(e,t,source[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(source)):l(Object(source)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(source,t))}))}return e}({errorData:function(){return this.errors&&void 0!==this.errors[this.type]?this.errors[this.type]:null}},Object(o.c)("ui",["errors"]))},d=c,m=r(14),component=Object(m.a)(d,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return e.errorData?r("ul",{staticClass:"error-list mb-15"},[r("li",{staticClass:"mb-10"},[e._v("Following error occurred")]),e._v(" "),e._l(e.errorData,(function(t,n){return r("li",{key:n},[e._v("\n    "+e._s(t)+"\n  ")])}))],2):e._e()}),[],!1,null,null,null);t.default=component.exports},350:function(e,t,r){"use strict";r(345)},351:function(e,t,r){var n=r(51)(!1);n.push([e.i,".ajax-btn{display:flex;justify-content:center;align-items:center}button:disabled,button[disabled]{opacity:.6;cursor:no-drop}",""]),e.exports=n},352:function(e,t,r){"use strict";r(29),r(40);t.a={data:function(){return{allowedImageExtensions:/(\.jpg|\.jpeg|\.png|\.svg|\.webp|\.gif)$/i,allowedVideoExtensions:/(\.mp4)$/i,passwordLength:6,maxImageSize:1,maxVideoSize:2,reg:/^(([^<>()\]\\.,;:\s@"]+(\.[^<>()\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/}},methods:{isValidImage:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.maxImageSize,r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2];return e.size>1024*t*1024?"File size must be less than ".concat(t,"MB"):r&&!this.allowedImageExtensions.exec(e.name)?"Invalid File Type":r||this.allowedVideoExtensions.exec(e.name)?null:"Invalid File Type"},isValidEmail:function(e){return this.reg.test(e)},isValidLength:function(e){return e&&e.length>=this.passwordLength||!1}}}},364:function(e,t,r){"use strict";r.r(t);var n={name:"PasswordField",data:function(){return{password:"",passwordFieldType:"password"}},props:{isInvalid:{type:Boolean,default:!1},value:{type:String}},watch:{value:function(e){this.password=e}},mixins:[],components:{},computed:{isPasswordTypePassword:function(){return"password"===this.passwordFieldType}},methods:{passwordFieldToggle:function(){this.isPasswordTypePassword?this.passwordFieldType="text":this.passwordFieldType="password"}},mounted:function(){this.password=this.value}},o=r(14),component=Object(o.a)(n,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"icon-input right-icon"},[r("i",{staticClass:"icon password-icon"}),e._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:e.password,expression:"password",modifiers:{trim:!0}}],class:{invalid:!e.password||e.isInvalid},attrs:{type:e.passwordFieldType,placeholder:"Password"},domProps:{value:e.password},on:{change:function(t){return e.$emit("change",e.password)},input:function(t){t.target.composing||(e.password=t.target.value.trim())},blur:function(t){return e.$forceUpdate()}}}),e._v(" "),r("i",{staticClass:"icon",class:e.isPasswordTypePassword?"eye-close-icon":"eye-icon",on:{click:e.passwordFieldToggle}})])}),[],!1,null,null,null);t.default=component.exports},726:function(e,t,r){"use strict";r.r(t);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(3),o=r(6),l=(r(23),r(100),r(11)),c=r(59),d=r(352),m=r(343),f=r(364),h=r(346);function v(object,e){var t=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(object,e).enumerable}))),t.push.apply(t,r)}return t}function w(e){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?v(Object(source),!0).forEach((function(t){Object(o.a)(e,t,source[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(source)):v(Object(source)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(source,t))}))}return e}var y={name:"login",layout:"login-layout",middleware:["non-logged-in"],data:function(){return{email:"",password:"",rememberToken:"",hasError:!1,formSubmitting:!1}},mixins:[c.a,d.a],components:{AjaxButton:m.default,ErrorFormatter:h.default,PasswordField:f.default},watch:{},computed:w({isDemo:function(){return!1},isInvalidEmail:function(){return this.email&&!this.isValidEmail(this.email)},isLengthInvalid:function(){return this.password&&!this.isValidLength(this.password)}},Object(l.c)("ui",["rememberMe"])),methods:w({setCredentials:function(data){data<0?(this.email="admin@mail.com",this.password="123456"):(this.email="vendor@mail.com",this.password="123456"),this.checkForm()},checkForm:function(){var e=this;return Object(n.a)(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(e.hasError=!1,e.email&&e.password&&!e.isInvalidEmail&&!e.isLengthInvalid){t.next=4;break}return e.hasError=!0,t.abrupt("return",!1);case 4:e.settingRemember(e.rememberToken),e.formSubmitting=!0,t.prev=6,"".trim()||(e.$axios.defaults.baseURL=window.location.origin+"/"),e.$auth.loginWith("local",{data:{remember_token:e.rememberToken,password:e.password,email:e.email}}).then((function(t){e.formSubmitting=!1;var data=t.data;200===data.status?(e.hasError=!1,e.setErrors(),e.$auth.$state.redirect?e.$router.push(e.$auth.$state.redirect):e.$router.push("/")):e.setErrors(null==data?void 0:data.data)})).catch((function(e){return console.error(e)})),t.next=14;break;case 11:return t.prev=11,t.t0=t.catch(6),t.abrupt("return",e.$nuxt.error(t.t0));case 14:case"end":return t.stop()}}),t,null,[[6,11]])})))()}},Object(l.b)("ui",["setErrors","settingRemember"])),mounted:function(){this.rememberToken="true"===this.rememberMe||"",this.setErrors()}},_=r(14),component=Object(_.a)(y,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("form",{ref:"loginForm",staticClass:"login-form",class:{"has-error":e.hasError},on:{submit:function(t){return t.preventDefault(),e.checkForm()}}},[r("h4",{staticClass:"mb-30 mb-sm-15"},[e._v("\n    Welcome back\n  ")]),e._v(" "),r("error-formatter"),e._v(" "),r("div",{staticClass:"input-wrapper"},[r("div",{staticClass:"icon-input"},[r("i",{staticClass:"icon email-icon"}),e._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:e.email,expression:"email",modifiers:{trim:!0}}],class:{invalid:!e.email||e.isInvalidEmail},attrs:{type:"text",placeholder:"Your Email"},domProps:{value:e.email},on:{input:function(t){t.target.composing||(e.email=t.target.value.trim())},blur:function(t){return e.$forceUpdate()}}})]),e._v(" "),!e.email&&e.hasError?r("span",{staticClass:"error"},[e._v("\n      Email is required\n    ")]):e.isInvalidEmail&&e.hasError?r("span",{staticClass:"error"},[e._v("\n      Email is invalid\n    ")]):e._e()]),e._v(" "),r("div",{staticClass:"input-wrapper"},[r("password-field",{attrs:{value:e.password,"is-invalid":e.isLengthInvalid},on:{change:function(t){e.password=t}}}),e._v(" "),!e.password&&e.hasError?r("span",{staticClass:"error"},[e._v("\n      Password is required\n    ")]):e.isLengthInvalid&&e.hasError?r("span",{staticClass:"error"},[e._v("\n      Invalid password length\n    ")]):e._e()],1),e._v(" "),r("div",{staticClass:"sided mt-15"},[r("label",{staticClass:"checkbox"},[r("input",{directives:[{name:"model",rawName:"v-model",value:e.rememberToken,expression:"rememberToken"}],attrs:{type:"checkbox"},domProps:{checked:Array.isArray(e.rememberToken)?e._i(e.rememberToken,null)>-1:e.rememberToken},on:{change:function(t){var r=e.rememberToken,n=t.target,o=!!n.checked;if(Array.isArray(r)){var l=e._i(r,null);n.checked?l<0&&(e.rememberToken=r.concat([null])):l>-1&&(e.rememberToken=r.slice(0,l).concat(r.slice(l+1)))}else e.rememberToken=o}}}),e._v("\n      Remember me\n    ")]),e._v(" "),r("nuxt-link",{staticClass:"link",attrs:{to:"/forgot-password"}},[e._v("\n      Forgot password\n    ")])],1),e._v(" "),r("ajax-button",{staticClass:"mt-20 primary-btn",attrs:{"fetching-data":e.formSubmitting,"loading-text":"Logging in",text:"Sign in"}}),e._v(" "),r("client-only",[e.isDemo?r("div",[r("button",{staticClass:"outline-btn block mtb-15 w-100",on:{click:function(t){return t.preventDefault(),e.setCredentials(-1)}}},[e._v("\n        Login as admin\n      ")]),e._v(" "),r("button",{staticClass:"outline-btn block w-100",on:{click:function(t){return t.preventDefault(),e.setCredentials(1)}}},[e._v("\n        Login as vendor\n      ")])]):e._e()])],1)}),[],!1,null,"0ba19eeb",null);t.default=component.exports;installComponents(component,{ErrorFormatter:r(346).default,PasswordField:r(364).default,AjaxButton:r(343).default})}}]);