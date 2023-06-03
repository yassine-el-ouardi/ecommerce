<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VouchersController extends ControllerHelper
{
    public function validity(Request $request)
    {
        $validate = Validation::voucherValidity($request);
        if($validate){
            return response()->json($validate);
        }

        $voucher = Voucher::where('code', $request->voucher)
            ->where('status', Config::get('constants.status.PUBLIC'))
            ->get()
            ->first();

        if (is_null($voucher)){
            return response()->json(Validation::noData());
        }

        $user = Auth::user();
        $existingCart = Cart::with('product_inner')
            ->with('shipping_place')
            ->where('selected', Config::get('constants.status.PUBLIC'))
            ->with('updated_inventory')
            ->where('user_id', $user->id)
            ->get();

        $totalPriceWithoutShipping = 0;
        foreach ($existingCart as $key => $cart) {
            if ($cart->shipping_place_id && !is_null($cart->product_inner)) {
                // Selling price calculation
                $inventoryPrice = (float) $cart->updated_inventory->price;
                $selling = (float)$cart->product_inner->selling;
                $offered = (float)$cart->product_inner->offered;
                $flashPrice = 0;
                if(!is_null($cart->product_inner->end_time)){
                    $flashPrice = (float)$cart->product_inner->price;
                }
                if($inventoryPrice > 0){
                    $currentPrice = $inventoryPrice;
                } else if($flashPrice > 0){
                    $currentPrice = $flashPrice;
                }else if($flashPrice > 0){
                    $currentPrice = $offered;
                }else {
                    $currentPrice = $selling;
                }
                // Bundle calculation
                $bundleQtyOffer = 0;
                $bundleDeal = $cart->product_inner->bundle_deal;
                if($bundleDeal){
                    if($cart->quantity >= $bundleDeal->buy){
                        $bundleQtyOffer = $bundleDeal->free;
                    }
                }
                $totalPriceWithoutShipping +=
                    (float)$currentPrice * ((int)$cart->quantity - $bundleQtyOffer);
            }
        }

        if ($totalPriceWithoutShipping < $voucher->min_spend){
            $setting = Setting::select('currency', 'currency_icon')->first();
            return response()->json(Validation::error(null,
                __('api.least_spend', ['amount'=> $setting->currency_icon . $voucher->min_spend])
            ));
        }



        $totalOrdered = Order::where('voucher_id', $voucher->id)->count();



        if ($totalOrdered >= $voucher->usage_limit){
            return response()->json(Validation::error(null,
                __('api.limit_exceeded')
            ));
        }

        $totalOrderedByUser = Order::where('voucher_id', $voucher->id)
            ->where('user_id', Auth::user()->id)
            ->count();



        if ($totalOrderedByUser >= $voucher->limit_per_customer){
            return response()->json(Validation::error(null,
                __('api.maximum_time')
            ));
        }

        $start = new Carbon($voucher->start_time);
        $end = new Carbon($voucher->end_time);
        $now = Carbon::now();


        if ($start < $now && $now < $end){
            if((int)$voucher->type === (int)Config::get('constants.priceType.FLAT')){
                $offered = $voucher->price;
            } else {
                $offered = number_format(
                    (float)($voucher->price * $totalPriceWithoutShipping) / 100,
                    2, '.', ''
                );
            }
            if(!is_null($voucher->capped_price) && $offered > $voucher->capped_price){
                $offered = (int) $voucher->capped_price;
            }
            return response()->json(new Response($request->token,
                ['offered' => $offered, 'voucher' => $request->voucher]));
        }

        return response()->json(Validation::error(null,
            __('api.voucher_expired')
        ));
    }

    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'voucher.view')){
            return $can;
        }

        $query = Voucher::orderBy($request->orderby, $request->type);
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
                $item['start_time'] =Utils::convertTimeToUSERzone($item->start_time, $request->time_zone);
                $item['end_time'] =Utils::convertTimeToUSERzone($item->end_time, $request->time_zone);
            }
        }else{
            foreach ($data as $item){
                $item['created'] = Utils::formatDate($item->created_at);
                $item['start_time'] = $item->start_time;
                $item['end_time'] = $item->end_time;
            }
        }

        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'voucher.view')){
            return $can;
        }

        $data = Voucher::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $data)) {
            return $isOwner;
        }

        if (is_null($data)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, Voucher $voucher)
    {
        $validate = Validation::voucherRules($request);
        if($validate){
            return response()->json($validate);
        }


        $endTime = date('Y-m-d H:i:s', strtotime($request->end_time));
        $startTime = date('Y-m-d H:i:s', strtotime($request->start_time));

        if($endTime <= $startTime){
            return response()->json(Validation::error(null,
                __('api.time_greater')
            ));
        }


        if($request->time_zone){
            if($request->end_time){
                $request['end_time'] = Utils::convertTimeToUTCzone($request->end_time, $request->time_zone);
            }
            if($request->start_time){
                $request['start_time'] = Utils::convertTimeToUTCzone($request->start_time, $request->time_zone);
            }
        }
        if($voucher->id){
            if($can = Utils::userCan($this->user, 'voucher.edit')){
                return $can;
            }

            $existing = Voucher::find($voucher->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $voucher->update(array_filter($filtered));
        }else{

            if($can = Utils::userCan($this->user, 'voucher.create')){
                return $can;
            }

            $voucherFromDb = Voucher::where('code', $request->code)
                ->get()
                ->first();

            if (!is_null($voucherFromDb)){
                return response()->json(Validation::error(null,
                    __('api.voucher_exists')
                ));
            }


            $request['admin_id'] = $request->user()->id;
            $voucher = Voucher::create($request->all());
        }

        if($request->time_zone){
            $voucher['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($voucher->created_at, $request->time_zone));
            $voucher['start_time'] =Utils::convertTimeToUSERzone($voucher->start_time, $request->time_zone);
            $voucher['end_time'] =Utils::convertTimeToUSERzone($voucher->end_time, $request->time_zone);

        }else{
            $voucher['created'] = Utils::formatDate($voucher->created_at);
            $voucher['start_time'] = $voucher->start_time;
            $voucher['end_time'] = $voucher->end_time;
        }
        return response()->json(new Response($request->token, $voucher));
    }

    public function delete(Request $request, $id)
    {
        try{

            if($can = Utils::userCan($this->user, 'voucher.delete')){
                return $can;
            }
            $voucher = Voucher::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $voucher)) {
                return $isOwner;
            }

            if (is_null($voucher)){
                return response()->json(Validation::noData());
            }


            $order = Order::where('voucher_id', $id)->get()->first();

            if($order){
                return response()->json(Validation::error($request->token,
                    __('api.used_in_order')
                ));
            }


            if ($voucher->delete()){
                return response()->json(new Response($request->token, $voucher));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }



    }
}
