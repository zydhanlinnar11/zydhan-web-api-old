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


$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->get('{provider_name}/redirect', ['as' => 'auth_redirect', 'uses' => 'AuthController@handle_redirect']);
    $router->get('{provider_name}/callback', ['as' => 'auth_callback', 'uses' => 'AuthController@handle_callback']);
});