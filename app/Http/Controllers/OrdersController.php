<?php

namespace App\Http\Controllers;

use App\Models\AttributeValue;
use App\Models\Cart;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\MailHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SiteSetting;
use App\Models\UpdatedInventory;
use App\Models\Voucher;
use Carbon\Carbon;
use Flutterwave\Service\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use PDF;
use Mail;

class OrdersController extends ControllerHelper
{
    public function vendorAll(Request $request)
    {
        if(!$this->isVendor){
            return Utils::isDataOwner(null, null);
        }

        if($can = Utils::userCan($this->user, 'order.view')){
            return $can;
        }

        $query = OrderedProduct::join('products as p', function($join) {
            $join->on('p.id', '=', 'ordered_products.product_id');
            $join->where('p.admin_id', '=', $this->user->id);
        });

        $query = $query->with('product');
        $query = $query->with('updated_inventory.inventory_attributes.attribute_value.attribute');
        $query = $query->with('shipping_place');

        $query = $query->select('ordered_products.*');
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        if ($request->time_zone) {
            foreach ($data as $item) {
                $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
            }
        } else {
            foreach ($data as $item) {
                $item['created'] = Utils::formatDate($item->created_at);
            }
        }

        return response()->json(new Response($request->token, $data));
    }



    public function all(Request $request)
    {
        if($this->isVendor){
            return Utils::isDataOwner(null, null);
        }

        if($can = Utils::userCan($this->user, 'order.view')){
            return $can;
        }

        $query = Order::query();

        $query = $query->with('user_info');
        $query = $query->orderBy('orders.'.$request->orderby, $request->type);

        if ($request->filter) {

            foreach (explode(',', $request->filter) as $i){
                if($i == 'cancelled'){
                    $query = $query->orWhere('cancelled', 1);
                }
                if($i == 'paid'){
                    $query = $query->orWhere('payment_done', 1);
                }
                if($i == 'unpaid'){
                    $query = $query->orWhere('payment_done', 0);
                }
                if($i == 'card_payment'){
                    $query = $query
                        ->orWhere('order_method', Config::get('constants.paymentMethod.RAZORPAY'))
                        ->orWhere('order_method', Config::get('constants.paymentMethod.STRIPE'));
                }
                if($i == 'paypal'){
                    $query = $query
                        ->orWhere('order_method', Config::get('constants.paymentMethod.PAYPAL'));
                }
                if($i == 'cash_on_delivery'){
                    $query = $query->orWhere('order_method',
                        Config::get('constants.paymentMethod.CASH_ON_DELIVERY'));
                }
            }
        }

        $query = $query->select('orders.*');
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        if ($request->time_zone) {
            foreach ($data as $item) {
                $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
            }
        } else {
            foreach ($data as $item) {
                $item['created'] = Utils::formatDate($item->created_at);
            }
        }

        return response()->json(new Response($request->token, $data));
    }


