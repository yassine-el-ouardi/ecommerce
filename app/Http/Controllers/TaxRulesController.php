<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Product;
use App\Models\TaxRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class TaxRulesController extends ControllerHelper
{
    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'tax_rule.view')){
            return $can;
        }

        $query = TaxRules::query()->orderBy($request->orderby, $request->type);
        if($this->isVendor) {
            $query = $query->where('admin_id', $this->user->id);
        }

        if($request->q){
            $query = $query->where('title', 'LIKE', "%{$request->q}%");
        }
        $data = $query->paginate(Config::get('constants.api.PAGINATION'));

        foreach ($data as $item){
            $item['created'] = Utils::formatDate($item->created_at);
        }

        return response()->json(new Response($request->token, $data));
    }

    public function allList(Request $request)
    {
        $data = TaxRules::orderBy('created_at')->get(['id', 'title']);
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'tax_rule.view')){
            return $can;
        }

        $data = TaxRules::find($id);

        if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $data)) {
            return $isOwner;
        }

        if (is_null($data)){
            return response()->json(Validation::nothing_found());
        }

        $data['created'] = Utils::formatDate($data->created_at);

        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, TaxRules $taxRules)
    {
        $validate = Validation::taxRules($request);
        if($validate){
            return response()->json($validate);
        }

        if($taxRules->id){
            if($can = Utils::userCan($this->user, 'tax_rule.edit')){
                return $can;
            }
            $existing = TaxRules::find($taxRules->id);
            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $existing)) {
                return $isOwner;
            }

            $price = (int)$request->price;

            $filtered = array_filter($request->all(), function ($element) {
                return '' !== trim($element);
            });

            $filtered = array_filter($filtered);

            if($price == 0){
                $filtered['price'] = 0;
            }
            $taxRules->update($filtered);

        }else{
            if($can = Utils::userCan($this->user, 'tax_rule.create')){
                return $can;
            }

            $request['admin_id'] = $request->user()->id;
            $taxRules = TaxRules::create($request->all());
        }

        $taxRules['created'] = Utils::formatDate($taxRules->created_at);
        return response()->json(new Response($request->token, $taxRules));
    }


    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'tax_rule.delete')){
                return $can;
            }

            $taxRules = TaxRules::find($id);

            if(!$this->isSuperAdmin && $isOwner = Utils::isDataOwner($this->user, $taxRules)) {
                return $isOwner;
            }

            if (is_null($taxRules)){
                return response()->json(Validation::noData());
            }

            $product = Product::where('tax_rule_id', $id)->get()->first();

            if($product){
                return response()->json(Validation::error($request->token,
                    __('api.tax_used')
                ));
            }

            if ($taxRules->delete()){
                return response()->json(new Response($request->token, $taxRules));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }


    }
}
