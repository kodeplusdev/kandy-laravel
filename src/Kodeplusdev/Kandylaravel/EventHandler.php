<?php
/**
 * Created by PhpStorm.
 * User: Khanh
 * Date: 23/7/2015
 * Time: 4:59 PM
 */

namespace Kodeplusdev\Kandylaravel;
use Event;

class EventHandler {
    public function onUserLogin($user)
    {
        $kandyUser = KandyUsers::where('main_user_id', $user->id)->first();
        //if login user is a chat agent
        if($kandyUser->type == Kandylaravel::USER_TYPE_CHAT_AGENT) {
            $model = KandyUserLogin::where('kandy_user_id', $kandyUser->user_id)->first();
            $remoteIp = $_SERVER['REMOTE_ADDR'];
            $now = time();
            if(!$model) {
                KandyUserLogin::create(array(
                    'kandy_user_id' => $kandyUser->user_id,
                    'type' => Kandylaravel::USER_TYPE_CHAT_AGENT,
                    'status' => Kandylaravel::USER_STATUS_ONLINE,
                    'browser_agent' => $_SERVER["HTTP_USER_AGENT"],
                    'ip_address' => $remoteIp,
                    'time'      => $now,
                ));
            } else {
                $model->update(array(
                    'status'    => Kandylaravel::USER_STATUS_ONLINE,
                    'time'      => $now,
                    'ip_address'=> $remoteIp,
                    'browser_agent' => $_SERVER["HTTP_USER_AGENT"],
                ));
            }
        }
        
    }

    public function onUserLogout($user){
        $kandyUser = KandyUsers::where('main_user_id', $user->id)->first();
        //if login user is a chat agent
        if($kandyUser->type == Kandylaravel::USER_TYPE_CHAT_AGENT) {
            $userLogin = KandyUserLogin::where('kandy_user_id', $kandyUser->user_id)->first();
            $userLogin->status = Kandylaravel::USER_STATUS_OFFLINE;
            $userLogin->save();
        }
    }

    public function subscribe($events)
    {
        $events->listen('auth.login', '\Kodeplusdev\Kandylaravel\EventHandler@onUserLogin');

        $events->listen('auth.logout', '\Kodeplusdev\Kandylaravel\EventHandler@onUserLogout');
    }

} 