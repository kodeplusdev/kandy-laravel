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
        if (!isset($_GET['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $contacts = $_GET['data'];
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
        if (!isset($_GET['data'])) {
            return Response::make('Your request is invalid.', 403);
        }
        $messages = $_GET['data'];
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

}
