(window.webpackJsonp=window.webpackJsonp||[]).push([[16,12,17,46,67,70],{455:function(t,e,r){"use strict";r.r(e);r(44),r(19),r(21),r(24),r(6),r(26),r(20),r(27);var n=r(9);r(200),r(22),r(80);function o(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}function c(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?o(Object(source),!0).forEach((function(e){Object(n.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):o(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}var l={name:"Pagination",data:function(){return{sortByType:this.$route.query.orderByType||"desc",sortBy:this.$route.query.orderBy||"created_at",currentPage:Number(this.$route.query.page)||this.page,search:this.$route.query.q||null}},props:{changingRoute:{type:Boolean,default:!0},page:{type:Number,default:1},totalPage:{type:Number},pagePer:{type:Number,default:5}},watch:{},directives:{},components:{},mixins:[],computed:{getActivePages:function(){var t=this.getPage+(this.pagePer-1);t%this.pagePer!=0&&(t=parseInt(t/this.pagePer)*this.pagePer);var e=t-(this.pagePer-1);return t>=this.totalPage&&(t=this.totalPage),[e-1,t]},getPage:function(){return this.currentPage},allPages:function(){for(var t=[],i=1;i<=this.totalPage;i++)t.push(i);return t}},methods:{routeParam:function(){this.isDefaultPage()?this.resettingRoute():(this.clearQuery(),this.$emit("fetching-data")),this.scrollToTop()},resettingRoute:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};this.clearQuery(),this.$router.push({query:c(c({},t),{orderBy:this.orderBy,orderByType:this.orderByType,page:this.currentPage,q:this.search})}),this.$emit("fetching-data")},clearQuery:function(){this.orderByType="desc",this.orderBy="created_at",this.currentPage=1},isDefaultPage:function(){return"desc"===this.orderByType&&"created_at"===this.orderBy&&1===this.currentPage},settingRoute:function(){this.$router.push({query:c(c({},this.$route.query),{orderBy:this.orderBy,orderByType:this.orderByType,page:this.currentPage,q:this.search})})},dropdownSelected:function(t,data){this.currentPage=1,t?this.orderByType=data.key:this.orderBy=data.key,this.getDataWithRoute()},scrollToTop:function(){window.scrollTo(0,0)},getDataFromRoute:function(){this.sortByType=this.$route.query.orderByType||"desc",this.sortBy=this.$route.query.orderBy||"created_at",this.currentPage=Number(this.$route.query.page)||1,this.search=this.$route.query.q||null,this.$emit("fetching-data")},getDataWithRoute:function(){this.changingRoute&&(this.settingRoute(),this.scrollToTop()),this.$emit("fetching-data",{orderBy:this.orderBy,orderByType:this.orderByType,page:this.currentPage,q:this.q})},navigate:function(param){param>0&&this.currentPage>=this.totalPage||param<0&&this.currentPage<=1||(this.currentPage+=param,this.getDataWithRoute())},paginate:function(t){this.currentPage!==t&&(this.currentPage=t,this.getDataWithRoute())},loadData:function(){this.getDataFromRoute()}},destroyed:function(){window.removeEventListener("popstate",this.loadData)},mounted:function(){window.addEventListener("popstate",this.loadData)}},d=l,h=r(13),component=Object(h.a)(d,(function(){var t=this,e=t._self._c;return t.totalPage>1?e("ul",{staticClass:"pagination"},[e("li",{class:{disabled:1===t.currentPage},on:{click:function(e){return e.preventDefault(),t.navigate(-1)}}},[e("i",{staticClass:"icon arrow-left black"})]),t._v(" "),t._l(t.allPages.slice(t.getActivePages[0],t.getActivePages[1]),(function(r,n){return e("li",{key:n,staticClass:"page",class:{disabled:t.currentPage===r},on:{click:function(e){return e.preventDefault(),t.paginate(r)}}},[e("span",[t._v("\n      "+t._s(r)+"\n    ")])])})),t._v(" "),e("li",{class:{disabled:t.currentPage===t.totalPage},on:{click:function(e){return e.preventDefault(),t.navigate(1)}}},[e("i",{staticClass:"icon arrow-right black"})])],2):t._e()}),[],!1,null,null,null);e.default=component.exports},459:function(t,e,r){"use strict";r.r(e);var n={name:"TileShimmer",data:function(){return{}},watch:{},props:{},components:{},computed:{},mixins:[],methods:{},created:function(){},mounted:function(){}},o=r(13),component=Object(o.a)(n,(function(){this._self._c;return this._m(0)}),[function(){var t=this,e=t._self._c;return e("div",{staticClass:"p-tile"},[e("div",{staticClass:"img-wrapper pb-100p"},[e("div",{staticClass:"shimmer"})]),t._v(" "),e("div",{staticClass:"h-24x shimmer mtb-10"}),t._v(" "),e("div",{staticClass:"h-24x w-70 shimmer"})])}],!1,null,null,null);e.default=component.exports},464:function(t,e,r){"use strict";r(200),r(22),r(80);e.a={data:function(){return{orderByType:"",orderBy:"",page:0,search:null}},methods:{settingParam:function(data){this.orderByType=(null==data?void 0:data.orderByType)||"desc",this.orderBy=(null==data?void 0:data.orderBy)||"created_at",this.page=Number(null==data?void 0:data.page)||1,this.search=(null==data?void 0:data.q)||null},settingRouteParam:function(){this.orderByType=this.$route.query.orderByType||"desc",this.orderBy=this.$route.query.orderBy||"created_at",this.page=Number(this.$route.query.page)||1,this.search=this.$route.query.q||null}}}},471:function(t,e,r){"use strict";r.r(e);var n=r(61),o=r(34),c={name:"BrandTile",props:{brand:{type:Object,default:function(){return null}}},data:function(){return{}},components:{LazyImage:n.default},mixins:[o.a],computed:{},mounted:function(){},methods:{}},l=r(13),component=Object(l.a)(c,(function(){var t=this,e=t._self._c;return e("nuxt-link",{staticClass:"block page-link",attrs:{to:t.brandLink(t.brand),title:t.brand.title}},[e("div",{staticClass:"img-wrapper"},[e("lazy-image",{attrs:{"data-src":t.thumbImageURL(t.brand),title:t.brand.title,alt:t.brand.title}})],1),t._v(" "),e("h5",{staticClass:"item-title ellipsis ellipsis-1"},[t._v("\n    "+t._s(t.brand.title)+"\n  ")])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{LazyImage:r(61).default})},472:function(t,e,r){"use strict";r.r(e);var n=r(61),o=r(34),c={name:"SubCategoryTile",props:{category:{type:Object,default:function(){return null}},subCategory:{type:Object,default:function(){return null}}},data:function(){return{}},components:{LazyImage:n.default},mixins:[o.a],computed:{},mounted:function(){},methods:{}},l=r(13),component=Object(l.a)(c,(function(){var t=this,e=t._self._c;return e("nuxt-link",{staticClass:"block page-link",attrs:{to:t.subCategoryLink(t.subCategory,t.category),title:t.subCategory.title}},[e("div",{staticClass:"img-wrapper"},[e("lazy-image",{attrs:{"data-src":t.thumbImageURL(t.subCategory),title:t.subCategory.title,alt:t.subCategory.title}})],1),t._v(" "),e("h5",{staticClass:"item-title ellipsis ellipsis-1"},[t._v("\n    "+t._s(t.subCategory.title)+"\n  ")])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{LazyImage:r(61).default})},486:function(t,e,r){"use strict";r.r(e);var n=r(61),o=r(34),c={name:"CategoryTile",props:{category:{type:Object,default:function(){return null}}},data:function(){return{}},components:{LazyImage:n.default},mixins:[o.a],computed:{},mounted:function(){},methods:{}},l=r(13),component=Object(l.a)(c,(function(){var t=this,e=t._self._c;return e("nuxt-link",{staticClass:"block page-link",attrs:{to:t.categoryLink(t.category),title:t.category.title}},[e("div",{staticClass:"img-wrapper"},[e("lazy-image",{attrs:{"data-src":t.thumbImageURL(t.category),title:t.category.title,alt:t.category.title}})],1),t._v(" "),e("h5",{staticClass:"item-title ellipsis ellipsis-1"},[t._v("\n    "+t._s(t.category.title)+"\n  ")])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{LazyImage:r(61).default})},497:function(t,e,r){"use strict";r.r(e);r(19),r(21),r(24),r(6),r(26),r(20),r(27);var n=r(0),o=r(9),c=(r(12),r(35),r(54),r(63),r(81),r(10)),l=r(34),d=r(126),h=r(464),y=r(61),f=r(459),m=r(486),v=r(455),P=r(79),C=r(471);function O(object,t){var e=Object.keys(object);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(object);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(object,t).enumerable}))),e.push.apply(e,r)}return e}function w(t){for(var i=1;i<arguments.length;i++){var source=null!=arguments[i]?arguments[i]:{};i%2?O(Object(source),!0).forEach((function(e){Object(o.a)(t,e,source[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(source)):O(Object(source)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(source,e))}))}return t}var _={components:{SubCategoryTile:r(472).default,BrandTile:C.default,Spinner:P.default,Pagination:v.default,CategoryTile:m.default,TileShimmer:f.default,LazyImage:y.default},data:function(){return{result:null,fetchingCategoryData:!0}},props:{subCategoriesMap:{type:Object,default:null}},mixins:[l.a,d.a,h.a],computed:{isBrandPage:function(){var t,e;return null===(t=this.$route)||void 0===t||null===(e=t.name)||void 0===e?void 0:e.includes("brands")},currentItems:function(){var t;return(null===(t=this.result)||void 0===t?void 0:t.data)||[]},totalPage:function(){var t;return null===(t=this.result)||void 0===t?void 0:t.last_page}},methods:w(w({fetchingData:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){var r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.fetchingCategoryData=!0,r=t,e.next=4,setTimeout(Object(n.a)(regeneratorRuntime.mark((function e(){var n,data;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.prev=0,r.settingRouteParam(),n="categories",r.isBrandPage&&(n="brands"),e.next=6,r.getRequest({params:{page:t.page},api:n});case 6:data=e.sent,r.result=null==data?void 0:data.data,r.fetchingCategoryData=!1,e.next=14;break;case 11:return e.prev=11,e.t0=e.catch(0),e.abrupt("return",r.$nuxt.error(e.t0));case 14:case"end":return e.stop()}}),e,null,[[0,11]])}))),100);case 4:case"end":return e.stop()}}),e)})))()}},Object(c.b)("common",["getRequest"])),Object(c.b)("category",["emptyCategories"])),mounted:function(){var t=this;return Object(n.a)(regeneratorRuntime.mark((function e(){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.emptyCategories(),e.next=3,t.fetchingData();case 3:case"end":return e.stop()}}),e)})))()}},B=r(13),component=Object(B.a)(_,(function(){var t=this,e=t._self._c;return e("client-only",[e("div",{staticClass:"container-fluid ptb-20 ptb-sm-15 flow-hidden"},[e("div",{staticClass:"mlr--5"},[t.fetchingCategoryData?e("div",{staticClass:"tile-container category-tile-wrapper"},[e("div",{staticClass:"shimmer-wrapper"},t._l(t.shimmerCount.PRODUCT,(function(t){return e("tile-shimmer",{key:t,staticClass:"category-tile"})})),1)]):e("div",{staticClass:"pos-rel"},[t.currentItems.length?e("div",[t.isBrandPage?e("div",{staticClass:"category-tile-wrapper"},t._l(t.currentItems,(function(t,r){return e("brand-tile",{key:r,staticClass:"category-tile",attrs:{brand:t}})})),1):e("div",{staticClass:"category-tile-wrapper"},t._l(t.currentItems,(function(r,n){return e("sub-category-tile",{key:n,staticClass:"category-tile",attrs:{"sub-category":r,category:t.subCategoriesMap[r.id]}})})),1)]):e("div",{staticClass:"info-msg"},[t._v("\n        "+t._s(t.$t("categoryListingLayout.noItemFound"))+"\n      ")]),t._v(" "),e("pagination",{staticClass:"mt-15 mt-sm-10",attrs:{"total-page":t.totalPage},on:{"fetching-data":t.fetchingData}})],1)])])])}),[],!1,null,null,null);e.default=component.exports;installComponents(component,{TileShimmer:r(459).default,BrandTile:r(471).default,SubCategoryTile:r(472).default,Pagination:r(455).default})}}]);