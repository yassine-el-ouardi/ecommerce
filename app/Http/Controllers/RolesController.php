<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends ControllerHelper
{
    public function allRoles(Request $request){
        $data = Role::all();
        return response()->json(new Response($request->token, $data));
    }

    public function allPermissions(Request $request)
    {
        $data = Permission::all();
        return response()->json(new Response($request->token, $data));
    }


    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'role.view')){
            return $can;
        }

        if($request->q){
            $data = Role::query()
                ->orderBy($request->orderby, $request->type)
                ->where('name', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.api.PAGINATION'));
        }else{
            $data = Role::orderBy($request->orderby, $request->type)
                ->paginate(Config::get('constants.api.PAGINATION'));
        }

        foreach ($data as $item){
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if($can = Utils::userCan($this->user, 'role.view')){
            return $can;
        }

        $data = Role::with('permissions')->find($id);
        if (is_null($data)){
            return response()->json(Validation::noData());
        }
        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, Role $role)
    {
        $validate = Validation::role($request);
        if($validate){
            return response()->json($validate);
        }
        if($role->id){
            if($can = Utils::userCan($this->user, 'role.edit')){
                return $can;
            }

            $role = Role::with('permissions')->find($role->id);

            if(!is_null($role) && Auth::user()->hasRole($role->name)){
                return response()->json(Validation::error($request->token,
                    __('api.change_permissions')
                ));
            }

            $filtered = array_filter($request->all(), function ($element) {
                return !is_array($element) && '' !== trim($element);
            });

            $role->update(array_filter($filtered));

        }else{
            if($can = Utils::userCan($this->user, 'role.create')){
                return $can;
            }

            $role = Role::create(['guard_name' => 'admin', 'name' => $request->name]);
        }


        if($request->updated_permissions){
            $permissions = $request->updated_permissions;
            $role->syncPermissions($permissions);
        }


        return response()->json(new Response($request->token, $role));
    }

    public function delete(Request $request, $id)
    {
        try{
            if($can = Utils::userCan($this->user, 'role.delete')){
                return $can;
            }

            $data = Role::find($id);

            if (is_null($data)){
                return response()->json(Validation::noData());
            }

            $adminUsingRole = Admin::whereHas('roles', function($q) use ($data) {
                $q->where('name', $data->name);
            })
                ->get()
                ->first();

            if(!is_null($adminUsingRole)){
                return response()->json(Validation::error($request->token,
                    __('api.delete_admin')
                ));
            }

            if ($data->delete()){
                return response()->json(new Response($request->token, $data));
            }

            return response()->json(Validation::error($request->token));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }
    }
}
