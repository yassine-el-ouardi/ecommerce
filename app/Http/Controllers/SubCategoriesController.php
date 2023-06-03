<?php

namespace App\Http\Controllers;

use App\Models\BannerSourceCategory;
use App\Models\BannerSourceSubCategory;
use App\Models\Category;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\HomeSliderSourceSubCategory;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use App\Models\Helper\FileHelper;

class SubCategoriesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'subcategory.view')){
            return $can;
        }

        $query = SubCategory::with('category')->orderBy($request->orderby, $request->type);

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item){
            $item['created'] = Utils::formatDate($item->created_at);
        }

        return response()->json(new Response($request->token, $data));
    }

    public function allSubCategories(Request $request)
    {
        $data = SubCategory::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'subcategory.view')){
            return $can;
        }
        $subCategory = SubCategory::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $subCategory)) {
            return $isOwner;
        }

        if (is_null($subCategory)){
            return response()->json(Validation::noData());
        }

        $subCategory['created'] = Utils::formatDate($subCategory->created_at);

        return response()->json(new Response($request->token, $subCategory));
    }


    public function action(Request $request, SubCategory $subCategory)
    {
        $validate = Validation::subCategory($request);
        if($validate){
            return response()->json($validate);
        }


        $bySlug = SubCategory::where('slug', $request['slug'])->get()->first();

        if($subCategory->id){
            if($can = Utils::userCan($this->user, 'subcategory.edit')){
                return $can;
            }

            $existing = SubCategory::find($subCategory->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            if($bySlug && $bySlug['id'] != $subCategory->id){
                return response()->json(Validation::error($request->token,
                    __('api.slug_exists')
                ));
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $subCategory->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'subcategory.create')){
                return $can;
            }

            if($bySlug){
                return response()->json(Validation::error($request->token,
                    __('api.slug_exists')
                ));
            }

            $request['image'] = Config::get('constants.media.DEFAULT_IMAGE');
            $request['admin_id'] = $request->user()->id;
            $request['id'] = Utils::idGenerator(new SubCategory());
            $subCategory = SubCategory::create($request->all());
        }

        $subCategory['created'] = Utils::formatDate($subCategory->created_at);

        return response()->json(new Response($request->token, $subCategory));
    }


    public function delete(Request $request, $id)
    {
        try{

            if($can = Utils::userCan($this->user, 'subcategory.delete')){
                return $can;
            }
            $subCategory = SubCategory::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $subCategory)) {
                return $isOwner;
            }

            if (is_null($subCategory))
                return response()->json(Validation::nothing_found());

            $product = Product::where('subcategory_id', $id)->get()->first();

            if($product){
                return response()->json(Validation::error($request->token,
                    __('api.item_used')
                ));
            }

            $homeSlidersSourceSubCategory = HomeSliderSourceSubCategory::where('sub_category_id', $id)
                ->get()->first();

            if($homeSlidersSourceSubCategory){
                return response()->json(Validation::error($request->token,
                    __('api.slider_used_sub_category')
                ));
            }

            $bannerSourceSubCat  = BannerSourceSubCategory::where('sub_category_id', $id)->get()->first();

            if($bannerSourceSubCat){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message'=> __('api.banner_used')])));
            }

            if ($subCategory->delete()){
                FileHelper::deleteFile($subCategory->image);
                return response()->json(new Response($request->token, $subCategory));
            }

            return response()->json(Validation::error($request->token));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }

    }


    public function upload(Request $request, $id = null)
    {
        try{
            $validate = Validation::subCategoryImage($request);
            if($validate){
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], 'sub-category');
            $request['image'] = $image_info['name'];

            $subCategory = $id ? SubCategory::find($id) : null;

            if (is_null($subCategory)){
                if($can = Utils::userCan($this->user, 'subcategory.create')){
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new SubCategory());
                $subCategory = SubCategory::create($request->all());

            }else{
                if($can = Utils::userCan($this->user, 'subcategory.edit')){
                    return $can;
                }
                if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $subCategory)) {
                    return $isOwner;
                }

                $sub_category_image = $subCategory->image;
                if($subCategory->update($request->all())){
                    FileHelper::deleteFile($sub_category_image);
                }
            }

            $subCategory['created'] = Utils::formatDate($subCategory->created_at);
            return response()->json(new Response($request->token, $subCategory));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
