<?php

namespace App\Http\Controllers;

use App\Models\CollectionWithProduct;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Product;
use App\Models\ProductCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProductCollectionsController extends ControllerHelper
{
    public function all(Request $request)
    {

        if($can = Utils::userCan($this->user, 'product_collection.view')){
            return $can;
        }

        $query = ProductCollection::query()
            ->orderBy($request->orderby, $request->type);
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

    public function allList(Request $request)
    {
        $data = ProductCollection::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'product_collection.view')){
            return $can;
        }

        $data = ProductCollection::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $data)) {
            return $isOwner;
        }

        if (is_null($data)){
            return response()->json(Validation::noData());
        }

        $category['created'] = Utils::formatDate($data->created_at);

        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, ProductCollection $productCollection)
    {
        $validate = Validation::collection($request);
        if($validate){
            return response()->json($validate);
        }

        if($productCollection->id){
            if($can = Utils::userCan($this->user, 'product_collection.edit')){
                return $can;
            }

            $existing = ProductCollection::find($productCollection->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $productCollection->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'product_collection.create')){
                return $can;
            }

            $request['admin_id'] = $request->user()->id;
            $productCollection = ProductCollection::create($request->all());
        }

        $productCollection['created'] = Utils::formatDate($productCollection->created_at);

        return response()->json(new Response($request->token, $productCollection));
    }



    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'product_collection.delete')){
                return $can;
            }
            $productCollection = ProductCollection::find($id);



            if (is_null($productCollection)){
                return response()->json(Validation::nothing_found());
            }

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $productCollection)) {
                return $isOwner;
            }


            $collectionWithProduct = CollectionWithProduct::where('product_collection_id',$id )->get()->first();


            if($collectionWithProduct){
                return response()->json(Validation::error($request->token,
                    __('api.using_collection')));
            }

            if ($productCollection->delete()){
                return response()->json(new Response($request->token, $productCollection));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }



    }
}
