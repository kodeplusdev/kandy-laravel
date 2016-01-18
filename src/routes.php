<?php
Route::post('/kandy/getNameForContact', 'Kodeplusdev\Kandylaravel\KandyController@getNameForContact');

Route::post('/kandy/getNameForChatContent', 'Kodeplusdev\Kandylaravel\KandyController@getNameForChatContent');

Route::get('/kandy/getUsersForSearch', 'Kodeplusdev\Kandylaravel\KandyController@getUsersForSearch');

Route::post('/kandy/registerGuest', 'Kodeplusdev\Kandylaravel\KandyController@registerGuest');

Route::get('/kandy/getFreeUser', 'Kodeplusdev\Kandylaravel\KandyController@getFreeUser');

Route::get('/kandy/endChatSession', array('as' => 'kandy.endChatSession', 'uses' => 'Kodeplusdev\Kandylaravel\KandyController@endChatSession'));

Route::get('/kandy/getUserForAgent', 'Kodeplusdev\Kandylaravel\KandyController@getUsersForChatAgent');

Route::post('/kandy/rateagent', 'Kodeplusdev\Kandylaravel\KandyController@rateAgent');

Route::get('/kandy/updateUserStatus','Kodeplusdev\Kandylaravel\KandyController@updateUserStatus');
Route::get('/kandy/stillAlive','Kodeplusdev\Kandylaravel\KandyController@stillAlive');

Route::post('/kandy/updatePresence', 'Kodeplusdev\Kandylaravel\KandyController@updatePresence');

Route::post('/kandy/getPresenceStatus', 'Kodeplusdev\Kandylaravel\KandyController@getPresenceStatus');

Route::get('/kandy/checkAgentOnline','Kodeplusdev\Kandylaravel\KandyController@checkAgentOnline');