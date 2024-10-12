<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AssinanteController;
use App\Http\Controllers\PesquisarController;
use App\Http\Controllers\VideoRentController;
use App\Http\Controllers\Api\AoVivoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GerenciarPerfilController;
use App\Http\Controllers\Api\PingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return view('welcome');
});



Route::middleware(['auth', 'verified'])->group(function () {
 Route::middleware(['access.report', 'verified'])->group(function () {

  Route::get('/user/authenticate',   [AssinanteController::class, 'login']);

  // chamadas na api que precisam receber request com sessao do usuario nao estao no grupo api
  Route::get('/api/v1/schedule',              [AoVivoController::class, 'schedule'])->name('aovivo.schedule');
  Route::get('/api/v1/first/ep/{serie_id}',   [VideoController::class, 'getFirstEp']);

  Route::get('/api/v1/ping/status',          [PingController::class, 'status'])->name('ping.status');
  Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

  Route::middleware('user.limit')->group(function () {

    Route::middleware(['parental.check', 'user.limit', 'video.isrent'])->group(function () {
      Route::get('/video/{id}',               [VideoController::class, 'showVideo'])->name('video');
      Route::get('/video/series/{id}',        [VideoController::class, 'showVideoSeries'])->name('video.series');
      Route::get('/video/playseries/{id}',    [VideoController::class, 'showVideoSeriesPlay'])->name('video.series_play');
      Route::get('/video/ao_vivo/{id}',       [VideoController::class, 'showVideoAovivo'])->name('video_aovivo');
    });

    Route::post('upload', [UploadController::class, 'index'])->name('upload.index');

    Route::get('/videorent/{id}/{kind}',            [VideoRentController::class, 'showVideo'])->name('videorent.show');
    Route::post('/videorent/{id}/{kind}/confirm',   [VideoRentController::class, 'rentConfirm'])->name('videorent.confirm');

    //Route::get('/home/filmes', [HomeController::class, 'moviesIndex'])->name('home_filmes');

    Route::middleware('user.limit')->group(function () {

      Route::get('/home/filmes/favorito',         [HomeController::class, 'moviesIndexFavorito'])->name('home_filmes_categoria');
      Route::get('/home/series/favorito',         [HomeController::class, 'seriesIndexFavorito'])->name('home_filmes_categoria');
      Route::get('/home/filmes/aluguel',          [HomeController::class, 'moviesIndexAluguel'])->name('home_filmes_categoria');
      Route::get('/home/filmes/aluguel_movies',   [HomeController::class, 'moviesIndexAluguel'])->name('home_filmes_categoria');
      Route::get('/home/series/aluguel',          [HomeController::class, 'seriesIndexAluguel'])->name('home_filmes_categoria');
      Route::get('/home/series/aluguel_series',   [HomeController::class, 'seriesIndexAluguel'])->name('home_filmes_categoria');

      Route::middleware('parental.check')->group(function () {
        Route::get('/home/filmes/{category}',   [HomeController::class, 'moviesIndexCategory'])->name('home_filmes_categoria');
        Route::get('/home/series/{category}',   [HomeController::class, 'seriesIndexCategory'])->name('home_series_categoria');
      });

      Route::get('/home/ao_vivo',         [HomeController::class, 'aoVivoIndex'])->name('home_ao_vivo');
      Route::get('/home/ao_vivo_radios',  [HomeController::class, 'aoVivoRadioIndex'])->name('home_ao_vivo_radios');
      Route::get('/home/filmes',          [HomeController::class, 'moviesIndex'])->name('home_filmes');
      Route::get('/home/desenhos',        [HomeController::class, 'seriesDesenhosIndex'])->name('home_desenhos');
      Route::get('/home/series',          [HomeController::class, 'seriesIndex'])->name('home_series');
      Route::get('/home/loja',            [HomeController::class, 'lojaIndex'])->name('home_loja');
    });

    Route::get('/checkout/index',                   [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/confirm',                [CheckoutController::class, 'confirm'])->name('checkout.confirm');

    Route::get('/user/settings', [HomeController::class, 'soon'])->name('user_settings');
    Route::get('/home/favorite', [HomeController::class, 'favorite'])->name('home_favorite');

    Route::get('/user/profile',  [AssinanteController::class, 'profile'])->name('user_profile');
    Route::get('/user/parental_profile',  [AssinanteController::class, 'profileParentalPassword'])->name('user_profile_parental_password');
    Route::post('/user/parental_profile',  [AssinanteController::class, 'profileParentalPassword'])->name('user_profile_parental_password_update');
    Route::get('/user/parental_profile_request',  [AssinanteController::class, 'profileParentalPasswordRequest'])->name('user_profile_parental_password_request');
    Route::post('/user/parental_profile_request',  [AssinanteController::class, 'profileParentalPasswordRequest'])->name('user_profile_parental_password_request_check');

    Route::get('/user/new_password',              [AssinanteController::class, 'newPassword'])->name('user_profile_new_password');
    Route::post('/user/new_password',             [AssinanteController::class, 'newPassword'])->name('user_profile_change_password');
    Route::get('/user/info',                      [AssinanteController::class, 'updateInfo'])->name('user_profile_update_info');
    Route::post('/user/info',                     [AssinanteController::class, 'updateInfo'])->name('user_profile_update_info');
    Route::get('/pesquisar/{string?}',            [PesquisarController::class, 'index'])->name('pesauisar');


    Route::get('perfil/gerenciar', [GerenciarPerfilController::class, 'index'])->name('perfil.gerenciar');
    Route::resource('perfil', PerfilController::class);
  });

  Route::get('user/devices', [AssinanteController::class, 'listDevicesConnected'])->name('user_devices');

  Route::delete('user/devices/{id}', [AssinanteController::class, 'deleteDevice'])->name('user_devices_delete');
 });
});///

Route::get('/home',             [HomeController::class, 'index'])->name('home');
Route::get('/home/noplan',      [HomeController::class, 'noPlan'])->name('home.noplan');
Route::get('/app/ios',          [HomeController::class, 'appIos'])->name('home.appios');
Route::get('/app/android',      [HomeController::class, 'appAndroid'])->name('home.appandroid');
Route::get('/privacidade',      [HomeController::class, 'privacidade'])->name('home.privacidade');
Route::get('/privacidade-kids', [HomeController::class, 'privacidade_kids'])->name('home.privacidade-kids');
Route::get('/suporte',          [HomeController::class, 'suporte'])->name('home.suporte');



//Route::get('/home/marketplace', [HomeController::class, 'soon'])->name('home_marketplace');



Route::get('/login',                [AssinanteController::class, 'login'])->name('login');
Route::get('/user/login',           [AssinanteController::class, 'login'])->name('assinante.login');
Route::get('/user/logout',          [AssinanteController::class, 'logout'])->name('assinante.logout');
Route::get('/user/new',                [AssinanteController::class, 'new'])->name('assinante.new');
Route::post('/user/create',         [AssinanteController::class, 'create'])->name('assinante.create');
Route::post('/user/authenticate',   [AssinanteController::class, 'authenticate'])->name('assinante.authenticate');
Route::get('/user/terms',           [AssinanteController::class, 'terms'])->name('assinante.terms');
Route::get('/user/rentals',         [VideoRentController::class, 'rentals'])->name('videorent.rentals');



Route::get('/forgot-password',              [AssinanteController::class, 'forgotPassword'])->name('forgot_password');
Route::post('/forgot-password',             [AssinanteController::class, 'forgotPassword'])->name('forgot_password');
Route::get('/reset-password/{id}/{token}',  [AssinanteController::class, 'resetPassword'])->name('reset_password');
Route::post('/reset-password/{id}/{token}', [AssinanteController::class, 'resetPassword'])->name('reset_password');
Route::get('/verify-email',                 [AssinanteController::class, 'verificationEmail'])->name('verification.notice');
Route::post('/verify-email/request',        [AssinanteController::class, 'verificationEmailRequest'])->name('verification.request')
  ->middleware(['auth']);
Route::get('/verify-email/{id}/{hash}',     [AssinanteController::class, 'verificationEmailVerify'])
  ->middleware(['auth'])
  ->name('verification.verify');


Route::get('/fpp', function () {
  $sub = App\Models\Assinante::where('email', 'slv.eziel@gmail.com')->first();
  $app = App\Models\App::find(2);

  $token = $sub->resetPasswordToken();
  $link  = route('reset_password', ['id' => $sub->id, 'token' => $token, 'app' => $app->id]);

  return new App\Mail\ForgotPassword($sub, $app, $link);
});

Route::get('/qr', [HomeController::class, 'showQRCode'])->name('home.show_qrcode');

Route::get('/', [HomeController::class, 'index']);
