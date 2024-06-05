<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\SalesController;

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

$router->get('/sales','SalesController@index');
$router->post('/sales', 'SalesController@store');
$router->get('/sales/{id}','SalesController@show');
$router->get('/sales/find/{id}','SalesController@findById');
$router->put('/sales/{id}', 'SalesController@update');
$router->delete('/sales/{id}', 'SalesController@destroy');


$router->get('/refunds','RefundsController@index');
$router->post('/refunds', 'RefundsController@store');
$router->get('/refunds/{id}', 'RefundsController@show');
$router->put('/refunds/{id}', 'RefundsController@update');
$router->delete('/refunds/{id}', 'RefundsController@destroy');

$router->get('/detailsale','DetailSaleController@index');
$router->post('/detailsale', 'DetailSaleController@store');
$router->get('/detailsale/{id}', 'DetailSaleController@show');
$router->put('/detailsale/{id}', 'DetailSaleController@update');
$router->delete('/detailsale/{id}', 'DetailSaleController@destroy');

$router->post('/import', 'ImportController@import');
$router->post('/importrefunds', 'ImportController@importRefunds');
$router->post('/importdetail', 'ImportController@importDetailSale');