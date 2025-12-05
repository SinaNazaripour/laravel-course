<?php

use App\Exceptions\CustomException;
use App\Http\Controllers\InvokableController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PhotoCommentsController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TestMwController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use App\Http\Middleware\BeforeAfterMiddleware;
use App\Http\Middleware\ExampleMiddleware;
use App\Http\Requests\CustomRequest;
use App\Models\User;
use App\Services\ExampleInterface;
use App\Services\ExampleService1;
use App\Services\NotificationDispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\support\Arr;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Lottery;
use Illuminate\Support\Number;
use Psy\Command\DumpCommand;
use Ramsey\Collection\Collection;

use Illuminate\Support\Str;



Route::get('/home/{name?}', function ($name) {
    return view('welcome')->with('name', $name);
})->name('home');

Route::get('/route-prarmeter-optional/{name}/{id?}', function ($name, $id = null) {
    return "hello $name:$id";
});

Route::get('/route-external-dependencies/{name}/{id?}', function (Request $request, $name, $id = null) {
    return "hello $name:$id";
});

Route::view('/welcome', 'welcome', ['name' => 'sina']);

// |--------------------------------
// |route parameter Constraints
// |--------------------------------

// Route::get('/route-parameter-constraints/{name}/{id?}', function (string $name, int $id) {
//     return "hello $name:$id";
// });

// ------secound way---------

// Route::get('/route-parameter-constraints/{name}/{id?}', function (string $name, int $id) {
//     return "hello $name:$id";
// })->where(['id' => "[0-9]+", "name" => "[a-zA-Z]+"]);

// -------third way is in ServiceProvider->boot method
Route::get('/route-parameter-constraints/{name}/{id?}', function ($name, $id) {
    return "hello $name:$id";
});


// |------------------------------
// |-----implicit binding--------
// |-------------------------------
Route::get('/route-implicit-binding/{user}', function (User $user) {
    return "hello {$user->email}";
});


Route::get('/missing-model/{user}', function (User $user) {
    return "hello {$user->email}";
})->missing(fn() => redirect('welcome'));

// |----------------------
// |----route verb matching
// |------------------------
Route::match(['get', 'post'], '/match', fn() => $_SERVER['REQUEST_METHOD']);
Route::patch('/patch', fn() => $_SERVER['REQUEST_METHOD']);
Route::put('/put', fn() => $_SERVER['REQUEST_METHOD']);
Route::delete('/delete', fn() => $_SERVER['REQUEST_METHOD']);
Route::any('/any', fn() => $_SERVER['REQUEST_METHOD']);
Route::options('/options', fn() => $_SERVER['REQUEST_METHOD']);
Route::post('/post', fn() => $_SERVER['REQUEST_METHOD']);

// |----------------------
// |----route prefixing
// |------------------------
Route::prefix('sina')->group(function () {
    Route::get('name', fn() => 'sina');
    Route::get('l-name', fn() => 'nazari');
});

// |----------------------
// |----named routes
// |------------------------
Route::get('/named-route/{id?}', function ($id = null) {
    dump(route('named_route', ['id' => 12, 'extra_param' => '11']));
})->name('named_route');


// |----------------------
// |----redirects
// |------------------------
Route::get('/redirect-to-welcome', fn() => redirect('welcome'));
Route::get('/redirect-to-named', fn() => redirect()->route('named_route', ['id' => 12, 'extra' => 'ex']));


// |----------------------
// |----controllerBased routes
// |------------------------
Route::get('/controller-based-route', [UserController::class, 'index']);



// |----------------------
// |----fallback route
// |------------------------
// Route::fallback(fn() => 'not found is modified');



// |----------------------

// |----protecting routes with middleware and rate limiter

// |------------------------
// for count of routes
Route::middleware('auth')->group(function () {
    Route::get('/auth-protected-route', fn() => 'this is auth protected');
});

// -----------------or
//  for a single route
Route::get('/auth-protected', fn() => 'this is auth protected')->middleware('auth');

// -----------------------------------------ratelimit middleware-----------

Route::get('/limit-protected', fn() => 'this is limit protected')->middleware('throttle:example_limit');



// |----------------------
// |----signed routes
// |----------------------
Route::get('/signed-link-maker', function () {
    echo url()->temporarySignedRoute('signed-route-test', now()->addMinute(1), ['user' => 1]);
})->name('signed-link');

