<?php

namespace App\Http\Controllers;

use App\Models\Helper\Response;
use App\Models\Helper\Validation;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    public function byUser(Request $request, $userId)
    {
        $data = UserAddress::where('user_id', $userId)->get();

        return response()->json(new Response($request->token, $data));
    }

    public function action(Request $request, UserAddress $userAddress)
    {
        $validate = Validation::user_address($request);
        if($validate)
            return response()->json($validate);

        if($userAddress->id){
            $filtered = array_filter($request->all(), function ($element) {
                return  !is_array($element) && '' !== trim($element);
            });

            $userAddress->update(array_filter($filtered));

        }else {
            $userAddress = UserAddress::create($request->all());
        }

        return response()->json(new Response($request->token, $userAddress));
    }

    public function delete(Request $request, $id)
    {
        try {
            $userAddress = UserAddress::find($id);

            if (is_null($userAddress))
                return response()->json(Validation::nothing_found());


            if ($userAddress->delete()){
                return response()->json(new Response($request->token, $userAddress));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }
}
