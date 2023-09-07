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

$router->get('/typebeer', 'TypeBeersController@index');
$router->get('/typebeer/{type}', 'TypeBeersController@show');
$router->post('/typebeer', 'TypeBeersController@store');
$router->put('/typebeer/{type}', 'TypeBeersController@update');
$router->delete('/typebeer/{type}', 'TypeBeersController@destroy');

$router->get('/beer', 'BeersController@index');
$router->get('/beer/{name}', 'BeersController@show');
$router->post('/beer', 'BeersController@store');
$router->put('/beer/{id}', 'BeersController@update');
$router->delete('/beer/{id}', 'BeersController@destroy');
