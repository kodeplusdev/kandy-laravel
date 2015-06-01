<?php
Route::post('/kandy/getNameForContact', 'Kodeplusdev\Kandylaravel\KandyController@getNameForContact');

Route::post('/kandy/getNameForChatContent', 'Kodeplusdev\Kandylaravel\KandyController@getNameForChatContent');

Route::get('/kandy/getUsersForSearch', 'Kodeplusdev\Kandylaravel\KandyController@getUsersForSearch');

Route::post('/kandy/registerGuest', 'Kodeplusdev\Kandylaravel\KandyController@registerGuest');

Route::get('/kandy/getFreeUser', 'Kodeplusdev\Kandylaravel\KandyController@getFreeUser');

Route::get('/kandy/endChatSession', 'Kodeplusdev\Kandylaravel\KandyController@endChatSession');

Route::get('/kandy/chatting', 'Kodeplusdev\Kandylaravel\KandyController@updateChatSession');
Route::get('/kandy/getUserForAgent', 'Kodeplusdev\Kandylaravel\KandyController@getUsersForChatAgent');

