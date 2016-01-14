<?php
namespace Kodeplus\Kandylaravel;


class EventHandler {
    public function onUserLogin($user)
    {
        $kandyUser = KandyUsers::where('main_user_id', $user->id)->first();
        //if login user is a chat agent
        if($kandyUser) {
            $model = KandyUserLogin::where('kandy_user_id', $kandyUser->user_id)->first();
            $remoteIp = $_SERVER['REMOTE_ADDR'];
            $now = time();
            if($user->can('admin') == false) {
                if(!$model) {
                    KandyUserLogin::create(array(
                        'kandy_user_id' => $kandyUser->user_id,
                        'type' => $kandyUser->type,
                        'status' => Kandylaravel::USER_STATUS_OFFLINE,
                        'browser_agent' => $_SERVER["HTTP_USER_AGENT"],
                        'ip_address' => $remoteIp,
                        'time'      => $now,
                    ));
                } else {
                    $model->update(array(
                        'status'    => Kandylaravel::USER_STATUS_OFFLINE,
                        'time'      => $now,
                        'ip_address'=> $remoteIp,
                        'browser_agent' => $_SERVER["HTTP_USER_AGENT"],
                    ));
                }
            }
            $kandyLaravel = new Kandylaravel();
            $full_user_id = $kandyUser->main_user_id . '@' . $kandyUser->domain_name;
            $kandyLaravel->getLastSeen([$full_user_id]);
        }
    }

    public function onUserLogout($user){
        $kandyUser = KandyUsers::where('main_user_id', $user->id)->first();
        //if login user is a chat agent
        if($kandyUser) {
            $userLogin = KandyUserLogin::where('kandy_user_id', $kandyUser->user_id)->first();
            if(!empty($userLogin) && $user->can('admin') == false) {
                $userLogin->status = Kandylaravel::USER_STATUS_OFFLINE;
                $userLogin->save();
            }
            $kandyLaravel = new Kandylaravel();
            $full_user_id = $kandyUser->main_user_id . '@' . $kandyUser->domain_name;
            $kandyLaravel->getLastSeen([$full_user_id]);
        }
    }

    public function subscribe($events)
    {
        $events->listen('auth.login', '\Kodeplus\Kandylaravel\EventHandler@onUserLogin');

        $events->listen('auth.logout', '\Kodeplus\Kandylaravel\EventHandler@onUserLogout');
    }

} 