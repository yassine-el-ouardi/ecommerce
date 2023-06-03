<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\ShippingPlace;
use App\Models\ShippingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ShippingRulesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'shipping_rule.view')){
            return $can;
        }

        $query = ShippingRule::with('shipping_places')
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
        $data = ShippingRule::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'shipping_rule.view')){
            return $can;
        }
        $data = ShippingRule::with('shipping_places')->find($id);
        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $data)) {
            return $isOwner;
        }

        if (is_null($data)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, ShippingRule $shippingRules)
    {
        $validate = Validation::shippingRule($request);
        if($validate) {
            return response()->json($validate);
        }

        $activeShippingRule = array_filter($request->shipping_places, function ($element) {
            return !key_exists('deleted', $element) || !$element['deleted'];
        });

        if(count($activeShippingRule) < 1){
            return response()->json(Validation::error($request->token,
                __('api.shipping_least')
            ));
        }

        if($shippingRules->id){
            if($can = Utils::userCan($this->user, 'shipping_rule.edit')){
                return $can;
            }

            $existing = ShippingRule::find($shippingRules->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $filtered = array_filter($request->all(), function ($element) {
                return !is_array($element) && '' !== trim($element);
            });

            $shippingRules->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'shipping_rule.create')){
                return $can;
            }

            $request['admin_id'] = $request->user()->id;
            $shippingRules = ShippingRule::create($request->all());
        }

        $data = ['add' => [], 'delete' => []];
        foreach ($request->shipping_places as  $value){

            if(!key_exists('id', $value) || (key_exists('id', $value) && '' === trim($value['id']))){
                array_push($data['add'],
                    [
                        "country"=> $value['country'],
                        "day_needed"=> $value['day_needed'],
                        "pickup_point"=> $value['pickup_point'],
                        "pickup_price"=> $value['pickup_price'],
                        "price"=> $value['price'],
                        "shipping_rule_id"=> $shippingRules->id,
                        "state"=> $value['state'],
                        'admin_id' => $request->user()->id
                    ]
                );
            }else if((key_exists('id', $value) && '' ==! trim($value['id'])) &&
                key_exists('deleted', $value) && $value['deleted']){


                $orderedProduct = OrderedProduct::where('shipping_place_id', $value['id'])->first();
                if($orderedProduct){
                    return response()->json(Validation::error($request->token,
                        __('api.place_delete')
                    ));
                }


                array_push($data['delete'], $value['id']);
            }else if(key_exists('id', $value)){
                ShippingPlace::where('id', $value['id'])
                    ->update([
                        "country"=> $value['country'],
                        "day_needed"=> $value['day_needed'],
                        "pickup_point"=> $value['pickup_point'],
                        "pickup_price"=> $value['pickup_price'],
                        "price"=> $value['price'],
                        "state"=> $value['state'],
                    ]);
            }
        }

        try{
            ShippingPlace::insert($data['add']);
            ShippingPlace::whereIn('id', $data['delete'])->delete();
        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }

        $attr = ShippingRule::with('shipping_places')->find($shippingRules->id);

        return response()->json(new Response($request->token, $attr));
    }

    public function delete(Request $request, $id)
    {

        try{

            if($can = Utils::userCan($this->user, 'shipping_rule.delete')){
                return $can;
            }
            $shippingRules = ShippingRule::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $shippingRules)) {
                return $isOwner;
            }

            if (is_null($shippingRules)){
                return response()->json(Validation::nothing_found());
            }

            $product = Product::where('shipping_rule_id', $id)->get()->first();

            if($product){
                return response()->json(Validation::error($request->token,
                    __('api.shipping_delete')
                ));
            }

            ShippingPlace::where('shipping_rule_id', $id)->delete();

            if ($shippingRules->delete()){
                return response()->json(new Response($request->token, $shippingRules));
            }

            return response()->json(Validation::error($request->token));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }




    }
}
