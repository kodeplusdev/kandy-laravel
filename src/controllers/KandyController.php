<?php

namespace Kodeplusdev\Kandylaravel;

class KandyController extends \BaseController
{

    /**
     * Get name for contacts
     *
     * @return mixed
     */
    public function getNameForContact()
    {
        if (!isset($_POST['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $contacts = $_POST['data'];
        foreach ($contacts as &$contact) {
            $userId = "";
            $domain = "";
            $contactUsername = $contact['contact_user_name'];
            $parseResult = explode('@', $contactUsername);
            if (!empty($parseResult[0])) {
                $userId = $parseResult[0];
            }
            if (!empty($parseResult[1])) {
                $domain = $parseResult[1];
            }
            $user = KandyUsers::whereuser_id($userId)->wheredomain_name($domain)->first();
            if (empty($user)) {
                $displayName = "";
            } else {
                $kandylaravel = new Kandylaravel();
                $displayName = $kandylaravel->getDisplayName($user->id);
                if (empty($displayName)) {
                    $displayName = $contact['contact_user_name'];
                }
            }
            $contact['display_name'] = $displayName;
        }
        return \Response::json($contacts, 200);

    }

    /**
     * Get name for chat content
     *
     * @return mixed
     */
    public function getNameForChatContent()
    {
        if (!isset($_POST['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $messages = $_POST['data'];
        foreach ($messages as &$message) {
            if (!isset($message['sender'])) {
                continue;
            }
            $sender = $message['sender'];
            $user = KandyUsers::whereuser_id($sender['user_id'])->first();
            if (empty($user)) {
                $displayName = "";
            } else {
                $kandylaravel = new Kandylaravel();
                $displayName = $kandylaravel->getDisplayName($user->id);
                if (empty($displayName)) {
                    $displayName = $sender['full_user_id'];
                }
            }
            $sender['display_name'] = $displayName;
            $sender['contact_user_name'] = $sender['full_user_id'];
            $message['sender'] = $sender;
        }
        return \Response::json($messages, 200);
    }

    public function getUsersForSearch()
    {
        if (!isset($_GET['q'])) {
            return \Response::make('Your request is invalid.', 403);
        }
        $users = array();

        $search = $_GET['q'];
        $kandyLaravel = new Kandylaravel();
        $kandyUserTable = \Config::get('kandy-laravel::kandy_user_table');
        $mainUserTable = \Config::get('kandy-laravel::user_table');
        $displayNameColumn = $kandyLaravel->getColumnForDisplayName('m');
        $mainUserTablePrimaryKey = $kandyLaravel->getMainUserIdColumn();

        $sql = "SELECT CONCAT(k.user_id, '@', k.domain_name)as kandyFullUsername, $displayNameColumn as mainUsername
                FROM  $mainUserTable m, $kandyUserTable k
                WHERE m.$mainUserTablePrimaryKey = k.main_user_id
                HAVING mainUsername like '%$search%'";
        $data = \DB::select($sql);
        foreach ($data as $user) {
            $userToAdd = array(
                'id' => $user->kandyFullUsername,
                'text' => $user->mainUsername
            );
            array_push($users, $userToAdd);
        }
        $result = array('results' => $users, 'more' => false);
        return \Response::json($result, 200);
    }

    /**
     * register user live chat session
     * @return mixed
     */
    public function registerGuest()
    {
        $username = \Request::get('customerName');
        $userEmail = \Request::get('customerEmail');
        //Save user info to database
        $userInfo = array(
            'username'  => $username,
            'email'     => $userEmail
        );
        $validateRules = array(
            'customerName'  => 'required',
            'customerEmail' => 'required|email',
        );
        $validator = \Validator::make($_POST, $validateRules);
        if($validator->passes()){
            if(!\Session::has('kandyLiveChatUserInfo')){
                \Session::put('kandyLiveChatUserInfo', $userInfo);
            }
            return \Response::json($userInfo);
        }else{
            return \Response::json(array('errors' => $validator->messages()));
        }
    }

    /**
     * Get agent - user pair for chatting
     * @return mixed
     */
    public function getFreeUser()
    {
        $userInfo = \Session::get('kandyLiveChatUserInfo');
        $kandyLaravel = new Kandylaravel();
        $freeUser = null;
        $availableAgent = null;
        //get all unassigned users
        $kandyUserTable = \Config::get('kandy-laravel::kandy_user_table');
        $kandyLiveChatUser = \Config::get('kandy-laravel::excluded_kandy_users.liveChat');
        $kandyLiveChatTable = \Config::get('kandy-laravel::kandy_live_chat_table');
        $userTable = \Config::get('kandy-laravel::user_table');
        \DB::setFetchMode(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
        $users = \DB::table($kandyUserTable)
            ->select(\DB::raw("CONCAT(user_id,'@',domain_name) as full_user_id, password"))
            ->whereIn('user_id', $kandyLiveChatUser)->get();
        $agents = \DB::table($kandyUserTable)
            ->leftJoin($kandyLiveChatTable, "$kandyUserTable.user_id", '=', "$kandyLiveChatTable.agent_user_id")
            ->leftJoin($userTable, "$kandyUserTable.main_user_id", '=', "$userTable.id")
            ->select(\DB::raw("CONCAT(user_id, '@', domain_name) as full_user_id, $kandyUserTable.password as password, $userTable.username as username, main_user_id"))
            ->where('type', '=', $kandyLaravel::USER_TYPE_CHAT_AGENT)
            ->orderBy("$kandyLiveChatTable.last_chat", "ASC")->get();
        \DB::setFetchMode(\PDO::FETCH_CLASS);
        $arrayUsers = array_merge(array_keys($users), array_keys($agents));
        $lastSeen = $kandyLaravel->getLastSeen($arrayUsers);
        if($lastSeen){
            if($lastSeen->message == 'success'){
                //current time of kandy server
                $serverTimestamp = $lastSeen->result->server_timestamp;
                foreach($lastSeen->result->users as $user){
                    //get user
                    if(isset($users[$user->full_user_id]) && !$freeUser){
                        //get users not online in last 10 secs
                        if(($serverTimestamp - $user->last_seen) > 10000){
                            $freeUser = $user;
                            $freeUser->password = $users[$user->full_user_id][0]['password'];
                        }
                    }
                    //get agent
                    if(isset($agents[$user->full_user_id]) && !$availableAgent){
                        // get agents online in last 3 secs
                        if(($serverTimestamp - $user->last_seen) < 3000) {
                            $availableAgent = $user;
                            $availableAgent->user_id = current(explode('@',$availableAgent->full_user_id));
                            $availableAgent->username = $agents[$user->full_user_id][0]['username'];
                            $availableAgent->main_user_id = $agents[$user->full_user_id][0]['main_user_id'];
                        }
                    }
                    if($freeUser && $availableAgent) break;
                }
            }

            if($freeUser && $availableAgent){

                \Session::set('kandyLiveChatUserInfo.agent', $availableAgent->user_id);

                $model = KandyLiveChat::whereRaw(
                    'customer_email=? AND agent_user_id=?', array($userInfo['email'], $availableAgent->user_id)
                )->first();
                $now = time();
                if(!$model){
                    KandyLiveChat::create(array(
                        'agent_user_id'     => $availableAgent->user_id,
                        'customer_user_id'  => $freeUser->full_user_id,
                        'customer_name'     => $userInfo['username'],
                        'customer_email'    => $userInfo['email'],
                        'last_time'         => $now,
                        'first_time'        => $now,
                        'times'             => 1,
                    ));
                }else{
                    $model->update(array(
                        'last_time' => $now,
                        'times'     => ++$model->times //increase times connect
                    ));
                }
                $result = array(
                    'status' => 'success',
                    'user'  => $freeUser,
                    'agent' => $availableAgent,
                    'apiKey' => \Config::get('kandy-laravel::key')
                );
            }else{
                $result = array(
                    'status'    => 'fail'
                );
            }
        }else{
            $result = array(
                'status'    => 'fail'
            );
        }
        return \Response::json($result);
    }

    /**
     * End chat session
     * @return mixed
     */
    public function endChatSession()
    {
        if(\Session::has('kandyLiveChatUserInfo')){
            \Session::forget('kandyLiveChatUserInfo');
        }
        if(\Request::ajax()){
            return \Response::json(array(
                'status'    => 'success'
            ));
        }
        return \Redirect::back();
    }

    /**
     *
     * Update last_chat when agent chat
     * @return mixed
     */
    public function updateChatSession(){
        $now = time();
        if(\Session::has('kandyLiveChatUserInfo')){
            $userInfo = \Session::get('kandyLiveChatUserInfo');
            $model = KandyLiveChat::whereRaw('customer_email=? AND agent_user_id=?',
                array($userInfo['email'],$userInfo['agent']))->update(array('last_chat' => $now));
        }
        return \Response::json(array('last' => $now));
    }

    /**
     *
     * get User for chat agent assign (admin functionality)
     * @return mixed
     */
    public function getUsersForChatAgent(){
        $result = array();
        $query = \Request::get('q',"");
        $kandyLaravel = new Kandylaravel();
        $kandyUserTable = \Config::get('kandy-laravel::kandy_user_table');
        $mainUserTable = \Config::get('kandy-laravel::user_table');
        $displayNameColumn = $kandyLaravel->getColumnForDisplayName($mainUserTable);
        $users = \DB::table($mainUserTable)
            ->join($kandyUserTable, "$mainUserTable.id",'=',"$kandyUserTable.main_user_id")
            ->select("$kandyUserTable.id as id", \DB::raw("$displayNameColumn as displayName"))
            ->where("$kandyUserTable.type", '<>', $kandyLaravel::USER_TYPE_CHAT_AGENT)
            ->havingRaw("displayName LIKE '%$query%'")->get();
        foreach($users as $user){
            array_push($result,array(
                'id'    => $user->id,
                'text'  => $user->displayName
            ));
        }
        return \Response::json(array('results' => $result));

    }

    /**
     * Rate for agent action
     * @return mixed
     */
    public function rateAgent()
    {
        $rate = \Request::get('rate', []);
        $userId = $rate['id'];
        $point = $rate['point'];
        $comment = \Request::get('comment', '');
        if(!\Session::has('kandyLiveChatUserInfo')){
            return \Response::json(array(
                'success' => false,
                'message' => 'not allowed'
            ));
        }
        if(!$userId){
            $result = array(
                'success'   => false,
                'message'   => 'agent is not specified'
            );
        }else{
            if(\Session::has('kandyLiveChatUserInfo.rated')){
                $result = array(
                    'success'   => true,
                    'message'   => 'Already rated'
                );
            }else{
                $now = time();
                KandyLiveChatRate::create(array(
                    'main_user_id'  => $userId,
                    'rated_time'    => $now,
                    'point'         => intval($point),
                    'rated_by'      => \Session::get('kandyLiveChatUserInfo.email'),
                    'comment'       => htmlspecialchars($comment)
                ));
                \Session::set('kandyLiveChatUserInfo.rated', true);
                $result = array(
                    'success'   => true,
                );
            }
        }

        return \Response::json($result);
    }






}
