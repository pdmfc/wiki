<?php

use Illuminate\Support\Facades\Route;

// Built-in Search..
Route::get('/search-index/{version}/{role?}', 'SearchController')->name('search');

// Styles & Scripts..
Route::get('/styles/{style}', 'StyleController')->name('styles');
Route::get('/scripts/{script}', 'ScriptController')->name('scripts');

// Documentation..
Route::get('/', 'DocumentationController@index')->name('index');
Route::get('/{version}/{role?}/{page?}', 'DocumentationController@show')->where('page', '(.*)')->name('show');
