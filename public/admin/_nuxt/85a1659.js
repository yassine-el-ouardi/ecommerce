(window.webpackJsonp=window.webpackJsonp||[]).push([[42,16],{344:function(e,t,o){"use strict";o.r(t);o(21),o(61),o(81);var n=o(60),r={name:"Dropdown",data:function(){return{optionsData:this.options,searched:"",dropdownOpen:!1,hasLayer:this.layer,selectedKeyData:""}},mounted:function(){this.selectedKeyData=this.selectedKey,this.processSelected(this.options)},watch:{options:function(e){this.processSelected(e)},selectedKey:function(e){this.selectedKeyData=e},searched:function(e){this.doSearch(e)}},props:{disabled:{type:Boolean,default:!1},defaultNull:{type:Boolean,default:!1},positionFixed:{type:Boolean,default:!0},options:{type:Object,default:function(){return{0:{title:"--------------"}}}},selectedKey:{default:function(){return Object.keys(this.options)[0]}},keyName:{type:String,default:"title"},searching:{type:Boolean,default:!1},layer:{type:Boolean,default:!1}},directives:{outsideClick:n.a},computed:{currentId:function(){return"dropdown-".concat(this._uid)},isSmallerDevice:function(){return window.innerWidth<=768},opt:function(){var e;return null!==(e=this.optionsData)&&void 0!==e?e:null},currentKey:function(){return this.selectedKeyData&&this.options[this.selectedKeyData]?this.selectedKeyData:Object.keys(this.optionsData)[0]},selectedValue:function(){return this.opt&&this.opt[this.currentKey]&&this.opt[this.currentKey][this.keyName]?this.opt[this.currentKey][this.keyName]:"--------------"}},methods:{processSelected:function(e){this.selectedKey||this.defaultNull?!this.selectedKey&&this.defaultNull?(e[0]={title:"--------------"},this.selectedKeyData="0"):this.defaultNull&&(e[0]={title:"--------------"}):(this.selectedKeyData=Object.keys(e)[0],this.$emit("clicked",{key:this.selectedKeyData,value:e[0]})),this.optionsData=e},doSearch:function(e){this.optionsData={};var object=this.options;for(var t in object)object[t][this.keyName].toLowerCase().includes(e.toLowerCase())&&(this.opt[t]=object[t])},openDropdown:function(){var e=this;if(this.disabled)return!1;this.isSmallerDevice&&(this.hasLayer=!0),this.hasLayer&&this.positionFixed&&document.body.classList.add("no-scroll"),this.dropdownOpen=!this.dropdownOpen,this.$emit("value",this.dropdownOpen),this.searching&&this.dropdownOpen&&this.$nextTick((function(){e.$refs.searcBox.focus()}))},closeDropdown:function(){this.positionFixed&&document.body.classList.remove("no-scroll"),this.dropdownOpen=!1,this.$emit("value",this.dropdownOpen)},clicked:function(e,t){this.closeDropdown(),this.searched="",("0"!==e&&this.currentKey!==e||this.defaultNull&&this.currentKey!==e)&&(this.selectedKeyData=e,this.$emit("clicked",{key:e,value:t}))}}},d=(o(353),o(14)),component=Object(d.a)(r,(function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"custom-dropdown",class:{open:e.dropdownOpen}},[o("span",{class:{disabled:e.disabled},attrs:{"data-ignore":e.currentId},on:{click:function(t){return t.preventDefault(),e.openDropdown.apply(null,arguments)}}},[e._v("\n    "+e._s(e.selectedValue)+"\n    "),o("i",{staticClass:"icon black ignore-click",class:[{"arrow-up":e.dropdownOpen},{"arrow-down":!e.dropdownOpen}]})]),e._v(" "),e.dropdownOpen?o("div",{staticClass:"dropdown-wrapper"},[e.hasLayer?o("div",{staticClass:"layer",attrs:{"data-ignore":e.currentId},on:{click:e.closeDropdown}}):e._e(),e._v(" "),o("div",{directives:[{name:"outside-click",rawName:"v-outside-click",value:e.closeDropdown,expression:"closeDropdown"}],staticClass:"dropdown-inner",attrs:{id:e.currentId}},[e.searching?o("input",{directives:[{name:"model",rawName:"v-model",value:e.searched,expression:"searched"}],ref:"searcBox",attrs:{type:"text"},domProps:{value:e.searched},on:{input:function(t){t.target.composing||(e.searched=t.target.value)}}}):e._e(),e._v(" "),o("ul",e._l(e.opt,(function(data,t){return o("li",{key:t,class:{active:""+e.selectedKeyData==""+t},on:{click:function(o){return o.preventDefault(),e.clicked(t,data)}}},[e._v("\n          "+e._s(data[e.keyName])+"\n        ")])})),0)])]):e._e()])}),[],!1,null,null,null);t.default=component.exports},347:function(e,t,o){var content=o(354);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[e.i,content,""]]),content.locals&&(e.exports=content.locals);(0,o(52).default)("178af679",content,!0,{sourceMap:!1})},353:function(e,t,o){"use strict";o(347)},354:function(e,t,o){var n=o(51)(!1);n.push([e.i,".dropdown-inner::-webkit-scrollbar-track{background-color:rgba(0,0,0,.1);background-color:#eee}.dropdown-inner::-webkit-scrollbar{width:8px}.dropdown-inner::-webkit-scrollbar-thumb{background-color:rgba(0,0,0,.15);border-radius:40px}.custom-dropdown+button>.undo-icon{opacity:.8;transform:scale(.8)}.custom-dropdown{position:relative;display:inline-block;line-height:0}.custom-dropdown .layer{z-index:1}.custom-dropdown span{display:flex;align-items:center;justify-content:space-between;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;padding:0 15px 0 20px;height:42px;line-height:42px;background:linear-gradient(180deg,#f7f8fa,#e7e9ec);border:1px solid #bbb;border-radius:5px;font-size:.95em;min-width:80px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:all .1s}.custom-dropdown span i{opacity:.5;margin-left:10px}.custom-dropdown span:hover:not(.disabled){background:#f6f4f4!important}.custom-dropdown span:active:not(.disabled){box-shadow:0 0 2px 1px #4285f4}.custom-dropdown .dropdown-inner{position:absolute;top:100%;left:0;min-width:200px;background:#fff;padding:7.5px 5px;border-radius:5px;box-shadow:0 2px 10px rgba(0,0,0,.1);z-index:2;max-height:400px;overflow:auto}.custom-dropdown .dropdown-inner>input{padding:0 10px;border:1px solid #ddd;margin-bottom:5px}.custom-dropdown .dropdown-inner ul{max-height:340px}.custom-dropdown .dropdown-inner ul li{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;cursor:pointer;display:block;padding:7.5px 20px;transition:all .1s;margin:0}.custom-dropdown .dropdown-inner ul li:hover{background:#eee}.custom-dropdown .dropdown-inner ul .active{background:#f6f6f7}.right-dropdown.custom-dropdown .dropdown-inner{left:auto;right:0}.open span{box-shadow:0 0 1px 1px #4285f4}@media only screen and (max-width:992px){.custom-dropdown .dropdown-wrapper{left:auto;right:0}}@media only screen and (max-width:768px){.custom-dropdown .dropdown-wrapper{position:fixed;top:0;left:0!important;right:0!important;bottom:0;z-index:10}.custom-dropdown .dropdown-wrapper .layer{top:0;display:block}.custom-dropdown .dropdown-wrapper .dropdown-inner{top:50%;left:50%;transform:translate(-50%,-50%)}.custom-dropdown .dropdown-wrapper ul{max-height:60vh}.custom-dropdown .dropdown-wrapper ul li{padding:7.5px 15px}}@media only screen and (max-width:576px){.custom-dropdown span{padding:0 10px 0 15px}}",""]),e.exports=n},550:function(e,t,o){"use strict";o.r(t);o(20),o(22),o(8),o(27),o(17),o(28);var n=o(6),r=(o(21),o(344));function d(object,e){var t=Object.keys(object);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(object);e&&(o=o.filter((function(e){return Object.getOwnPropertyDescriptor(object,e).enumerable}))),t.push.apply(t,o)}return t}function c(e){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?d(Object(source),!0).forEach((function(t){Object(n.a)(e,t,source[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(source)):d(Object(source)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(source,t))}))}return e}var l={name:"SliderFormTab",data:function(){return{selectedIdsData:null}},props:{title:{type:String,default:""},selectedIds:{type:Object,default:function(){return null}},options:{type:Object,default:function(){return null}},route:{type:Object,default:function(){return null}}},mixins:[],components:{Dropdown:r.default},computed:{},methods:{toggle:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;e?(this.selectedIdsData=c(c({},this.selectedIdsData),Object(n.a)({},e,void 0)),delete this.selectedIdsData[e]):this.selectedIdsData=c(c({},this.selectedIdsData),Object(n.a)({},Object.keys(this.selectedIdsData).length,Object.keys(this.options)[0])),this.$emit("selected-ids",this.selectedIdsData)},selected:function(data){this.selectedIdsData[data.index]=data.evt.key,this.$emit("selected-ids",this.selectedIdsData)}},created:function(){},mounted:function(){this.selectedIdsData=this.selectedIds}},h=l,f=o(14),component=Object(f.a)(h,(function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{staticClass:"dply-felx mb-5"},[e.options&&Object.keys(e.options).length?o("div",{staticClass:"w-100"},[o("div",{staticClass:"sided mb-15"},[o("span",{staticClass:"lite-bold"},[e._v("\n        "+e._s(e.title)+"\n      ")]),e._v(" "),o("button",{staticClass:"primary-btn mn-w-200x",on:{click:function(t){return t.preventDefault(),e.toggle(null)}}},[e._v("\n        Add New\n      ")])]),e._v(" "),o("div",{staticClass:"dply-felx f-wrap start"},e._l(e.selectedIds,(function(t,n){return o("div",{key:n,staticClass:"dply-felx mr-15 mb-10"},[o("dropdown",{attrs:{options:e.options,selectedKey:t},on:{clicked:function(t){return e.selected({evt:t,index:n})}}}),e._v(" "),o("button",{staticClass:"ml-5 close-btn",on:{click:function(t){return t.preventDefault(),e.toggle(n)}}},[o("i",{staticClass:"icon close-icon"})])],1)})),0)]):o("a",{staticClass:"link",attrs:{href:"/"+e.route.link,title:"Add a "+e.title},on:{click:function(t){return t.preventDefault(),e.$router.push({name:e.route.name})}}},[e._v("Add a "+e._s(e.title))])])}),[],!1,null,null,null);t.default=component.exports;installComponents(component,{Dropdown:o(344).default})}}]);