(window.webpackJsonp=window.webpackJsonp||[]).push([[25,40,67],{456:function(t,e,n){"use strict";n.r(e);n(200),n(81);var l=n(476),r={name:"ImageSlider",data:function(){return{glide:null}},watch:{},props:{activeSlide:{type:Number,default:0},imageCount:{type:Number,default:0},perView:{type:Number,default:1},responsive:{type:Array,default:function(){return[1,1,1,1,1]}},gap:{type:Number,default:1},loop:{type:Boolean,default:!1},bullets:{type:Boolean,default:!1},autoplay:{type:Number,default:0},lazyImage:{type:Boolean,default:!1}},components:{},computed:{currentPerView:function(){var t,e;return null===(t=this.glide)||void 0===t||null===(e=t.settings)||void 0===e?void 0:e.perView}},mixins:[],methods:{sliderInit:function(){var t=this;this.glide=new l.a(this.$refs.glide,{startAt:this.activeSlide,perView:this.perView,autoplay:this.autoplay,gap:this.gap,perTouch:3,bound:!0,rewind:this.loop,breakpoints:{1200:{perView:this.responsive[0]},992:{perView:this.responsive[1]},768:{perView:this.responsive[2]},576:{perView:this.responsive[3],gap:10},360:{perView:this.responsive[4],gap:10}}}),this.bullets||this.$nextTick((function(){var e=t.currentPerView;t.glide.on("run.before",(function(t){t.steps=">"===t.direction?-e:e}))})),this.lazyImage&&this.$nextTick((function(){t.glide.on("run.before",(function(e){t.imageCount-1>=t.glide.index&&("="===e.direction?t.$emit("change",{index:e.steps-1,direction:1}):t.$emit("change",{index:t.glide.index,direction:">"===e.direction?1:-1}))}))})),this.glide.on("mount.after",(function(){setTimeout((function(){t.$emit("loaded",t.glide.index)}),50)})),this.$emit("glide",this.glide),this.glide.mount()}},created:function(){},mounted:function(){var t=this;this.$nextTick((function(){t.sliderInit()}))}},o=n(13),component=Object(o.a)(r,(function(){var t=this,e=t._self._c;return e("div",{ref:"glide",staticClass:"glide"},[t._t("bullet-area",(function(){return[t.bullets?e("ul",{staticClass:"glide-bullets",attrs:{"data-glide-el":"controls[nav]"}},t._l(t.imageCount,(function(t){return e("li",{key:t,attrs:{"data-glide-dir":"=".concat(t-1)}})})),0):t._e()]})),t._v(" "),t._m(0),t._v(" "),e("div",{staticClass:"glide__track",attrs:{"data-glide-el":"track"}},[e("ul",{staticClass:"glide__slides"},[t._t("content")],2)])],2)}),[function(){var t=this._self._c;return t("div",{staticClass:"glide-nav",attrs:{"data-glide-el":"controls"}},[t("button",{staticClass:"prev-btn",attrs:{"aria-label":"prev","data-glide-dir":"<"}},[t("i",{staticClass:"m-0 icon arrow-left"})]),this._v(" "),t("button",{staticClass:"next-btn",attrs:{"aria-label":"next","data-glide-dir":">"}},[t("i",{staticClass:"m-0 icon arrow-right"})])])}],!1,null,null,null);e.default=component.exports},472:function(t,e,n){"use strict";n.r(e);var l=n(61),r=n(34),o={name:"SubCategoryTile",props:{category:{type:Object,default:function(){return null}},subCategory:{type:Object,default:function(){return null}}},data:function(){return{}},components:{LazyImage:l.default},mixins:[r.a],computed:{},mounted:function(){},methods:{}},d=n(13),component=Object(d.a)(o,(function(){var t=this,e=t._self._c;return e("nuxt-link",{staticClass:"block page-link",attrs:{to:t.subCategoryLink(t.subCategory,t.category),title:t.subCategory.title}},[e("div",{staticClass:"img-wrapper"},[e("lazy-image",{attrs:{"data-src":t.thumbImageURL(t.subCategory),title:t.subCategory.title,alt:t.subCategory.title}})],1),t._v(" "),e("h5",{staticClass:"item-title ellipsis ellipsis-1"},[t._v("\n    "+t._s(t.subCategory.title)+"\n  ")])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{LazyImage:n(61).default})},523:function(t,e,n){"use strict";n.r(e);var l=n(34),r=n(456),o=n(61),d=n(472),c={name:"Featured",data:function(){return{}},watch:{},props:{title:{type:String,default:""},itemList:{type:Array,default:function(){return[]}}},components:{SubCategoryTile:d.default,ImageSlider:r.default,LazyImage:o.default},computed:{},mixins:[l.a],methods:{},created:function(){},mounted:function(){}},f=n(13),component=Object(f.a)(c,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"area home-section"},[t.itemList&&t.itemList.length?[e("div",{staticClass:"flex sided title"},[e("h4",[t._v(t._s(t.$t("featured.featured"))+" "+t._s(t.title))]),t._v(" "),e("nuxt-link",{staticClass:"link",attrs:{to:"/categories"}},[t._v("\n        "+t._s(t.$t("featured.showAll"))+"\n      ")])],1),t._v(" "),e("div",{staticClass:"area-content"},[e("image-slider",{attrs:{"image-count":t.itemList.length,"per-view":9,gap:15,responsive:[7,5,4,3,2]},scopedSlots:t._u([{key:"content",fn:function(){return t._l(t.itemList,(function(t,n){return e("li",{key:n,staticClass:"center-text"},[e("sub-category-tile",{attrs:{category:t.category,"sub-category":t}})],1)}))},proxy:!0}],null,!1,591971033)})],1)]:t._e()],2)}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{SubCategoryTile:n(472).default,ImageSlider:n(456).default})}}]);