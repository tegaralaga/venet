<?php

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
$router->group(['prefix' => '/', 'namespace' => 'Rest'], function () use ($router) {
    $router->get('/', ['as' => 'index', 'uses' => 'MiscellaneousController@index']);
    $router->group(['prefix' => 'api'], function () use ($router) {
        $router->get('/', ['uses' => 'MiscellaneousController@index']);
        $router->group(['prefix' => 'v1'], function () use ($router) {
            $router->get('/', ['uses' => 'MiscellaneousController@index']);
            $router->group(['prefix' => 'location'], function () use ($router) {
                $router->get('/', ['uses' => 'MiscellaneousController@index']);
                $router->get('/{id:[0-9]+}', ['uses' => 'VenueController@index']);
                $router->post('create', ['uses' => 'VenueController@create']);
            });
        });
    });
});