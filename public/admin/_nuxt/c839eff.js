(window.webpackJsonp=window.webpackJsonp||[]).push([[26],{620:function(t,e,n){"use strict";n.r(e);var l=n(59),c={name:"FooterImageLink",data:function(){return{deleting:!1}},props:{result:{type:Object,default:function(){return{payment_links:[],social_links:[]}}}},mixins:[l.a],components:{},computed:{},methods:{},mounted:function(){}},o=n(14),component=Object(o.a)(c,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"dply-felx"},[n("h4",[t._v("Payment links")]),t._v(" "),t.$can("footer_link","create")?n("nuxt-link",{staticClass:"lite-btn button",attrs:{to:"/footer-links/add?type=1"}},[t._v("\n      Add new link\n    ")]):t._e()],1),t._v(" "),n("div",[n("div",{staticClass:"table-wrapper mt-0 mb-15"},[t.result.payment_links.length?n("table",{staticClass:"mtb-15"},[t._m(0),t._v(" "),t._l(t.result.payment_links,(function(e,l){return n("tr",{key:l},[n("td",[n("img",{attrs:{src:t.getThumbImageURL(e.image)}})]),t._v(" "),n("td",[t._v(t._s(e.title))]),t._v(" "),n("td",{staticClass:"status",class:{active:1==e.status}},[n("span",[t._v(t._s(t.getStatus(e.status)))])]),t._v(" "),n("td",{staticClass:"right-text"},[t.$can("footer_link","edit")?n("nuxt-link",{staticClass:"edit-btn lite-btn button",attrs:{to:"/footer-links/"+e.id+"?type=1"}},[n("i",{staticClass:"edit-icon icon"})]):t._e(),t._v(" "),t.$can("footer_link","delete")?n("button",{staticClass:"delete-btn lite-btn",on:{click:function(n){return n.preventDefault(),t.$emit("delete-item",e.id)}}},[n("i",{staticClass:"delete-icon icon"})]):t._e()],1)])}))],2):n("p",[t._v("\n        No data found.\n      ")])])]),t._v(" "),n("div",{staticClass:"dply-felx"},[n("h4",[t._v("\n      Social links\n    ")]),t._v(" "),t.$can("footer_link","create")?n("nuxt-link",{staticClass:"lite-btn button",attrs:{to:"/footer-links/add?type=2"}},[t._v("\n      Add new link\n    ")]):t._e()],1),t._v(" "),n("div",{staticClass:"table-wrapper mt-0"},[t.result.social_links.length?n("table",{staticClass:"mtb-15"},[t._m(1),t._v(" "),t._l(t.result.social_links,(function(e,l){return n("tr",{key:l},[n("td",[n("img",{attrs:{src:t.getThumbImageURL(e.image)}})]),t._v(" "),n("td",[t._v(t._s(e.title))]),t._v(" "),n("td",{staticClass:"status",class:{active:1==e.status}},[n("span",[t._v(t._s(t.getStatus(e.status)))])]),t._v(" "),n("td",{staticClass:"right-text"},[t.$can("footer_link","edit")?n("nuxt-link",{staticClass:"edit-btn lite-btn button",attrs:{to:"/footer-links/"+e.id+"?type=2"}},[n("i",{staticClass:"edit-icon icon"})]):t._e(),t._v(" "),t.$can("footer_link","delete")?n("button",{staticClass:"delete-btn lite-btn",on:{click:function(n){return n.preventDefault(),t.$emit("delete-item",e.id)}}},[n("i",{staticClass:"delete-icon icon"})]):t._e()],1)])}))],2):n("p",[t._v("\n      No data found.\n    ")])])])}),[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("tr",{staticClass:"lite-bold"},[n("th",[t._v("Image")]),t._v(" "),n("th",[t._v("Title")]),t._v(" "),n("th",[t._v("Status")]),t._v(" "),n("th")])},function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("tr",{staticClass:"lite-bold"},[n("th",[t._v("Image")]),t._v(" "),n("th",[t._v("Title")]),t._v(" "),n("th",[t._v("Status")]),t._v(" "),n("th")])}],!1,null,null,null);e.default=component.exports}}]);