Route::get('/signed-route', function () {
    return "this is signed route";
})->name('signed-route-test')->middleware('signed');



// |----------------------
// |----Route Actions limit
// |----------------------

Route::get('/action-rate-limit', function () {
    $executed = RateLimiter::attempt(
        'sendMessage:',
        $perMinutes = 3,
        function () {
            return ('Message sent!');
        }
    );
    dump($executed);

    if (!$executed) {
        return "Too many messages sent!";
    }
});


// |----------------------
// |----Encryption and Hashing 
// |----------------------

Route::get('/hash-encryption/{text}', function ($text) {
    $encrypted = Crypt::encryptString($text);
    $decrypted = Crypt::decryptString($encrypted);
    $hashed = Hash::make($text);
    $check = Hash::check($text, $hashed);

    $response = "Encrypted:{$encrypted}<br><br>Decrypted:{$decrypted}<br><br>Hashed:{$hashed}<br><br>test is {$check}";
    return ($response);
});


// |----------------------
// |----resource full route and controllers
// |----------------------
// Route::resource('photo', PhotoController::class);
// Route::resource('photo', PhotoController::class)->only(['show', 'index']);#----->only specified methods
// Route::resource('photo', PhotoController::class)->except(['show', 'index']);#----->not specified methods
Route::apiResource('photo', PhotoController::class); #only required for api except any method that render html file

// |----------------------
// |----Invokable controllers
// |----------------------
Route::get('/invokable-controller', InvokableController::class); #artisan make:controller .. --invokable


// |----------------------
// |----nasted resources
// |----------------------
Route::resource('photo.comments', PhotoCommentsController::class); #php artisan make:controller PhotoCommentsController  --resource

// -------------------------------------------------------------------------------------------------------------------------------------------------------------
// |----------------------
// |----views and directives
// |----------------------
Route::get('/view', [ViewController::class, 'basics']);

// |----------------------
// |----Work With html&css
// |----------------------
Route::get('/view/work-with-html', [ViewController::class, 'workwithhtml']);


// |----------------------
// |----View composers
// |----------------------

Route::get('/view/composer', [ViewController::class, 'composer']);


// |----------------------
// |----ananymous components
// |----------------------

Route::get('/view/ananymous-components', [ViewController::class, 'ananymousComponents']);

// |----------------------
// |----class based components
// |----------------------
Route::get('/view/class-components', [ViewController::class, 'classComponents']);

// |----------------------
// |----component based layouts
// |----------------------
Route::get('/view/component-layout', [ViewController::class, 'componentBasedLayout']);

// |----------------------
// |----inherit based layouts
// |----------------------
Route::get('/view/inherit-layout', [ViewController::class, 'inheritBasedLayout']);


// |-----------------------------------------------------------------------------------------------------------------------
// |----service providers
// |----------------------
// services that have Typehint dependencies are handled auto but for manuall dependecy look at Exaple service provider.php
Route::get('/service-providers', function (ExampleService1 $service) {
    return $service->serviceOneMethod();
});

// |----------------------
// |---- singleton
// |---------------------
// to aviod  make an instance frequent in one request we user singleton method instead bind in register method in Sprovider
Route::get('/service-providers/singleton', function (ExampleService1 $service) {
    $r = app(ExampleService1::class);
    dump($r->serviceOneMethod());
    return $service->serviceOneMethod();
});

// |----------------------
// |---- interface binding
// |---------------------
Route::get('/service-providers/interface-binding', function (ExampleInterface $service) {
    return $service->implementMethod();
});


// |----------------------
// |----binding interfaces in runtime
// |---------------------
Route::get('/service-providers/interface-binding-at-runtime', [PhotoController::class, 'index']); #goto ExampleService provider


// |----------------------
// |----tagged bindings
// |---------------------

Route::get('/service-providers/tagged-binding/{message}', function (NotificationDispatcher $service, $message) {
    return $service->sendNotification($message);
}); #goto ExampleService provider
// ------------------------------------------------------------------------------------------------------------------------

// |-----------------------------------------------------------------------------------------------------------------------
// |---- facades
// |----------------------
// once way-------------------------->
Route::get('/facades', function () {
    // return Response::json(['first way' => 'illuminate\support\facades\felan']);
    // return response()->json(['secound way' => 'laravel helper functions']);
    dd(app('hash')); #third way from app service container
});

