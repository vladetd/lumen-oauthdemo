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

$router->post('/auth/access_token', 'AuthController@issueAccessToken');

$router->group(['middleware' => 'oauth'], function () use ($router) {
    $router->get('/', function ()    {
        return 1;
    });
});


