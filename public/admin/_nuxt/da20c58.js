/*! For license information please see LICENSES */
(window.webpackJsonp=window.webpackJsonp||[]).push([[89,24],{348:function(e,t,r){e.exports=function(){"use strict";var g="undefined"!=typeof document&&document.documentMode,e={rootMargin:"0px",threshold:0,load:function(e){if("picture"===e.nodeName.toLowerCase()){var t=e.querySelector("img"),r=!1;null===t&&(t=document.createElement("img"),r=!0),g&&e.getAttribute("data-iesrc")&&(t.src=e.getAttribute("data-iesrc")),e.getAttribute("data-alt")&&(t.alt=e.getAttribute("data-alt")),r&&e.append(t)}if("video"===e.nodeName.toLowerCase()&&!e.getAttribute("data-src")&&e.children){for(var a=e.children,n=void 0,i=0;i<=a.length-1;i++)(n=a[i].getAttribute("data-src"))&&(a[i].src=n);e.load()}e.getAttribute("data-poster")&&(e.poster=e.getAttribute("data-poster")),e.getAttribute("data-src")&&(e.src=e.getAttribute("data-src")),e.getAttribute("data-srcset")&&e.setAttribute("srcset",e.getAttribute("data-srcset"));var o=",";if(e.getAttribute("data-background-delimiter")&&(o=e.getAttribute("data-background-delimiter")),e.getAttribute("data-background-image"))e.style.backgroundImage="url('"+e.getAttribute("data-background-image").split(o).join("'),url('")+"')";else if(e.getAttribute("data-background-image-set")){var l=e.getAttribute("data-background-image-set").split(o),u=l[0].substr(0,l[0].indexOf(" "))||l[0];u=-1===u.indexOf("url(")?"url("+u+")":u,1===l.length?e.style.backgroundImage=u:e.setAttribute("style",(e.getAttribute("style")||"")+"background-image: "+u+"; background-image: -webkit-image-set("+l+"); background-image: image-set("+l+")")}e.getAttribute("data-toggle-class")&&e.classList.toggle(e.getAttribute("data-toggle-class"))},loaded:function(){}};function t(e){e.setAttribute("data-loaded",!0)}var r=function(e){return"true"===e.getAttribute("data-loaded")},n=function(e){var t=1<arguments.length&&void 0!==arguments[1]?arguments[1]:document;return e instanceof Element?[e]:e instanceof NodeList?e:t.querySelectorAll(e)};return function(){var o,a,l=0<arguments.length&&void 0!==arguments[0]?arguments[0]:".lozad",c=1<arguments.length&&void 0!==arguments[1]?arguments[1]:{},d=Object.assign({},e,c),i=d.root,m=d.rootMargin,f=d.threshold,u=d.load,g=d.loaded,s=void 0;"undefined"!=typeof window&&window.IntersectionObserver&&(s=new IntersectionObserver((o=u,a=g,function(e,n){e.forEach((function(e){(0<e.intersectionRatio||e.isIntersecting)&&(n.unobserve(e.target),r(e.target)||(o(e.target),t(e.target),a(e.target)))}))}),{root:i,rootMargin:m,threshold:f}));for(var v,h=n(l,i),b=0;b<h.length;b++)(v=h[b]).getAttribute("data-placeholder-background")&&(v.style.background=v.getAttribute("data-placeholder-background"));return{observe:function(){for(var e=n(l,i),o=0;o<e.length;o++)r(e[o])||(s?s.observe(e[o]):(u(e[o]),t(e[o]),g(e[o])))},triggerLoad:function(e){r(e)||(u(e),t(e),g(e))},observer:s}}}()},513:function(e,t,r){"use strict";r.d(t,"a",(function(){return c}));var n=r(133);var o=r(161),l=r(103);function c(e){return function(e){if(Array.isArray(e))return Object(n.a)(e)}(e)||Object(o.a)(e)||Object(l.a)(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}},762:function(e,t,r){"use strict";r.r(t);r(21),r(20),r(27),r(17),r(28);var n=r(3),o=r(513),l=r(6),c=(r(23),r(54),r(8),r(29),r(82),r(62),r(68),r(525),r(45),r(526),r(527),r(528),r(529),r(530),r(531),r(532),r(533),r(534),r(535),r(536),r(537),r(538),r(539),r(540),r(541),r(46),r(22),r(80),r(357)),d=r(59),m=r(344),f=r(11);function v(object,e){var t=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);e&&(r=r.filter((function(e){return Object.getOwnPropertyDescriptor(object,e).enumerable}))),t.push.apply(t,r)}return t}function h(e){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?v(Object(source),!0).forEach((function(t){Object(l.a)(e,t,source[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(source)):v(Object(source)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(source,t))}))}return e}var P={name:"roles-permissions",middleware:["auth"],data:function(){return{allSelected:!1,groupPermissions:[],selectedPermissions:[],result:{id:"",name:"",updated_permissions:[],permissions:[]}}},mixins:[d.a],components:{DataPage:c.default,Dropdown:m.default},watch:{resultPermissions:function(e){this.selectedPermissions=null==e?void 0:e.map((function(i){return i.id}))}},computed:h({resultPermissions:function(){var e;return null===(e=this.result)||void 0===e?void 0:e.permissions}},Object(f.c)("common",["allPermissions"])),methods:h({groupBy:function(e,t){return e.reduce((function(e,r){var n=r[t];return e[n]||(e[n]=[]),e[n].push(r),e}),{})},formatPermissionName:function(e){return this.capitalizeFirstLetter(null==e?void 0:e.replace("_"," ").split(".")[1])},formatGroupName:function(e){return this.capitalizeFirstLetter(null==e?void 0:e.replace(/\.|_/g," "))},capitalizeFirstLetter:function(e){return(null==e?void 0:e.charAt(0).toUpperCase())+(null==e?void 0:e.slice(1))},selectAll:function(){if(this.groupPermissions=[],this.selectedPermissions=[],this.allSelected)for(var i in this.allPermissions)this.selectedPermissions.push(this.allPermissions[i].id),this.groupPermissions.push(this.allPermissions[i].group_name);this.groupPermissions=Object(o.a)(new Set(this.groupPermissions)),this.result.updated_permissions=this.selectedPermissions},selectGroup:function(data,e){var t=this.allPermissions.filter((function(i){return i.group_name===data})).map((function(i){return i.id}));e.target.checked?this.selectedPermissions=Object(o.a)(new Set([].concat(Object(o.a)(this.selectedPermissions),Object(o.a)(t)))):this.selectedPermissions=this.selectedPermissions.filter((function(i){return-1===t.indexOf(i)})),this.result.updated_permissions=this.selectedPermissions},permissionChanged:function(){this.groupPermissions=[],this.allSelected=!1,this.result.updated_permissions=this.selectedPermissions}},Object(f.b)("common",["getAllList"])),mounted:function(){var e=this;return Object(n.a)(regeneratorRuntime.mark((function t(){return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(e.allPermissions){t.next=9;break}return t.prev=1,t.next=4,e.getAllList({api:"allPermissions",mutation:"SET_ALL_PERMISSIONS"});case 4:t.next=9;break;case 6:return t.prev=6,t.t0=t.catch(1),t.abrupt("return",e.$nuxt.error(t.t0));case 9:case"end":return t.stop()}}),t,null,[[1,6]])})))()}},y=r(14),component=Object(y.a)(P,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return e.allPermissions?r("data-page",{ref:"dataPage",attrs:{"set-api":"setRole","get-api":"getRole","route-name":"roles-permissions","empty-store-variable":"allRoles",name:"roles permissions","validation-keys":["name"],result:e.result,gate:"role"},on:{result:function(t){e.result=t}},scopedSlots:e._u([{key:"form",fn:function(t){var n=t.hasError;return[r("div",{staticClass:"input-wrapper"},[r("label",[e._v("Name")]),e._v(" "),r("input",{directives:[{name:"model",rawName:"v-model",value:e.result.name,expression:"result.name"}],ref:"title",class:{invalid:!e.result.name&&n},attrs:{type:"text",placeholder:"Title",name:"title"},domProps:{value:e.result.name},on:{input:function(t){t.target.composing||e.$set(e.result,"name",t.target.value)}}}),e._v(" "),!e.result.name&&n?r("span",{staticClass:"error"},[e._v("Name is required")]):e._e()]),e._v(" "),r("div",{staticClass:"input-wrapper"},[r("label",[e._v("Permissions")]),e._v(" "),r("div",{staticClass:"b-b mb-10 mb-md-15 pb-10"},[r("input",{directives:[{name:"model",rawName:"v-model",value:e.allSelected,expression:"allSelected"}],staticClass:"styled-checkbox",attrs:{id:"styled-checkbox-all",type:"checkbox"},domProps:{checked:Array.isArray(e.allSelected)?e._i(e.allSelected,null)>-1:e.allSelected},on:{change:[function(t){var r=e.allSelected,n=t.target,o=!!n.checked;if(Array.isArray(r)){var l=e._i(r,null);n.checked?l<0&&(e.allSelected=r.concat([null])):l>-1&&(e.allSelected=r.slice(0,l).concat(r.slice(l+1)))}else e.allSelected=o},e.selectAll]}}),e._v(" "),r("label",{staticClass:"mtb-5",attrs:{for:"styled-checkbox-all"}},[e._v("\n            All\n          ")])]),e._v(" "),e._l(e.groupBy(e.allPermissions,"group_name"),(function(t,n,o){return r("div",{key:o,staticClass:"permission-group"},[r("div",[r("input",{directives:[{name:"model",rawName:"v-model",value:e.groupPermissions,expression:"groupPermissions"}],staticClass:"styled-checkbox",attrs:{id:"styled-checkbox-"+n,type:"checkbox"},domProps:{value:n,checked:Array.isArray(e.groupPermissions)?e._i(e.groupPermissions,n)>-1:e.groupPermissions},on:{change:[function(t){var r=e.groupPermissions,o=t.target,l=!!o.checked;if(Array.isArray(r)){var c=n,d=e._i(r,c);o.checked?d<0&&(e.groupPermissions=r.concat([c])):d>-1&&(e.groupPermissions=r.slice(0,d).concat(r.slice(d+1)))}else e.groupPermissions=l},function(t){return e.selectGroup(n,t)}]}}),e._v(" "),r("label",{staticClass:"mtb-5 mt-md-15",attrs:{for:"styled-checkbox-"+n}},[e._v("\n              "+e._s(e.formatGroupName(n))+"\n            ")])]),e._v(" "),r("div",{staticClass:"permission-item "},e._l(t,(function(i,t){return r("span",{key:o+"-"+t,staticClass:"mr-15"},[r("input",{directives:[{name:"model",rawName:"v-model",value:e.selectedPermissions,expression:"selectedPermissions"}],staticClass:"styled-checkbox",attrs:{id:"checkbox-"+o+"-"+t,type:"checkbox"},domProps:{value:i.id,checked:Array.isArray(e.selectedPermissions)?e._i(e.selectedPermissions,i.id)>-1:e.selectedPermissions},on:{change:[function(t){var r=e.selectedPermissions,n=t.target,o=!!n.checked;if(Array.isArray(r)){var l=i.id,c=e._i(r,l);n.checked?c<0&&(e.selectedPermissions=r.concat([l])):c>-1&&(e.selectedPermissions=r.slice(0,c).concat(r.slice(c+1)))}else e.selectedPermissions=o},e.permissionChanged]}}),e._v(" "),r("label",{staticClass:"mtb-7-5",attrs:{for:"checkbox-"+o+"-"+t}},[e._v("\n                "+e._s(e.formatPermissionName(i.name))+"\n              ")])])})),0)])}))],2)]}}],null,!1,3597914751)}):e._e()}),[],!1,null,"de0a2e46",null);t.default=component.exports}}]);