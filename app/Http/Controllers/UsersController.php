<?php

namespace App\Http\Controllers;

use App\Models\Cancellation;
use App\Models\Cart;
use App\Models\CompareList;
use App\Models\Helper\ControllerHelper;
use App\Models\Helper\FileHelper;
use App\Models\Helper\MailHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\RatingReview;
use App\Models\ReviewImage;
use App\Models\User;
use App\Models\Helper\Validation;
use App\Models\UserAddress;
use App\Models\UserWishlist;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Laravel\Socialite\Facades\Socialite;

class UsersController extends ControllerHelper
{
    public function redirectToProvider($service)
    {
        return Socialite::driver($service)->redirect();
    }

    public function handleProviderCallback($service)
    {
        try {
            $user = Socialite::driver($service)->stateless()->user();
            $socialVariable = $service . '_id';

            $existingUser = User::where($socialVariable, $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($existingUser) {

                Auth::login($existingUser);

            } else {

                $newUser = [
                    'name' => $user->name,
                    'email' => $user->email,
                    $socialVariable => $user->id,
                    'password' => encrypt(rand(1000, 9999)),
                    'verified' => 1
                ];
                $newUser = User::create($newUser);
                Auth::login($newUser);
            }

            $authUser = Auth::user();
            $token = $authUser->createToken('user', ['user'])->accessToken;
            $id = $authUser->id;
            $name = $authUser->name;
            $email = $authUser->email;

            return redirect(env('FRONTEND_SOCIAL_REDIRECT', Utils::frontendSocialRedirect()) . '?token=' . $token . '&&user=' . $id . '&&email=' . $email . '&&name=' . $name);
        } catch (\Exception $ex) {
            return redirect(env('FRONTEND_SOCIAL_REDIRECT', Utils::frontendSocialRedirect()) . '?error=' . explode('response', $ex->getMessage())[0]);
        }
    }

    public function all(Request $request)
    {
        if ($can = Utils::userCan($this->user, 'user.view')) {
            return $can;
        }

        if ($request->q) {
            $data = User::query()
                ->orderBy($request->orderby, $request->type)
                ->where('email', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.api.PAGINATION'));
        } else {
            $data = User::orderBy($request->orderby, $request->type)
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
            if ($can = Utils::userCan($this->user, 'user.delete')) {
                return $can;
            }
            $user = User::find($id);

            if (is_null($user)) {
                return response()->json(Validation::noData());
            }

            Cart::where('user_id', $id)->delete();

            UserWishlist::where('user_id', $id)->delete();

            CompareList::where('user_id', $id)->delete();

            // Ordered products delete
            $orderedProducts = OrderedProduct::leftJoin('orders', 'ordered_products.order_id', '=', 'orders.id')
                ->where('orders.user_id', $id);

            $orderedProducts->delete();

            // Cancellation message  delete
            $cancellation = Cancellation::leftJoin('orders', 'cancellations.order_id', '=', 'orders.id')
                ->where('orders.user_id', $id);

            $cancellation->delete();

            Order::where('user_id', $id)->delete();

            // Review delete
            $reviewImages = ReviewImage::leftJoin('rating_reviews', 'review_images.rating_review_id', '=', 'rating_reviews.id')
                ->where('rating_reviews.user_id', $id);

            $rimages = $reviewImages->get();
            foreach ($rimages as $img) {
                FileHelper::deleteFile($img->image);
            }

            $reviewImages->delete();

            RatingReview::where('user_id', $id)->delete();



            // Address delete
            UserAddress::where('user_id', $id)->delete();

            if ($user->delete()) {
                return response()->json(new Response($request->token, $user));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }



    public function login(Request $request)
    {
        $validator = Validation::admin_login($request);
        if ($validator)
            return response()->json($validator);

        if ($request->input('token')) {
            $this->auth->setToken($request->input('token'));

            $user = $this->auth->authenticate();
            if ($user) {
                return response()->json(new Response($request->input('token'), $request->user()));
            }
        }

        $user = User::where('email', request('email'))->first();

        $password_check = Validation::password_check($user, request('password'));
        if ($password_check) {
            return response()->json($password_check);
        }

        Auth::login($user);

        if (!$user->verified) {
            return response()->json(Validation::error(null,
                __('api.not_verified')
            ));
        }

        $data['expires_in'] = Carbon::now()->addDays(Config::get('constants.auth.EXPIRATION_IN_DAYS'));


        if (request('remember_token')) {
            $data['expires_in'] = Carbon::now()->addMonth(12);
        }
        Passport::personalAccessTokensExpireIn($data['expires_in']);

        $data['token'] = Auth::user()->createToken('user', ['user'])->accessToken;

        $userArr['id'] = $user->id;
        $userArr['name'] = $user->name;
        $userArr['email'] = $user->email;
        $userArr['cart_count'] = Cart::where('user_id', $user->id)->sum('quantity');

        $data['user'] = $userArr;

        return response()->json(new Response($request->token, $data));
    }

    public function updatePassword(Request $request)
    {
        $validator = Validation::update_password($request);
        if ($validator) {
            return response()->json($validator);
        }

        $existingUser = User::where('email', $request->email)->get()->first();

        if (!$existingUser) {
            return response()->json(Validation::error(null,
                __('api.no_email')
            ));
        }


        if ($existingUser->code != $request->code) {
            return response()->json(Validation::error(null,
                __('api.code_invalid')
            ));
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(new Response($request->token, $request->email));
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validation::email_verification($request);
        if ($validator) {
            return response()->json($validator);
        }

        $existingUser = User::where('email', request('email'))->get()->first();

        if ($existingUser) {

            if (!$existingUser->verified) {
                return response()->json(Validation::error(null,
                    __('api.not_verified')
                ));
            }

            try {
                $code = MailHelper::codeSender($request, 'forgot_password');
                User::where('email', $existingUser->email)->update(array('code' => $code));
                return response()->json(new Response(null, $existingUser->email));

            } catch (\Exception $ex) {
                return response()->json(Validation::error(null, explode('.', $ex->getMessage())[0]));
            }


        } else {
            return response()->json(Validation::error(null,
                __('api.not_exists')
            ));
        }
    }


    public function verify(Request $request)
    {
        $validator = Validation::user_verification($request);
        if ($validator) {
            return response()->json($validator);
        }

        $existingUser = User::where('email', request('email'))->get()->first();
        if ($existingUser) {
            if ($existingUser->code == request('code')) {
                User::where('email', $existingUser->email)->update(array('verified' => true));
                return response()->json(new Response($request->token, $existingUser));
            } else {
                return response()->json(Validation::error(null,
                    __('api.code_invalid')
                ));
            }

        } else {
            return response()->json(Validation::error(null,
                __('api.not_exists')
            ));
        }
    }


    public function signup(Request $request)
    {
        $validator = Validation::user_signup($request);
        if ($validator) {
            return response()->json($validator);
        }

        $existingUser = User::where('email', request('email'))->get()->first();

        if ($existingUser && $existingUser->verified) {
            return response()->json(Validation::error(null,
                __('api.email_verified')
            ));
        }

        $request['password'] = Hash::make(request('password'));

        try {

            $request['code'] = MailHelper::codeSender($request, null,
                __('api.account_registration')
            );

            if (!$existingUser) {
                User::create($request->all());
            } else {
                User::where('email', $existingUser->email)->update([
                    'code' => $request['code'],
                    'password' => $request['password'],
                    'name' => $request['name']
                ]);
            }

            return response()->json(new Response(null, $request->email));

        } catch (\Exception $ex) {
            return response()->json(Validation::error(null, explode('.', $ex->getMessage())[0]));
        }
    }


    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            if ($token->name === "user") $token->delete();
        });
        return Validation::success($request,
            __('api.logged_out')
        );
    }


    public function updateProfile(Request $request)
    {
        $validator = Validation::userProfile($request);
        if ($validator) {
            return response()->json($validator);
        }

        User::where('id', Auth::user()->id)->update([
            'name' => $request->name
        ]);

        return Validation::success($request,
            __('api.profile_updated')
            , ['name' => $request->name]);
    }


    public function updateUserPassword(Request $request)
    {
        $validator = Validation::updateUserPassword($request);
        if ($validator) {
            return response()->json($validator);
        }

        $user = User::where('id', Auth::user()->id)->first();

        $password_check = Validation::password_check($user, $request->current_password,
            __('api.Password_matched')
        );
        if ($password_check) {
            return response()->json($password_check);
        }

        User::where('id', Auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return Validation::success($request,
            __('api.password_updated')
        );
    }


    public function profile(Request $request)
    {
        $user = $request->user();
        $user['cart_count'] = Cart::where('user_id', $user->id)->sum('quantity');
        unset($user['code']);
        return response()->json(new Response($request->token, $user));
    }


    public function addressAction(Request $request)
    {
        $validate = Validation::user_address($request);
        if ($validate)
            return response()->json($validate);

        $user = Auth::user();

        $userAddress = null;
        if ($request->id) {
            $userAddress = UserAddress::where('id', $request->id)->where('user_id', $user->id)->get()->first();
        }

        $message = '';

        if (is_null($userAddress)) {
            $request['user_id'] = $user->id;
            $userAddress = UserAddress::create($request->all());

            $message =  __('api.created');
        } else {
            $userAddress = $request->all();
            $request['created'] = null;
            $request['id'] = null;

            $filtered = array_filter($request->all(), function ($element) {
                return !is_array($element) && '' !== trim($element);
            });

            UserAddress::where('id', $userAddress['id'])->update(array_filter($filtered));

            $message =  __('api.updated');
        }

        return Validation::success($request,
            __('api.address_message', ['message'=> $message])
            , $userAddress);
    }


    public function deleteAddress(Request $request, $id)
    {
        try {
            $userId = Auth::user()->id;

            $userAddress = UserAddress::where('id', $id)->where('user_id', $userId);

            if (is_null($userAddress)){
                return response()->json(Validation::nothing_found());
            }


            $order = Order::where('user_address_id', $id)->first();

            if($order){
                return response()->json(Validation::error($request->token,
                    __('api.address_used')
                ));
            }


            if ($userAddress->delete()) {
                return Validation::success($request,
                    __('api.address_message', ['message'=> __('api.deleted')])
                    , $userAddress);
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function addresses(Request $request)
    {
        $user = Auth::user();
        if ($request->q) {
            $data = UserAddress::query()
                ->orderBy($request->orderby, $request->type)
                ->where('user_id', $user->id)
                ->where('address_1', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.frontend.PAGINATION'));
        } else {
            $data = UserAddress::where('user_id', $user->id)
                ->orderBy($request->order_by, $request->type)
                ->paginate(Config::get('constants.frontend.PAGINATION'));
        }

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


    public function vouchers(Request $request)
    {
        $currentTime = Carbon::now()->format('Y-m-d H:i:s');

        if ($request->q) {
            $data = Voucher::query()
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->where('status', Config::get('constants.status.PUBLIC'))
                ->orderBy($request->order_by, $request->type)
                ->where('title', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.frontend.PAGINATION'));
        } else {
            $data = Voucher::orderBy($request->order_by, $request->type)
                ->where('status', Config::get('constants.status.PUBLIC'))
                ->orderBy($request->order_by, $request->type)
                ->where('end_time', '>=', $currentTime)
                ->where('start_time', '<=', $currentTime)
                ->paginate(Config::get('constants.frontend.PAGINATION'));
        }

        if ($request->time_zone) {
            foreach ($data as $item) {
                $item['start_time'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->start_time, $request->time_zone),
                    Config::get('constants.dateTime.ONLY_DATE'));
                $item['end_time'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->end_time, $request->time_zone),
                    Config::get('constants.dateTime.ONLY_DATE'));
                $item['created'] = Utils::formatDate(Utils::convertTimeToUSERzone($item->created_at, $request->time_zone));
            }

        } else {
            foreach ($data as $item) {
                $item['start_time'] = Utils::formatDate($item->start_time, Config::get('constants.dateTime.ONLY_DATE'));
                $item['end_time'] = Utils::formatDate($item->end_time, Config::get('constants.dateTime.ONLY_DATE'));
                $item['created'] = Utils::formatDate($item->created_at);
            }
        }

        return response()->json(new Response($request->token, $data));
    }
}