// |----------------------
// |----contracts
// |---------------------
Route::get('/contracts', function (Illuminate\Contracts\Routing\ResponseFactory $response) {
    //    with contracts interfaces and dipendency injection 
    return  $response->json(['contract' => 'object']);
});

// |-----------------------------------------------------------------------------------------------------------------------
// |---- middleware
// |----------------------
Route::get('/middleware', function () {
    return " page is verified by midlleware!";
})->middleware(ExampleMiddleware::class);

// |----------------------
// |----apply globally middleware
// |---------------------
// not for any single route, but for all ! open app.php in bootstrap in middleware function $middleware->append(MyMiddleware::class) 

// |----------------------
// |----before and after request handling
// |----------------

Route::get('/before-after', function () {
    echo "request handeled<br>";
    return " response returned";
})->middleware(BeforeAfterMiddleware::class);

// |----------------------
// |----define middleware in controller with implementing hasmiddleware interface
// |----------------
Route::resource('/middleware-in-controller', TestMwController::class);

// |----------------------
// |----alias and parameter for made middWs
// |----------------
Route::get('/middleware-alias-parameter', function () {
    return "page content";
})->middleware('aliastest.midlleware:ali');


// |----------------------
// |----deactivation default middlewares 
// |----------------
Route::post('/without-csrf', function () {
    return "dontverifaied csrf mw";
})->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);


//                   <---------------------------------Requests------------------------------------->

// |----------------------
// |----RequestBasics 
// |----------------

Route::match(['get', 'post'], '/request/basics', function (Request $request) {

    // -check request method

    if ($request->isMethod('get')) {
        dump("this is get request");
    }
    //  request path
    if ($request->is('request/*')) { #for pattern of route path

        //  request path
        dump($request->path());

        // route name check
        dump($request->routeIs("request.*") ? 'route name is match' : 'no');

        // url without qurey string
        dump($request->url());

        // full url 
        dump($request->fullUrl());

        // for speceific parameter
        dump($request->query('a', 'default'));
    }
})->name('request.basics');

// |----------------------
// |----Request Accept headers 
// |----------------
Route::match(['get', 'post'], 'request/accept-header', function (Request $request) {
    // if ($request->accepts(['text/html'])) {
    //     dump("request header is text/html:" . $request->path());
    // } elseif ($request->accepts(['application/json'])) {
    //     return response()->json(["message" => "request header is application/json:" . $request->path()]);
    // }

    // ----------------when application expects to give json

    if ($request->expectsJson()) {
        return response()->json(["message" => "request header is application/json:" . $request->path()]);
    }
});


// |----------------------
// |----Token & headers 
// |----------------
Route::post('request/header-and-token', function (Request $request) {
    return response()->json([
        'token' => $request->bearerToken(),
        'header:param' => $request->header('param')

    ]);
})->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);


// |----------------
// |----form & json inputs reading 
// |----------------

Route::post("request/input-test", function (Request $request) {
    if ($request->expectsJson()) {
        return response()->json(["name" => $request->input('user.name')]);
    } elseif ($request->accepts(['text/html'])) {
        // dump($request->input('name'));

        // to see bool of a field
        dump($request->boolean('ok'));
        // to see a field exists
        dump($request->has('paramname'));
        // you may know this 
        dump($request->except('paramname'));
    }
})->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);



Route::post('request/file', function (Request $request) {
    // property of file
    $name = $request->file('photo')->getClientOriginalName();
    // $type=$request->file('photo')->getClientMimeType()
    // $extension=$request->file('photo')->getClientOriginalExtension()
    // $path=$request->file('photo')->getClientOriginalPath()

    // __________storage________
    // $path = $request->photo->store('images');
    $path = $request->file('photo')->store('images');
    return $path;

    // __________________save old data for redirection as failedvalidatio______

    // $name = $request->old('name');
    // redirectTo()->withInput()

    // ________________________________read cookies____________________________
    // dump($request->coockie('laravel_session'))

})->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);;



// |-----------------------------------------------------------------------------------
// |----Responses!
// |----------------

Route::get('responser/{user?}', function (Request $request, User $user) {

    // by defaul its going to be json response
    // return ['name' => 1];
    // return $user

    #and also we can use response->json function

    return response()->json(
        [
            'name' => 'sina',
            'soldure' => 'false'
        ]
    );
});

