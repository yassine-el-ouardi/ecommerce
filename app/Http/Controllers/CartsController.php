<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Helper\Response;
use App\Models\Helper\Validation;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\UpdatedInventory;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CartsController extends Controller
{
    public function byUser(Request $request)
    {

        $userId = Auth::user()->id;

        $data = Cart::with('flash_product.shipping_rule.shipping_places')
            ->with('updated_inventory.inventory_attributes.attribute_value.attribute')
            ->with('shipping_place')
            ->where('user_id', $userId)
            ->select('id', 'product_id', 'user_id', 'inventory_id','quantity',
                'selected', 'shipping_place_id', 'shipping_type')
            ->get();

        return response()->json(new Response($request->token, $data));
    }


    public function buyNow(Request $request)
    {
        $validate = Validation::cart($request);
        if($validate){
            return response()->json($validate);
        }

        $existingCart = Cart::where('product_id', $request->product_id)
            ->where('user_id', $request->user_id)
            ->where('inventory_id', $request->inventory_id)
            ->get()->first();

        if($existingCart){
            $inventory = UpdatedInventory::find($request->inventory_id);

            if($request->quantity > $inventory->quantity){
                return response()->json(Validation::error($request->token,
                    __('api.quantity_exceeds')
                ));
            }
            Cart::where('id', $existingCart->id)->update([
                'quantity' => $request->quantity,
                'selected' => Config::get('constants.status.PUBLIC')
            ]);

            $existingCart->quantity = $request->quantity;
            $cart = $existingCart;

        }else {
            $cart = Cart::create($request->all());
        }

        Cart::where('selected', Config::get('constants.status.PUBLIC'))
            ->where('id', '!=' , $cart->id)
            ->update(['selected' => Config::get('constants.status.PRIVATE')]);

        return response()->json(new Response($request->token, $cart));
    }


    public function action(Request $request)
    {
        $validate = Validation::cart($request);
        if($validate) {
            return response()->json($validate);
        }

        $existingCart = Cart::where('product_id', $request->product_id)
            ->where('user_id', $request->user_id)
            ->where('inventory_id', $request->inventory_id)
            ->get()
            ->first();

        if($existingCart){
            $inventory = UpdatedInventory::find($request->inventory_id);

            if($existingCart->quantity + $request->quantity > $inventory->quantity){
                return response()->json(Validation::error($request->token,
                    __('api.quantity_exceeds')
                ));
            }
            Cart::where('id', $existingCart->id)->update([
                'quantity' => $existingCart->quantity + $request->quantity
            ]);

            $existingCart->quantity = $existingCart->quantity + $request->quantity;
            $cart = $existingCart;

        }else {
            $cart = Cart::create($request->all());
        }

        return response()->json(new Response($request->token, $cart));
    }


    public function updateShipping(Request $request)
    {
        $validate = Validation::shippingCart($request);
        if($validate)
            return response()->json($validate);

        $cartArrz = [];

        $cartIds = [];

        foreach ($request->cart as $i){
            array_push($cartIds, $i['cart']);
            $v['shipping_place_id'] = $i['shipping_place']['id'];
            $v['shipping_type'] = $i['shipping_type'];
            $v['cart'] = $i['cart'];
            array_push($cartArrz, $v);
        }

        $cartError = [];
        $carts = Cart::whereIn('id', $cartIds)
            ->where('selected', Config::get('constants.status.PUBLIC'))
            ->with('product')
            ->with('updated_inventory')
            ->get();

        foreach ($carts as $c){
            $productErr = [];
            $error = false;

            if($c->product->status != Config::get('constants.status.PUBLIC')){
                array_push($productErr, $c->product->title . __('api.uncheck_cart'));
                $error = true;
            }
            if((int)$c->updated_inventory->quantity < 1){
                array_push($productErr, $c->product->title . __('api.stock_out'));
                $error = true;
            }
            if($error){
                $cartError[$c->id]=$productErr;
            }
        }

        if(count($cartError) > 0){
            return response()->json(Validation::error($request->token, $cartError, 'product'));
        }


        User::where('id', Auth::user()->id)->update(['default_address' => $request->selected_address]);

        \DB::transaction(function () use ($cartArrz) {
            foreach ($cartArrz as $key => $value) {
                Cart::where('id', '=', $value['cart'])->update([
                    'shipping_place_id' => $value['shipping_place_id'],
                        'shipping_type' => $value['shipping_type']
                    ]
                );
            }
        });

        $data = Cart::with('flash_product.shipping_rule.shipping_places')
            ->with('updated_inventory.inventory_attributes.attribute_value.attribute')
            ->with('shipping_place')
            ->where('user_id', Auth::user()->id)
            ->select('id', 'product_id', 'user_id', 'inventory_id','quantity',
                'selected', 'shipping_place_id', 'shipping_type')
            ->get();


        return response()->json(new Response($request->token, $data));
    }


    public function changeSelected(Request $request){
        Cart::whereIn('id', $request->checked)
            ->update(['selected' => 1]);

        Cart::whereIn('id', $request->unchecked)
            ->update(['selected' => 2]);

        return response()->json(new Response('', true));
    }

    public function delete(Request $request, $id)
    {
        try {
            $cart = Cart::find($id);

            if (is_null($cart))
                return response()->json(Validation::nothing_found());


            if ($cart->delete()){
                return response()->json(new Response($request->token, $cart));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }

}
