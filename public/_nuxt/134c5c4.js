(window.webpackJsonp=window.webpackJsonp||[]).push([[62],{474:function(t,e,r){"use strict";r.r(e);r(19),r(21),r(24),r(6),r(26),r(20),r(27);var n=r(9),o=r(10);function c(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}var l={data:function(){return{}},props:{product:{type:Object}},components:{},mixins:[],computed:function(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?c(Object(source),!0).forEach((function(e){Object(n.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):c(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}({currentURL:function(){var t=window.location.origin;return this.$route?t+this.$route.path:t},metaTitle:function(){return this.site_setting.meta_title},metaDescription:function(){return this.site_setting.meta_description},productTags:function(){var t,e;return null!==(t=null===(e=this.product)||void 0===e?void 0:e.tags)&&void 0!==t?t:""}},Object(o.c)("common",["site_setting"])),methods:{},mounted:function(){}},h=l,m=r(13),component=Object(m.a)(h,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"flex start mt-15 mt-sm social-share"},[e("span",{staticClass:"mr-10 color-lite hide-sm"},[t._v("\n  "+t._s(t.$t("socialShare.share"))+":\n")]),t._v(" "),e("ShareNetwork",{attrs:{network:"facebook",url:t.currentURL,title:t.metaTitle,description:t.metaDescription,quote:t.metaTitle,hashtags:t.productTags}},[e("i",{staticClass:"icon facebook-icon"}),t._v(" "),e("span",{staticClass:"hide block-sm"},[t._v("\n    "+t._s(t.$t("socialShare.facebook"))+"\n  ")])]),t._v(" "),e("ShareNetwork",{staticClass:"mlr-5",attrs:{network:"twitter",url:t.currentURL,title:t.metaTitle,description:t.metaDescription,quote:t.metaTitle,hashtags:t.productTags}},[e("i",{staticClass:"icon twitter-icon"}),t._v(" "),e("span",{staticClass:"hide block-sm"},[t._v("\n    "+t._s(t.$t("socialShare.twitter"))+"\n  ")])]),t._v(" "),e("ShareNetwork",{attrs:{network:"pinterest",url:t.currentURL,title:t.metaTitle,description:t.metaDescription,quote:t.metaTitle,hashtags:t.productTags}},[e("i",{staticClass:"icon pinterest-icon"}),t._v(" "),e("span",{staticClass:"hide block-sm"},[t._v("\n     "+t._s(t.$t("socialShare.pinterest"))+"\n  ")])])],1)}),[],!1,null,null,null);e.default=component.exports}}]);