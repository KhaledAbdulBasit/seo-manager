<?php

Route::group([
    'middleware' => /** @scrutinizer ignore-call */config('seo-manager.middleware'),
    'prefix' => /** @scrutinizer ignore-call */config('seo-manager.route'),
    'as' => 'seo-manager.',
    'namespace' => 'Lionix\SeoManager'
], function () {
    Route::get('/', 'ManagerController@index')->name('home');
    Route::get('get-routes', 'ManagerController@getRoutes')->name('get-routes');
    Route::get('import-routes', 'ImportController')->name('import');
    Route::post('delete-route', 'ManagerController@deleteRoute')->name('delete-route');
    Route::get('get-models', 'ManagerController@getModels')->name('get-models');
    Route::post('get-model-columns', 'ManagerController@getModelColumns')->name('get-model-columns');
    Route::post('store-data', 'ManagerController@storeData')->name('store-data');
    Route::post('get-example-title', 'ManagerController@getExampleTitle')->name('get-example-title');
    Route::group(['prefix' => 'locales', 'as' => 'locales.'], function () {
        Route::get('get-locales', 'LocalesController@getLocales')->name('get');
        Route::post('add-locale', 'LocalesController@addLocale')->name('add');
    });
});
