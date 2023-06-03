<?php

namespace App\Http\Controllers;

use App\Models\BannerSourceBrand;
use App\Models\BannerSourceCategory;
use App\Models\Category;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\HomeSliderSourceCategory;
use App\Models\Page;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CategoriesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if ($can = Utils::userCan($this->user, 'category.view')) {
            return $can;
        }

        $query = Category::query()
            ->orderBy($request->orderby, $request->type);
        if ($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if ($request->q) {
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item) {
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }

    public function allCategories(Request $request)
    {
        $data = Category::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if ($can = Utils::userCan($this->user, 'category.view')) {
            return $can;
        }
        $category = Category::find($id);

        if (!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $category)) {
            return $isOwner;
        }

        if (is_null($category)) {
            return response()->json(Validation::noData());
        }

        $category['created'] = Utils::formatDate($category->created_at);

        return response()->json(new Response($request->token, $category));
    }


    public function action(Request $request, Category $category)
    {
        $validate = Validation::category($request);
        if ($validate) {
            return response()->json($validate);
        }

        $bySlug = Category::where('slug', $request['slug'])->get()->first();

        if ($category->id) {
            if ($can = Utils::userCan($this->user, 'category.edit')) {
                return $can;
            }
            $existing = Category::find($category->id);

            if (!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            if ($bySlug && $bySlug['id'] != $category->id) {
                return response()->json(Validation::error($request->token, __('api.slug_exists')));
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $category->update(array_filter($filtered));

        } else {
            if ($can = Utils::userCan($this->user, 'category.create')) {
                return $can;
            }

            if ($bySlug) {
                return response()->json(Validation::error($request->token, __('api.slug_exists')));
            }

            $request['image'] = Config::get('constants.media.DEFAULT_IMAGE');
            $request['admin_id'] = $request->user()->id;
            $request['id'] = Utils::idGenerator(new Category());
            $category = Category::create($request->all());
        }

        $category['created'] = Utils::formatDate($category->created_at);

        return response()->json(new Response($request->token, $category));
    }


    public function delete(Request $request, $id)
    {

        try {


            if ($can = Utils::userCan($this->user, 'category.delete')) {
                return $can;
            }

            $category = Category::find($id);

            if (!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $category)) {
                return $isOwner;
            }

            if (is_null($category))
                return response()->json(Validation::nothing_found());

            $sub_category = SubCategory::where('category_id', $id)->get()->first();

            if ($sub_category) {
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message' => __('api.category_used')])
                ));
            }

            $product = Product::where('category_id', $id)->get()->first();

            if ($product) {
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message' => __('api.category_by_product')])
                ));
            }

            $homeSlidersSourceCategory = HomeSliderSourceCategory::where('category_id', $id)->get()->first();
            if ($homeSlidersSourceCategory) {
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message' => __('api.slider_by_cat')])
                ));
            }


            $bannerSourceCat  = BannerSourceCategory::where('category_id', $id)->get()->first();

            if($bannerSourceCat){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message'=> __('api.banner_used')])));
            }

            if ($category->delete()) {
                FileHelper::deleteFile($category->image);
                return response()->json(new Response($request->token, $category));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function upload(Request $request, $id = null)
    {
        try {


            $validate = Validation::image($request);
            if ($validate) {
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], 'category');
            $request['image'] = $image_info['name'];

            $category = $id ? Category::find($id) : null;

            if (is_null($category)) {
                if ($can = Utils::userCan($this->user, 'category.create')) {
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new Category());
                $category = Category::create($request->all());

            } else {
                if ($can = Utils::userCan($this->user, 'category.edit')) {
                    return $can;
                }
                if (!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $category)) {
                    return $isOwner;
                }

                $category_image = $category->image;
                if ($category->update($request->all())) {
                    FileHelper::deleteFile($category_image);
                }
            }

            $category['created'] = Utils::formatDate($category->created_at);
            return response()->json(new Response($request->token, $category));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }

    }
}