// |----------------
// |----after reading cookie and header from request we have to write this from respons 
// |----------------

Route::prefix('response')->group(function () {
    Route::get(
        'coockie-header',
        function (Request $request) {
            #write cookie
            // return response('felan')->cookie(
            //     'name',
            //     'value',
            //     // $minutes,
            //     // $path,
            //     // $domain,
            //     // $secure,
            //     // httpOnly

            // );
            #delete cookie
            return response('bobo')->withoutCookie('name');
            #header
            return response('reza', 200)->header('melika', 'reza');
            #send header fstr
            // return response(null, 200, ["header" => "this is header fast way"]);
            // return "ok";
        }
    );
    // |----------------
    // |----redirection responses
    // |----------------
    Route::get('redirection', function () {

        // -----------------externall path---------------------
        // return redirect()->away('https://www.google.com');

        // ----------------named simple redirection----------
        // return redirect('welcome');

        // ----------------named with argument redirection----------
        return redirect()->route('home', ['name' => 'rezo']);

            // ----------------named with controller method redirection----------
            // return redirect()->action([PhotoController::class, "show"], ['photo' => 1]);

        ;

        //     // ---------------------------for flash messages and inputes to faild form_______
        // ----------------more-------------

        // return redirect('home')->with('sessionname', 'value');
        // ->in method $request->session()->get('name')

        //     return back()
        //     redirect('felan')->withInput($request->except('password'));
        //     back()->withInput($request->except('password'));
    });


    // |----------------
    // |----download files (sending a file to client )
    // |----------------

    Route::get('download', function () {
        //---------downloads-----------
        return response()->download(storage_path('app\private\images\rwQowNHp0d8imW683RNIOzTPMkdkgFrm6z5LzCrp.png'));

        // -------views-------
        // return response()->file(storage_path('app\private\images\rwQowNHp0d8imW683RNIOzTPMkdkgFrm6z5LzCrp.png'));
    });
});

// |-----------------------------------------------------------------------------------
// |----Session intrupt
// |----------------

Route::get('/session', function (Request $request) {
    // -------read from session---------
    dump($request->session()->get("key", 'default'));

    // -------write session---------
    $request->session()->put("key", 'value');

    // -------forget session---------
    // $request->session()->forget("key");


    // --------------pop from session----------
    // $value = $request->session()->pull("key");


    // -----------clear entire session---------
    // $request->session()->flush();

    // ---------------------set session only for next request-------
    // $request->session()->flash('key','value');

    // ---------------check session------------
    dump($request->session()->has('key'));
    dump($request->session()->exists('key'));
});

// |-----------------------------------------------------------------------------------
// |----configuration
// |----------------
Route::get('/configuration', function () {
    dump(App::environment());
    // ------------------read config files----------
    dump(config("app.locale", "default"));
    dump(config("app.timezone", "default"));
    // -------------------set config at runtime (temporary not permanent)
    config(['app.timezone' => 'Asia/tehran']);
    dump(config("app.timezone", "default"));

    //------------use it conditionally-----
    if (App::environment(['local', 'staging'])) {
        return "in dev mode";
    }
});

// |-----------------------------------------------------------------------------------
// |----helper functions and collections
// |----------------

route::get('/collection', function () {
    // --------collect function gives an object we can process as array
    // dump(collect([1, 2, 3, 5, 32])->min());
    // dump(collect([1, 2, 3, 5, 32])->max());
    // dump(collect([1, 2, 3, 5, 32])->avg());
    // dump(collect([1, 2, 3, 5, 32])->sum());
    // dump(collect([1, 2, 3, 5, 32])->count());
    // collect([1, 2, 3, 5, 32])->dump();
    // collect([1, 2, 3, 5, 32])->dd();

    // -------------------operate on collection---------------
    $collection = collect([1, 2, 3, 4, 5]);

    // -------------------- filter like array_filter()---------
    $filtered = $collection->filter(function (int $value, int $key) {
        return $key > 1;
    });
    // dump($filtered->all()); #gives normall array not object of collection
    // dump($filtered); gives  object of collection

    // -------------------- rejects instead filter!---------
    $rejected = $collection->reject(function (int $value, int $key) {
        return $key > 1;
    });
    // dump($rejected->all()); #gives normall array not object of collection
    // dump($rejected); #gives  object of collection

    // -------------------- map like array_map()---------
    $mapped = $collection->map(function (int $value) {
        return $value + 1;
    });
    // // dump($mapped->all()); #gives normall array not object of collection
    // // dump($mapped); #gives  object of collection

    // ----------transform :like array_map() but doesnt make new collection it changes current collection---------
    $collection->transform(function (int $item, $key) {
        return $item * 2;
    });
    // dump($collection->all()); #gives normall array not object of collection
    // // dump($mapped); #gives  object of collection


    // ----------each :like foreach---------
    $each = $collection->each(function (int $item) {
        echo $item * 2;
    });
    // dump($each->all()); #gives normall array not object of collection
    // // dump($mapped); #gives  object of collection
});


