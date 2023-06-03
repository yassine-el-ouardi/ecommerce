<?php

namespace App\Http\Controllers;

use App\Models\BundleDeal;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BundleDealsController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'bundle_deal.view')){
            return $can;
        }

        $query = BundleDeal::orderBy($request->orderby, $request->type);
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
        $data = BundleDeal::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'bundle_deal.view')){
            return $can;
        }
        $data = BundleDeal::find($id);
        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $data)) {
            return $isOwner;
        }

        if (is_null($data)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, BundleDeal $bundleDeal)
    {
        $validate = Validation::bundleDeals($request);
        if($validate){
            return response()->json($validate);
        }

        if($bundleDeal->id){
            if($can = Utils::userCan($this->user, 'bundle_deal.edit')){
                return $can;
            }

            $existing = BundleDeal::find($bundleDeal->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $bundleDeal->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'bundle_deal.create')){
                return $can;
            }
            $request['admin_id'] = $request->user()->id;
            $bundleDeal = BundleDeal::create($request->all());
        }

        $bundleDeal['created'] = Utils::formatDate($bundleDeal->created_at);
        return response()->json(new Response($request->token, $bundleDeal));
    }



    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'bundle_deal.delete')){
                return $can;
            }
            $bundleDeal = BundleDeal::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $bundleDeal)) {
                return $isOwner;
            }

            if (is_null($bundleDeal)){
                return response()->json(Validation::noData());
            }

            $product = Product::where('bundle_deal_id', $id)->get()->first();

            if($product){
                return response()->json(Validation::error($request->token,
                    __('api.unable_delete', ['message'=> __('api.deal_used')])
                ));
            }

            if ($bundleDeal->delete()){
                return response()->json(new Response($request->token, $bundleDeal));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }




    }
}
