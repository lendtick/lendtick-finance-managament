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

$router->get('/', function () use ($router) {
    // return $router->app->version();
    return redirect('/api/documentation');
});
$router->get('/api', function () use ($router) {
    // return $router->app->version();
    return redirect('/api/documentation');
});

$router->group(['prefix'=>'doku'], function() use($router){
    // request for doku
    $router->post('/va/inquiry', 'InquiryDokuController@request');
    // notify from doku
    $router->post('/va/notify', 'DokuController@notify');


    // request for doku
    $router->post('/va/request', 'DokuController@request');

    // test
    $router->get('/test','BillerController@index');
});

// biller

$router->group(['prefix'=>'biller'], function() use($router){
    // list all
    $router->get('/list','BillerController@listBiller');

    // ubah biller
    $router->post('/update','BillerController@updateBiller');

    // check balance biller
    $router->post('/balance','BillerBalanceController@check');

    // check biller inquiry
    $router->post('/inquiry','BillerInquiryController@store');

    // check biller inquiry
    $router->post('/inquiry-electricity-postpaid','BillerInquiryController@store');
     
    
});