// |----------------
// |--sorting--chunk
// |----------------

Route::get('collection/sort-chunk/{chunk?}', function (int $chunk = 2) {
    $products = collect([
        ['id' => 1, 'name' => 'pink', 'price' => '100'],
        ['id' => 2, 'name' => 'red', 'price' => '150'],
        ['id' => 3, 'name' => 'brown', 'price' => '70'],
        ['id' => 4, 'name' => 'black', 'price' => '30'],
    ])->sortBy('price'); # many functions are like sort or sortByDesc and in use you'll learn it.
    // dump($products->all());

    return view('test.views.chunk', ['products' => $products, "chunk" => $chunk]);
});

// |----------------
// |----HELPERS!
// |----------------
Route::get('/helpers', function () {
    // path helpers is known by names
    dump(public_path('css\felan')); #pulic folder and we can also complete this as argument
    dump(app_path('felan')); #app folder and we can also complete this as argument
    dump(base_path('felan')); #root folder and we can also complete this as argument
    dump(config_path('felan')); #config folder and we can also complete this as argument
    dump(storage_path('felan')); #storage folder and we can also complete this as argument

    // link heplers

    dump(action([ViewController::class, 'basics'], ['param' => 'ok']));

    dump(asset(('file.jpg'))); #uses in view files

    dump(route('home', ['name' => 'ali'], $absolute = true)); # like action but for named routes instead controller methods

    dump(url('hali', ['ali'])); # for make a url with params
    dump(url()->current()); # for current url
    // dump(url()->full()); # with query string
});

// |----------------
// |----Str::helpers
// |----------------

Route::get('/helpers-string/{text?}', function (string $text = 'sample text') {
    dump(Str::limit($text, $limit = 5, $end = '  more...')); #to truncate
    dump(Str::mask($text, $character = '*', $index = -5, $length = 4)); #to mask and hide
    dump(Str::length($text)); #to mask and hide
    dump(Str::password($length = 8)); #to make password
    dump(Str::plural('go')); #to pluralize 
    dump(Str::singular('')); #to singular
    dump(Str::slug('go to room')); #to slugify
    dump(Str::reverse('go to room')); #to reverse  
});

// |----------------
// |----Miscellaneous::helpers
// |----------------
Route::get('/miscellaneous', function () {
    dump(app());
    dump(app('App\Services\ExampleService1')->serviceOneMethod()); #to get instance fronm app service container

    // to get current logged in user
    // dump(auth()->user());

    // to read config files
    dump(config($key = 'app.timezone', 'default'));

    // to get... from .env
    dump(env('APP_ENV', 'default'));

    // fake data for test application
    // dump(fake()->email());
    // dump(fake()->firefox());
    // dump(fake()->phoneNumber());
    // dump(fake('fr_FR')->firstName());

    // translate function
    dump(__('سلام'));

    // make query string
    dump(Arr::query([
        'me' => 'tierd'
    ]));

    // number for humans
    dump(Number::forHumans(1230000, precision: 2));

    // now
    dump(now()->addMinute(3));

    // --htmlform helpers-------

    dump(csrf_field());
    dump(method_field('delete'));

    // --------benchmark and lottery-------
    // Benchmark::dd([
    //     'senario1' => fn() => sleep(2),
    //     'senario2' => fn() => sleep(1)
    // ], iterations: 3);

    //  lottery is to make propability

    $chance = Lottery::odds(1, 2)->winner(fn() => 'you won a phone!')->loser(fn() => 'oh sorry');

    dump($chance->choose());

    // ----return once------
    // return once();
});


// |----------------
// |----Localization
// |----------------
Route::get('/localization/{locale?}', [LocaleController::class, 'locale']);

