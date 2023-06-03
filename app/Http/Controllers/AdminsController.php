<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ContactUs;
use App\Models\Helper\FileHelper;
use App\Models\Helper\MailHelper;
use App\Models\Helper\Response;
use App\Models\Helper\Utils;
use App\Models\Helper\Validation;
use App\Models\Order;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SiteSetting;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Laravel\Passport\Passport;

class AdminsController extends Controller
{

    public $user;
    public $isVendor = false;
    public $isSuperAdmin = false;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            if ($this->user) {
                foreach ($this->user->roles->pluck('name') as $i) {
                    if ($i == 'vendor') {
                        $this->isVendor = true;
                        break;
                    } else if ($i == 'superadmin') {
                        $this->isSuperAdmin = true;
                        break;
                    }
                }
            }
            return $next($request);
        });
    }


    public function clearCache(Request $request)
    {


        Artisan::call('config:cache');
        Artisan::call('config:clear');
        Artisan::call('route:cache');
        Artisan::call('route:clear ');
        Artisan::call('cache:clear');
        //Artisan::call('optimize');
        return response()->json(new Response($request->token, true));
    }


    public function statistic(Request $request)
    {
        try{
            if ($can = Utils::userCan($this->user, 'dashboard.view')) {
                return $can;
            }

            $data = [];
            $time = null;
            if ($request->time_zone && $request->time) {
                $time = Utils::convertTimeToUTCzone($request->time, $request->time_zone);
            }

            if ($this->isVendor) {
                $cancelled = Order::join('ordered_products as op', function ($join) {
                    $join->on('op.order_id', '=', 'orders.id');
                    $join->join('products as p', function ($join2) {
                        $join2->on('p.id', '=', 'op.product_id');
                        $join2->where('p.admin_id', $this->user->id);
                    });
                })->where('orders.cancelled', Config::get('constants.status.PUBLIC'));

                $statistics = Order::join('ordered_products as op', function ($join) {
                    $join->on('op.order_id', '=', 'orders.id');
                    $join->join('products as p', function ($join2) {
                        $join2->on('p.id', '=', 'op.product_id');
                        $join2->where('p.admin_id', $this->user->id);
                    });
                })
                    ->where('orders.cancelled', '!=', Config::get('constants.status.PUBLIC'))
                    ->select(
                        "orders.id",
                        "orders.status",
                        DB::raw("(count(orders.id)) as total")
                    )
                    ->orderBy('orders.status')
                    ->groupBy('orders.status');

            } else {
                $cancelled = Order::where('cancelled', Config::get('constants.status.PUBLIC'));

                $statistics = Order::where('cancelled', '!=', Config::get('constants.status.PUBLIC'))
                    ->select(
                        "id",
                        "status",
                        DB::raw("(count(id)) as total")
                    )
                    ->orderBy('status')
                    ->groupBy('status');
            }

            if (!is_null($time)) {
                $cancelled = $cancelled->where('orders.created_at', '>=', $time);
            }
            $data['cancelled'] = $cancelled->count();

            if (!is_null($time)) {
                $statistics = $statistics->where('orders.created_at', '>=', $time);
            }
            $data['statistics'] = $statistics->get();

            // Fetching category list
            $q = OrderedProduct::join('products as p', function ($join) {
                $join->on('p.id', '=', 'ordered_products.product_id');
            })
                ->join('categories as c', function ($join) {
                    $join->on('c.id', '=', 'p.category_id');
                });

            if ($this->isVendor) {
                $q = $q->where('p.admin_id', $this->user->id);
            }

            $categories = $q->select(
                'ordered_products.product_id',
                'ordered_products.selling as price',
                'ordered_products.created_at',
                'p.id',
                'p.category_id',
                'c.id',
                'c.image',
                'c.title',
                DB::raw("(COUNT(c.id)) as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )
                ->orderBy('total_price', 'DESC')
                ->groupBy('c.id')
                ->limit(Config::get('constants.pagination.DASHBOARD'));

            if (!is_null($time)) {
                $categories = $categories->where('ordered_products.created_at', '>=', $time);
            }

            $data['categories'] = $categories->get();

            // Fetching brands list
            $brandQuery = OrderedProduct::join('products as p', function ($join) {
                $join->on('p.id', '=', 'ordered_products.product_id');
            })->join('brands as b', function ($join) {
                $join->on('b.id', '=', 'p.brand_id');
            });
            if ($this->isVendor) {
                $brandQuery = $brandQuery->where('p.admin_id', $this->user->id);
            }
            $brands = $brandQuery->select(
                'ordered_products.product_id',
                'ordered_products.selling as price',
                'ordered_products.created_at',
                'p.id',
                'p.brand_id',
                'b.id',
                'b.image',
                'b.title',
                DB::raw("(COUNT(b.id)) as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )
                ->orderBy('total_price', 'DESC')
                ->groupBy('b.id')
                ->limit(Config::get('constants.pagination.DASHBOARD'));

            if (!is_null($time)) {
                $brands = $brands->where('ordered_products.created_at', '>=', $time);
            }

            $data['brands'] = $brands->get();

            // Fetching brands list
            $productQuery = OrderedProduct::join('products as p', function ($join) {
                $join->on('p.id', '=', 'ordered_products.product_id');
                if ($this->isVendor) {
                    $join->where('p.admin_id', $this->user->id);
                }
            });
            if ($this->isVendor) {
                $productQuery = $productQuery->where('p.admin_id', $this->user->id);
            }
            $products = $productQuery->select(
                'ordered_products.product_id',
                'ordered_products.selling as price',
                'ordered_products.created_at',
                'p.id',
                'p.image',
                'p.title',
                DB::raw("(COUNT(p.id)) as total"),
                DB::raw("(SUM(ordered_products.selling)) as total_price")
            )
                ->orderBy('total_price', 'DESC')
                ->groupBy('p.id')
                ->limit(Config::get('constants.pagination.DASHBOARD'));

            if (!is_null($time)) {
                $products = $products->where('ordered_products.created_at', '>=', $time);
            }
            $data['products'] = $products->get();

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function dashboard(Request $request)
    {
        try{
            if ($can = Utils::userCan($this->user, 'dashboard.view')) {
                return $can;
            }

            $data = [];
            if ($request['dashboard'] === 'false') {
                $dashboard['users'] = User::count();

                if ($this->isVendor) {
                    $dashboard['products'] = Product::where('admin_id', $this->user->id)->count();
                    $query = Order::join('ordered_products as op', function ($join) {
                        $join->on('op.order_id', '=', 'orders.id');
                        $join->join('products as p', function ($join2) {
                            $join2->on('p.id', '=', 'op.product_id');
                            $join2->where('p.admin_id', $this->user->id);
                        });
                    });
                    $dashboard['orders'] = $query->count();
                    $dashboard['orders_amount'] = OrderedProduct::join('products as p', function ($join) {
                        $join->on('p.id', '=', 'ordered_products.product_id');
                        $join->where('p.admin_id', $this->user->id);
                    })
                        ->sum('ordered_products.selling');
                } else {
                    $dashboard['products'] = Product::count();
                    $dashboard['orders'] = Order::count();
                    $dashboard['orders_amount'] = Order::sum('total_amount');
                }
                $data['dashboard'] = $dashboard;
            }

            $m = date('M');
            $y = date('Y');
            if ($request['month']) {
                $m = $request['month'];
            }
            if ($request['year']) {
                $y = $request['year'];
            }

            if ($this->isVendor) {
                $dateStr = 'DATE(ordered_products.created_at) as  time';
                if ($request->time_zone) {

                    $time = new \DateTime('now', new \DateTimeZone($request->time_zone));
                    $timezoneOffset = $time->format('P');

                    $dateStr  = "DATE(CONVERT_TZ(ordered_products.created_at, '+00:00', '" . $timezoneOffset . "')) as  time";
                }

                $chartData['monthly_order'] = OrderedProduct::join('products as p', function ($join) {
                    $join->on('p.id', '=', 'ordered_products.product_id');
                    $join->where('p.admin_id', $this->user->id);
                })
                    ->whereMonth('ordered_products.created_at', $m)->whereYear('ordered_products.created_at', $y)
                    ->select(
                        "ordered_products.id",
                        DB::raw("(count(ordered_products.selling)) as total"),
                        DB::raw("(sum(ordered_products.selling)) as value"),
                        DB::raw($dateStr)
                    )
                    ->orderBy('time')
                    ->groupBy('time')
                    ->get();
            } else {

                $dateStr  = 'DATE(created_at) as time';
                if ($request->time_zone) {

                    $time = new \DateTime('now', new \DateTimeZone($request->time_zone));
                    $timezoneOffset = $time->format('P');

                    $dateStr  = "DATE(CONVERT_TZ(created_at, '+00:00', '" . $timezoneOffset . "')) as  time";
                }

                $chartData['monthly_order'] = Order::whereMonth('created_at', $m)->whereYear('created_at', $y)
                    ->select(
                        "id",
                        DB::raw("(count(total_amount)) as total"),
                        DB::raw("(sum(total_amount)) as value"),
                        DB::raw($dateStr)
                    )
                    ->orderBy('time')
                    ->groupBy('time')
                    ->get();
            }

            $data['chart_data'] = $chartData;

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function login(Request $request)
    {



        try {
            $validator = Validation::admin_login($request);
            if ($validator) {
                return response()->json($validator);
            }

            $admin = Admin::where('email', request('email'))->first();



            if (is_null($admin)) {
                return response()->json(Validation::error(null,
                    __('api.wrong_email')));
            }

            $temp_admin = clone $admin;


            $password_check = Validation::password_check($admin, request('password'));
            if ($password_check) {
                return response()->json($password_check);
            }


            Auth::login($admin);


            $data['expires_in'] = Carbon::now()->addHours(Config::get('constants.auth.EXPIRATION_IN_HOURS'));


            if (request('remember_token')) {
                $data['expires_in'] = Carbon::now()->addMonths(12);
            }

            Auth::user()->tokens->each(function ($token, $key) {
                // if ($token->name === "admin") $token->delete();
            });

            Passport::personalAccessTokensExpireIn($data['expires_in']);

            $data['token'] = Auth::user()->createToken('admin', ['admin'])->accessToken;
            $data['admin'] = $temp_admin;


            return response()->json(new Response($request->token, $data));


        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }
    }


    public function update(Request $request)
    {
        try {
            if ($can = Utils::userCan($this->user, 'profile.edit')) {
                return $can;
            }

            $validator = Validation::admin_signup($request);
            if ($validator) {
                return response()->json($validator);
            }

            $admin = Admin::where('email', $request->email)
                ->first();

            $adminExists = false;
            if (!is_null($admin)) {
                if ($this->user->id != $admin->id) {
                    $adminExists = true;
                }
            }

            if ($adminExists) {
                return response()->json(Validation::error($request->token,
                    __('api.email_exists')));
            }

            $password_check = Validation::password_check($this->user, request('password'));
            if ($password_check) {
                return response()->json($password_check);
            }

            if (Admin::where('id', $this->user->id)->update([
                'name' => $request['name'],
                'username' => $request['username'],
                'email' => $request['email'],
            ])) {

                return response()->json(new Response($request->token, Admin::find($this->user->id)));
            }

            return response()->json(Validation::error());

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token, $ex->getMessage()));
        }

    }


    public function updatePassword(Request $request)
    {
        if ($can = Utils::userCan($this->user, 'profile.edit')) {
            return $can;
        }

        $validator = Validation::admin_password($request);
        if ($validator) {
            return response()->json($validator);
        }

        $current_admin = $request->user();

        $admin = Admin::where('email', $current_admin->email)->first();

        $password_check = Validation::password_check($admin, request('password'));
        if ($password_check) {
            return response()->json($password_check);
        }

        $new_admin['password'] = Hash::make(request('new_password'));

        if ($admin->update($new_admin)) {
            Auth::user()->tokens->each(function ($token, $key) {
                // if ($token->name === "admin") $token->delete();
            });

            return response()->json(new Response($request->token, $admin));
        }
        return response()->json(Validation::error());
    }


    public function forgotPassword(Request $request)
    {
        try {

            $validator = Validation::forgotPassword($request);
            if ($validator) {
                return response()->json($validator);
            }

            $existingAdmin = Admin::where('email', $request->email)->first();
            if (is_null($existingAdmin)) {
                return response()->json(Validation::error($request->token,
                    __('api.no_found')));
            }

            Admin::where('id', $existingAdmin->id)
                ->update([
                    'code' => MailHelper::codeSender($existingAdmin, 'forgot_password')
                ]);
            return response()->json(
                new Response(
                    $request->token, true, 200, __('api.success_email')
                ));

        } catch (\Exception $ex) {
            return response()->json(Validation::error(null, explode('.', $ex->getMessage())[0]));
        }

    }

    public function verifyCode(Request $request)
    {
        $validator = Validation::verifyCode($request);
        if ($validator) {
            return response()->json($validator);
        }

        $existingAdmin = Admin::where('email', $request->email)->first();
        if (is_null($existingAdmin)) {
            return response()->json(Validation::error($request->token,
                __('api.no_email')));
        }

        if ($existingAdmin->code != $request->code) {
            return response()->json(Validation::error($request->token,
                __('api.code_not_match')));
        }

        Admin::where('id', $existingAdmin->id)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        return response()->json(
            new Response(
                $request->token, true, 200, __('api.password_updated')
            ));
    }


    public function signup(Request $request)
    {
        try {
            $validator = Validation::admin_signup($request);
            if ($validator)
                return response()->json($validator);

            $request['password'] = Hash::make(request('password'));

            return response()->json(new Response($request->token, Admin::create($request->all())));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }

    }

    public function logout(Request $request)
    {
        try {
            Auth::user()->tokens->each(function ($token, $key) {
                if ($token->name === "admin") $token->delete();
            });

            return response()->json(new Response($request->token, [], 200, __('api.logged_out')));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = new User();
            $authUser = Auth::user();
            $user->id = $authUser->id;
            $user->name = $authUser->name;
            $user->username = $authUser->username;
            $user->email = $authUser->email;

            $data['user'] = $user;
            $data['super_admin'] = $this->isSuperAdmin;
            $data['vendor'] = $this->isVendor;
            $data['store'] = Store::where('id', $authUser->id)->get()->first();
            $data['setting'] = Setting::select()->first();
            $data['site_setting'] = SiteSetting::select()->get()->first();
            $data['permissions'] = $this->user->getAllPermissions()->pluck('name');
            $data['message_count'] = ContactUs::where('viewed', '!=', Config::get('constants.status.PUBLIC'))
                ->count();

            $data['media_storage'] = env('MEDIA_STORAGE');
            $data['img_src_url'] = FileHelper::imgSrcUrl();
            $data['thumb_prefix'] = env('THUMB_PREFIX');
            $data['default_image'] = env('DEFAULT_IMAGE');

            return response()->json(new Response($request->token, $data));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }

    }


    public function all(Request $request)
    {
        if ($can = Utils::userCan($this->user, 'admin.view')) {
            return $can;
        }

        if ($request->q) {
            $data = Admin::with('roles')
                ->orderBy($request->orderby, $request->type)
                ->where('name', 'LIKE', "%{$request->q}%")
                ->where('email', 'LIKE', "%{$request->q}%")
                ->paginate(Config::get('constants.api.PAGINATION'));
        } else {
            $data = Admin::with('roles')
                ->orderBy($request->orderby, $request->type)
                ->paginate(Config::get('constants.api.PAGINATION'));
        }

        foreach ($data as $item) {
            $item['created'] = Utils::formatDate($item->created_at);
        }
        return response()->json(new Response($request->token, $data));
    }

    public function find(Request $request, $id)
    {
        if ($can = Utils::userCan($this->user, 'admin.view')) {
            return $can;
        }

        $data = Admin::with('roles')->find($id);
        if (is_null($data)) {
            return response()->json(Validation::noData());
        }
        return response()->json(new Response($request->token, $data));
    }


    public function action(Request $request, Admin $admin)
    {
        $validate = Validation::admin($request);
        if ($validate) {
            return response()->json($validate);
        }

        if ($request->roles[0] == 'vendor' && !is_numeric($request->commission)) {
            return response()->json(Validation::error($request->token,
                __('api.commission_must')));
        }

        $existingAdmin = Admin::where('email', $request->email)
            ->orWhere('username', $request->username)
            ->first();

        $adminExists = false;
        if (!is_null($existingAdmin)) {
            if ($admin->id) {
                if ($admin->id != $existingAdmin->id) {
                    $adminExists = true;
                }
            } else {
                $adminExists = true;
            }
        }

        if ($adminExists) {
            return response()->json(Validation::error($request->token,
                __('api.already_exists')));
        }

        if ($request->password) {
            $request->merge([
                'password' => Hash::make($request->password),
            ]);
        }

        $request['created_at'] = $request['updated_at'] = '';

        $filtered = array_filter($request->all(), function ($element) {
            return !is_array($element) && '' !== trim($element);
        });

        if ($admin->id) {
            if ($can = Utils::userCan($this->user, 'admin.edit')) {
                return $can;
            }

            if (Auth::user()->id == $admin->id) {
                return response()->json(Validation::error($request->token,
                    __('api.own_role')));
            }
            Admin::where('id', $admin->id)->update($filtered);
            $admin = Admin::find($admin->id);


        } else {
            if ($can = Utils::userCan($this->user, 'admin.create')) {
                return $can;
            }

            $admin = Admin::create($filtered);

            $store['name'] = $admin->name;
            $store['slug'] = $admin->username;
            $store['admin_id'] = $admin->id;

            $existingSlug = Store::where('slug', $store['slug'])->first();
            if($existingSlug){
                $store['slug'] = $admin->username . Utils::generateRandomString(5);
            }

            Store::create($store);
        }

        if ($request->roles) {
            $admin->roles()->detach();
            $admin->assignRole($request->roles);
        }

        return response()->json(new Response($request->token, $admin));
    }

    public function delete(Request $request, $id)
    {
        try {

            if ($can = Utils::userCan($this->user, 'admin.delete')) {
                return $can;
            }

            $data = Admin::find($id);

            if (is_null($data)) {
                return response()->json(Validation::noData());
            }

            if (Auth::user()->id == $data->id) {
                return response()->json(Validation::error($request->token,
                    __('api.delete_account')));
            }

            $product = Product::where('admin_id', $id)->get()->first();
            if ($product) {
                return response()->json(Validation::error($request->token,
                    __('api.has_products')));
            }

            Store::where('admin_id', $id)->delete();

            if ($data->delete()) {
                return response()->json(new Response($request->token, $data));
            }

            return response()->json(Validation::error($request->token));

        } catch (\Exception $ex) {
            return response()->json(Validation::error($request->token,$ex->getMessage()));
        }


    }

}
