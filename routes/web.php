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
    });

    
    $router->group(['middleware' => 'auth:api'], function() use ($router) {
        $router->group([
            'prefix' => '/home'
        ], function () use ($router) {
            $router->get('year', 'HomeController@year');
            $router->get('month', 'HomeController@month');
            $router->get('get-data', 'HomeController@getMoreData');
        });

        $router->group([
            'prefix' => '/target'
        ], function() use ($router) {
            $router->get('/', 'TargetController@index');
        });
    });

});
