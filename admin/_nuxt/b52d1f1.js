(window.webpackJsonp=window.webpackJsonp||[]).push([[56,15,21],{343:function(t,e,n){"use strict";n.r(e);var o={name:"AjaxButton",components:{Spinner:n(44).default},props:{color:{type:String,default:"white"},text:{type:String,default:"Submit"},onlyIcon:{type:String,default:null},loadingText:{type:String,default:"Getting Response"},fetchingData:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1},type:{type:String,default:"Submit"}},computed:{buttonText:function(){return this.fetchingData?this.loadingText:this.text},disable:function(){return this.fetchingData}},methods:{btnClicked:function(){"Submit"!==this.type&&this.$emit("clicked")}}},l=(n(350),n(14)),component=Object(l.a)(o,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("button",{staticClass:"ajax-btn",attrs:{disabled:t.disable||t.disabled,type:t.type},on:{"&click":function(e){return t.btnClicked.apply(null,arguments)}}},[t.fetchingData?n("spinner",{class:{"mr-15":!t.onlyIcon},attrs:{color:t.color}}):t._e(),t._v(" "),t.onlyIcon&&!t.fetchingData?n("i",{staticClass:"icon",class:t.onlyIcon}):t._e(),t._v(" "),t.onlyIcon?t._e():n("span",[t._v("\n    "+t._s(t.buttonText)+"\n  ")])],1)}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{Spinner:n(44).default})},345:function(t,e,n){var content=n(351);content.__esModule&&(content=content.default),"string"==typeof content&&(content=[[t.i,content,""]]),content.locals&&(t.exports=content.locals);(0,n(52).default)("91a8990a",content,!0,{sourceMap:!1})},350:function(t,e,n){"use strict";n(345)},351:function(t,e,n){var o=n(51)(!1);o.push([t.i,".ajax-btn{display:flex;justify-content:center;align-items:center}button:disabled,button[disabled]{opacity:.6;cursor:no-drop}",""]),t.exports=o},352:function(t,e,n){"use strict";n(29),n(40);e.a={data:function(){return{allowedImageExtensions:/(\.jpg|\.jpeg|\.png|\.svg|\.webp|\.gif)$/i,allowedVideoExtensions:/(\.mp4)$/i,passwordLength:6,maxImageSize:1,maxVideoSize:2,reg:/^(([^<>()\]\\.,;:\s@"]+(\.[^<>()\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/}},methods:{isValidImage:function(t){var e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:this.maxImageSize,n=!(arguments.length>2&&void 0!==arguments[2])||arguments[2];return t.size>1024*e*1024?"File size must be less than ".concat(e,"MB"):n&&!this.allowedImageExtensions.exec(t.name)?"Invalid File Type":n||this.allowedVideoExtensions.exec(t.name)?null:"Invalid File Type"},isValidEmail:function(t){return this.reg.test(t)},isValidLength:function(t){return t&&t.length>=this.passwordLength||!1}}}},566:function(t,e,n){"use strict";n.r(e);n(29),n(82),n(234),n(583),n(8),n(588),n(590),n(591),n(592),n(593),n(594),n(595),n(596),n(597),n(598),n(599),n(600),n(601),n(602),n(603),n(605),n(606),n(607),n(608),n(609),n(610),n(611),n(612),n(613),n(45),n(46),n(614),n(235);var o=n(343),l=n(59),r=n(352),d={name:"VideoUpload",data:function(){return{scaleFactor:"",uploadMessage:null}},mixins:[l.a,r.a],components:{AjaxButton:o.default},props:{type:{type:String,default:"image"},fileUploading:{type:Boolean,default:!1},video:{type:String,default:""},image:{type:String,default:""},imageTitle:{type:String,default:""},btnText:{type:String,default:"Upload"}},computed:{isImage:function(){return"image"===this.type},errors:function(){return this.ERRORS&&this.ERRORS[this.type]},videoName:function(){return this.video||""},imageName:function(){var t;return null!==(t=this.image)&&void 0!==t?t:this.defaultImage}},methods:{dataURLtoFile:function(t,e){for(var n=t.split(","),o=n[0].match(/:(.*?);/)[1],l=atob(n[1]),r=l.length,d=new Uint8Array(r);r--;)d[r]=l.charCodeAt(r);return new File([d],e,{type:o})},capture:function(t){var video=document.getElementById("video");document.getElementById("source").src=URL.createObjectURL(t),video.load();var e=this;video.addEventListener("loadeddata",(function(){var n=this.videoWidth,o=this.videoHeight,canvas=document.createElement("canvas");canvas.width=n,canvas.height=o,canvas.getContext("2d").drawImage(this,0,0,n,o);var l=canvas.toDataURL("image/jpeg"),r={canvas:canvas,file:e.dataURLtoFile(l,"".concat(+new Date,".jpg")),base64:l};e.$emit("file-upload",{video:t,thumb:r.file})}))},fileChanged:function(t){var e=t.target.files[0];e&&(this.uploadMessage=this.isValidImage(e,this.maxVideoSize,!1),this.uploadMessage||this.capture(e))}}},c=n(14),component=Object(c.a)(d,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"file-wrapper",class:{"has-error":t.uploadMessage}},[n("div",{staticClass:"file-input",class:{"pt-0":!t.isImage}},[n("input",{ref:"fileInput",attrs:{type:"file"},on:{change:t.fileChanged}}),t._v(" "),t.isImage?n("img",{attrs:{src:t.getImageURL(t.imageName),alt:t.imageTitle}}):n("div",[t.videoName?n("video",{attrs:{controls:"",autostart:"0"}},[n("source",{attrs:{src:t.getVideoURL(t.videoName),type:"video/mp4"}})]):n("div",[t._v("\n        No Video\n      ")])]),t._v(" "),t._m(0)]),t._v(" "),t.uploadMessage?n("span",{staticClass:"error mb-10"},[t._v("\n    "+t._s(t.uploadMessage)+"\n  ")]):t._e(),t._v(" "),n("ajax-button",{staticClass:"outline-btn",attrs:{type:"button",text:t.btnText,color:"primary","loading-text":"Uploading","fetching-data":t.fileUploading},on:{clicked:function(e){return t.$refs.fileInput.click()}}})],1)}),[function(){var t=this.$createElement,e=this._self._c||t;return e("video",{staticStyle:{height:"0",width:"0",opacity:"0"},attrs:{id:"video",controls:"",autoplay:""}},[e("source",{attrs:{id:"source",type:"video/mp4"}})])}],!1,null,null,null);e.default=component.exports;installComponents(component,{AjaxButton:n(343).default})}}]);