    public function find(Request $request, $id)
    {
        if($this->isVendor){
            return Utils::isDataOwner(null, null);
        }

        if($can = Utils::userCan($this->user, 'order.view')){
            return $can;
        }

        $order = Order::with('ordered_products.product')
            ->with('cancellation')
            ->with('voucher')
            ->with('ordered_products.updated_inventory.inventory_attributes.attribute_value.attribute')
            ->with('address')
            ->with('user')
            ->with('ordered_products.shipping_place')->find($id);

        if (is_null($order)){
            return response()->json(Validation::nothing_found());
        }

        $order['calculated'] = Utils::calcPrice($order);

        if ($request->time_zone) {
            $order['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($order->created_at, $request->time_zone));
        } else {
            $item['created'] = Utils::formatDate($order->created_at);
        }

        return response()->json(new Response($request->token, $order));
    }


    public function updateStatus(Request $request)
    {
        if($this->isVendor){
            return Utils::isDataOwner(null, null);
        }

        if($can = Utils::userCan($this->user, 'order.edit')){
            return $can;
        }

        $validate = Validation::orderStatus($request);
        if ($validate) {
            return response()->json($validate);
        }

        $order = Order::find($request->id);

        if (is_null($order)) {
            return response()->json(Validation::nothing_found());
        }

        $updatedStatus['status'] = $request->status;

        if((int)Config::get('constants.orderStatus.DELIVERED') == (int)$request->status &&
            (int)Config::get('constants.paymentMethod.CASH_ON_DELIVERY') == (int)$order->order_method){
            $updatedStatus['payment_done'] = Config::get('constants.status.PUBLIC');
        }

        Order::where('id', $request->id)->update($updatedStatus);

        return response()->json(new Response($request->token, ['result' =>
            [
                'status' => $request->status,
                'payment_done' => Config::get('constants.status.PUBLIC'),
                'id' => $request->id
            ]]));
    }


    public function delete(Request $request, $id)
    {

        try {
            if($this->isVendor){
                return Utils::isDataOwner(null, null);
            }

            $order = Order::find($id);

            if (is_null($order)) {
                return response()->json(Validation::nothing_found());
            }

        OrderedProduct::where('order_id', $id)->delete();

            if (Order::where('id', $id)->delete()) {
                return response()->json(new Response($request->token, $order));
            }
            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }
    }


    public function byUser(Request $request)
    {
        $userId = Auth::user()->id;
        if ($request->order_id) {

            $order = Order::with('ordered_products.product.bundle_deal')
                ->with('ordered_products.updated_inventory.inventory_attributes.attribute_value.attribute')
                ->with('address')
                ->with('user_info')
                ->with('voucher')
                ->with('cancellation')
                ->with('ordered_products.shipping_place')
                ->find($request->order_id);

            if (is_null($order)) {
                return response()->json(Validation::error($request->token,
                    __('api.no_order')
                ));
            }

            if ((int)$order->user_id !== $userId) {
                return response()->json(Validation::error($request->token,
                    __('api.not_order')
                ));
            }


            $order['user'] = $order->user_info;
            unset($order->user_info);
            $order['calculated'] = Utils::calcPrice($order);
            $order['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($order->created_at, $request->time_zone));

            return response()->json(new Response($request->token, $order));

        } else {

            $query = Order::with('ordered_products.product');
            $query = $query->orderBy('created_at', 'DESC');

            if ($request->cancelled) {
                $query = $query->where('cancelled', $request->cancelled);
            }

            if ($request->paid) {
                $query = $query->where('payment_done', 1);

                if ($request->unpaid) {
                    $query = $query->orWhere('payment_done', 0);
                }
            } else if ($request->unpaid) {
                $query = $query->where('payment_done', 0);
            }

            if ($request->card_payment) {
                $query = $query
                    ->where('order_method', Config::get('constants.paymentMethod.RAZORPAY'))
                    ->orWhere('order_method', Config::get('constants.paymentMethod.STRIPE'));

                if ($request->cash_on_delivery) {
                    $query = $query->orWhere('order_method', Config::get('constants.paymentMethod.CASH_ON_DELIVERY'));
                }

            } else if ($request->cash_on_delivery) {
                $query = $query->where('order_method', Config::get('constants.paymentMethod.CASH_ON_DELIVERY'));
            }
            $query = $query->where('user_id', $userId);
            $data = $query->paginate(Config::get('constants.frontend.PAGINATION'));

            if ($request->time_zone) {
                foreach ($data as $item) {

                    $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
                }
            } else {
                foreach ($data as $item) {

                    $item['created'] = Utils::formatDate($item->created_at);
                }
            }
            return response()->json(new Response($request->token, $data));
        }
    }

    public function paymentDone(Request $request)
    {
        try {
            $params = json_decode(Utils::jsDecryption($request->data));

            $request->request->add(['id' => $params->id]);
            $request->request->add(['payment_token' => $params->payment_token]);
            $request->request->add(['order_method' => $params->order_method]);

            $validate = Validation::orderStatus($request);
            if ($validate){
                return response()->json($validate);
            }

            $user = Auth::user();

            $order = Order::with('voucher')->where('id', $request->id)
                ->first();

            if (is_null($order)) {
                return response()->json(Validation::error($request->token,
                    __('api.invalid_order')
                ));
            }

            if ($order->user_id != $user->id) {
                return response()->json(Validation::error($request->token,
                    __('api.invalid_user')
                ));
            }

            $payment = Payment::first();

            if ((int)$payment->cash_on_delivery != 1 &&
                ((int)$request->order_method == Config::get('constants.paymentMethod.CASH_ON_DELIVERY'))) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_cod')
                ));

            } else if ((int)$payment->paypal != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.PAYPAL')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_paypal')
                ));

            } else if ((int)$payment->stripe != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.STRIPE')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_gateway')
                ));

            } else if ((int)$payment->razorpay != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.RAZORPAY')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_gateway')
                ));

            } else if ((int)$payment->flutterwave != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.FLUTTERWAVE')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_gateway')
                ));
            }
            $paymentDone = false;

            if ((int)$request->order_method == Config::get('constants.paymentMethod.FLUTTERWAVE')) {

                try {

                    $con = \Flutterwave\Helper\Config::setUp(
                        $payment->fw_secret_key,
                        $payment->fw_public_key,
                        $payment->fw_encryption_key,
                        $payment->fw_environment
                    );
                    $transactions = new \Flutterwave\Service\Transactions($con);
                    $response = $transactions->verify($request->payment_token);

                    if ($response->status === "success") {

                        $paymentDone = true;

                    } else {

                        return response()->json(Validation::error($request->token,
                            __('api.flutterwave_error')
                        ));
                    }

                }catch (\Exception $e) {

                    if(str_contains($e->getMessage(), 'The stream or file')) {
                        $paymentDone = true;
                    } else {
                        return response()->json(Validation::error($request->token,
                            $e->getMessage()
                        ));
                    }
                }
            }
            else if ((int)$request->order_method == Config::get('constants.paymentMethod.PAYPAL')) {
                $paymentDone = true;

            } else if ((int)$request->order_method == Config::get('constants.paymentMethod.RAZORPAY')) {
                if ($order->payment_token != $request->payment_token) {
                    return response()->json(Validation::error($request->token,
                        __('api.invalid_token')
                    ));
                }
                $paymentDone = true;

            } else if ((int)$request->order_method == Config::get('constants.paymentMethod.STRIPE')) {

                // Calculating price
                $orderedProduct = OrderedProduct::with('product.bundle_deal')
                    ->where('order_id', $request->id)->get();
                $voucherPrice = 0;
                $shippingPrice = 0;
                $subtotal = 0;
                foreach ($orderedProduct as $item){
                    // Bundle calculation
                    $bundleQtyOffer = 0;
                    $bundleDeal = $item->product->bundle_deal;
                    if($bundleDeal){
                        $bundleQtyOffer = $bundleDeal->free;
                    }
                    $shippingPrice += $item->shipping_price;

                    $subtotal += ($item->selling * ($item->quantity - $bundleQtyOffer))
                        + ($item->tax_price * (int)$item->quantity);
                }
                if($order->voucher){
                    if ((int)$order->voucher->type === (int)Config::get('constants.priceType.FLAT')) {
                        $voucherPrice = $order->voucher->price;
                    } else {
                        $voucherPrice = number_format((float)($order->voucher->price * $subtotal) / 100, 2, '.', '');
                    }
                    if (!is_null($order->voucher->capped_price) && $voucherPrice > $order->voucher->capped_price) {
                        $voucherPrice = (int)$order->voucher->capped_price;
                    }
                }
                $totalPrice = $subtotal - $voucherPrice + $shippingPrice;

                $sSecret = $payment->stripe_secret;
                $setting = Setting::select('currency')->first();

                \Stripe\Stripe::setApiKey($sSecret);
                \Stripe\Charge::create([
                    'amount' => $totalPrice * 100,
                    'currency' => $setting->currency,
                    'source' => $request->payment_token,
                    'description' => 'order_id_' . $order->id,
                    'receipt_email' => $user->email,
                    'metadata' => [
                        'order_id' => $order->id,
                    ]
                ]);

                $paymentDone = true;
            }
            $result = Order::where('id', $request->id)->update([
                'payment_done' => $paymentDone,
                'order_method' => $request->order_method
            ]);
        } catch (\Exception $e) {
            return response()->json(Validation::error($request->token, $e->getMessage()));
        }
        return response()->json(new Response($request->token, $result));
    }


    public function action(Request $request)
    {
        try {
            $params = json_decode(Utils::jsDecryption($request->data));

            $request->request->add(['order_method' => $params->order_method]);
            $request->request->add(['voucher' => $params->voucher]);
            $request->request->add(['time_zone' => $params->time_zone]);

            $payment = Payment::first();

            if ((int)$payment->cash_on_delivery != 1 &&
                ((int)$request->order_method == Config::get('constants.paymentMethod.CASH_ON_DELIVERY'))) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_cod')
                ));

            } else if ((int)$payment->paypal != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.PAYPAL')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_paypal')
                ));

            } else if ((int)$payment->stripe != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.STRIPE')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_gateway')
                ));

            } else if ((int)$payment->razorpay != 1 &&
                (int)$request->order_method == Config::get('constants.paymentMethod.RAZORPAY')
            ) {
                return response()->json(Validation::error($request->token,
                    __('api.accepted_gateway')
                ));
            }


            $validate = Validation::order($request);
            if ($validate){
                return response()->json($validate);
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
                    }else if($offered > 0){
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
                    $totalPriceWithoutShipping += (float)$currentPrice * ((int)$cart->quantity - $bundleQtyOffer);
                }
            }

            $offeredVoucher = 0;
            $voucher = null;

            if ($request->voucher) {
                $voucher = Voucher::where('code', $request->voucher)
                    ->where('status', Config::get('constants.status.PUBLIC'))
                    ->get()->first();

                if (is_null($voucher)){
                    return response()->json(Validation::error($request->token,
                    __('api.invalid_voucher')
                    ));
                }

                if ($totalPriceWithoutShipping < $voucher->min_spend) {
                    $setting = Setting::select('currency', 'currency_icon', 'currency_position')->first();
                    $price =  $voucher->min_spend . $setting->currency_icon;

                    if((int)$setting->currency_position == Config::get('constants.currencyPosition.PRE')){
                        $price = $setting->currency_icon . $voucher->min_spend;
                    }

                    return response()->json(Validation::error($request->token,
                        __('api.min_spent', ['amount' => $price])
                    ));
                }

                $totalOrdered = Order::where('voucher_id', $voucher->id)->count();

                if ($totalOrdered >= $voucher->usage_limit) {
                    return response()->json(Validation::error($request->token,
                        __('api.voucher_exceeded')
                    ));
                }

                $totalOrderedByUser = Order::where('voucher_id', $voucher->id)
                    ->where('user_id', Auth::user()->id)
                    ->count();

                if ($totalOrderedByUser >= $voucher->limit_per_customer) {
                    return response()->json(Validation::error($request->token,
                        __('api.voucher_max')
                    ));
                }

                $start = new Carbon($voucher->start_time);
                $end = new Carbon($voucher->end_time);
                $now = Carbon::now();

                if ($start >= $now && $now >= $end) {
                    return response()->json(Validation::error($request->token,
                        __('api.voucher_expired')
                    ));
                }

                if ((int)$voucher->type === (int)Config::get('constants.priceType.FLAT')) {
                    $offeredVoucher = $voucher->price;
                } else {
                    $offeredVoucher = number_format((float)($voucher->price * $totalPriceWithoutShipping) / 100, 2, '.', '');
                }
                if (!is_null($voucher->capped_price) && $offeredVoucher > $voucher->capped_price) {
                    $offeredVoucher = (int)$voucher->capped_price;
                }
            }
            $cartError = [];

            foreach ($existingCart as $c){
                $productErr = [];
                $error = false;

                if($c->product->status != Config::get('constants.status.PUBLIC')){
                    array_push($productErr,
                        __('api.private_product', ['product' => $c->product->title])
                    );
                    $error = true;
                }
                if((int)$c->updated_inventory->quantity < 1){
                    array_push($productErr,
                        __('api.out_stock_product', ['product' => $c->product->title])
                    );
                    $error = true;
                }
                if($error){
                    $cartError[$c->id]=$productErr;
                }
            }

            if(count($cartError) > 0){
                return response()->json(Validation::error($request->token, $cartError, 'product'));
            }

            $setting = Setting::select('currency')->first();

            if(!$voucher){
                $voucher['id'] = null;
            }

            if (count($existingCart) > 0) {
                $now = Carbon::now();
                $order = Order::create([
                    'order_method' => $request->order_method,
                    'user_id' => $user->id,
                    'user_address_id' => $user->default_address,
                    'voucher_id' => $voucher['id'],
                    'currency' => $setting->currency,
                    'updated_at' => $now,
                    'created_at' => $now,
                    'order' => Utils::generateTrackingId([
                        "user_id" => $user->id
                    ]),
                ]);

                $orderedProducts = [];
                $totalPrice = 0;

                $commission = 0;
                if($this->isVendor){
                    $commission = $this->user->commission;
                }

                foreach ($existingCart as $key => $cart) {
                    if ($cart->shipping_place_id && !is_null($cart->product_inner)) {
                        // Selling price calculation
                        $inventoryPrice = (float) $cart->updated_inventory->price;
                        $selling = (float)$cart->product_inner->selling;
                        $offered = (float)$cart->product_inner->offered;
                        $flashPrice = null;
                        if(!is_null($cart->product_inner->end_time)){
                            $flashPrice = (float)$cart->product_inner->price;
                        }
                        if($inventoryPrice > 0){
                            $currentPrice = $inventoryPrice;
                        } else if($flashPrice !== null){
                            $currentPrice = $flashPrice;
                        } else if($offered > 0){
                            $currentPrice = $offered;
                        } else {
                            $currentPrice = $selling;
                        }

                        // Selling price calculation
                        $shippingPrice = 0;
                        if ((int)$cart->shipping_type === Config::get('constants.shippingTypeIn.LOCATION')) {
                            $shippingPrice = $cart->shipping_place->price;
                        } else if ((int)$cart->shipping_type === Config::get('constants.shippingTypeIn.PICKUP')) {
                            $shippingPrice = $cart->shipping_place->pickup_price;
                        }

                        // Bundle calculation
                        $bundleQtyOffer = 0;
                        $bundleDeal = $cart->product_inner->bundle_deal;
                        if($bundleDeal){
                            if($cart->quantity >= $bundleDeal->buy){
                                $bundleQtyOffer = $bundleDeal->free;
                            }
                        }

                        // Tax calculation
                        $taxQtyOffer = 0;
                        $taxRule = $cart->product_inner->tax_rules;
                        if($taxRule){
                            if ((int)$taxRule->type === (int)Config::get('constants.priceType.FLAT')) {
                                $taxQtyOffer = $taxRule->price;
                            } else {
                                $taxQtyOffer = number_format(
                                    (float)($taxRule->price * $currentPrice) / 100,
                                    2, '.', '');
                            }
                        }

                        $totalTax = (float)($taxQtyOffer * (int)$cart->quantity);
                        $priceWithoutBundle = (float)($currentPrice * ((int)$cart->quantity - (int)$bundleQtyOffer));
                        $total = (float)($shippingPrice + $totalTax + $priceWithoutBundle);

                        $totalPrice += $total;

                        // Inserting ordered product
                        array_push($orderedProducts, [
                            'commission' => $commission,
                            'tax_price' => $taxQtyOffer,
                            'commission_amount' => ($currentPrice * $cart->quantity * $commission)/100,
                            'product_id' => $cart->product_inner->id,
                            'inventory_id' => $cart->inventory_id,
                            'quantity' => $cart->quantity,
                            'shipping_place_id' => $cart->shipping_place_id,
                            'shipping_type' => $cart->shipping_type,
                            'purchased' => $cart->product_inner->purchased,
                            'bundle_offer' => $bundleQtyOffer,
                            'shipping_price' => $shippingPrice,
                            'selling' => $currentPrice,
                            'order_id' => $order->id,
                            'updated_at' => $now,
                            'created_at' => $now
                        ]);

                        UpdatedInventory::where('id', $cart->inventory_id)->decrement('quantity', $cart->quantity);
                    }
                }

                $totalPrice = number_format($totalPrice, 2, '.', '');

                $result = OrderedProduct::insert($orderedProducts);

                if ($result) {
                    Cart::where('user_id', $user->id)
                       ->where('selected', Config::get('constants.status.PUBLIC'))->delete();

                    $re['currency'] = $setting->currency;
                    $re['amount'] = number_format((float)$totalPrice - $offeredVoucher, 2, '.', '');
                    $re['id'] = $order->id;
                    $re['name'] = $user->name;
                    $re['email'] = $user->email;
                    $re['order'] = $order->order;


                    if ((int)$request->order_method == Config::get('constants.paymentMethod.STRIPE')) {

                        $re['order_method'] = Config::get('constants.paymentMethod.STRIPE');

                        // Saving total amount in order to generate report for admin easily
                        Order::where('id', $order->id)->update([
                            'total_amount' => $totalPrice - $offeredVoucher,
                        ]);

                        return response()->json(new Response($request->token, $re));

                    } else if ((int)$request->order_method == Config::get('constants.paymentMethod.RAZORPAY')) {

                        try {
                            $api = new Api($payment->razorpay_key, $payment->razorpay_secret);
                            $razorpayOrder = $api->order->create([
                                'receipt' => 'order_id_' . $order->id,
                                'amount' => ($totalPrice - $offeredVoucher) * 100,
                                'currency' => $setting->currency
                            ]);
                            $re['payment_token'] = $razorpayOrder->id;
                            $re['order_method'] = Config::get('constants.paymentMethod.RAZORPAY');

                            // Saving total amount in order to generate report for admin easily
                            Order::where('id', $order->id)->update([
                                'payment_token' => $razorpayOrder->id,
                                'total_amount' => $totalPrice - $offeredVoucher,
                            ]);

                            return response()->json(new Response($request->token, $re));
                        } catch (\Exception $e) {

                            $ops = OrderedProduct::where('order_id', $order->id)->get();

                            foreach ($existingCart as $ops) {

                                OrderedProduct::where('id', $ops->id)->delete();

                                UpdatedInventory::where('id', $ops->inventory_id)->increment('quantity', $ops->quantity);

                            }

                            Order::where('id', $order->id)->delete();
                            return response()->json(Validation::error($request->token, $e->getMessage()));
                        }
                    } else if ((int)$request->order_method == Config::get('constants.paymentMethod.CASH_ON_DELIVERY')) {

                        // Saving total amount in order to generate report for admin easily
                        Order::where('id', $order->id)->update([
                            'total_amount' => $totalPrice - $offeredVoucher,
                        ]);
                        return response()->json(new Response($request->token, $order));
                    }else if ((int)$request->order_method == Config::get('constants.paymentMethod.PAYPAL')) {

                        // Saving total amount in order to generate report for admin easily
                        Order::where('id', $order->id)->update([
                            'total_amount' => $totalPrice - $offeredVoucher,
                        ]);
                        return response()->json(new Response($request->token, $re));

                    } else if ((int)$request->order_method == Config::get('constants.paymentMethod.FLUTTERWAVE')) {

                        // Saving total amount in order to generate report for admin easily
                        Order::where('id', $order->id)->update([
                            'total_amount' => $totalPrice - $offeredVoucher,
                        ]);
                        return response()->json(new Response($request->token, $re));
                    }
                }
                return response()->json(Validation::error($request->token,
                    __('api.went_wrong')
                    ));
            }
            return response()->json(Validation::error($request->token,
                __('api.no_cart')
            ));

        } catch (\Exception $e) {

            return response()->json(Validation::error($request->token, $e->getMessage()));
        }
    }


    public function sendOrderEmail(Request $request, $id)
    {
        try {
            $mailData = MailHelper::sendingOrderEmail($request, $id);
            if(is_null($mailData)){
                return response()->json(Validation::error($request->token,
                    __('api.invalid_order')
                ));
            }

            $setting = $mailData['setting'];
            $order = $mailData['order'];

            $pdf = PDF::loadView('mail_templates.order_pdf', $mailData)
                ->setPaper('a4', 'potrait')->setWarnings(false);

            Mail::send('mail_templates.order_placed', $mailData,
                function ($message) use ($setting, $pdf, $order) {
                    $message->to($order->user->email, $order->user->name)
                        ->subject(
                            __('api.confirmation', ['store'=> $setting->store_name])
                        )
                        ->attachData($pdf->output(), $order['order'] . ".pdf");

                });

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }

        return response()->json(new Response($request->token, true));

    }

    public function sendDeliveredEmail(Request $request, $id)
    {
        try {
            $mailData = MailHelper::sendingOrderEmail($request, $id);
            if(is_null($mailData)){
                return response()->json(Validation::error($request->token,
                    __('api.invalid_order')
                ));
            }

            if((int)Config::get('constants.orderStatus.DELIVERED') != (int)$mailData['order']['status']){
                return response()->json(new Response($request->token, true));
            }

            $order = $mailData['order'];

            $name = $order->user['name'] ? $order->user['name'] : '';
            $email = $order->user['email'] ? $order->user['email'] : null;

            if(is_null($email)){
                return response()->json(Validation::error($request->token,
                    __('api.no_user')
                ));
            }

            Mail::send('mail_templates.package_delivered', $mailData,
                function ($message) use ($order, $email, $name) {
                    $message->to($email, $name)
                        ->subject(__('api.package_delivered'));

                });

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, explode('.', $ex->getMessage())[0]));
        }

        return response()->json(new Response($request->token, true));
    }


    public function generatePDF($id)
    {
        if($this->isVendor){
            return Utils::isDataOwner(null, null);
        }

        if($can = Utils::userCan($this->user, 'order.view')){
            return $can;
        }

        if($can = Utils::userCan($this->user, 'order.edit')){
            return $can;
        }

        $key = hex2bin("0123456470abcdef0123456789abcdef");
        $iv =  hex2bin("abcdef1876343516abcdef9876543210");

        $encrypted = '3z8tIolfpCM8iqPnvDbv3w==';
        $decrypted = openssl_decrypt($encrypted, 'AES-128-CBC', $key, OPENSSL_ZERO_PADDING, $iv);

        $decrypted = trim($decrypted);

        dd($decrypted);


        $order = MailHelper::order($id);
        $objDemo = MailHelper::emailData('jj');
        $objDemo->logo_base64 = FileHelper::imageToBase64($objDemo->image);

        return view('mail_templates.package_delivered', ['order' => $order, 'setting' => $objDemo]);
        // return view('mail_templates.order_placed', ['order' => $order, 'setting' => $objDemo]);
        return view('mail_templates.order_pdf', ['order' => $order, 'setting' => $objDemo]);

        $pdf = PDF::loadView('mail_templates.order_pdf', ['order' => $order, 'setting' => $objDemo])
            ->setPaper('a4', 'potrait')->setWarnings(false);
        return $pdf->download('disney.pdf');
    }
}
