<?php

namespace App\Http\Controllers;

use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Helper\ControllerHelper;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use Illuminate\Support\Facades\Config;

class FlashSalesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'flash_sale.view')){
            return $can;
        }

        $query = FlashSale::orderBy($request->orderby, $request->type);

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if ($request->q) {
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item) {
            if ($request->time_zone) {
                $item['created'] = Utils::convertTimeToUSERzone($item->created_at, $request->time_zone);
                $item['start_time'] = Utils::convertTimeToUSERzone($item->start_time, $request->time_zone);
                $item['end_time'] = Utils::convertTimeToUSERzone($item->end_time, $request->time_zone);
            }else {
                $item['created'] = Utils::formatDate($item->created_at);
                $item['start_time'] = Utils::formatDate($item->start_time);
                $item['end_time'] = Utils::formatDate($item->end_time);
            }
        }

        return response()->json(new Response($request->token, $data));
    }

    public function action(Request $request, FlashSale $flashSale)
    {
        $validate = Validation::flashSale($request);
        if ($validate) {
            return response()->json($validate);
        }

        $endTime = date('Y-m-d H:i:s', strtotime($request->end_time));
        $startTime = date('Y-m-d H:i:s', strtotime($request->start_time));

        if($endTime <= $startTime){
            return response()->json(Validation::error(null,
                __('api.end_time_must')));
        }

        if ($request->time_zone) {
            $request['start_time'] = Utils::convertTimeToUTCzone($request->start_time, $request->time_zone);
            $request['end_time'] = Utils::convertTimeToUTCzone($request->end_time, $request->time_zone);
        }


        $productIds = [];
        if($request['products']){
            foreach ($request->products as  $value){

                if(!key_exists('deleted', $value) ||
                    (key_exists('deleted', $value) && !$value['deleted']))
                {
                    array_push($productIds, $value['product']['id']);
                }
            }
        }


        if($flashSale->id){

            if($can = Utils::userCan($this->user, 'flash_sale.edit')){
                return $can;
            }

            $existing = FlashSale::find($flashSale->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }


            $flashProduct = FlashSaleProduct::with('product')
                ->where('flash_sale_id', '!=', $flashSale->id)
                ->whereIn('product_id', $productIds)
                ->first();


            if($flashProduct){
                return response()->json(Validation::error($request->token,
                    __('api.product_already', ['product'=> $flashProduct->product->title])
                ));
            }


            $filtered = array_filter($request->all(), function ($element) {
                return  !is_array($element) && '' !== trim($element);
            });

            $flashSale->update($filtered);

        }else{
            if($can = Utils::userCan($this->user, 'flash_sale.create')){
                return $can;
            }

            $flashProduct = FlashSaleProduct::with('product')
                ->whereIn('product_id', $productIds)
                ->first();

            if($flashProduct){
                return response()->json(Validation::error($request->token,
                    __('api.product_already', ['product'=> $flashProduct->product->title])
                ));
            }

            $request['admin_id'] = $request->user()->id;
            $flashSale = FlashSale::create($request->all());
        }

        if($request['products']){
            $data = ['add' => [], 'delete' => []];
            foreach ($request->products as  $value){

                if((
                        !key_exists('id', $value) ||
                        (key_exists('id', $value) && '' === trim($value['id']))
                    ) && !(key_exists('deleted', $value) && $value['deleted'])
                ) {
                    array_push($data['add'],
                        [
                            "product_id"=> $value['product']['id'],
                            "price"=> $value['price'],
                            "flash_sale_id"=> $flashSale->id,
                            'admin_id' => $request->user()->id
                        ]
                    );
                }else if((key_exists('id', $value) && '' ==! trim($value['id'])) &&
                    key_exists('deleted', $value) && $value['deleted']){
                    array_push($data['delete'], $value['id']);

                }else if(key_exists('id', $value) && key_exists('updated', $value) && $value['updated']){
                    FlashSaleProduct::where('id', $value['id'])
                        ->update([
                            "price"=> $value['price']
                        ]);
                }
            }

            try{
                FlashSaleProduct::insert($data['add']);
                FlashSaleProduct::whereIn('id', $data['delete'])->delete();
            } catch (\Exception $ex) {
                return response()->json(Validation::error($request->token, $ex->getMessage()));
            }
        }

        $flashSale = FlashSale::with('products.product')->find($flashSale->id);

        return response()->json(new Response($request->token, $flashSale));
    }

    public function findProducts(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'product.view')){
            return $can;
        }

        $flashSaleProducts = FlashSaleProduct::where(['flash_sale_id' => $id])->get();

        foreach ($flashSaleProducts as $item) {
            $product = Product::where(['id' => $item['product_id']])
                ->get(['title', 'selling', 'offered'])
                ->first();
            $item['title'] = $product['title'];
            $item['current_price'] = $product['offered'] ? $product['offered'] : $product['selling'];
        }

        return response()->json(new Response($request->token, $flashSaleProducts));
    }


    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'flash_sale.view')){
            return $can;
        }

        $flashSale = FlashSale::with('products.product')->find($id);

        if (is_null($flashSale)) {
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $flashSale));
    }



    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'flash_sale.delete')){
                return $can;
            }
            $flashSale = FlashSale::with('products')->find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $flashSale)) {
                return $isOwner;
            }

            if (is_null($flashSale)){
                return response()->json(Validation::noData());
            }

            if (count($flashSale['products']) > 0) {
                FlashSaleProduct::where(['flash_sale_id' => $id])->delete();
            }

            if ($flashSale->delete()) {
                return response()->json(new Response($request->token, $flashSale));
            }

            return response()->json(Validation::error($request->token));
        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
