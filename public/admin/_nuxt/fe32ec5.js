(window.webpackJsonp=window.webpackJsonp||[]).push([[87,15,17,21,51],{343:function(t,e,r){"use strict";r.r(e);var n={name:"AjaxButton",components:{Spinner:r(44).default},props:{color:{type:String,default:"white"},text:{type:String,default:"Submit"},onlyIcon:{type:String,default:null},loadingText:{type:String,default:"Getting Response"},fetchingData:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},type:{type:String,default:"Submit"}},computed:{buttonText:function(){return this.fetchingData?this.loadingText:this.text},disable:function(){return this.fetchingData}},methods:{btnClicked:function(){"Submit"!==this.type&&this.$emit("clicked")}}},o=(r(350),r(14)),component=Object(o.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("button",{staticClass:"ajax-btn",attrs:{disabled:t.disable||t.disabled,type:t.type},on:{"&click":function(e){return t.btnClicked.apply(null,arguments)}}},[t.fetchingData?r("spinner",{class:{"mr-15":!t.onlyIcon},attrs:{color:t.color}}):t._e(),t._v(" "),t.onlyIcon&&!t.fetchingData?r("i",{staticClass:"icon",class:t.onlyIcon}):t._e(),t._v(" "),t.onlyIcon?t._e():r("span",[t._v("\n    "+t._s(t.buttonText)+"\n  ")])],1)}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{Spinner:r(44).default})},345:function(t,e,r){var content=r(351);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[t.i,content,""]]),content.locals&&(t.exports=content.locals);(0,r(52).default)("91a8990a",content,!0,{sourceMap:!1})},346:function(t,e,r){"use strict";r.r(e);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(6),o=r(11);function l(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}var d={name:"ErrorFormatter",props:{type:{type:String,default:"form"}},computed:function(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?l(Object(source),!0).forEach((function(e){Object(n.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):l(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}({errorData:function(){return this.errors&&void 0!==this.errors[this.type]?this.errors[this.type]:null}},Object(o.c)("ui",["errors"]))},c=d,m=r(14),component=Object(m.a)(c,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return t.errorData?r("ul",{staticClass:"error-list mb-15"},[r("li",{staticClass:"mb-10"},[t._v("Following error occurred")]),t._v(" "),t._l(t.errorData,(function(e,n){return r("li",{key:n},[t._v("\n    "+t._s(e)+"\n  ")])}))],2):t._e()}),[],!1,null,null,null);e.default=component.exports},350:function(t,e,r){"use strict";r(345)},351:function(t,e,r){var n=r(51)(!1);n.push([t.i,".ajax-btn{display:flex;justify-content:center;align-items:center}button:disabled,button[disabled]{opacity:.6;cursor:no-drop}",""]),t.exports=n},352:function(t,e,r){"use strict";r(29),r(40);e.a={data:function(){return{allowedImageExtensions:/(\.jpg|\.jpeg|\.png|\.svg|\.webp|\.gif)$/i,allowedVideoExtensions:/(\.mp4)$/i,passwordLength:6,maxImageSize:1,maxVideoSize:2,reg:/^(([^<>()\]\\.,;:\s@"]+(\.[^<>()\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/}},methods:{isValidImage:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.maxImageSize,r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2];return t.size>1024*e*1024?"File size must be less than ".concat(e,"MB"):r&&!this.allowedImageExtensions.exec(t.name)?"Invalid File Type":r||this.allowedVideoExtensions.exec(t.name)?null:"Invalid File Type"},isValidEmail:function(t){return this.reg.test(t)},isValidLength:function(t){return t&&t.length>=this.passwordLength||!1}}}},364:function(t,e,r){"use strict";r.r(e);var n={name:"PasswordField",data:function(){return{password:"",passwordFieldType:"password"}},props:{isInvalid:{type:Boolean,default:!1},value:{type:String}},watch:{value:function(t){this.password=t}},mixins:[],components:{},computed:{isPasswordTypePassword:function(){return"password"===this.passwordFieldType}},methods:{passwordFieldToggle:function(){this.isPasswordTypePassword?this.passwordFieldType="text":this.passwordFieldType="password"}},mounted:function(){this.password=this.value}},o=r(14),component=Object(o.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"icon-input right-icon"},[r("i",{staticClass:"icon password-icon"}),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:t.password,expression:"password",modifiers:{trim:!0}}],class:{invalid:!t.password||t.isInvalid},attrs:{type:t.passwordFieldType,placeholder:"Password"},domProps:{value:t.password},on:{change:function(e){return t.$emit("change",t.password)},input:function(e){e.target.composing||(t.password=e.target.value.trim())},blur:function(e){return t.$forceUpdate()}}}),t._v(" "),r("i",{staticClass:"icon",class:t.isPasswordTypePassword?"eye-close-icon":"eye-icon",on:{click:t.passwordFieldToggle}})])}),[],!1,null,null,null);e.default=component.exports},731:function(t,e,r){"use strict";r.r(e);r(21),r(20),r(22),r(8),r(27),r(17),r(28);var n=r(3),o=r(6),l=(r(23),r(29),r(62),r(11)),d=r(343),c=r(352),m=r(44),f=r(346),w=r(364);function v(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}function h(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?v(Object(source),!0).forEach((function(e){Object(o.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):v(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}var _={name:"profile",middleware:["auth"],data:function(){return{formSubmitting:!1,hasError:!1,adminData:{id:null,name:null,username:null,email:null,password:""},password:{password:null,confirm_password:null,new_password:null},tabId:["profile-update","password-update"],activeTab:"profile-update",tabs:[{title:"Update Profile",tabId:"profile-update"},{title:"Change Password",tabId:"password-update"}]}},mixins:[c.a],components:{ErrorFormatter:f.default,AjaxButton:d.default,Spinner:m.default,PasswordField:w.default},computed:h({isInvalidEmail:function(){return this.adminData.email&&!this.isValidEmail(this.adminData.email)}},Object(l.c)("admin",["profile"])),methods:h(h({tabSelect:function(t){t.tabId!==this.$route.hash.replace("#","")&&this.$router.push({hash:t.tabId}),this.hasError=!1,this.activeTab=t.tabId},checkForm:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t.adminData.username&&t.adminData.email&&t.adminData.password)){e.next=15;break}return t.formSubmitting=!0,e.prev=2,e.next=5,t.updateProfile(t.adminData);case 5:e.sent&&(t.adminData=Object.assign({},t.profile)),e.next=12;break;case 9:return e.prev=9,e.t0=e.catch(2),e.abrupt("return",t.$nuxt.error(e.t0));case 12:t.formSubmitting=!1,e.next=16;break;case 15:t.hasError=!0;case 16:case"end":return e.stop()}}),e,null,[[2,9]])})))()},updatePassword:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){var data;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t.password.password&&t.password.new_password&&t.password.confirm_password&&t.password.new_password===t.password.confirm_password)){e.next=12;break}return t.formSubmitting=!0,e.next=4,t.setPassword(t.password);case 4:if(200!==(null==(data=e.sent)?void 0:data.status)){e.next=9;break}return e.next=8,t.$auth.logout();case 8:window.location.reload();case 9:t.formSubmitting=!1,e.next=13;break;case 12:t.hasError=!0;case 13:case"end":return e.stop()}}),e)})))()}},Object(l.b)("ui",["setErrors"])),Object(l.b)("admin",["updateProfile","setPassword"])),mounted:function(){this.activeTab=this.$route.hash?this.$route.hash.replace("#",""):this.tabs[0].tabId,this.adminData=Object.assign({},this.profile)}},y=r(14),component=Object(y.a)(_,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return t.$can("profile","view")?r("div",{staticClass:"tab-sidebar"},[r("h5",{staticClass:"title bold"},[t._v("\n    Admin Profile\n  ")]),t._v(" "),r("div",{staticClass:"dply-felx"},[r("ul",{staticClass:"left-area"},t._l(t.tabs,(function(e,n){return r("li",{key:n,class:{active:e.tabId===t.activeTab},on:{click:function(r){return t.tabSelect(e)}}},[t._v("\n        "+t._s(e.title)+"\n      ")])})),0),t._v(" "),r("div",{staticClass:"right-area"},[t.tabId[0]===t.activeTab?r("form",{staticClass:"pos-rel",class:{"has-error":t.hasError},on:{submit:function(e){return e.preventDefault(),t.checkForm.apply(null,arguments)}}},[r("error-formatter"),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Name\n          ")]),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model",value:t.adminData.name,expression:"adminData.name"}],attrs:{type:"text",placeholder:"Name",name:"name"},domProps:{value:t.adminData.name},on:{input:function(e){e.target.composing||t.$set(t.adminData,"name",e.target.value)}}})]),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Username\n          ")]),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:t.adminData.username,expression:"adminData.username",modifiers:{trim:!0}}],class:{invalid:!t.adminData.username&&t.hasError},attrs:{type:"text",placeholder:"Username",name:"username"},domProps:{value:t.adminData.username},on:{input:function(e){e.target.composing||t.$set(t.adminData,"username",e.target.value.trim())},blur:function(e){return t.$forceUpdate()}}}),t._v(" "),!t.adminData.username&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Username is required\n          ")]):t._e()]),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Email\n          ")]),t._v(" "),r("input",{directives:[{name:"model",rawName:"v-model.trim",value:t.adminData.email,expression:"adminData.email",modifiers:{trim:!0}}],class:{invalid:!t.adminData.email||t.isInvalidEmail},attrs:{type:"text",placeholder:"Your Email"},domProps:{value:t.adminData.email},on:{input:function(e){e.target.composing||t.$set(t.adminData,"email",e.target.value.trim())},blur:function(e){return t.$forceUpdate()}}}),t._v(" "),!t.adminData.email&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Email is required\n          ")]):t.isInvalidEmail&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Email is invalid\n          ")]):t._e()]),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Password\n          ")]),t._v(" "),r("password-field",{attrs:{value:t.adminData.password,"is-invalid":!t.isValidLength(t.adminData.password)},on:{change:function(e){t.adminData.password=e}}}),t._v(" "),!t.adminData.password&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Password is required\n          ")]):!t.isValidLength(t.adminData.password)&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Invalid password length\n          ")]):t._e()],1),t._v(" "),r("div",{staticClass:"oflow-hidden"},[t.$can("profile","edit")?r("ajax-button",{staticClass:"primary-btn",attrs:{"fetching-data":t.formSubmitting}}):t._e()],1)],1):t._e(),t._v(" "),t.tabId[1]===t.activeTab?r("form",{class:{"has-error":t.hasError},on:{submit:function(e){return e.preventDefault(),t.updatePassword.apply(null,arguments)}}},[r("error-formatter"),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Password\n          ")]),t._v(" "),r("password-field",{attrs:{value:t.password.password,"is-invalid":!t.isValidLength(t.password.password)},on:{change:function(e){t.password.password=e}}}),t._v(" "),!t.password.password&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Password is required\n          ")]):!t.isValidLength(t.password.password)&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Invalid password length\n          ")]):t._e()],1),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            New Password\n          ")]),t._v(" "),r("password-field",{attrs:{value:t.password.new_password,"is-invalid":!t.isValidLength(t.password.new_password)},on:{change:function(e){t.password.new_password=e}}}),t._v(" "),!t.password.new_password&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            New Password is required\n          ")]):!t.isValidLength(t.password.new_password)&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Invalid password length\n          ")]):t._e()],1),t._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[t._v("\n            Confirm Password\n          ")]),t._v(" "),r("password-field",{attrs:{value:t.password.confirm_password,"is-invalid":!t.isValidLength(t.password.confirm_password)},on:{change:function(e){t.password.confirm_password=e}}}),t._v(" "),!t.password.confirm_password&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Confirm Password is required\n          ")]):!t.isValidLength(t.password.confirm_password)&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Invalid password length\n          ")]):t.password.confirm_password!==t.password.new_password&&t.hasError?r("span",{staticClass:"error"},[t._v("\n            Two password didn't match\n          ")]):t._e()],1),t._v(" "),r("div",{staticClass:"oflow-hidden"},[t.$can("profile","edit")?r("ajax-button",{staticClass:"primary-btn",attrs:{"fetching-data":t.formSubmitting}}):t._e()],1)],1):t._e()])])]):t._e()}),[],!1,null,"21e1c91d",null);e.default=component.exports;installComponents(component,{ErrorFormatter:r(346).default,PasswordField:r(364).default,AjaxButton:r(343).default})}}]);