<?php

namespace Kodeplus\Kandylaravel;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Response;
use Session;
use Request;
use Config;

class KandyController extends Controller
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
            if(isset($contact['contact_user_name'])) {
                $contactUsername = $contact['contact_user_name'];
            } else {
                $contactUsername = $contact['full_user_id'];
            }
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
                    $displayName = $contactUsername;
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
        $message = $_POST['data'];
        if (!isset($message['sender'])) {
            return;
        }
        $sender = $message['sender'];
        //if incoming message is from live chat users
        $user = KandyUsers::whereuser_id($sender['user_id'])->first();
        if((!empty($user) && $user->type == Kandylaravel::USER_TYPE_CHAT_AGENT) || strpos($sender['user_id'], 'anonymous') !== false){
            $fakeEndTime = PHP_INT_MAX;
            $customerUser = $sender['user_id'];
            if(strpos($sender['user_id'], 'anonymous') !== false) {
                $customerUser = $sender['full_user_id'];
            }
            $user = KandyLiveChat::where('customer_user_id', $customerUser)
                ->where('end_at', $fakeEndTime)->first();
            if($user){
                $displayName = $user->customer_name;
                $sender['user_email'] = $user->customer_email;
            }
        } else {
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
        }

        $sender['display_name'] = $displayName;
        $sender['contact_user_name'] = $sender['full_user_id'];
        $message['sender'] = $sender;
        return \Response::json($message, 200);
    }

    public function getUsersForSearch()
    {
        if (!isset($_GET['q'])) {
            return \Response::make('Your request is invalid.', 403);
        }
        $users = array();

        $search = $_GET['q'];
        $kandyLaravel = new Kandylaravel();
        $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');
        $mainUserTable = \Config::get('kandy-laravel.user_table');
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
        if(isset($userInfo['username']) && isset($userInfo['email'])) {
            $userTable = \Config::get('kandy-laravel.user_table');
            $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');
            //if user already has session
            $kandyLaravel = new Kandylaravel();
            $fakeEndTime = PHP_INT_MAX;
            //get all unassigned users
            $kandyLiveChatTable = \Config::get('kandy-laravel.kandy_live_chat_table');
            $userLoginTable = 'kandy_user_login';
            if(isset($userInfo['user'])){
                $user = $userInfo['user'];
            } else {
                $user = $kandyLaravel->getAnonymousUser();
                $user->full_user_id = $user->email;
                if($user) {
                    \Session::set('kandyLiveChatUserInfo.user', $user);
                }
            }

            if (!empty($user)) {
                $now = time();
                $logEndUser = KandyUserLogin::where('kandy_user_id', $user->user_id)->first();
                if (!$logEndUser) {
                    KandyUserLogin::create(array(
                        'kandy_user_id' => $user->user_id,
                        'type' => Kandylaravel::USER_TYPE_CHAT_END_USER,
                        'status' => Kandylaravel::USER_STATUS_ONLINE,
                        'browser_agent' => '',
                        'time' => $now,
                        'ip_address' => $_SERVER['REMOTE_ADDR']
                    ));
                } else {
                    $logEndUser->status = Kandylaravel::USER_STATUS_ONLINE;
                    $logEndUser->time = $now;
                    $logEndUser->save();
                }
            }

            $agent = \DB::table($kandyUserTable)
                ->leftJoin($kandyLiveChatTable, "$kandyUserTable.user_id", '=', "$kandyLiveChatTable.agent_user_id")
                ->leftJoin($userTable, "$kandyUserTable.main_user_id", '=', "$userTable.id")
                ->join($userLoginTable, "$kandyUserTable.user_id", '=', "$userLoginTable.kandy_user_id")
                ->select(\DB::raw("user_id, main_user_id, CONCAT(user_id, '@', domain_name) as full_user_id, $userTable.username as username,
                        MAX($kandyLiveChatTable.end_at) as last_end_chat, (UNIX_TIMESTAMP() - $userLoginTable.time) as last_active"))
                ->where("$kandyUserTable.type", '=', $kandyLaravel::USER_TYPE_CHAT_AGENT)
                ->where("$userLoginTable.status", '=', Kandylaravel::USER_STATUS_ONLINE)
                ->groupBy("$userTable.id")
                ->having('last_end_chat', '<', $fakeEndTime)
                ->having('last_active', '<=',60000)
                ->orHavingRaw('last_end_chat IS NULL')
                ->orderBy("last_end_chat", "ASC")
                ->first();

            if (!empty($user) && !empty($agent)) {
                KandyLiveChat::create(array(
                    'agent_user_id' => $agent->user_id,
                    'customer_user_id' => $user->email,
                    'customer_name' => $userInfo['username'],
                    'customer_email' => $userInfo['email'],
                    'begin_at' => $now,
                    'end_at' => $fakeEndTime

                ));
                //save last insert id for user later
                $agentUser = new KandyUsers();
                $agentUser->full_user_id = $agent->full_user_id;
                $agentUser->username = $agent->username;
                $agentUser->main_user_id = $agent->main_user_id;
                $result = array(
                    'status' => 'success',
                    'user' => $user,
                    'agent' => $agentUser,
                    'apiKey' => \Config::get('kandy-laravel.key')
                );
            } else {
                //clean inactive user status if there is somthing wrong with end chat session function
                $inActiveUsers = KandyUserLogin::whereRaw('type = ' . $kandyLaravel::USER_TYPE_CHAT_AGENT)->lists('kandy_user_id')->all();
                if(!empty($inActiveUsers)){
                    $inActiveUsersStr = "('". implode("','", $inActiveUsers) . "')";
                    \DB::table($kandyLiveChatTable)
                        ->whereRaw("agent_user_id IN $inActiveUsersStr AND end_at = $fakeEndTime")
                        ->update(array('end_at' => time()));
                }
                $result = array(
                    'status' => 'fail'
                );
            }
            return \Response::json($result);
        } else {
            \Session::forget('kandyLiveChatUserInfo');
        }
        return '';
    }

    /**
     * Check agent user is online
     *
     * @return mixed
     */
    public function checkAgentOnline()
    {
        $fullUserId = \Request::get('full_user_id');
        if(!empty($fullUserId)) {
            $userTable = \Config::get('kandy-laravel.user_table');
            $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');
            $kandyLaravel = new Kandylaravel();
            $kandyLiveChatTable = \Config::get('kandy-laravel.kandy_live_chat_table');
            $userLoginTable = 'kandy_user_login';
            $agents = \DB::table($kandyUserTable)
                ->leftJoin($kandyLiveChatTable, "$kandyUserTable.user_id", '=', "$kandyLiveChatTable.agent_user_id")
                ->leftJoin($userTable, "$kandyUserTable.main_user_id", '=', "$userTable.id")
                ->join($userLoginTable, "$kandyUserTable.user_id", '=', "$userLoginTable.kandy_user_id")
                ->select(\DB::raw("user_id, main_user_id, CONCAT(user_id, '@', domain_name) as full_user_id,
                $userTable.username as username, MAX($kandyLiveChatTable.end_at) as last_end_chat, (UNIX_TIMESTAMP() - $userLoginTable.time) as last_active"))
                ->where("$kandyUserTable.type", '=', $kandyLaravel::USER_TYPE_CHAT_AGENT)
                ->where("$userLoginTable.status", '=', Kandylaravel::USER_STATUS_ONLINE)
                ->groupBy("$userTable.id")
                ->having('last_active', '<=',60000)
                ->orHavingRaw('last_end_chat IS NULL')
                ->orderBy("last_end_chat", "ASC")
                ->get();

            if(!empty($agents)) {
                $newFullUserId = null;
                foreach($agents as $agent) {
                    $newFullUserId = $agent->full_user_id;
                    $agentUser = new KandyUsers();
                    $agentUser->full_user_id = $newFullUserId;
                    $agentUser->username = $agent->username;
                    $agentUser->main_user_id = $agent->main_user_id;
                    if($newFullUserId == $fullUserId) {
                        break;
                    }
                }
                if(!empty($newFullUserId)) {
                    return \Response::json(array(
                        'full_user_id' => $newFullUserId,
                        'isOnline' => true,
                        'agent' => $agentUser
                    ));
                } else {
                    return \Response::json(array('isOnline' => false));
                }
            }
        }
        return \Response::json(array('isOnline' => false));
    }

    public function updateUserStatus() {
        if(\Auth::check()){
            ignore_user_abort(true);
            $kandyUser = (new Kandylaravel())->getKandyUserFromMainUser(\Auth::user()->id);
            if($kandyUser) {
                $userLogin = KandyUserLogin::where('kandy_user_id', explode('@',$kandyUser)[0])->first();
                if($userLogin) {
                    $userLogin->status = Kandylaravel::USER_STATUS_OFFLINE;
                    $userLogin->save();
                }
            }
        }
        return \Response::json(array(
            'status'    => 'success'
        ));

    }

    /**
     * End chat session
     * @return mixed
     */
    public function endChatSession()
    {
        ignore_user_abort(true);
        if(\Session::has('kandyLiveChatUserInfo')){
            $userInfo = \Session::pull('kandyLiveChatUserInfo');
            if(!empty($userInfo['sessionId'])) {
                $currentSession = KandyLiveChat::find($userInfo['sessionId']);
                //save end session time
                $currentSession->end_at = time();
                $currentSession->save();
            }

            if(!empty($userInfo['user'])) {
                $userLogin = KandyUserLogin::where('kandy_user_id', $userInfo['user'])->where('status', Kandylaravel::USER_STATUS_ONLINE)->first();
                if($userLogin) {
                    $userLogin->status = Kandylaravel::USER_STATUS_OFFLINE;
                    $userLogin->save();
                }
            }
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
     * get User for chat agent assign (admin functionality)
     * @return mixed
     */
    public function getUsersForChatAgent(){
        $result = array();
        $query = \Request::get('q',"");
        $kandyLaravel = new Kandylaravel();
        $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');
        $mainUserTable = \Config::get('kandy-laravel.user_table');
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
        $rate = isset($_POST['rate']) ? $_POST['rate'] : [];
        $userId = $rate['id'];
        $point = isset($rate['point']) ? $rate['point'] : 5;
        $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
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

    public function stillAlive()
    {
        $now = time();
        if(Auth::check()){
            $kandyUserId = (new Kandylaravel)->getUser(Auth::user()->id)->user_id;
        } else if(\Session::has('kandyLiveChatUserInfo.user')) {
            $kandyUserId = \Session::get('kandyLiveChatUserInfo.user');
        }
        if($kandyUserId){
            $kandyUserLogin = KandyUserLogin::where('kandy_user_id', $kandyUserId)
                ->where('status', Kandylaravel::USER_STATUS_ONLINE)->first();
            if($kandyUserLogin){
                $kandyUserLogin->time = $now;
                $kandyUserLogin->save();
            }

        }
        exit;
    }

    /**
     * Update presence for user
     *
     * @return mixed
     */
    public function updatePresence()
    {
        $presenceStatus = isset($_POST['status']) ? $_POST['status'] : Kandylaravel::USER_STATUS_AVAILABLE;
        if(\Auth::check()){
            ignore_user_abort(true);
            $fullUserId = (new Kandylaravel())->getKandyUserFromMainUser(\Auth::user()->id, true);
            if(!empty($fullUserId)) {
                $kandyUser = (new Kandylaravel())->getKandyUserByFullUserId($fullUserId);
                if(!empty($kandyUser)) {
                    $kandyUser->presence_status = $presenceStatus;
                    $kandyUser->save();
                    return \Response::json(array(
                        'status'    => 'success'
                    ));
                } else {
                    return \Response::json(array(
                        'status'    => 'fail',
                        'message'   => 'Kandy user not found'
                    ));
                }
            } else {
                return \Response::json(array(
                    'status'    => 'fail',
                    'message'   => 'Kandy full user id not found'
                ));
            }
        } else {
            return \Response::json(array(
                'status'    => 'fail',
                'message'   => 'User is logged out'
            ));
        }
    }

    /**
     * Get presence status by full user id
     *
     * @return mixed
     */
    public function getPresenceStatus()
    {
        $fullUserId = isset($_POST['full_user_id']) ? $_POST['full_user_id'] : null;
        if(!empty($fullUserId)) {
            $kandyUser = (new Kandylaravel())->getKandyUserByFullUserId($fullUserId);
            if(!empty($kandyUser)) {
                return \Response::json(array(
                    'status'    => 'success',
                    'data'   => array(
                        'full_user_id' => $fullUserId,
                        'presence_status' => $kandyUser->presence_status,
                        'presence_text' => Kandylaravel::$presenceStatus[$kandyUser->presence_status]
                    )
                ));
            } else {
                return \Response::json(array(
                    'status'    => 'fail',
                    'message'   => 'Kandy user not found'
                ));
            }
        } else {
            return \Response::json(array(
                'status'    => 'fail',
                'message'   => 'Full user id not found'
            ));
        }
    }
}
