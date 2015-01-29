<?php

Route::get('/', [
    'as' => 'home',
    'uses' => 'LinksController@home'
]);

Route::get('/login', [
    'as' => 'login',
    'uses' => 'UserController@showLogin'
]);

Route::get('/register', [
    'as' => 'register',
    'uses' => 'UserController@showRegister'
]);

Route::group(['before' => 'csrf'], function() {

    Route::post('/login', [
        'uses' => 'UserController@postLogin'
    ]);

    Route::post("/register", [
        'uses' => 'UserController@register'
    ]);
});

Route::group(['before' => 'auth'], function(){

    Route::get("/logout", [
        'uses' => 'UserController@logout'
    ]);

    Route::get("/links/{id}/delete", [
        'uses' => 'LinksController@showDeleteLink'
    ])->where("id", '[\d]+');

    Route::get('/search', [
        'as' => 'search',
        'uses' => 'LinksController@search'
    ]);

    Route::get("/profile", [
        "as" => "profile",
        'uses' => 'UserController@showProfile'
    ]);

    Route::group(['before' => 'csrf'], function(){
        Route::post('/', [
            'uses' => 'LinksController@addLink'
        ]);

        Route::delete("/links/{id}/delete", [
            'uses' => 'LinksController@deleteLink'
        ])->where("id", '[\d]+');

        Route::put("/profile", [
            'uses' => 'UserController@updateProfile'
        ]);
    });

});
