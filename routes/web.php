<?php

use App\Http\Controllers\InvokableController;
use App\Http\Controllers\PhotoCommentsController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViewController;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

Route::get('/', function () {
    return view('welcome');
});

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
// |----views
// |----------------------
Route::get('/view', [ViewController::class, 'basics']);
