<?php

namespace App\Http\Controllers;

use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\UserWishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserWishlistsController extends Controller
{
    public function wishlistAction(Request $request)
    {
        $validate = Validation::user_wishlist($request);
        if ($validate)
            return response()->json($validate);

        $user = Auth::user();

        $userWishlist = UserWishlist::where('product_id', $request->product_id)->where('user_id', $user->id)->get()->first();

        if (is_null($userWishlist)) {
            $request['user_id'] = $user->id;
            $userWishlist = UserWishlist::create($request->all());

            $message =  __('api.added');
        } else {
            UserWishlist::where('id', $userWishlist->id)->delete();

            $message =  __('api.removed');
            $userWishlist = null;
        }

        return Validation::success($request,
            __('api.wishlist_message', ['message'=> $message])
            , $userWishlist);
    }


    public function wishlists(Request $request)
    {
        $user = Auth::user();

        $data = UserWishlist::with('product')->where('user_id', $user->id)
            ->orderBy($request->order_by, $request->type)
            ->paginate(Config::get('constants.api.PAGINATION'));



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
