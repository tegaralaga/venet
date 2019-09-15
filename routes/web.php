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
                $router->get('/{id:[0-9]+}', ['uses' => 'VenueController@venue_info']);
                $router->post('create', ['uses' => 'VenueController@create']);
                $router->post('contact/create', ['uses' => 'VenueController@create_contact']);
                $router->post('venue_type/create', ['uses' => 'VenueController@create_venue_type']);
            });
            $router->group(['prefix' => 'event'], function () use ($router) {
                $router->get('/', ['uses' => 'MiscellaneousController@index']);
                $router->post('ticket/create', ['uses' => 'EventController@ticket_create']);
                $router->get('ticket/{id:[0-9A-Za-z]+}', ['uses' => 'EventController@ticket_info']);
                $router->post('create', ['uses' => 'EventController@create']);
                $router->post('toggle', ['uses' => 'EventController@toggle']);
                $router->post('contact/create', ['uses' => 'EventController@create_contact']);
                $router->post('type/create', ['uses' => 'EventController@create_type']);
                $router->post('tag/create', ['uses' => 'EventController@create_tag']);
                $router->post('rule/create', ['uses' => 'EventController@create_rule']);
                $router->get('{id:[0-9A-Za-z]+}', ['uses' => 'EventController@event_info']);
            });
            $router->group(['prefix' => 'transaction'], function () use ($router) {
                $router->get('/', ['uses' => 'MiscellaneousController@index']);
                $router->post('purchase', ['uses' => 'TransactionController@purchase']);
                $router->get('{id:[0-9A-Za-z]+}', ['uses' => 'TransactionController@transaction_info']);
            });
            $router->group(['prefix' => 'line_up'], function () use ($router) {
                $router->get('/', ['uses' => 'MiscellaneousController@index']);
                $router->get('/{id:[0-9]+}', ['uses' => 'LineUpController@index']);
                $router->post('create', ['uses' => 'LineUpController@create']);
                $router->post('contact/create', ['uses' => 'LineUpController@create_contact']);
            });
            $router->group(['prefix' => 'organizer'], function () use ($router) {
                $router->get('/', ['uses' => 'MiscellaneousController@index']);
                $router->get('/{id:[0-9]+}', ['uses' => 'OrganizerController@index']);
                $router->post('create', ['uses' => 'OrganizerController@create']);
                $router->post('contact/create', ['uses' => 'OrganizerController@create_contact']);
            });
        });
    });
});