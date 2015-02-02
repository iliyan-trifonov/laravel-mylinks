<?php

Route::get('/', [
    'as' => 'home',
    'uses' => 'MyLinks\Controllers\LinksController@home'
]);

Route::get('/login', [
    'as' => 'login',
    'uses' => 'MyLinks\Controllers\UserController@showLogin'
]);

Route::get('/register', [
    'as' => 'register',
    'uses' => 'MyLinks\Controllers\UserController@showRegister'
]);

Route::group(['before' => 'csrf'], function() {

    Route::post('/login', [
        'uses' => 'MyLinks\Controllers\UserController@postLogin'
    ]);

    Route::post("/register", [
        'uses' => 'MyLinks\Controllers\UserController@register'
    ]);
});

Route::group(['before' => 'auth'], function(){

    Route::get("/logout", [
        'uses' => 'MyLinks\Controllers\UserController@logout'
    ]);

    Route::get("/links/{id}/delete", [
        'uses' => 'MyLinks\Controllers\LinksController@showDeleteLink'
    ])->where("id", '[\d]+');

    Route::get('/search', [
        'as' => 'search',
        'uses' => 'MyLinks\Controllers\LinksController@search'
    ]);

    Route::get("/profile", [
        "as" => "profile",
        'uses' => 'MyLinks\Controllers\UserController@showProfile'
    ]);

    Route::group(['before' => 'csrf'], function(){
        Route::post('/', [
            'uses' => 'MyLinks\Controllers\LinksController@addLink'
        ]);

        Route::delete("/links/{id}/delete", [
            'uses' => 'MyLinks\Controllers\LinksController@deleteLink'
        ])->where("id", '[\d]+');

        Route::put("/profile", [
            'uses' => 'MyLinks\Controllers\UserController@updateProfile'
        ]);
    });

});
