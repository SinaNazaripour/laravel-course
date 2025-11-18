<?php

use App\Http\Controllers\InvokableController;
use App\Http\Controllers\PhotoCommentsController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TestMwController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use App\Http\Middleware\BeforeAfterMiddleware;
use App\Http\Middleware\ExampleMiddleware;
use App\Models\User;
use App\Services\ExampleInterface;
use App\Services\ExampleService1;
use App\Services\NotificationDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Psy\Command\DumpCommand;


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
Route::apiResource('photo', PhotoController::class); #only reqired for api except any method that render html file

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
// |----define middleware in controlle with implementing hasmiddleware interface
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
        dump($request->query('a', 'defaul'));
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
        dump($request->input('user.name'));
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
    // $name=$request->file('photo')->getClientOriginalPath()

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
