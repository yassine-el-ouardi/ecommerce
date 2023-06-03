<?php

namespace App\Http\Controllers;

use App\Models\Helper\ControllerHelper;
use App\Models\Helper\MailHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\SubscriptionEmail;
use App\Models\SubscriptionEmailFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SubscriptionEmailsController extends ControllerHelper
{

    public function allSubscribers(Request $request){
        $data = SubscriptionEmail::orderBy('created_at')->get(['id', 'email']);
        return response()->json(new Response($request->token, $data));
    }



    public function emailSubscription(Request $request)
    {
        $validate = Validation::emailSubscription($request);
        if($validate){
            return response()->json($validate);
        }

        $existingEmail = SubscriptionEmail::where('email', $request->email)->first();

        if(is_null($existingEmail)){
            SubscriptionEmail::create($request->all());
        }

        return response()->json(new Response('', true));
    }



    public function all(Request $request)
    {
        if($can = Utils::userCan($this->user, 'subscriber.view')){
            return $can;
        }

        if ($request->q) {
            $data = SubscriptionEmail::query()
                ->orderBy($request->orderby, $request->type)
                ->where('email', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.api.PAGINATION'));
        } else {
            $data = SubscriptionEmail::orderBy($request->orderby, $request->type)
                ->paginate(Config::get('constants.api.PAGINATION'));
        }

        foreach ($data as $item) {
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }


    public function delete(Request $request, $id)
    {
        try {

            if($can = Utils::userCan($this->user, 'subscriber.delete')){
                return $can;
            }


            $subscriptionEmail = SubscriptionEmail::find($id);

            if (is_null($subscriptionEmail)){
                return response()->json(Validation::noData());
            }

            if ($subscriptionEmail->delete()) {
                return response()->json(new Response($request->token, $subscriptionEmail));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }

    public function sendSubscriptionEmail(Request $request)
    {
        if($can = Utils::userCan($this->user, 'subscriber.view')){
            return $can;
        }

        try {
            $subscriptionEmailFormat = SubscriptionEmailFormat::find($request->id);

            if (is_null($subscriptionEmailFormat)){
                return response()->json(Validation::error($request->token,
                    __('api.email_format')
                ));
            }

            $subscribers = SubscriptionEmail::get();

            if(count($subscribers) < 1){
                return response()->json(Validation::error($request->token,
                     __('api.no_subscriber')
                ));
            }

            $subscribers = MailHelper::sendingSubscriptionEmail($subscriptionEmailFormat->subject,
                $subscriptionEmailFormat->body, $subscribers);

            return response()->json(new Response($request->token, $subscribers));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


}
