<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Validation;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends ControllerHelper
{
    public function find(Request $request)
    {
        $data = Store::where('admin_id', $this->user->id)->get()->first();
        return response()->json(new Response($request->token, $data));
    }

    public function action(Request $request)
    {
        $validate = Validation::store($request);
        if($validate){
            return response()->json($validate);
        }

        $data = Store::where('admin_id', $this->user->id)->first();

        $bySlug = Store::where('slug', $request['slug'])->first();


        $request['created_at'] = $request['updated_at'] = '';

        $filtered = array_filter($request->all(), function ($element) {
            return  '' !== trim($element);
        });

        if($data){

            if ($bySlug && $bySlug->id != $data->id) {
                return response()->json(Validation::error($request->token, __('api.slug_exists')));
            }

            Store::where('admin_id', $this->user->id)->update($filtered);
        }else{

            if ($bySlug) {
                return response()->json(Validation::error($request->token, __('api.slug_exists')));
            }

            $filtered['admin_id'] = $this->user->id;
            Store::create($filtered);
        }

        return response()->json(new Response($request->token, $filtered));
    }


    public function uploadLogo(Request $request)
    {
        try{
            $validate = Validation::image($request);
            if($validate){
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], 'image');

            $existingStore = Store::where('admin_id', $this->user->id)->get()->first();

            if(is_null($existingStore)){
                $existingStore = Store::create(['image' => $image_info['name'], 'admin_id' => $this->user->id]);
            }else{
                if($existingStore->image) {
                    FileHelper::deleteFile($existingStore->image);
                }
                $existingStore->image = $image_info['name'];
                Store::where('admin_id', $this->user->id)->update(['image' => $image_info['name']]);
            }

            return response()->json(new Response($request->token, $existingStore));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }


    }
}
