(window.webpackJsonp=window.webpackJsonp||[]).push([[51],{364:function(e,o,t){"use strict";t.r(o);var r={name:"PasswordField",data:function(){return{password:"",passwordFieldType:"password"}},props:{isInvalid:{type:Boolean,default:!1},value:{type:String}},watch:{value:function(e){this.password=e}},mixins:[],components:{},computed:{isPasswordTypePassword:function(){return"password"===this.passwordFieldType}},methods:{passwordFieldToggle:function(){this.isPasswordTypePassword?this.passwordFieldType="text":this.passwordFieldType="password"}},mounted:function(){this.password=this.value}},n=t(14),component=Object(n.a)(r,(function(){var e=this,o=e.$createElement,t=e._self._c||o;return t("div",{staticClass:"icon-input right-icon"},[t("i",{staticClass:"icon password-icon"}),e._v(" "),t("input",{directives:[{name:"model",rawName:"v-model.trim",value:e.password,expression:"password",modifiers:{trim:!0}}],class:{invalid:!e.password||e.isInvalid},attrs:{type:e.passwordFieldType,placeholder:"Password"},domProps:{value:e.password},on:{change:function(o){return e.$emit("change",e.password)},input:function(o){o.target.composing||(e.password=o.target.value.trim())},blur:function(o){return e.$forceUpdate()}}}),e._v(" "),t("i",{staticClass:"icon",class:e.isPasswordTypePassword?"eye-close-icon":"eye-icon",on:{click:e.passwordFieldToggle}})])}),[],!1,null,null,null);o.default=component.exports}}]);