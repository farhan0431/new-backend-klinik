<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/'], function () use ($router) {
    $router->group([
        'prefix' => '/auth'
    ], function () use ($router) {
        $router->post('login', 'AuthController@login');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->get('me', 'AuthController@me');
        $router->post('profile', 'AuthController@profile');

        $router->post('changepassword', 'AuthController@changePassword');

        $router->post('/register', 'UserController@store');

        $router->get('/testing', 'LaporanController@testing');
    });

    $router->group([
        'prefix' => '/home'
    ], function () use ($router) {
        $router->get('year', 'HomeController@new_year');
        $router->get('new-year', 'HomeController@year');
        $router->get('new-month', 'HomeController@month');
        $router->get('month', 'HomeController@new_month');
        $router->get('get-data', 'HomeController@getMoreData');
        $router->get('settings', 'SettingsController@index');
        $router->get('load', 'HomeController@load_data');
        
        
    });

    // $router->group([
    //     'prefix' => '/realisasi-pendapatan'
    // ], function () use ($router) {
    //     $router->get('/', 'RealisasiPendapatan@index');
    // });

    $router->get('/realisasi-pendapatan', 'RealisasiPendapatan@index');
    $router->get('/realisasi-perhari','RealisasiPendapatan@per_hari');

    $router->get('laporan/dashboard', 'LaporanController@bulanIni');

    
    $router->group(['middleware' => 'auth:api'], function() use ($router) {
        

        $router->group([
            'prefix' => '/janji'
        ], function() use ($router) {
            $router->get('/', 'JanjiController@index');
            $router->post('/', 'JanjiController@store');
            $router->put('/', 'JanjiController@update');
            $router->delete('/{id}', 'JanjiController@delete');
            $router->get('/active','JanjiController@activeJanji');
            $router->post('/batal','JanjiController@batalJanji');
            $router->post('/resep', 'JanjiController@resepSend');
            $router->get('/resep', 'JanjiController@getResep');
            $router->get('/berita', 'JanjiController@berita');
            $router->get('/data-berita','JanjiController@getBerita');

            
            
        });

        $router->group([
            'prefix' => '/dokter'
        ], function() use ($router) {
            $router->get('/', 'DokterController@index');
            $router->get('/get-janji','DokterController@getJanjiDokter');
            $router->post('/', 'DokterController@store');
            $router->put('/', 'DokterController@update');
            $router->delete('/{id}', 'DokterController@delete');
            $router->post('/kartu','DokterController@storeKartuBerobat');
            $router->get('/kartu','DokterController@getKartu');


            $router->get('/jadwal/{id}', 'DokterController@jadwal');
            
            
        });


        $router->group([
            'prefix' => '/identitas'
        ], function() use ($router) {
            $router->post('/update', 'IdentitasController@update');
        });


        $router->group([
            'prefix' => '/chat'
        ], function() use ($router) {
            $router->get('/','ChatController@index');
            $router->post('/','ChatController@store');
            $router->post('/dokter','ChatController@storeDokter');
            $router->get('/get-chat','ChatController@getChat');
            $router->Get('/get-chat-dokter','chatController@chatDokter');
        });

        


       

        $router->group([
            'prefix' => '/user'
        ], function() use ($router) {
            $router->get('/', 'UserController@index');
            $router->post('/', 'UserController@store');
            $router->put('/', 'UserController@update');
            $router->post('/update', 'UserController@editData');
            $router->delete('/{id}', 'UserController@delete');
            $router->get('/roles','UserController@roles');
            $router->post('/upload', 'UserController@uploadPicture');
            $router->post('/token','UserController@setToken');
            $router->get('/notif','UserController@notif');
            $router->post('/notif','UserController@readNotif');

        });

        

        $router->group([
            'prefix' => '/settings'
        ], function() use ($router) {
            $router->get('/', 'SettingsController@index');
            $router->put('/', 'SettingsController@update');
            $router->post('/upload', 'SettingsController@uploadLogo');
            $router->get('/provinsi', 'SettingsController@getProvinsi');
            $router->get('/kota/{id}','SettingsController@getKota');
        });



        $router->group([
            'prefix' => '/admin'
        ], function() use ($router) {
            $router->get('/', 'AdminController@index');
            $router->get('/janji','AdminController@getJanji');
            $router->get('/dokter','AdminController@getDokter');
            $router->post('/upload','AdminController@uploadGambar');
            $router->post('/dokter','AdminController@updateDokter');
            $router->put('/berita','AdminController@updateBerita');
            $router->post('/upload-berita','AdminController@uploadGambarBerita');
            $router->post('/berita','AdminController@storeBerita');
        });
    });

});
