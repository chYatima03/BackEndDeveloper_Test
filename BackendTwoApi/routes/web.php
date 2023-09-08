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

$router->get('/parent', 'ParentController@index');
$router->get('/parent/{parent}', 'ParentController@show');
$router->post('/parent', 'ParentController@store');
$router->put('/parent/{parent}', 'ParentController@update');
$router->delete('/parent/{parent}', 'ParentController@destroy');

$router->get('/children', 'ChildrenController@index');
$router->get('/children/{children}', 'ChildrenController@show');
$router->post('/children', 'ChildrenController@store');
$router->put('/children/{children}', 'ChildrenController@update');
$router->delete('/children/{children}', 'ChildrenController@destroy');
