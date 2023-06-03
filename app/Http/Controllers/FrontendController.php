<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\FooterImageLink;
use App\Models\FooterLink;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\HomeSlider;
use App\Models\Order;
use App\Models\Page;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\RatingReview;
use App\Models\Setting;
use App\Models\ShippingRule;
use App\Models\SiteSetting;
use App\Models\Store;
use App\Models\SubCategory;
use App\Models\Tag;
use App\Models\UpdatedInventory;
use App\Models\UserFollowStore;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class FrontendController extends Controller
{
    public function all(Request $request)
    {
        try {
            $subCategory = null;
            $category = null;

            $query = Product::query();
            $query = $query->leftJoin('flash_sale_products', function ($join) {
                $join->on('products.id', '=', 'flash_sale_products.product_id');
            })
                ->leftJoin('flash_sales', function ($join) {
                    $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                    $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                        ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
                })
                ->groupBy('products.id');


            if ($request->sub_category) {

                $subCategory = SubCategory::with('category')
                    ->where('slug', $request->sub_category)
                    ->first();

                if ($subCategory) {
                    $query = $query->where('products.subcategory_id', $subCategory->id);
                }


            } else if ($request->category) {

                $category = Category::where('slug', $request->category)->first();

                if ($category) {
                    $query = $query->where('products.category_id', $category->id);
                }
            }

            $allShipping = null;
            $allCollection = null;
            $allBrand = null;
            $sidebarData = $request->sidebar_data === 'true' ? true : false;


            if ($sidebarData) {

                $allBrand = Brand::where('status', Config::get('constants.status.PUBLIC'))->select('id', 'title')->get();
                $allCollection = ProductCollection::where('status', Config::get('constants.status.PUBLIC'))
                    ->select('id', 'title')->get();

                $allShipping = ShippingRule::select('id', 'title')->get();

            }

            if ($request->brand) {
                $query = $query->whereIn('products.brand_id', explode(',', $request->brand));
            }

            if ($request->collection) {
                $query = $query
                    ->rightJoin('collection_with_products as cwp', function ($join) {
                        $join->on('products.id', '=', 'cwp.product_id');
                    })
                    ->rightJoin('product_collections as pc', function ($join) use ($request) {
                        $join->on('pc.id', '=', 'cwp.product_collection_id');
                        $join->where('pc.status', Config::get('constants.status.PUBLIC'))
                            ->whereIn('pc.id', explode(',', $request->collection));
                    });
            }

            if ($request->rating != 0) {
                $query = $query->where('products.rating', '>=', $request->rating);
            }

            if ($request->max > 0 || $request->min > 0) {
                if ($request->max == 0) {
                    $request->max = 999999;
                }

                $query = $query->where(function ($q) use ($request) {
                    $q->where(function ($qr) use ($request) {
                        $qr->whereNotNull('flash_sales.end_time');
                        $qr->whereBetween('flash_sale_products.price', [$request->min, $request->max]);

                    });
                    $q->orWhere(function ($qr) use ($request) {
                        $qr->whereNull('flash_sales.end_time');
                        $qr->where('products.offered', '=', 0);
                        $qr->whereBetween('products.selling', [$request->min, $request->max]);;

                    });
                    $q->orWhere(function ($qr) use ($request) {
                        $qr->whereNull('flash_sales.end_time');
                        $qr->where('products.offered', '>', 0);
                        $qr->whereBetween('products.offered', [$request->min, $request->max]);
                    });
                });
            }

            $query = $query->where('products.status', Config::get('constants.status.PUBLIC'));
            $query = $query->select('products.id', 'products.title', 'products.badge',
                'products.selling', 'products.offered',
                'products.image', 'products.review_count', 'products.rating', 'flash_sale_products.price',
                'flash_sales.end_time');


            if ($request->shipping) {
                $query = $query->whereIn('products.shipping_rule_id', explode(',', $request->shipping));
            }

            if ($request->sortby) {
                if ($request->sortby == 'price_low_to_high') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'asc');

                } else if ($request->sortby == 'price_high_to_low') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'desc');

                } else if ($request->sortby == 'avg_customer_review') {
                    $query = $query->orderBy('products.rating', 'desc');
                } else {
                    $query = $query->orderBy('products.created_at', 'desc');
                }
            } else {
                $query = $query->orderBy('products.updated_at', 'desc');
            }

            $pagination = Config::get('constants.listing.PAGINATION');

            $data['result'] = $query->paginate($pagination);

            $data['sub_category'] = $subCategory;
            $data['category'] = $category;
            $data['shipping'] = $allShipping;
            $data['shipping'] = $allShipping;
            $data['brands'] = $allBrand;
            $data['collections'] = $allCollection;

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function store(Request $request)
    {
        try {

            $slug = $request->slug;


            $store = Store::where('slug', $slug)->first();

            $query = Product::query();
            $query = $query->leftJoin('flash_sale_products', function ($join) {
                $join->on('products.id', '=', 'flash_sale_products.product_id');
            })
                ->leftJoin('flash_sales', function ($join) {
                    $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                    $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                        ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
                })
                ->groupBy('products.id');


            $query = $query->where('products.admin_id', $store->admin_id);
            $query = $query->where('products.status', Config::get('constants.status.PUBLIC'));
            $query = $query->select('products.id', 'products.title', 'products.badge',
                'products.selling', 'products.offered',
                'products.image', 'products.review_count', 'products.rating', 'flash_sale_products.price',
                'flash_sales.end_time');


            if ($request->sortby) {
                if ($request->sortby == 'price_low_to_high') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'asc');

                } else if ($request->sortby == 'price_high_to_low') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'desc');

                } else if ($request->sortby == 'avg_customer_review') {
                    $query = $query->orderBy('products.rating', 'desc');
                } else {
                    $query = $query->orderBy('products.created_at', 'desc');
                }
            } else {
                $query = $query->orderBy('products.updated_at', 'desc');
            }

            $pagination = Config::get('constants.listing.PAGINATION');

            $data['result'] = $query->paginate($pagination);
            $data['store'] = $store;
            $data['following'] = false;
            $data['review'] = 0;


            if($request->required_rating){
                $data['review'] = Product::where('admin_id', $store->admin_id)->avg('rating');
            }




            if (Auth::guard('user')->check()) {

                $user = Auth::guard('user')->user();

                $followed = UserFollowStore::where('user_id', $user->id)
                    ->where('store_id', $store->id)
                    ->first();

                if($followed){
                    $data['following'] = true;
                }
            }


            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }



    public function paymentGateway(Request $request)
    {
        try {
            $data = Payment::first();

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }

    public function categories(Request $request)
    {
        try {
            $query = SubCategory::query();

            $query = $query->select('title', 'image', 'slug', 'category_id', 'id');
            $query = $query->where('status', Config::get('constants.status.PUBLIC'));
            $data = $query->paginate(Config::get('constants.frontend.PAGINATION'));

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function brands(Request $request)
    {
        try {
            $query = Brand::query();

            $query = $query->select('title', 'image', 'id');
            $query = $query->where('status', Config::get('constants.status.PUBLIC'));
            $data = $query->paginate(Config::get('constants.frontend.PAGINATION'));

            return response()->json(new Response($request->token, $data));


        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }

    public function search(Request $request)
    {
        try {
            $data['product'] = [];
            $data['suggested'] = [];
            $data['category'] = [];
            $data['sub_category'] = [];

            if ($request->q) {
                $queryS = SubCategory::with('category');
                $queryS = $queryS->where('title', 'LIKE', "%{$request->q}%");
                $queryS = $queryS->where('status', Config::get('constants.status.PUBLIC'));
                $queryS = $queryS->select('id', 'title', 'image', 'category_id', 'slug');
                $queryS = $queryS->limit(Config::get('constants.pagination.FRONTEND_SEARCH'));
                $data['sub_category'] = $queryS->get();

                $queryC = Category::query();
                $queryC = $queryC->where('title', 'LIKE', "%{$request->q}%");
                $queryC = $queryC->where('status', Config::get('constants.status.PUBLIC'));
                $queryC = $queryC->select('id', 'image', 'title', 'slug');
                $queryC = $queryC->limit(Config::get('constants.pagination.FRONTEND_SEARCH'));

                $data['category'] = $queryC->get();

                $queryT = Tag::query();
                $queryT = $queryT->where(function ($q) use ($request) {
                    $q->where('title', 'LIKE', "%{$request->q}%");
                });
                $queryT = $queryT->limit(Config::get('constants.pagination.FRONTEND_SEARCH'));
                $queryT = $queryT->select('id', 'title');
                $data['suggested'] = $queryT->get();

                $queryP = Product::query();
                $queryP = $queryP->leftJoin('flash_sale_products', function ($join) {
                    $join->on('products.id', '=', 'flash_sale_products.product_id');
                })->leftJoin('flash_sales', function ($join) {
                    $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                    $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                        ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
                });
                $queryP = $queryP->where('products.status', Config::get('constants.status.PUBLIC'));
                $queryP = $queryP->where(function ($q) use ($request) {
                    $q->where('products.title', 'LIKE', "%{$request->q}%")
                        ->orWhere('products.tags', 'LIKE', "%{$request->q}%");
                });
                $queryP = $queryP->limit(Config::get('constants.pagination.FRONTEND_SEARCH'));
                $queryP = $queryP->select('products.id', 'products.title', 'products.selling', 'products.offered',
                    'products.image', 'products.review_count', 'products.rating', 'flash_sale_products.price',
                    'flash_sales.end_time');
                $data['product'] = $queryP->get();
            }

            return response()->json(new Response($request->token, $data));


        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function products(Request $request)
    {
        try {
            $query = Product::query();
            $query = $query->leftJoin('flash_sale_products', function ($join) {
                $join->on('products.id', '=', 'flash_sale_products.product_id');
            })
                ->leftJoin('flash_sales', function ($join) {
                    $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                    $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                        ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
                })
                ->groupBy('products.id');

            $allCategories = null;
            $category = null;
            $allShipping = null;
            $allCollection = null;
            $allBrand = null;
            $noCategory = false;
            $sidebarData = $request->sidebar_data === 'true' ? true : false;

            if ($request->q) {
                $query = $query->where(function ($q) use ($request) {
                    $q->where('products.title', 'LIKE', "%{$request->q}%")
                        ->orWhere('products.tags', 'LIKE', "%{$request->q}%");
                });
            } else if ($request->home_spm || $request->banner) {

                if ($request->home_spm) {
                    // Products of slider
                    $source = HomeSlider::with('source_brands.brand')
                        ->with('source_categories.category')
                        ->with('source_sub_categories.sub_category')
                        ->with('source_products.product')
                        ->find($request->home_spm);
                } else {
                    $source = Banner::with('source_brands.brand')
                        ->with('source_categories.category')
                        ->with('source_sub_categories.sub_category')
                        ->with('source_products.product')
                        ->find($request->banner);
                }


                if (!is_null($source)) {
                    if ((int)$source['source_type'] === Config::get('constants.sliderSourceType.CATEGORY')) {
                        $itemIds = [];
                        foreach ($source['source_categories'] as $item) {
                            array_push($itemIds, $item['category']['id']);
                        }
                        $query = $query->whereIn('products.category_id', $itemIds);
                    } else if ((int)$source['source_type'] === Config::get('constants.sliderSourceType.SUB_CATEGORY')) {
                        $itemIds = [];
                        foreach ($source['source_sub_categories'] as $item) {
                            array_push($itemIds, $item['sub_category']['id']);
                        }
                        $query = $query->whereIn('products.subcategory_id', $itemIds);
                    } else if ((int)$source['source_type'] === Config::get('constants.sliderSourceType.BRAND')) {
                        $itemIds = [];
                        foreach ($source['source_brands'] as $item) {
                            array_push($itemIds, $item['brand']['id']);
                        }
                        $query = $query->whereIn('products.brand_id', $itemIds);
                    } else if ((int)$source['source_type'] === Config::get('constants.sliderSourceType.TAG')) {
                        foreach (explode(',', $source->tags) as $tag) {
                            if ($tag) {
                                $query = $query->where('products.tags', 'LIKE', "%{$tag}%");
                            }
                        }
                    } else if ((int)$source['source_type'] === Config::get('constants.sliderSourceType.PRODUCT')) {
                        $itemIds = [];
                        foreach ($source['source_products'] as $item) {
                            array_push($itemIds, $item['product']['id']);
                        }
                        $query = $query->whereIn('products.id', $itemIds);
                    }
                }

            } else {
                if ($request->sub_category) {
                    $query = $query->where('products.subcategory_id', $request->sub_category);
                    $noCategory = true;
                }

                if ($request->category) {
                    $query = $query->where('products.category_id', $request->category);

                    $category = Category::with('public_sub_categories')->where('id', $request->category)->first();

                    if (is_null($category)) {
                        return response()->json(Validation::frontendError());
                    }
                    $noCategory = true;
                }
            }

            if ($sidebarData) {
                if (!$noCategory) {
                    $allCategories = Category::where('status', Config::get('constants.status.PUBLIC'))->get();
                }
                $allBrand = Brand::where('status', Config::get('constants.status.PUBLIC'))->select('id', 'title')->get();
                $allCollection = ProductCollection::where('status', Config::get('constants.status.PUBLIC'))
                    ->select('id', 'title')->get();

                $allShipping = ShippingRule::select('id', 'title')->get();
            }

            if ($request->brand) {
                $query = $query->whereIn('products.brand_id', explode(',', $request->brand));
            }

            if ($request->collection) {
                $query = $query
                    ->rightJoin('collection_with_products as cwp', function ($join) {
                        $join->on('products.id', '=', 'cwp.product_id');
                    })
                    ->rightJoin('product_collections as pc', function ($join) use ($request) {
                        $join->on('pc.id', '=', 'cwp.product_collection_id');
                        $join->where('pc.status', Config::get('constants.status.PUBLIC'))
                            ->whereIn('pc.id', explode(',', $request->collection));
                    });
            }

            if ($request->rating != 0) {
                $query = $query->where('products.rating', '>=', $request->rating);
            }

            if ($request->max > 0 || $request->min > 0) {
                if ($request->max == 0) {
                    $request->max = 999999;
                }

                $query = $query->where(function ($q) use ($request) {
                    $q->where(function ($qr) use ($request) {
                        $qr->whereNotNull('flash_sales.end_time');
                        $qr->whereBetween('flash_sale_products.price', [$request->min, $request->max]);

                    });
                    $q->orWhere(function ($qr) use ($request) {
                        $qr->whereNull('flash_sales.end_time');
                        $qr->where('products.offered', '=', 0);
                        $qr->whereBetween('products.selling', [$request->min, $request->max]);;

                    });
                    $q->orWhere(function ($qr) use ($request) {
                        $qr->whereNull('flash_sales.end_time');
                        $qr->where('products.offered', '>', 0);
                        $qr->whereBetween('products.offered', [$request->min, $request->max]);
                    });
                });
            }

            $query = $query->where('products.status', Config::get('constants.status.PUBLIC'));
            $query = $query->select('products.id', 'products.title', 'products.badge',
                'products.selling', 'products.offered',
                'products.image', 'products.review_count', 'products.rating', 'flash_sale_products.price',
                'flash_sales.end_time');


            if ($request->shipping) {
                $query = $query->whereIn('products.shipping_rule_id', explode(',', $request->shipping));
            }

            if ($request->sortby) {
                if ($request->sortby == 'price_low_to_high') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'asc');

                } else if ($request->sortby == 'price_high_to_low') {

                    $query = $query
                        ->addSelect(DB::raw(
                            '(CASE
                        WHEN flash_sales.end_time IS NOT NULL
                            THEN flash_sale_products.price
                        WHEN products.offered=0
                            THEN products.selling
                        ELSE products.offered
                        END) AS current_price'
                        ))
                        ->orderBy('current_price', 'desc');

                } else if ($request->sortby == 'avg_customer_review') {
                    $query = $query->orderBy('products.rating', 'desc');
                } else {
                    $query = $query->orderBy('products.created_at', 'desc');
                }
            } else {
                $query = $query->orderBy('products.updated_at', 'desc');
            }

            $pagination = Config::get('constants.listing.PAGINATION');
            if ($request->is_home_page) {
                $pagination = Config::get('constants.homeProduct.PAGINATION');
            }

            $data['result'] = $query->paginate($pagination);
            $data['category'] = $category;
            $data['all_categories'] = $allCategories;
            $data['shipping'] = $allShipping;
            $data['brands'] = $allBrand;
            $data['collections'] = $allCollection;

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }

    public function common()
    {
        try {
            $commonData = Utils::cacheRemember('common', function () {

                // PAYMENT
                /*$paymentGateway = Payment::first();
                if ($paymentGateway) {
                    $data['payment_gateway'] = $paymentGateway;
                }*/

                $data['img_src_url'] = FileHelper::imgSrcUrl();
                $data['thumb_prefix'] = env('THUMB_PREFIX');
                $data['default_image'] = env('DEFAULT_IMAGE');

                // SETTING
                $siteSetting = SiteSetting::select()->first();
                if ($siteSetting) {
                    $data['site_setting'] = $siteSetting;
                }

                $data['site_setting']['api_base'] = url('/');


                // SETTING
                $setting = Setting::select('currency', 'currency_icon', 'currency_position', 'phone', 'email',
                    'address_1', 'address_2', 'city', 'state', 'zip', 'country')
                    ->first();
                if ($setting) {
                    $data['setting'] = $setting;
                }

                // FOOTER CATEGORIES
                $categories = Category::with('public_sub_categories')
                    ->orderBy('created_at', 'desc')
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->select('id', 'title', 'slug')
                    ->get();

                $data['categories'] = $categories;

                $footerLinks = FooterLink::with('page')
                    ->orderBy('created_at', 'DESC')
                    ->get();

                // Top banner
                $data['top_banner'] = Banner::where('type', Config::get('constants.banner.BANNER_8'))->get()->first();
                $data['popup_banner'] = Banner::where('type', Config::get('constants.banner.BANNER_9'))->get()->first();

                // FOOTER PAGES
                $data['services'] = [];
                $data['about'] = [];
                foreach ($footerLinks as $item) {
                    if ($item->page->title) {
                        $page['id'] = $item->id;
                        $page['title'] = $item->page->title;
                        $page['slug'] = $item->page->slug;

                        if ((int)$item->type === Config::get('constants.footerLinkType.SERVICE')) {
                            array_push($data['services'], $page);
                        } else {
                            array_push($data['about'], $page);
                        }
                    }
                }

                $footerImageLinks = FooterImageLink::orderBy('created_at', 'desc')
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->get();

                $data['payment'] = [];
                $data['social'] = [];
                foreach ($footerImageLinks as $item) {
                    if ((int)$item->type === Config::get('constants.footerImageLinkType.PAYMENT')) {
                        array_push($data['payment'], $item);
                    } else {
                        array_push($data['social'], $item);
                    }
                }

                // return response()->json(new Response(null, $data));
                return $data;
            });

            return response()->json(new Response(null, $commonData));


        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function home(Request $request)
    {
        try {
            $homeData = Utils::cacheRemember('home', function () use ($request) {

                // SLIDER
                $sliders = HomeSlider::where('status', Config::get('constants.status.PUBLIC'))->get();

                $sliderImages['main'] = [];
                foreach ($sliders as $item) {
                    if ((int)$item->type === Config::get('constants.homeSlider.MAIN')) {
                        array_push($sliderImages['main'], $item);
                    } else if ((int)$item->type === Config::get('constants.homeSlider.RIGHT_TOP')) {
                        $sliderImages['right_top'] = $item;
                    } else if ((int)$item->type === Config::get('constants.homeSlider.RIGHT_BOTTOM')) {
                        $sliderImages['right_bottom'] = $item;
                    }
                }

                $data['slider'] = $sliderImages;


                // Banners

                $data['banners'] = Banner::where('status', Config::get('constants.status.PUBLIC'))->get();


                // FEATURED CATEGORIES
                $featured_categories = SubCategory::with('category')
                    ->where('featured', Config::get('constants.status.PUBLIC'))
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->offset(0)
                    ->limit(Config::get('constants.homePagePagination.FEATURED_CATEGORIES'))
                    ->get();

                $data['featured_categories'] = $featured_categories;


                // FLASH SALES
                $flashSales = FlashSale::with('public_products')
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->where('end_time', '>=', date('Y-m-d H:i:s'))
                    ->get();

                $data['flash_sales'] = [];
                foreach ($flashSales as $item) {
                    if (count($item->public_products) > 0) {
                        array_push($data['flash_sales'], $item);
                    }
                }

                $data['time_zone'] = Carbon::now()->timezoneName;

                // PRODUCT COLLECTION
                $collections = ProductCollection::query();
                $totalRecordCount = $collections->count();
                $data['collections'] = $collections->with([
                    'product_collections' => function ($query) use ($totalRecordCount) {
                        $query->take(Config::get('constants.homePagePagination.COLLECTION') * $totalRecordCount);
                        $query->with('product');
                    }
                ])
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->get();

                // FEATURED BRANDS
                $featured_brands = Brand::where('featured', Config::get('constants.status.PUBLIC'))
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->offset(0)
                    ->limit(Config::get('constants.homePagePagination.FEATURED_BRANDS'))
                    ->get();

                $data['featured_brands'] = $featured_brands;

                //return response()->json(new Response(null, $data));
                return $data;
            });

            return response()->json(new Response(null, $homeData));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function reviews(Request $request, $id)
    {
        try {
            if ($request->get_total && $request->get_total === 'true') {
                $data['total'] = RatingReview::select(DB::raw('count(user_id) as total'), DB::raw('rating'))
                    ->groupBy(DB::raw('rating'))
                    ->where('product_id', $id)
                    ->get();


                $data['banner'] = Banner::where('type', Config::get('constants.banner.BANNER_7'))
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->first();
            }

            $data['all'] = RatingReview::with('user')
                ->with('review_images')
                ->where('product_id', $id)
                ->orderBy($request->order_by, $request->type)
                ->paginate(Config::get('constants.pagination.FRONTEND_PRODUCT_RATING'));


            if ($request->time_zone) {
                foreach ($data['all'] as $item) {
                    $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
                }

            } else {
                foreach ($data['all'] as $item) {
                    $item['created'] = Utils::formatDate($item->created_at);
                }
            }

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }

    public function product(Request $request, $id)
    {
        try {
            // $productData = Utils::cacheRemember('detail.' . $id, function () use ($request, $id) {
            $query = Product::with('brand')
                ->with('store')
                ->with('bundle_deal')
                ->with('current_categories')
                ->with('category')
                ->with('sub_category')
                ->with('product_image_names')
                ->with('shipping_rule.shipping_places');

            $query = $query->leftJoin('flash_sale_products', function ($join) {
                $join->on('products.id', '=', 'flash_sale_products.product_id');
            })->leftJoin('flash_sales', function ($join) {
                $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                    ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
            });

            $query = $query->leftJoin('user_wishlists', function ($join) use ($request) {
                $join->on('products.id', '=', 'user_wishlists.product_id');
                $join->where('user_wishlists.user_id', $request->user_id);
            });

            $query = $query->select('products.*', 'flash_sale_products.price', 'flash_sales.end_time',
                'user_wishlists.id as wishlisted');


            $query = $query->where('products.status', Config::get('constants.status.PUBLIC'));

            $data = $query->find($id);

            if (!$data) {
                return response()->json(Validation::frontendError());
            }


            if (!$data->image) {
                $data->image = Config::get('constants.media.DEFAULT_IMAGE');
            }

            if (count($data->product_image_names) > 0) {
                $data['images'] = $data->product_image_names;
            }

            $slug = [];

            array_push($slug, $data->category);

            if ($data->sub_category) {
                $subCat = $data->sub_category;
                $subCat['category'] = $data->category;

                array_push($slug, $subCat);
            }

            $data['slug'] = $slug;

            $data['time_zone'] = Carbon::now()->timezoneName;

            $data['in_stock'] = false;


            $productInventoryAttr = Attribute::whereHas('values', function ($q) use ($id) {
                $q->join('inventory_attributes as ia', function ($join) {
                    $join->on('ia.attribute_value_id', '=', 'attribute_values.id');
                })->join('updated_inventories as i', function ($join) use ($id) {
                    $join->on('i.id', '=', 'ia.inventory_id');
                    $join->where('i.product_id', $id);
                });
            })->with(['values' => function ($q) use ($id) {
                $q->join('inventory_attributes as ia', function ($join) {
                    $join->on('ia.attribute_value_id', '=', 'attribute_values.id');
                })->join('updated_inventories as i', function ($join) use ($id) {
                    $join->on('i.id', '=', 'ia.inventory_id');
                    $join->where('i.product_id', $id);
                })->groupBy('attribute_values.id');
            }])->get();

            $data['attribute'] = $productInventoryAttr;
            $data['inventory'] = UpdatedInventory::with('inventory_attributes')
                ->where('product_id', $id)->get();

            if (count($data['inventory']) > 0) {
                foreach ($data['inventory'] as $i) {
                    if ($i['quantity'] > 0) {
                        $data['in_stock'] = true;
                        break;
                    }
                }
            }

            $currentTime = Carbon::now()->format('Y-m-d H:i:s');

            $data['vouchers'] = Voucher::where('end_time', '>=', $currentTime)
                ->where('start_time', '<=', $currentTime)
                ->where('status', Config::get('constants.status.PUBLIC'))
                ->select('title', 'price', 'type', 'code', 'min_spend', 'usage_limit', 'limit_per_customer')
                ->get();

            return response()->json(new Response(null, $data));
            // return $data;

            //});

            //return response()->json(new Response(null, $productData));


        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function contactUs(Request $request)
    {
        try {
            ContactUs::create($request->all());

            return response()->json(new Response('', true));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function trackOrder(Request $request)
    {
        try {
            $order = Order::with('ordered_products.shipping_place')
                ->with('ordered_products.product')
                ->where('order', $request->tracking_id)->get()->first();

            if (is_null($order)){
                return response()->json(Validation::nothing_found(201));
            }

            return response()->json(new Response('', $order));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }



    public function page($slug)
    {
        try {
            $page = Utils::cacheRemember('pages' . $slug, function () use ($slug) {

                $page = Page::where('slug', $slug)
                    ->select('slug', 'title', 'description', 'meta_title', 'meta_description', 'page_from_component')
                    ->first();

                if ($page->description == 'Sitemap') {
                    $page['categories'] = Category::with(['public_sub_categories' => function ($query) {
                        $query->with(['products' => function ($q) {
                            $q->where('status', Config::get('constants.status.PUBLIC'))
                                ->select('id', 'subcategory_id', 'title');

                        }])
                            ->where('status', Config::get('constants.status.PUBLIC'))
                            ->select('id', 'category_id', 'title', 'slug');
                    }])
                        ->where('status', Config::get('constants.status.PUBLIC'))
                        ->select('id', 'title', 'slug')
                        ->get();
                }


                if (!is_null($page)) {

                    return $page;
                }

                return response()->json(new Response(null, []));

            });

            return response()->json(new Response(null, $page));
        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function flashSale($id = null)
    {
        try {
            if (!is_null($id)) {
                $flashSale = FlashSale::where('id', $id)
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->where('end_time', '>=', date('Y-m-d H:i:s'))
                    ->first();

                if (!is_null($flashSale)) {
                    $query = FlashSaleProduct::query();
                    $query = $query->where('flash_sale_id', $id);
                    $query = $query->join('products as p', function ($join) {
                        $join->on('p.id', '=', 'flash_sale_products.product_id');
                        $join->where('p.status', Config::get('constants.status.PUBLIC'));
                    });
                    $query = $query->select('flash_sale_products.*', 'p.id', 'p.title', 'p.selling', 'p.offered',
                        'p.image', 'p.review_count', 'p.rating');

                    $data = $query->paginate(Config::get('constants.frontend.PAGINATION'));

                    return response()->json(new Response(null, $data));
                }

                return response()->json(Validation::frontendError());

            } else {
                $flashSales = FlashSale::with('public_products')
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->where('end_time', '>=', date('Y-m-d H:i:s'))
                    ->get();

                return response()->json(new Response(null, $flashSales));
            }

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }
    }


    public function productSuggestion(Request $request, $id)
    {

        try {
            $product = Product::select('subcategory_id', 'category_id')->find($id);
            $data['suggestion_1'] = [];
            $data['suggestion_2'] = [];

            if ($product) {

                $query = Product::query();
                $query = $query->leftJoin('flash_sale_products', function ($join) {
                    $join->on('products.id', '=', 'flash_sale_products.product_id');
                    $join->where('products.status', Config::get('constants.status.PUBLIC'));
                })
                    ->leftJoin('flash_sales', function ($join) {
                    $join->on('flash_sales.id', '=', 'flash_sale_products.flash_sale_id');
                    $join->where('flash_sales.end_time', '>=', date('Y-m-d H:i:s'))
                        ->where('flash_sales.status', Config::get('constants.status.PUBLIC'));
                });

                $query = $query->select('products.id', 'products.title', 'products.badge',
                    'products.selling', 'products.offered',
                    'products.image', 'products.review_count', 'products.rating', 'flash_sale_products.price',
                    'flash_sales.end_time');

                $query = $query->where('products.status', Config::get('constants.status.PUBLIC'));

                $query2 = clone $query;


                if ($product->subcategory_id) {

                    $tempQuery = clone $query;


                    $query = $query->where('products.id', '!=', $id);
                    $query = $query->where('products.subcategory_id', $product->subcategory_id);
                    $data['suggestion_1'] = $query->paginate(Config::get('constants.imageSlider.PAGINATION'));


                    $query2 = $query2->where('products.category_id', $product->category_id);
                    $query2 = $query2->where('products.subcategory_id', '!=', $product->subcategory_id);
                    $data['suggestion_2'] = $query2->paginate(Config::get('constants.imageSlider.PAGINATION'));

                    $count1 = Config::get('constants.imageSlider.PAGINATION') - count($data['suggestion_1']);
                    $count2 = Config::get('constants.imageSlider.PAGINATION') - count($data['suggestion_2']);

                    if ($request->page == 1 && ($count1 > 0 || $count2 > 0)) {
                        $tempQuery = $tempQuery->where('products.id', '!=', $id);
                        $tempQuery = $tempQuery->where('products.category_id', '!=', $product->category_id);
                        $productsOtherCategory = $tempQuery->limit($count1 + $count2)->get();

                        if ($count1 > 0) {
                            $spliced1 = array_slice($productsOtherCategory->toArray(), 0, $count1);
                            $updated1 = $data['suggestion_1']->toBase()->merge($spliced1);
                            $data['suggestion_1'] = $data['suggestion_1']->setCollection($updated1);
                        }

                        if ($count2 > 0) {
                            $spliced2 = array_slice($productsOtherCategory->toArray(), $count1 - 1, $count2);
                            $updated2 = $data['suggestion_2']->toBase()->merge($spliced2);
                            $data['suggestion_2'] = $data['suggestion_2']->setCollection($updated2);
                        }
                    }

                } else {
                    $query2 = $query2->where('products.category_id', $product->category_id);
                    $query2 = $query2->where('products.id', '!=', $id);
                    $data['suggestion_2'] = $query2->paginate(Config::get('constants.imageSlider.PAGINATION'));
                }
            }

            return response()->json(new Response(null, $data));

        } catch (\Exception $e) {

            if ($e instanceof \PDOException) {
                return response()->json(Validation::error(null, explode('.', $e->getMessage())[0]));
            } else {
                return response()->json(Validation::error(null, $e->getMessage()));
            }
        }


    }
}
