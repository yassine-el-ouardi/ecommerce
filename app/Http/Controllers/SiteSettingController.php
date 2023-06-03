<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends ControllerHelper
{
    public function find(Request $request)
    {
        if ($can = Utils::userCan($this->user, 'site_setting.view')) {
            return $can;
        }

        $data = SiteSetting::get()->first();


        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request)
    {

        try {

            if ($can = Utils::userCan($this->user, 'site_setting.edit')) {
                return $can;
            }

            $validate = Validation::siteSetting($request);
            if ($validate) {
                return response()->json($validate);
            }

            $admin_id = $request->user()->id;
            $data = SiteSetting::where('id', $request->id)->get();

            $request['created_at'] = null;
            $request['updated_at'] = null;
            $request['admin_id'] = $admin_id;

            if (count($data) == 0) {

                SiteSetting::create($request->all());
            } else {

                SiteSetting::where('id', $request->id)->update($request->all());
            }

            $request['id'] = null;

            return response()->json(new Response($request->token, $request->all()));


        } catch (\Exception $ex) {
            return response()->json(Validation::error(null, explode('.', $ex->getMessage())[0]));
        }



    }


    public function upload(Request $request)
    {
        try {
            if ($can = Utils::userCan($this->user, 'site_setting.edit')) {
                return $can;
            }


            $validate = Validation::image($request);
            if ($validate) {
                return response()->json($validate);
            }

            $image_info = FileHelper::uploadImage($request['photo'], $request['type']);

            $existingSetting = SiteSetting::find($request->id);

            if (SiteSetting::where('id', $request->id)->update([request('type') => $image_info['name']])) {


                if ($existingSetting[request('type')]) {
                    FileHelper::deleteFile($existingSetting[request('type')]);
                }
                $existingSetting[request('type')] = $image_info['name'];
                return response()->json(new Response($request->token, $existingSetting));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }


    }

}