// |----------------
// |----Localization in real application step 1
// |----------------

Route::prefix('{locale?}')->group(function () {
    Route::get('address-1', function () {
        return __('hello :name', ['name' => 'akbar']) . route("localized-route-2");
    })->name('localized-route-1');
    Route::get('address-2', function () {
        return __('hello :name', ['name' => 'mmd']) . route("localized-route-1");
    })->name('localized-route-2');
});

// |----------------
// |----Localization in real application step 2
// |----------------

Route::get('/choose-languge/{locale}', function (Request $request, $locale = 'en') {
    $request->session()->put('app_locale', $locale);

    return back();
})->name('setlocale');


// |----------------
// |----Form & Validation
// |----------------

Route::get('/validation', function () {
    return view('validation.form');
});

Route::post('/validation', function (Request $request) {
    // dd($request->input());
    // $request->validate(['email' => 'email|unique:users,name_of_collumn']); #validate method redirects automaticly with old vlaue when form is not valid 
    $request->validate([
        'email' => 'email|unique:users,name_of_collumn',
        "title" => 'bail|required|string|max:10', #|fakeruleToTestBailRule bail rule at first helps to first fail and not check all rules 
        //  uploaded file validation
        // "photo" => "required|image",
        // "photo" => "mimetypes:image/jpeg",
        "photo" => "extensions:jpg,png|image",
        // -------------custom validation-----------
        "custom" => ['required', function (string $attribute, mixed $value, Closure $fail) {
            if ($value !== 'custom') {
                // $fail("can not pass {$value} as {$attribute}"); #to error in form 

                // Error translate
                $fail("validation.custom_rule")->translate(["name" => $attribute], 'fa'); # we have to write this error key(custom_rule) in lang/validation.php for all langs 
            }
        }],

        // -------------validation afield based on another field------------------
        "payment" => "required",
        "card_number" => "required_if:payment,cc",
        //  validating password and date

        "published_at" => "nullable|date",
        "start_date" => "required|date|after:tomorrow",
        // "password" => "current_password",
        "v_01" => "required"

        // ----------------------size rule-----------------

        // "string"=>"size:10"=>means chars should be 10
        // "integer"=>"size:10" field==10
        // "file"=>"size:10"=>means size of file should be 10kb
        // "array"=>"size:10"=>means array_count should be 10

    ]);

    dd($request->input());
});

// |----------------
// |----json requests validation
// |----------------
// request with content type=apllication/json accepts="Application/json" body json  
Route::post('/json-validation', function (Request $request) {
    if ($request->has('author')) {
        $request->validate([
            'author.name' => 'required|string|max:10'

        ]);
        return "request is valid ";
    };

    return " data not sent!";
})->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);


// |----------------
// |----custom Request classes
// |----------------

Route::post('/custom-request/{optionalParameter?}', function (CustomRequest $request) {
    if ($request->validated()) {
        return $request;
    }

    return "not valid";
})->withoutMiddleware(Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);


// |----------------
// |---- handle multiple forms in one page and one function
// |----------------

Route::get('/multiple-forms', fn() => view('validation.multipleForm'));

Route::post('/multiple-forms', function (Request $request) {
    if ($request->has('formOne')) {
        // $request->validate(['firstFormField' => "required"]);#not works
        $firstFormValidator = Validator::make($request->all(), ['firsFormField' => "required"]);
        if ($firstFormValidator->fails()) {
            return redirect()->back()->withErrors($firstFormValidator, "formOne");
        } else {
            dump($request->input());
        }
        dump('request one sent');
    } elseif ($request->has('formTwo')) {
        dump('request Two sent');
    }
});



// |----------------
// |---- Logging
// |----------------

Route::get('/logging', function () {
    // Log::info(" this is my info log message");
    // create custom log
    // Log::channel("myLog")->emergency(" this is my emergeny log message");
    // Log::channel("myLog")->alert(" this is my alert log message");
    // Log::channel("myLog")->critical(" this is my critical log message");
    // Log::channel("myLog")->error(" this is my error log message");
    // Log::channel("myLog")->warning(" this is my warning log message");
    // Log::channel("myLog")->notice(" this is my notice log message"); #this log will not be written in logs ile becuse the level of our channel is warning and each log that lelev uus under this not be written!
    // Log::channel("myLog")->info(" this is my info log message");
    // Log::channel("myLog")->debug(" this is my debug log message");

    Log::stack(['single', 'myLog'])->emergency("to a couple of channels");



    return "ok";
});


