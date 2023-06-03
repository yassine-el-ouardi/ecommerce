<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\BundleDeal;
use App\Models\Category;
use App\Models\CollectionWithProduct;
use App\Models\FlashSaleProduct;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Inventory;
use App\Models\InventoryAttribute;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\ProductCollection;
use App\Models\ProductImage;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use App\Models\ShippingRule;
use App\Models\SubCategory;
use App\Models\TaxRules;
use App\Models\UpdatedInventory;
use App\Models\UserWishlist;
use App\Models\WysiwygImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ProductsController extends ControllerHelper
{

    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'product.view')){
            return $can;
        }

        $query = Product::query()
            ->with('sub_category')
            ->with('brand')
            ->with('category')
            ->with('tax_rules')
            ->orderBy($request->orderby, $request->type);

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }

        $data = $query->select('id', 'title', 'image', 'unit', 'category_id',
            'subcategory_id', 'tax_rule_id', 'shipping_rule_id',
            'brand_id', 'purchased', 'selling', 'offered', 'status', 'created_at')
            ->paginate(Config::get('constants.api.PAGINATION'));

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

    public function dropDownData(Request $request)
    {
        $res['sub_categories'] = SubCategory::orderBy('created_at', 'ASC')->get(['id', 'title', 'category_id']);
        $res['brands'] = Brand::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['categories'] = Category::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['tax_rules'] = TaxRules::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['shipping_rules'] = ShippingRule::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['product_collections'] = ProductCollection::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['bundle_deals'] = BundleDeal::orderBy('created_at', 'ASC')->get(['id', 'title']);
        $res['attributes'] = Attribute::with('values')
            ->orderBy('created_at', 'ASC')->get(['id', 'title']);

        return response()->json(new Response($request->token, $res));
    }



    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'product.view')){
            return $can;
        }

        $product = Product::with('product_images')
            ->with('flash_sale_product.flash_sale')
            ->with('product_collections')
            ->find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product)) {
            return $isOwner;
        }

        if (is_null($product)){
            return response()->json(Validation::noData());
        }

        return response()->json(new Response($request->token, $product));
    }

    public function action(Request $request, Product $product)
    {
        try{

            $validate = Validation::productMain($request);
            if($validate){
                return response()->json($validate);
            }

            if($product->id){
                if($can = Utils::userCan($this->user, 'product.edit')){
                    return $can;
                }

                $existing = Product::find($product->id);
                if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                    return $isOwner;
                }

                $filtered = array_filter($request->all(), function ($element) {
                    return !is_array($element) && '' !== trim($element);
                });

                $product->update($filtered);

            }else{
                if($can = Utils::userCan($this->user, 'product.create')){
                    return $can;
                }

                $request['image'] = Config::get('constants.media.DEFAULT_IMAGE');
                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new Product);
                $product = Product::create($request->all());
            }

            //Product collection

            if(!is_null($request['product_collections'])){


                CollectionWithProduct::where('product_id', $product['id'])->delete();
                $productCollectionIds = $request['product_collections'];
                $now = Carbon::now();
                $productCollections = [];
                foreach ($productCollectionIds as $i){
                    array_push($productCollections,
                        [
                            'product_collection_id' => $i,
                            'product_id' => $product['id'],
                            'updated_at' => $now,
                            'created_at' => $now
                        ]
                    );
                }

                CollectionWithProduct::insert($productCollections);
                $product = Product::with('product_images')
                    ->with('product_collections')
                    ->find($product['id']);
            }

            return response()->json(new Response($request->token, $product));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function delete(Request $request, $id)
    {
        try {
            if($can = Utils::userCan($this->user, 'product.delete')){
                return $can;
            }
            $product = Product::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product)) {
                return $isOwner;
            }

            if (is_null($product)){
                return response()->json(Validation::noData());
            }

            $flash_sale_product = FlashSaleProduct::where('product_id', $id)->get()->first();

            if($flash_sale_product){
                return response()->json(Validation::error($request->token,
                    'Unable to delete. Product is being used in the flash sale.'));
            }

            $orderedProduct = OrderedProduct::where('product_id', $id)->first();
            if($orderedProduct){
                return response()->json(Validation::error($request->token,
                    'Unable to delete. Product is being used in the order. Delete the order first.'));
            }

            //Product collection
            CollectionWithProduct::where('product_id', $id)->delete();

            $product_inventories = UpdatedInventory::where('product_id', $id)->get();

            foreach ($product_inventories as $inv){
                InventoryAttribute::where('inventory_id', $inv->id)->delete();
                $inv->delete();
            }

            $description_images = WysiwygImage::where('item_id', $id)->get();
            foreach ($description_images as $di){
                $di->delete();
                FileHelper::deleteFile($di->image);
            }

            $product_images = ProductImage::where(['product_id' => $id])->get();

            foreach ($product_images as $img){
                $img->delete();
                FileHelper::deleteFile($img->image);
            }

            UserWishlist::where('product_id', $id)->delete();


            //Review delete
            $reviewImages = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=','rating_reviews.id')
                ->where('rating_reviews.product_id', $id);

            $rimages = $reviewImages->get();
            foreach ($rimages as $img){
                FileHelper::deleteFile($img->image);
            }

            $reviewImages->delete();

            RatingReview::where('product_id', $id)->delete();

            if ($product->delete()){
                FileHelper::deleteFile($product->image);
                FileHelper::deleteFile($product->video);
                FileHelper::deleteFile($product->video_thumb);

                return response()->json(new Response($request->token, $product));
            }
            return response()->json(Validation::error($request->token));
        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function allImages(Request $request, $productId)
    {
        $data = ProductImage::orderBy('created_at', 'ASC')->where(['product_id' => $productId])->get();
        return response()->json(new Response($request->token, $data));
    }


    public function deleteProductImage(Request $request, $productImageId){
        if($can = Utils::userCan($this->user, 'product.edit')){
            return $can;
        }

        $product_image = ProductImage::find($productImageId);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product_image)) {
            return $isOwner;
        }

        if (is_null($product_image)){
            return response()->json(Validation::nothing_found());
        }

        if ($product_image->delete()){
            if (config('env.media.STORAGE') != config('env.media.URL')) {
                FileHelper::deleteFile($product_image->image);
            }

            $images = ProductImage::where('product_id', $product_image->product_id)->get();
            return response()->json(new Response($request->token, $images));
        }
        return response()->json($request->token, Validation::error());
    }


    public function multipleImageUpload(Request $request, $productId)
    {
        try{
            if($can = Utils::userCan($this->user, 'product.edit')){
                return $can;
            }

            $product = Product::find($productId);

            if (is_null($product)){
                return response()->json(Validation::noData(201, null, 'multiple_image'));
            }

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product)) {
                return $isOwner;
            }

            // Checking if the image resource is URL
            if (config('env.media.STORAGE') == config('env.media.URL')) {
                $validate = Validation::image($request, 'multiple_image');
                if($validate){
                    return response()->json($validate);
                }

                $image_info = FileHelper::uploadImage($request['photo'], 'product');

                $product_image['image'] = $image_info['name'];
                $product_image['admin_id'] = $request->user()->id;
                $product_image['product_id'] = $productId;

                ProductImage::create($product_image);
                $images = ProductImage::where('product_id', $productId)->get();

                return response()->json(new Response($request->token, $images));
            }


            if($request->hasFile('images')){
                $images_arr = [];

                foreach ($request->images as $img){

                    $validate = Validation::multipleImages(['photo' => $img], $request->token);
                    if($validate){
                        return response()->json($validate);
                    }

                    $image_info = FileHelper::uploadImage($img, 'product');

                    $product_image['image'] = $image_info['name'];
                    $product_image['admin_id'] = $request->user()->id;
                    $product_image['product_id'] = $productId;

                    array_push($images_arr, $product_image);
                }

                ProductImage::insert($images_arr);
                $images = ProductImage::where('product_id', $productId)->get();

                return response()->json(new Response($request->token, $images));
            }

            return response()->json(Validation::error($request->token,
                __('api.invalid_parameter'),
                'multiple_image'));
           // return response()->json(Validation::invalid_parameter($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage(), 'multiple_image'));
        }
    }

    public function uploadVideo(Request $request, $id = null)
    {

        try{
            $validate = Validation::video($request);
            if($validate){
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['video_file'], 'product-video', false);
            $thumb_info = FileHelper::uploadImage($request['thumb'], 'product-video-thumb', false);
            $request['video'] = null;


            $product = $id ? Product::with('product_images')
                    ->with('flash_sale_product.flash_sale')
                    ->with('product_collections')
                    ->find($id) : null;

            if (is_null($product)){

                if($can = Utils::userCan($this->user, 'product.create')){
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new Product);
                $request['video'] = $image_info['name'];
                $request['video_thumb'] = $thumb_info['name'];

                $product = Product::create($request->all());

            }else{

                if($can = Utils::userCan($this->user, 'product.edit')){
                    return $can;
                }

                if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product)) {
                    return $isOwner;
                }

                $video = $product->video;
                $thumb = $product->video_thumb;
                if($product->update([
                    'video' => $image_info['name'],
                    'video_thumb' => $thumb_info['name']
                ])){
                    FileHelper::deleteFile($video);
                    FileHelper::deleteFile($thumb);
                }
            }

            $product['created'] = Utils::formatDate($product->created_at);
            return response()->json(new Response($request->token, $product));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function upload(Request $request, $id = null)
    {
        try{
            $validate = Validation::image($request);
            if($validate)
                return response()->json($validate);

            $image_info = FileHelper::uploadImage($request['photo'], 'product');
            $request['image'] = $image_info['name'];



            $product = $id ? Product::with('product_images')
                ->with('flash_sale_product.flash_sale')
                ->with('product_collections')
                ->find($id) : null;

            if (is_null($product)){

                if($can = Utils::userCan($this->user, 'product.create')){
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;
                $request['id'] = Utils::idGenerator(new Product);
                $product = Product::create($request->all());

            }else{

                if($can = Utils::userCan($this->user, 'product.edit')){
                    return $can;
                }

                if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $product)) {
                    return $isOwner;
                }

                $image = $product->image;
                if($product->update($request->all())){
                    FileHelper::deleteFile($image);
                }
            }



            $product['created'] = Utils::formatDate($product->created_at);
            return response()->json(new Response($request->token, $product));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
