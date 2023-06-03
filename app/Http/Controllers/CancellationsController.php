<?php

namespace App\Http\Controllers;

use App\Models\Cancellation;
use App\Models\Helper\Response;
use App\Models\Helper\Validation;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CancellationsController extends Controller
{

    public function refund(Request $request, $id)
    {
        $cancellation = Cancellation::find($id);

        if (is_null($cancellation)){
            return response()->json(Validation::nothing_found(201));
        }

        $order = Order::find($cancellation->order_id);

        if($order->payment_done != Config::get('constants.status.PUBLIC')){
            return response()->json(Validation::error($request->token, 'This is an unpaid order.'));
        }

        if($order->order_method == Config::get('constants.paymentMethod.CASH_ON_DELIVERY')){
            return response()->json(Validation::error($request->token, 'Unable to refund for cash on delivery.'));
        }

        $hasNotRefundableProduct = OrderedProduct::join('products as p', function($join) use ($cancellation) {
                $join->on('p.id', '=', 'ordered_products.product_id');
                $join->where('ordered_products.order_id', $cancellation->order_id);
                $join->where('p.refundable', '!=', Config::get('constants.status.PUBLIC'));
            })
            ->first();

        if(!is_null($hasNotRefundableProduct)){
            return response()->json(Validation::error($request->token,
                'One of the product of this order is not refundable.'));
        }

        Cancellation::where('id', $id)->update(['refunded' => true]);

        $cancellation['refunded'] = 1;

        return Validation::success(
            $request,
            'Order refunded successfully',
            $cancellation
        );
    }

    public function find(Request $request, $orderId)
    {
        $cancellation = Cancellation::where('order_id', $orderId)->get()->first();

        if (is_null($cancellation))
            return response()->json(Validation::nothing_found(201));

        return response()->json(new Response($request->token, $cancellation));
    }

    public function findCancellation(Request $request, $orderId)
    {
        $cancellation = Cancellation::where('order_id', $orderId)
            ->where('user_id', Auth::user()->id)->get()->first();

        if (is_null($cancellation))
            return response()->json(Validation::nothing_found(201));

        return response()->json(new Response($request->token, $cancellation));
    }

    public function cancelOrder(Request $request)
    {
        $validate = Validation::cancelled($request);
        if($validate) {
            return response()->json($validate);
        }
        $userId = Auth::user()->id;

        $order = Order::where('id', $request->order_id)
            ->where('user_id', $userId)
            ->get()
            ->first();

        if (is_null($order)) {
            return response()->json(Validation::nothing_found());
        }

        if($order->status != Config::get('constants.orderStatus.PENDING')){
            return response()->json(Validation::error($request->token,
                __('api.cancel_order')));
        }

        $updated = Order::where('id', $request->order_id)->update(['cancelled' => true]);

        if($updated){
            $existing = Cancellation::where('order_id', $request->order_id)
                ->where('user_id',$userId)->get()->first();

            if(is_null($existing)){
                $request['user_id'] = $userId;
                Cancellation::create($request->all());
            }else{
                Cancellation::where('id', $existing->id)->update([
                    'title' => $request->title,
                    'message' => $request->message,
                ]);
            }

            $data = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=','rating_reviews.id')
                ->where('rating_reviews.order_id', $request->order_id);

            $data->delete();

            RatingReview::where('order_id', $request->order_id)->delete();

        }

        return Validation::success(
            $request,
            'Order cancelled successfully',
            ['result' => ['cancelled' => true, 'order_id' => $request->order_id]]
        );
    }
}
