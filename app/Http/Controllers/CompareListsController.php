<?php

namespace App\Http\Controllers;

use App\Models\CompareList;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CompareListsController extends Controller
{
    public function action(Request $request)
    {
        $validate = Validation::user_wishlist($request);
        if ($validate)
            return response()->json($validate);

        $user = Auth::user();

        $list = CompareList::where('product_id', $request->product_id)
            ->where('user_id', $user->id)
            ->get()
            ->first();

        if (is_null($list)) {
            $request['user_id'] = $user->id;
            $list = CompareList::create($request->all());
            return Validation::success($request,
                __('api.compare_success'),
                $list);
        } else {
            CompareList::where('id', $list->id)->delete();
            return Validation::success($request,
                __('api.compare_remove'),
                null);
        }
    }


    public function all(Request $request){
        $user = Auth::user();

        $data = CompareList::with('product')->where('user_id', $user->id)
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
