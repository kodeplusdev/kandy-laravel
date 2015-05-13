<?php
Route::post('/kandy/getNameForContact', 'Kodeplusdev\Kandylaravel\KandyController@getNameForContact');

Route::post('/kandy/getNameForChatContent', 'Kodeplusdev\Kandylaravel\KandyController@getNameForChatContent');

Route::get('/kandy/getUsersForSearch', 'Kodeplusdev\Kandylaravel\KandyController@getUsersForSearch');