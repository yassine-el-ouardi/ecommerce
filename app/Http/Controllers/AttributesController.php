<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Helper\ControllerHelper;
use App\Models\Inventory;
use App\Models\InventoryAttribute;
use Illuminate\Http\Request;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use Illuminate\Support\Facades\Config;

class AttributesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'attribute.view')){
            return $can;
        }

        $query = Attribute::query()
            ->with('values')
            ->orderBy($request->orderby, $request->type);

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }

        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item){
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }


    public function allAttributes(Request $request)
    {
        $data = Attribute::with('values')->orderBy('created_at')->get();
        return response()->json(new Response($request->token, $data));
    }


    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'attribute.view')){
            return $can;
        }

        $attribute = Attribute::with('values')->find($id);

        if (is_null($attribute)){
            return response()->json(Validation::noData());
        }

        $attribute['created'] = Utils::formatDate($attribute->created_at);

        return response()->json(new Response($request->token, $attribute));
    }


    public function action(Request $request, Attribute $attribute)
    {
        try{
            $validate = Validation::attribute($request);
            if($validate){
                return response()->json($validate);
            }

            if($attribute->id){
                if($can = Utils::userCan($this->user, 'attribute.edit')){
                    return $can;
                }

                $filtered = array_filter($request->all(), function ($element) {
                    return !is_array($element) && '' !== trim($element);
                });

                $data = ['add' => [], 'delete' => []];
                foreach ($request->values as  $value){

                    if(!key_exists('id', $value) && ('' != trim($value['title']))){
                        array_push($data['add'],
                            [
                                'title' => $value['title'],
                                'attribute_id' => $attribute->id,
                                'admin_id' => $request->user()->id
                            ]
                        );
                    }else if(key_exists('id', $value) && '' == trim($value['title'])){

                        $inventoryAttribute = InventoryAttribute::where('attribute_value_id', $value['id'])
                            ->get()->first();

                        if(!is_null($inventoryAttribute)){
                            return response()->json(Validation::error($request->token,
                                __('api.used_inventory')));
                        }

                        array_push($data['delete'], $value['id']);
                    }else if(key_exists('id', $value) && '' != trim($value['title'])){
                        AttributeValue::where('id', $value['id'])
                            ->update(['title' => $value['title']]);
                    }
                }

                if(count($data['add']) > 0){
                    if($can = Utils::userCan($this->user, 'attribute.create')){
                        return $can;
                    }
                    AttributeValue::insert($data['add']);
                }

                if(count($data['delete']) > 0){
                    if($can = Utils::userCan($this->user, 'attribute.delete')){
                        return $can;
                    }

                    AttributeValue::whereIn('id', $data['delete'])->delete();
                }

                $attribute->update(array_filter($filtered));

            }else{
                if($can = Utils::userCan($this->user, 'attribute.create')){
                    return $can;
                }

                $request['admin_id'] = $request->user()->id;

                $attribute = Attribute::create($request->all());

                $data = ['add' => []];
                foreach ($request->values as  $value){
                    if(!key_exists('id', $value) && ('' != trim($value['title']))){
                        array_push($data['add'],
                            [
                                'title' => $value['title'],
                                'attribute_id' => $attribute->id,
                                'admin_id' => $request->user()->id
                            ]
                        );
                    }
                }


                if(count($data['add']) > 0){
                    if($can = Utils::userCan($this->user, 'attribute.create')){
                        return $can;
                    }
                    AttributeValue::insert($data['add']);
                }

            }
            $attr =  Attribute::with('values')->find($attribute->id);

            return response()->json(new Response($request->token, $attr));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }
    }


    public function delete(Request $request, $id)
    {
        try {
            if($can = Utils::userCan($this->user, 'attribute.delete')){
                return $can;
            }

            $attribute = Attribute::find($id);

            if (is_null($attribute)){
                return response()->json(Validation::nothing_found());
            }

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $attribute)) {
                return $isOwner;
            }

            $attributeValues = AttributeValue::where('attribute_id', $id)->get();

            foreach ($attributeValues as $attributeValue){

                $inventoryAttribute = InventoryAttribute::where('attribute_value_id', $attributeValue->id)
                    ->get()->first();

                if(!is_null($inventoryAttribute)){
                    return response()->json(Validation::error($request->token,
                        __('api.attribute_used')));
                }
            }

            AttributeValue::where('attribute_id', $id)->delete();

            if ($attribute->delete()){
                return response()->json(new Response($request->token, $attribute));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }
    }
}
