<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Order;
use App\Models\Product;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

class RatingReviewsController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'rating_review.view')){
            return $can;
        }

        if($this->isVendor) {
            $query = RatingReview::join('products as p', function($join) {
                $join->on('p.id', '=', 'rating_reviews.product_id');
                $join->where('p.admin_id', $this->user->id);;
            });
            $query = $query->where('admin_id', $this->user->id);
        } else {
            $query = RatingReview::query();
        }

        $query = $query->with('product');
        $query = $query->with('user');
        $query = $query->with('review_images');
        $query = $query->orderBy('rating_reviews.'.$request->orderby, $request->type);

        if ($request->q) {
            $query = $query->where('review', 'LIKE', "%{$request->q}%");
        }

        if ($request->product) {
            $query = $query->where('product_id', $request->product);
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


    public function delete(Request $request, $id)
    {
        try {
            if($can = Utils::userCan($this->user, 'rating_review.delete')){
                return $can;
            }

            $ratingReview = RatingReview::with('product_admin')->find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $ratingReview->product_admin)) {
                return $isOwner;
            }

            if (is_null($ratingReview)){
                return response()->json(Validation::noData());
            }

            $reviewImages = ReviewImage::where('rating_review_id', $id)->get();

            foreach ($reviewImages as $i){
                ReviewImage::where('id', $i->id)->delete();
                FileHelper::deleteFile($i->image);
            }

            if ($ratingReview->where('id', $id)->delete()) {
                $product = Product::find($ratingReview->product_id);
                $total = $product->rating * $product->review_count;

                $avg = ($total - $ratingReview->rating) / ($product->review_count - 1);

                Product::where('id', $ratingReview->product_id)
                    ->update([
                        'rating' => $avg,
                        'review_count' => ($product->review_count - 1)
                    ]);
                return response()->json(new Response($request->token, $ratingReview));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }

    public function find(Request $request, $productId)
    {
        $ratingReview = RatingReview::with('review_images')
            ->where('product_id', $productId)
            ->where('user_id', Auth::user()->id)
            ->get()
            ->first();

        if (is_null($ratingReview)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $ratingReview));
    }


    public function action(Request $request, RatingReview $ratingReview)
    {
        $validate = Validation::ratingReview($request);
        if($validate){
            return response()->json($validate);
        }

        $ratingReviewId = 0;

        $order = Order::find($request->order_id);

        if((int)$order->status != Config::get('constants.orderStatus.DELIVERED')){
            return response()
                ->json(Validation::error($request->token,
                    __('api.until_delivered')
                ));
        }

        if((boolean)$order->cancelled){
            return response()
                ->json(Validation::error($request->token,
                    __('api.order_cancelled')
                ));
        }

        $product = Product::find($request->product_id);
        if (is_null($product)){
            return response()->json(Validation::nothing_found(201));
        }

        if($request->id){

            $existingRatingReview = RatingReview::find($request->id);

            RatingReview::where('id', $request->id)->update([
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'review' => $request->review
            ]);

            if($request->deleted_images){
                foreach (json_decode($request->deleted_images) as $i){
                    ReviewImage::where(['id' => $i->id])->delete();
                    FileHelper::deleteFile($i->image);
                }
            }

            $reviewCount = $product->review_count;
            $avgRating =
                (($product->rating * $product->review_count) + $request->rating - $existingRatingReview->rating) / $reviewCount;

            $ratingReviewId = $request->id;

        }else{

            $ratingReview = RatingReview::create([
                'user_id' => Auth::user()->id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'order_id' => $request->order_id,
                'review' => $request->review,
            ]);

            $reviewCount = $product->review_count + 1;
            $avgRating = (($product->rating * $product->review_count) + $request->rating) / $reviewCount;

            $ratingReviewId = $ratingReview->id;
        }

        $reviewImages = [];
        $hasError = [];

        if($request->images){
            foreach ($request->images as  $img){
                $imgValidate = Validator::make(['photo' => $img], Validation::imageRules());
                if($imgValidate->fails()){
                    array_push($hasError, $imgValidate->errors()->messages());
                }else{
                    $image_info = FileHelper::uploadImage($img, 'review');
                    $rImage['rating_review_id'] = $ratingReviewId;
                    $rImage['image'] = $image_info['name'];
                    $rImage['created_at'] = Carbon::now();
                    $rImage['updated_at'] = Carbon::now();
                    array_push($reviewImages, $rImage);
                }
            }
        }

        Product::where('id', $product->id)->update([
            'review_count' => $reviewCount,
            'rating' => round($avgRating,2)
        ]);

        if(count($reviewImages) > 0){
            ReviewImage::insert($reviewImages);
            $ratingReview['review_images'] = $reviewImages;
        }

        if(count($hasError) > 0){
            return response()->json(new Response($request->token, $ratingReview, 200,
                __('api.error_uploading')
            ));
        }

        return response()->json(new Response($request->token, $ratingReview, 200,
            __('api.thank_feedback')
        ));
    }
}
