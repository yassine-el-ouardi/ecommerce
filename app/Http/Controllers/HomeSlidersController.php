<?php

namespace App\Http\Controllers;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\HomeSlider;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\HomeSliderSourceBrand;
use App\Models\HomeSliderSourceCategory;
use App\Models\HomeSliderSourceProduct;
use App\Models\HomeSliderSourceSubCategory;
use App\Models\ShippingRule;
use App\Models\SubCategory;
use App\Models\TaxRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class HomeSlidersController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'home_slider.view')){
            return $can;
        }

        $query = HomeSlider::orderBy('created_at', 'ASC');

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        $sliders = $query->get();

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

        return response()->json(new Response($request->token, $sliderImages));
    }


    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'home_slider.view')){
            return $can;
        }
        $homeSlider = HomeSlider::with('source_brands.brand')
            ->with('source_categories.category')
            ->with('source_sub_categories.sub_category')
            ->with('source_products.product')
            ->find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $homeSlider)) {
            return $isOwner;
        }

        if (is_null($homeSlider)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $homeSlider));
    }

    public function action(Request $request, HomeSlider $homeSlider)
    {
        $validate = Validation::homeSlider($request);
        if($validate){
            return response()->json($validate);
        }

        $filteredCategories = [];
        $filteredSubCategories = [];
        $filteredBrands = [];
        $filteredProducts = [];

        if($homeSlider->id){
            if($can = Utils::userCan($this->user, 'home_slider.edit')){
                return $can;
            }

            $existing = HomeSlider::find($homeSlider->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $homeSlider->update($request->all());

            $existingHomeSlider = HomeSlider::with('source_brands.brand')
                ->with('source_categories.category')
                ->with('source_sub_categories.sub_category')
                ->with('source_products.product')
                ->find($homeSlider->id);

            // Adding item id in array to prevent adding same id twice
            foreach ($existingHomeSlider['source_categories'] as $i){
                $filteredCategories[$i->category->id] = $i->category->id;
            }

            foreach ($existingHomeSlider['source_sub_categories'] as $i){
                $filteredSubCategories[$i->sub_category->id] = $i->sub_category->id;
            }

            foreach ($existingHomeSlider['source_brands'] as $i){
                $filteredBrands[$i->brand->id] = $i->brand->id;
            }

            foreach ($existingHomeSlider['source_products'] as $i){
                $filteredProducts[$i->product->id] = $i->product->id;
            }

        }else{
            if($can = Utils::userCan($this->user, 'home_slider.create')){
                return $can;
            }

            $request['admin_id'] = $request->user()->id;
            $homeSlider = HomeSlider::create($request->all());
        }

        // Adding / deleting / updating categories
        $categoryDeleted = [];
        $categoryAdded = [];

        foreach ($request['source_categories'] as $i){
            // Checking the item is to not be deleted and already in the database
            if(key_exists($i['category']['id'], $filteredCategories) && !key_exists('deleted', $i)){
                continue;
            }
            // Check if this item is not being deleted, then added to the existing list
            if(!key_exists('deleted', $i)){
                $filteredCategories[$i['category']['id']] = $i['category']['id'];
            }

            if(key_exists('deleted', $i) && key_exists('id', $i) && $i['id'] && $i['deleted']){
                // Deleted category
                array_push($categoryDeleted, $i['id']);
            } else if((!key_exists('id', $i) || (key_exists('id', $i) && !$i['id']))
                && ((key_exists('deleted', $i) && !$i['deleted']) || !key_exists('deleted', $i))
                && $i['category']['id']) {
                // Added category
                array_push($categoryAdded, [
                    'home_slider_id' => $homeSlider->id,
                    'category_id'=> $i['category']['id']
                ]);
            } else if(key_exists('id', $i) && $i['id'] && (key_exists('updated', $i) && $i['updated']) && $i['category']['id']){
                // Updated category
                HomeSliderSourceCategory::where('id', $i['id'])->update(['category_id'=> $i['category']['id']]);
            }
        }

        HomeSliderSourceCategory::whereIn('id', $categoryDeleted)->delete();
        HomeSliderSourceCategory::insert($categoryAdded);

        // Adding / deleting / updating sub categories
        $subCategoryDeleted = [];
        $subCategoryAdded = [];

        foreach ($request['source_sub_categories'] as $i){
            // Checking the item is to not be deleted and already in the database
            if(key_exists($i['sub_category']['id'], $filteredSubCategories) && !key_exists('deleted', $i)){
                continue;
            }
            // Check if this item is not being deleted, then added to the existing list
            if(!key_exists('deleted', $i)){
                $filteredSubCategories[$i['sub_category']['id']] = $i['sub_category']['id'];
            }

            if(key_exists('deleted', $i) && key_exists('id', $i) && $i['id'] && $i['deleted']){
                // Deleted sub category
                array_push($subCategoryDeleted, $i['id']);
            } else if((!key_exists('id', $i) || (key_exists('id', $i) && !$i['id']))
                && ((key_exists('deleted', $i) && !$i['deleted']) || !key_exists('deleted', $i))
                && $i['sub_category']['id']) {
                // Added sub  category
                array_push($subCategoryAdded, [
                    'home_slider_id' => $homeSlider->id,
                    'sub_category_id'=> $i['sub_category']['id']
                ]);
            } else if(key_exists('id', $i) && $i['id'] && (key_exists('updated', $i) && $i['updated']) && $i['sub_category']['id']){
                // Updated sub category
                HomeSliderSourceSubCategory::where('id', $i['id'])->update(['sub_category_id'=> $i['sub_category']['id']]);
            }
        }

        HomeSliderSourceSubCategory::whereIn('id', $subCategoryDeleted)->delete();
        HomeSliderSourceSubCategory::insert($subCategoryAdded);

        // Adding / deleting / updating brands
        $brandDeleted = [];
        $brandAdded = [];

        foreach ($request['source_brands'] as $i){
            // Checking the item is to not be deleted and already in the database
            if(key_exists($i['brand']['id'], $filteredBrands) && !key_exists('deleted', $i)){
                continue;
            }
            // Check if this item is not being deleted, then added to the existing list
            if(!key_exists('deleted', $i)){
                $filteredBrands[$i['brand']['id']] = $i['brand']['id'];
            }

            if(key_exists('deleted', $i) && key_exists('id', $i) && $i['id'] && $i['deleted']){
                // Deleted brand
                array_push($brandDeleted, $i['id']);
            } else if((!key_exists('id', $i) || (key_exists('id', $i) && !$i['id']))
                && ((key_exists('deleted', $i) && !$i['deleted']) || !key_exists('deleted', $i))
                && $i['brand']['id']) {
                // Added brand
                array_push($brandAdded, [
                    'home_slider_id' => $homeSlider->id,
                    'brand_id'=> $i['brand']['id']
                ]);
            } else if(key_exists('id', $i) && $i['id'] && (key_exists('updated', $i) && $i['updated']) && $i['brand']['id']){
                // Updated brand
                HomeSliderSourceBrand::where('id', $i['id'])->update(['brand_id' => $i['brand']['id']]);
            }
        }

        HomeSliderSourceBrand::whereIn('id', $brandDeleted)->delete();
        HomeSliderSourceBrand::insert($brandAdded);


        // Adding / deleting / updating products
        $productDeleted = [];
        $productAdded = [];

        foreach ($request['source_products'] as $i){
            // Checking the item is to not be deleted and already in the database
            if(key_exists($i['product']['id'], $filteredProducts) && !key_exists('deleted', $i)){
                continue;
            }
            // Check if this item is not being deleted, then added to the existing list
            if(!key_exists('deleted', $i)){
                $filteredProducts[$i['product']['id']] = $i['product']['id'];
            }

            if(key_exists('deleted', $i) && key_exists('id', $i) && $i['id'] && $i['deleted']){
                // Deleted brand
                array_push($productDeleted, $i['id']);
            } else if((!key_exists('id', $i) || (key_exists('id', $i) && !$i['id']))
                && ((key_exists('deleted', $i) && !$i['deleted']) || !key_exists('deleted', $i))
                && $i['product']['id']) {
                // Added product
                array_push($productAdded, [
                    'home_slider_id' => $homeSlider->id,
                    'product_id'=> $i['product']['id']
                ]);
            } else if(key_exists('id', $i) && $i['id'] && (key_exists('updated', $i) && $i['updated']) && $i['product']['id']){
                // Updated product
                HomeSliderSourceProduct::where('id', $i['id'])->update(['product_id' => $i['product']['id']]);
            }
        }

        HomeSliderSourceProduct::whereIn('id', $productDeleted)->delete();
        HomeSliderSourceProduct::insert($productAdded);


        $homeSlider = HomeSlider::with('source_brands.brand')
            ->with('source_categories.category')
            ->with('source_sub_categories.sub_category')
            ->with('source_products.product')
            ->find($homeSlider->id);

        return response()->json(new Response($request->token, $homeSlider));
    }


    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'home_slider.delete')){
                return $can;
            }

            $homeSlider = HomeSlider::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $homeSlider)) {
                return $isOwner;
            }

            if (is_null($homeSlider)){
                return response()->json(Validation::nothing_found());
            }

            HomeSliderSourceBrand::where('home_slider_id', $id)->delete();
            HomeSliderSourceCategory::where('home_slider_id', $id)->delete();
            HomeSliderSourceSubCategory::where('home_slider_id', $id)->delete();
            HomeSliderSourceProduct::where('home_slider_id', $id)->delete();

            if ($homeSlider->delete()){
                FileHelper::deleteFile($homeSlider->image);
                return response()->json(new Response($request->token, $homeSlider));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }

    public function upload(Request $request, $id = null)
    {
        try{
            $validate = Validation::homeSliderImage($request);
            if($validate){
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], 'footer');
            $request['image'] = $image_info['name'];

            $homeSlider = $id ? HomeSlider::find($id) : null;

            if (is_null($homeSlider)){
                $request['admin_id'] = $request->user()->id;
                $homeSlider = HomeSlider::create($request->all());

            }else{
                $image = $homeSlider->image;
                if($homeSlider->update($request->all())){
                    FileHelper::deleteFile($image);
                }
            }

            $homeSlider = HomeSlider::with('source_brands.brand')
                ->with('source_categories.category')
                ->with('source_sub_categories.sub_category')
                ->with('source_products.product')
                ->find($homeSlider->id);
            return response()->json(new Response($request->token, $homeSlider));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
