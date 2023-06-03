<?php

namespace App\Http\Controllers;

use App\Models\BannerSourceBrand;
use App\Models\Brand;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\HomeSliderSourceBrand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BrandsController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'brand.view')){
            return $can;
        }

        $query = Brand::query()->orderBy($request->orderby, $request->type);

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        if($request->time_zone){
            foreach ($data as $item){
                $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
            }
        }else{
            foreach ($data as $item){
                $item['created'] = Utils::formatDate($item->created_at);
            }
        }

        return response()->json(new Response($request->token, $data));
    }

    public function allBrands(Request $request)
    {
        $data = Brand::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'brand.view')){
            return $can;
        }
        $brand = Brand::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $brand)) {
            return $isOwner;
        }

        if (is_null($brand)){
            return response()->json(Validation::noData());
        }

        if($request->time_zone){
            $brand['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($brand->created_at, $request->time_zone));

        }else{
            $brand['created'] = Utils::formatDate($brand->created_at);
        }

        return response()->json(new Response($request->token, $brand));
    }


    public function action(Request $request, Brand $brand)
    {
        $validate = Validation::brand($request);
        if($validate){
            return response()->json($validate);
        }

        if($brand->id){
            if($can = Utils::userCan($this->user, 'brand.edit')){
                return $can;
            }

            $existing = Brand::find($brand->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $brand->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'brand.create')){
                return $can;
            }

            $request['image'] = Config::get('constants.media.DEFAULT_IMAGE');
            $request['admin_id'] = $request->user()->id;
            $request['id'] = Utils::idGenerator(new Brand());
            $brand = Brand::create($request->all());
        }

        $brand['created'] = Utils::formatDate($brand->created_at);

        return response()->json(new Response($request->token, $brand));
    }


    public function delete(Request $request, $id)
    {
        try{

            if($can = Utils::userCan($this->user, 'brand.delete')){
                return $can;
            }

            $brand = Brand::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $brand)) {
                return $isOwner;
            }

            if (is_null($brand)){
                return response()->json(Validation::noData());
            }

            $product = Product::where('brand_id', $id)->get()->first();

            if($product){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message' => __('api.brand_used')])));
            }

            $homeSlidersSourceBrand = HomeSliderSourceBrand::where('brand_id', $id)->get()->first();

            if($homeSlidersSourceBrand){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message'=> __('api.slider_used')])));
            }



            $bannerSourceBrand = BannerSourceBrand::where('brand_id', $id)->get()->first();

            if($bannerSourceBrand){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message'=> __('api.banner_used')])));
            }


            if ($brand->delete()){
                FileHelper::deleteFile($brand->image);
                return response()->json(new Response($request->token, $brand));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function upload(Request $request, $id = null)
    {

        try{
            $validate = Validation::image($request);
            if($validate){
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], 'brand');
            $request['image'] = $image_info['name'];

            $brand = $id ? Brand::find($id) : null;

            if (is_null($brand)){
                if($can = Utils::userCan($this->user, 'brand.create')){
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new Brand());
                $brand = Brand::create($request->all());

            }else{
                if($can = Utils::userCan($this->user, 'brand.edit')){
                    return $can;
                }

                $existing = Brand::find($brand->id);
                if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                    return $isOwner;
                }

                $brand_image = $brand->image;
                if($brand->update($request->all())){
                    FileHelper::deleteFile($brand_image);
                }
            }

            $brand['created'] = Utils::formatDate($brand->created_at);
            return response()->json(new Response($request->token, $brand));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
