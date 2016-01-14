<?php
Route::post('/kandy/getNameForContact', 'Kodeplus\Kandylaravel\KandyController@getNameForContact');

Route::post('/kandy/getNameForChatContent', 'Kodeplus\Kandylaravel\KandyController@getNameForChatContent');

Route::get('/kandy/getUsersForSearch', 'Kodeplus\Kandylaravel\KandyController@getUsersForSearch');

Route::post('/kandy/registerGuest', 'Kodeplus\Kandylaravel\KandyController@registerGuest');

Route::get('/kandy/getFreeUser', 'Kodeplus\Kandylaravel\KandyController@getFreeUser');

Route::get('/kandy/endChatSession', array('as' => 'kandy.endChatSession', 'uses' => 'Kodeplus\Kandylaravel\KandyController@endChatSession'));

Route::get('/kandy/getUserForAgent', 'Kodeplus\Kandylaravel\KandyController@getUsersForChatAgent');

Route::post('/kandy/rateagent', 'Kodeplus\Kandylaravel\KandyController@rateAgent');

Route::get('/kandy/updateUserStatus','Kodeplus\Kandylaravel\KandyController@updateUserStatus');
Route::get('/kandy/stillAlive','Kodeplus\Kandylaravel\KandyController@stillAlive');

Route::post('/kandy/updatePresence', 'Kodeplus\Kandylaravel\KandyController@updatePresence');

Route::post('/kandy/getPresenceStatus', 'Kodeplus\Kandylaravel\KandyController@getPresenceStatus');

Route::get('/kandy/checkAgentOnline','Kodeplus\Kandylaravel\KandyController@checkAgentOnline');