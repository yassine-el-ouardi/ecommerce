(window.webpackJsonp=window.webpackJsonp||[]).push([[55,15,21],{343:function(t,e,n){"use strict";n.r(e);var o={name:"AjaxButton",components:{Spinner:n(44).default},props:{color:{type:String,default:"white"},text:{type:String,default:"Submit"},onlyIcon:{type:String,default:null},loadingText:{type:String,default:"Getting Response"},fetchingData:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},type:{type:String,default:"Submit"}},computed:{buttonText:function(){return this.fetchingData?this.loadingText:this.text},disable:function(){return this.fetchingData}},methods:{btnClicked:function(){"Submit"!==this.type&&this.$emit("clicked")}}},l=(n(350),n(14)),component=Object(l.a)(o,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("button",{staticClass:"ajax-btn",attrs:{disabled:t.disable||t.disabled,type:t.type},on:{"&click":function(e){return t.btnClicked.apply(null,arguments)}}},[t.fetchingData?n("spinner",{class:{"mr-15":!t.onlyIcon},attrs:{color:t.color}}):t._e(),t._v(" "),t.onlyIcon&&!t.fetchingData?n("i",{staticClass:"icon",class:t.onlyIcon}):t._e(),t._v(" "),t.onlyIcon?t._e():n("span",[t._v("\n    "+t._s(t.buttonText)+"\n  ")])],1)}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{Spinner:n(44).default})},345:function(t,e,n){var content=n(351);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[t.i,content,""]]),content.locals&&(t.exports=content.locals);(0,n(52).default)("91a8990a",content,!0,{sourceMap:!1})},350:function(t,e,n){"use strict";n(345)},351:function(t,e,n){var o=n(51)(!1);o.push([t.i,".ajax-btn{display:flex;justify-content:center;align-items:center}button:disabled,button[disabled]{opacity:.6;cursor:no-drop}",""]),t.exports=o},567:function(t,e,n){"use strict";n.r(e);n(100);var o=n(343),l={name:"VideoInput",data:function(){return{videoThumbData:null,videoData:null}},mixins:[n(59).a],components:{AjaxButton:o.default},props:{saving:{type:Boolean,default:!1},videoThumb:{type:String,default:""},video:{type:String,default:""}},watch:{videoThumb:function(t){this.videoThumbData=t},video:function(t){this.$refs.videoRef.src=t,this.$refs.videoRef.play(),this.videoData=t}},computed:{thumbImageName:function(){var t;return null!==(t=this.videoThumbData)&&void 0!==t&&t.trim()?this.videoThumb:this.defaultImage}},methods:{},mounted:function(){this.videoData=this.video,this.videoThumbData=this.videoThumb}},d=n(14),component=Object(d.a)(l,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"image-url-wrap file-wrapper"},[n("h5",[t._v("Video Thumb Image")]),t._v(" "),n("div",{staticClass:"file-input"},[n("img",{attrs:{src:t.thumbImageName}})]),t._v(" "),n("h5",[t._v("Video")]),t._v(" "),n("div",{staticClass:"file-input"},[n("video",{ref:"videoRef",attrs:{src:"",id:"video-container",width:"100%",controls:""}})]),t._v(" "),n("div",{staticClass:"input-wrapper"},[n("label",[t._v("Video thumb name/URL")]),t._v(" "),n("input",{directives:[{name:"model",rawName:"v-model",value:t.videoThumbData,expression:"videoThumbData"}],attrs:{type:"text",placeholder:"Video thumb name/URL"},domProps:{value:t.videoThumbData},on:{input:function(e){e.target.composing||(t.videoThumbData=e.target.value)}}})]),t._v(" "),n("div",{staticClass:"input-wrapper"},[n("label",[t._v("Video name/URL")]),t._v(" "),n("div",{staticClass:"image-input"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.videoData,expression:"videoData"}],attrs:{type:"text",placeholder:"File name/URL"},domProps:{value:t.videoData},on:{input:function(e){e.target.composing||(t.videoData=e.target.value)}}}),t._v(" "),n("ajax-button",{staticClass:"primary-btn",attrs:{type:"button",text:"Save","loading-text":"","fetching-data":t.saving},on:{clicked:function(e){return t.$emit("image-change",{video:t.videoData,thumb:t.videoThumbData})}}})],1)])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{AjaxButton:n(343).default})}}]);