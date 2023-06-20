(window.webpackJsonp=window.webpackJsonp||[]).push([[38,40],{451:function(t,e,n){"use strict";n.r(e);n(197),n(81);var l=n(471),r={name:"ImageSlider",data:function(){return{glide:null}},watch:{},props:{activeSlide:{type:Number,default:0},imageCount:{type:Number,default:0},perView:{type:Number,default:1},responsive:{type:Array,default:function(){return[1,1,1,1,1]}},gap:{type:Number,default:1},loop:{type:Boolean,default:!1},bullets:{type:Boolean,default:!1},autoplay:{type:Number,default:0},lazyImage:{type:Boolean,default:!1}},components:{},computed:{currentPerView:function(){var t,e;return null===(t=this.glide)||void 0===t||null===(e=t.settings)||void 0===e?void 0:e.perView}},mixins:[],methods:{sliderInit:function(){var t=this;this.glide=new l.a(this.$refs.glide,{startAt:this.activeSlide,perView:this.perView,autoplay:this.autoplay,gap:this.gap,perTouch:3,bound:!0,rewind:this.loop,breakpoints:{1200:{perView:this.responsive[0]},992:{perView:this.responsive[1]},768:{perView:this.responsive[2]},576:{perView:this.responsive[3],gap:10},360:{perView:this.responsive[4],gap:10}}}),this.bullets||this.$nextTick((function(){var e=t.currentPerView;t.glide.on("run.before",(function(t){t.steps=">"===t.direction?-e:e}))})),this.lazyImage&&this.$nextTick((function(){t.glide.on("run.before",(function(e){t.imageCount-1>=t.glide.index&&("="===e.direction?t.$emit("change",{index:e.steps-1,direction:1}):t.$emit("change",{index:t.glide.index,direction:">"===e.direction?1:-1}))}))})),this.glide.on("mount.after",(function(){setTimeout((function(){t.$emit("loaded",t.glide.index)}),50)})),this.$emit("glide",this.glide),this.glide.mount()}},created:function(){},mounted:function(){var t=this;this.$nextTick((function(){t.sliderInit()}))}},o=n(13),component=Object(o.a)(r,(function(){var t=this,e=t._self._c;return e("div",{ref:"glide",staticClass:"glide"},[t._t("bullet-area",(function(){return[t.bullets?e("ul",{staticClass:"glide-bullets",attrs:{"data-glide-el":"controls[nav]"}},t._l(t.imageCount,(function(t){return e("li",{key:t,attrs:{"data-glide-dir":"=".concat(t-1)}})})),0):t._e()]})),t._v(" "),t._m(0),t._v(" "),e("div",{staticClass:"glide__track",attrs:{"data-glide-el":"track"}},[e("ul",{staticClass:"glide__slides"},[t._t("content")],2)])],2)}),[function(){var t=this._self._c;return t("div",{staticClass:"glide-nav",attrs:{"data-glide-el":"controls"}},[t("button",{staticClass:"prev-btn",attrs:{"aria-label":"prev","data-glide-dir":"<"}},[t("i",{staticClass:"m-0 icon arrow-left"})]),this._v(" "),t("button",{staticClass:"next-btn",attrs:{"aria-label":"next","data-glide-dir":">"}},[t("i",{staticClass:"m-0 icon arrow-right"})])])}],!1,null,null,null);e.default=component.exports},476:function(t,e,n){"use strict";n(42);e.a={data:function(){return{currentSlider:this.activeId||0,glide:null}},methods:{changed:function(t){var e=t.index,n=t.direction;if(e+n<0)return!1;var l=this,img=this.loadedImage(e+n);null==img||img.addEventListener("load",(function(){img.style.opacity=1})),null==img||img.addEventListener("error",(function(){img.style.opacity=1,img.src=null==l?void 0:l.getImageURL()}))},firstImgLoaded:function(){var t=this,e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,n=this,img=this.loadedImage(e);null==img||img.addEventListener("load",(function(){img.style.opacity=1,t.imgLoaded=!0})),null==img||img.addEventListener("error",(function(){img.style.opacity=1;var l=document.getElementById(t.generateElemId(e));null==l||l.setAttribute("src",null==n?void 0:n.getImageURL())}))},loadedImage:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,e=document.getElementById(this.generateElemId(t));return null==e||e.setAttribute("src",null==e?void 0:e.getAttribute("data-source")),e},generateElemId:function(t){return"".concat(this._uid,"--").concat(t)},loadImage:function(t){var e=this,n=document.getElementById(this.generateElemId(t)),l=(null==n?void 0:n.getAttribute("data-src"))||null;l&&(n.src=l,n.onload=function(){n.removeAttribute("data-src")},n.onerror=function(){n.src=e.imageURL()})},glideSlider:function(data){data.on("run",function(){this.loadImage(data.index),this.currentSlider=data.index}.bind(this))}}}},520:function(t,e,n){"use strict";n.r(e);var l=n(34),r=n(476),o=n(451),d=n(60),c={name:"HomeHero",data:function(){return{imgLoaded:!1}},watch:{},props:{slider:{type:Object,default:function(){return null}}},components:{ImageSlider:o.default,LazyImage:d.default},computed:{rightBottom:function(){var t;return null===(t=this.slider)||void 0===t?void 0:t.right_bottom},rightTop:function(){var t;return null===(t=this.slider)||void 0===t?void 0:t.right_top}},mixins:[l.a,r.a],methods:{},created:function(){},mounted:function(){}},m=n(13),component=Object(m.a)(c,(function(){var t=this,e=t._self._c;return t.slider&&t.slider.main.length?e("div",{staticClass:"main-slider"},[e("div",{staticClass:"slider-wrapper",class:{"has-right":t.rightTop||t.rightBottom}},[e("div",{staticClass:"left flow-hidden"},[e("div",{staticClass:"pos-rel"},[e("client-only",[e("image-slider",{staticClass:"opacity-0",class:{"img-loading":t.imgLoaded},attrs:{"image-count":t.slider.main.length,bullets:!0,autoplay:6e3,loop:!0,"lazy-image":!0},on:{glide:t.glideSlider,loaded:t.firstImgLoaded,change:t.changed},scopedSlots:t._u([{key:"content",fn:function(){return t._l(t.slider.main,(function(n,l){return e("li",{key:l},[e("nuxt-link",{staticClass:"slider-content block",attrs:{to:t.sourceUrl(n)}},[e("div",{staticClass:"slider-content-inner"},[e("img",{staticClass:"full-dimen",attrs:{id:t.generateElemId(l),alt:"Slider image","data-source":t.imageURL(n),height:"100",width:"100"}})])])],1)}))},proxy:!0}],null,!1,1153546437)})],1),t._v(" "),e("img",{staticClass:"full-dimen placeholder-img",class:{"img-loaded":t.imgLoaded},attrs:{alt:"Slider image",height:"100",width:"100",src:t.imageURL(t.slider.main[0])}})],1)]),t._v(" "),t.rightTop||t.rightBottom?e("div",{staticClass:"right"},[t.rightTop?e("nuxt-link",{staticClass:"img-wrap block",attrs:{to:t.sourceUrl(t.slider.right_top)}},[t.slider&&t.slider.right_top?[e("img",{attrs:{src:t.imageURL(t.slider.right_top),height:"100",width:"100",alt:"Slider image"}})]:t._e()],2):t._e(),t._v(" "),t.rightBottom?e("nuxt-link",{staticClass:"img-wrap block",attrs:{to:t.sourceUrl(t.slider.right_bottom)}},[t.slider&&t.slider.right_bottom?[e("img",{attrs:{src:t.imageURL(t.slider.right_bottom),height:"100",width:"100",alt:"Slider image"}})]:t._e()],2):t._e()],1):t._e()])]):t._e()}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{ImageSlider:n(451).default})}}]);