// |----------------
// |---- Errors
// |----------------

// builtin excetions handling


Route::get("/form-exception-handling", fn() => view('test.views.exceptionHandlingView'));

// Route::post("/form-exception-handling", function (Request $request) {
//     try {
//         return User::findOrFail($request->input('user', 1));
//     } catch (\Throwable $error) {
//         return back()->withError($error->getMessage())->withInput();
//     }
// });
//  ----or using repository pattern----
Route::post("/form-exception-handling", function (Request $request) {
    $userRepository = new class {
        public function getUser($id)
        {
            $user = User::find($id);

            if (!$user) {
                throw new ModelNotFoundException("user not found");
            }
        }
    };
    try {
        $user = $userRepository->getUser($request->input('user'));
    } catch (\Throwable $error) {
        return back()->withError($error->getMessage())->withInput();
    }
    return $user;
});

// |----------------
// |---- Custom Exception -> php artisan make:exception
// |----------------

Route::get("/custom-exception", function () {
    $missingData = null;
    if (!$missingData) {
        throw new CustomException("وای وای");
        // also we can use report($exception) when we not allowed to throw!
    }
});



// |----------------
// |---- Cache
// |----------------
Route::get('/cache-basics', function () {
    // put data in cache
    // Cache::put('10_secound_available', 'value_of_cache', $secounds = 10);
    #-----or
    // Cache::put('10_secound_available', 'value_of_cache', now()->addSeconds(4));

    // put with custom driver
    // Cache::store('array')->put('array', 'array cache', $secounds = 10);

    // get data from cache
    $data = Cache::get('10_secound_available', "default");
    dump($data); #this can be replaced with cache($key,$default) helper

    // remember cache
    // $value = Cache::remember("remember", $secounds = 12, function () {
    //     dump('callback of remember is executed!');
    //     return "value of remember cache";
    // });
    // get method have call  back but not store in cache
    $value = Cache::get("remember", function () {
        dump('this message is seen untill we have not this key in cache');
        return "value of remember cache";
    });
    dump($value);

    // --delete data from cache--
    Cache::forget("reza");
    // Cache::flush();#delete entire cache

});

// -------------------->HTTP Cache<-------------
Route::middleware("cache.headers:public;max_age=2628000;etag")->group(function () {
    Route::get('/http-cache', function () {
        return response("http-cache");
    });
});



// |----------------
// |---- file system
// |----------------

// --------------put content-----------

Route::get('file/put/{content?}', function (string|int|null $content = null) {
    if ($content) {
        // Storage::put('fileSystem/file.txt', "content one{$content}");
        // or another disk
        Storage::disk('public')->put('fileSystem/file.txt', "content one{$content}");
        // -also we can use more methods
        Storage::disk('public')->prepend('fileSystem/file.txt', "prepend{$content}");
        Storage::disk('public')->append('fileSystem/file.txt', "append{$content}");

        // -----get contents or download files----
        $content = Storage::disk('public')->get('fileSystem/file.txt');
        // dump($content);
        // return  Storage::disk('public')->download('fileSystem/file.txt');
        // copy move delete
        // $res = Storage::disck('public')->copy("fileSystem/file.txt", "fileSystem/fileCopy.txt"); #file will be copied and $res is true or false
        // $res = Storage::disck('public')->move("fileSystem/file.txt", "fileSystem/fileCopy.txt"); #file will be moved and $res is true or false
        // $res = Storage::disck('public')->delete("fileSystem/file.txt"); #file will be deleted and $res is true or false


    }
    return "content not valid";
});

//------------file properties-----------

Route::get("/file/properties", function () {
    // return Storage::disk('public')->mimeType('fileSystem/file.txt');
    // return Storage::disk('public')->size('fileSystem/file.txt');#kb
    // return Storage::disk('public')->path('fileSystem/file.txt');#absolute path
    return asset(('storage/fileSystem/file.txt')); #link to show you have to ->php artisan storage:link
});

// |----------------
// |---- console commands
// |----------------

//  php artisan
// php artisan tinker
// php optimize/cache/route/config:cleare/cache
// php artisan down/up --secret=ksdjfhkjfds
// php artisan make:class or trait
// php artisan stub:publish
// php artisan make:command