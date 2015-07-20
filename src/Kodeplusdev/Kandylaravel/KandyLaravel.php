<?php
namespace Kodeplusdev\Kandylaravel;
use Kodeplusdev\Kandylaravel\KandyUsers;

class Kandylaravel
{
    /**
     * Base URL of the API
     */
    const API_BASE_URL = 'https://api.kandy.io/v1.2/';

    const KANDY_CSS = 'packages/kandy-io/kandy-laravel/assets/css/kandylaravel.css';
    const KANDY_JS_CUSTOM = 'packages/kandy-io/kandy-laravel/assets/js/kandylaravel.js';

    // Default KANDY JS from cloud
    const KANDY_JS_FCS = 'https://kandy-portal.s3.amazonaws.com/public/javascript/fcs/3.0.4/fcs.js';
    const KANDY_JS = 'https://kandy-portal.s3.amazonaws.com/public/javascript/kandy/2.2.1/kandy.js';
    const KANDY_JQUERY = 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';

    // KANDY USER FILTERING STATUS
    const KANDY_USER_ALL = 1;
    const KANDY_USER_ASSIGNED = 2;
    const KANDY_USER_UNASSIGNED = 3;

    // DISPLAY NAME FOR KANDY UNASSIGNED USER
    const KANDY_UNASSIGNED_USER = "KANDY UNASSIGNED USER";

    // SELECT2
    const SELECT2_CSS = "packages/kandy-io/kandy-laravel/assets/css/select2.css";
    const SELECT2_JS = "packages/kandy-io/kandy-laravel/assets/js/select2.min.js";

    // Kandy Laravel configuration variables which could be overridden at application
    public $domainAccessToken;
    public $username;
    public $password;
    public $apiKey;

    /**
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    public $htmlBuilder;

    /**
     * Contents of the widget
     *
     * @var
     */
    public $contents;

    /**
     * Default empty constructor
     */
    public function __construct()
    {

    }

    /**
     * Create a Kandy user
     *
     * @param string $username
     * @param string $email
     * @param int|null $mainUserId
     *
     * @return array
     * @throws RestClientException
     */
    public function createUser($username, $email, $mainUserId = null)
    {
        $result = $this->getDomainAccessToken();
        if ($result['success'] == true) {
            $this->domainAccessToken = $result['data'];
        } else {
            // Catch errors
        }

        $params = array(
            'key' => $this->domainAccessToken
        );
        $postFields = array(
            'user_id'    => $username,
            'user_email' => $email
        );

        $postFieldsString = json_encode($postFields);

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/users/user_id' . '?'
            . $fieldsString;
        $headers = array(
            'Content-Type: application/json'
        );

        try {
            $response = (new RestClient())->post(
                $url,
                $postFieldsString,
                $headers
            )->getContent();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }

        $response = json_decode($response);
        if ($response) {
            if (!empty($response->result)) {
                $res = $response->result;
                $user = new KandyUsers();
                $user->user_id = $res->user_id;
                $user->password = $res->user_password;
                $user->email = $email;
                $user->domain_name = $res->domain_name;
                $user->api_key = $res->user_api_key;
                $user->api_secret = $res->user_api_secret;
                $user->main_user_id = $mainUserId;
                if ($user->save()) {
                    return array(
                        'success' => true
                    );
                } else {
                    return array(
                        'success' => false,
                        'message' => 'Cannot create kandy user!'
                    );
                }
            } else {
                return array(
                    'success' => false,
                    'message' => $response->message
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => 'Response none json format!!'
            );
        }
    }

    /**
     * Get domain access token
     *
     * @return array A list of message and data
     * @throws RestClientException
     */
    public function getDomainAccessToken()
    {
        $params = array(
            'key'               => \Config::get('kandy-laravel.key'),
            'domain_api_secret' => \Config::get(
                'kandy-laravel.domain_api_secret'
            )
        );

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/accesstokens' . '?'
            . $fieldsString;

        try {
            $response = (new RestClient())->get($url)->getContent();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }

        $response = json_decode($response);
        if ($response->message == 'success') {
            return array(
                'success' => true,
                'data'    => $response->result->domain_access_token,
            );
        } else {
            return array(
                'success' => false,
                'message' => $response->message
            );
        }
    }

    /**
     * Get all users from Kandy and import/update to kandy_user
     *
     * @return array A json status and message
     */
    public function syncUsers()
    {
        $kandyUsers = $this->listUser(self::KANDY_USER_ALL, true);
        $getDomainNameResponse = $this->getDomain();
        if ($getDomainNameResponse['success']) {
            $domainName = $getDomainNameResponse['data'];
            \DB::transaction(
                function () use ($domainName, $kandyUsers) {
                    $receivedUsers = array();
                    foreach ($kandyUsers as $kandyUser) {
                        array_push($receivedUsers, $kandyUser->user_id);
                        $model = KandyUsers::whereuser_id($kandyUser->user_id)->wheredomain_name($domainName)
                            ->first();
                        $now = date("Y-m-d H:i:s");
                        if (empty($model)) {
                            // Create a new record
                            $model = new KandyUsers();
                            $model->user_id = $kandyUser->user_id;
                            $model->created_at = $now;
                        }
                        $model->first_name = $kandyUser->user_first_name;
                        $model->last_name = $kandyUser->user_last_name;
                        $model->email = $kandyUser->user_email;
                        $model->domain_name = $kandyUser->domain_name;
                        $model->api_key = $kandyUser->user_api_key;
                        $model->api_secret = $kandyUser->user_api_secret;

                        $model->password = $kandyUser->user_password;
                        $model->updated_at = $now;
                        $model->save();
                    }
                    // Delete records which no longer exist on server
                    KandyUsers::wheredomain_name($domainName)->whereNotIn(
                        'user_id',
                        $receivedUsers
                    )->delete();
                }
            );
            $result = array(
                'success' => true,
                'message' => "Synchronization successfully"
            );
        } else {
            $result = array(
                'success' => false,
                'message' => "Cannot get domain name."
            );
        }
        return $result;
    }

    /**
     * Get a list of kandy users
     *
     * @param int  $type    Type of users:
     *                      type = 1, get all kandy users
     *                      type = 2, get kandy users who are tied to any framework users
     *                      type = 3, get kandy users who are not tied to any framework users
     * @param bool $remote  Whether to list users from Kandy server or from local kandy_user table
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function listUser($type = self::KANDY_USER_ALL, $remote = false)
    {
        $result = array();
        if ($remote) {

            $getTokenResponse = $this->getDomainAccessToken();
            if ($getTokenResponse['success'] == true) {
                $this->domainAccessToken = $getTokenResponse['data'];
            } else {
                // Catch errors
            }

            $params = array(
                'key' => $this->domainAccessToken
            );

            $fieldsString = http_build_query($params);
            $url = Kandylaravel::API_BASE_URL . 'domains/users' . '?'
                . $fieldsString;
            $headers = array(
                'Content-Type: application/json'
            );

            try {
                $response = (new RestClient())->get($url, $headers)->getContent(
                );
            } catch (Exception $ex) {
                return array(
                    'success' => false,
                    'message' => $ex->getMessage()
                );
            }
            $response = json_decode($response);

            if ($response) {
                $data = $response->result;
                $result = $data->users;
            }
        } else {
            if ($type == self::KANDY_USER_ALL) {
                $models = KandyUsers::all();
            } else {
                if ($type == self::KANDY_USER_ASSIGNED) {
                    $models = KandyUsers::whereNotNull('main_user_id')->get();
                } else {
                    // type = self::KANDY_USER_UNASSIGNED
                    $models = KandyUsers::whereNull('main_user_id')->get();
                }
            }
            $result = $models;
        }
        return $result;
    }

    /**
     * Get the domain from domain key in the configuration
     *
     * @return array A list of message the data
     * @throws RestClientException
     */
    public function getDomain()
    {
        $getTokenResponse = $this->getDomainAccessToken();
        if ($getTokenResponse['success'] == true) {
            $this->domainAccessToken = $getTokenResponse['data'];
        } else {
            // Catch errors
        }
        $params = array(
            'key'            => $this->domainAccessToken,
            'domain_api_key' => \Config::get('kandy-laravel.key')
        );

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'accounts/domains/details' . '?'
            . $fieldsString;

        try {
            $response = (new RestClient())->get($url)->getContent();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }

        $response = json_decode($response);
        if ($response->message == 'success') {
            return array(
                'success' => true,
                'data'    => $response->result->domain->domain_name,
            );
        } else {
            return array(
                'success' => false,
                'message' => $response->message
            );
        }
    }

    /**
     * Random assign kandy accounts for users
     *
     */
    public function assignAllUser()
    {
        $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');
        $mainUserTable = \Config::get('kandy-laravel.user_table');
        $mainUserTablePrimaryKey = $this->getMainUserIdColumn();

        $sql = "SELECT m.$mainUserTablePrimaryKey as id
        FROM $mainUserTable m LEFT JOIN $kandyUserTable k ON m.$mainUserTablePrimaryKey = k.main_user_id
        WHERE k.main_user_id is NULL ";

        $unassignedUsers = \DB::select($sql);
        shuffle($unassignedUsers);

        foreach ($unassignedUsers as $user) {
            $this->assignUser($user->id);
        }
    }

    /**
     * Assign an application user to a Kandy user
     *
     * @param int $mainUserId Application User Id
     * @param string $user_id Kandy user, null in case random assignment.
     *
     * @return bool True if success, false if fail
     */
    public function assignUser($mainUserId, $user_id = null)
    {
        $getDomainNameResponse = $this->getDomain();
        if ($getDomainNameResponse['success']) {
            // Get domain name successfully
            KandyUsers::wheremain_user_id($mainUserId)->update(array('main_user_id' => null));
            $domainName = $getDomainNameResponse['data'];
            $kandyUser = is_null($user_id) ? KandyUsers::whereNull('main_user_id')->wheredomain_name(
                $domainName
            )->first() : KandyUsers::whereuser_id($user_id)->wheredomain_name($domainName)->first();
            if (empty($kandyUser)) {
                $result = array('success' => false, 'message' => 'Cannot find the Kandy user.');
            } else {
                $kandyUser->main_user_id = $mainUserId;
                if ($kandyUser->save()) {
                    $result = array(
                        'success' => true,
                        'message' => 'Assign successfully',
                        'user' => $kandyUser->user_id
                    );
                } else {
                    $result = array('success' => false, 'message' => 'Cannot assign the Kandy user.');
                }
            }
        } else {
            // Cannot get domain name
            $result = array('success' => false, 'message' => 'Cannot get domain name.');
        }
        return $result;
    }

    /**
     * Unassign application user from kandy user
     *
     * @param int $mainUserId Application user ID
     *
     * @return bool True if success, false if fail
     */
    public function unassignUser($mainUserId)
    {
        $user = KandyUsers::wheremain_user_id($mainUserId)->first();
        if (!empty($user)) {
            $user->main_user_id = null;
            $result = $user->save();
        } else {
            $result = true;
        }
        return $result;
    }

    /**
     * Initialize all css/js needed for Kandy widgets
     *
     * @param int $userId Framework or application user ID
     *
     * @return mixed|string Html portion that include css/js
     */
    public function init($userId)
    {
        $kandyUser = $this->getUser($userId);
        if ($kandyUser) {
            $this->username = $kandyUser->user_id;
            $this->password = $kandyUser->password;
        }
        $this->apiKey = \Config::get('kandy-laravel.key');

        $return = $this->css();
        $return .= $this->js();

        return $return;
    }

    /**
     * Get Kandy User from the framework/application user id.
     *
     * @param int $userId Framework or application user Id
     *
     * @return KandyUser A Kandy User
     */
    public function getUser($userId)
    {
        $model = KandyUsers::where("main_user_id", $userId)->first();
        return $model;
    }

    //------------------------ JS Application methods ------------------------//

    /**
     * Perform an action on a resource location
     *
     * @param string $type     Type of action performed on html object. E.g: script
     * @param string $location Location of the resource. E.g: kandylaravel.css
     *
     * @return mixed
     */
    protected function add($type, $location)
    {
        return $this->htmlBuilder->$type($location);
    }

    /**
     * @return mixed
     */
    public function css()
    {
        $return = $this->add('style', asset(self::KANDY_CSS));
        $return .= $this->add('style', asset(self::SELECT2_CSS));
        return $return;
    }

    /**
     *
     * @return string
     */
    public function js()
    {
        $return = "";
        $jqueryReload = \Config::get('kandy-laravel.key');
        if ($jqueryReload) {
            $return .= $this->add('script', asset(self::KANDY_JQUERY));
        }
        $return .= $this->add('script', self::KANDY_JS_FCS);
        $return .= $this->add('script', self::KANDY_JS);
        $return .= "<script>
                    window.login = function() {
                        KandyAPI.Phone.login('" . $this->apiKey . "', '" . $this->username . "', '" . $this->password . "');
                    };
                    </script>";
        $return .= $this->add('script', asset(self::KANDY_JS_CUSTOM));
        $return .= $this->add('script', asset(self::SELECT2_JS));

        return $return;
    }

    /**
     * Return logout script
     *
     * @return string
     */
    public function logout()
    {
        $return = "";
        $jqueryReload = \Config::get('kandy-laravel.key');
        if ($jqueryReload) {
            $return .= $this->add('script', asset(self::KANDY_JQUERY));
        }
        $return .= $this->add('script', self::KANDY_JS_FCS);
        $return .= $this->add('script', self::KANDY_JS);
        $return .= $this->add('script', asset(self::KANDY_JS_CUSTOM));
        $return .= $this->add('script', asset(self::SELECT2_JS));
        $return .= "<script>
                       window.kandy_logout = function() {
                                KandyAPI.Phone.logout();
                            };
                    </script>";
        return $return;
    }

    /**
     * Get user options
     *
     * @param int $type
     * @return array
     */
    public function getUserOptions($type = self::KANDY_USER_ALL)
    {
        $result = array();
        $users = $this->listUser($type);
        foreach ($users as $user) {
            $kandyUser = $this->getKandyUser($user->id);
            $displayName = $this->getDisplayName($user->id);
            $result[$kandyUser] = $displayName;
        }
        return $result;
    }

    /**
     * Get display name for specific kandy user
     *
     * @param $id
     * @return mixed|string
     */
    public function getDisplayName($id)
    {
        $result = "";
        $displayNameColumn = $this->getColumnForDisplayName('u');
        $mainUserTable = \Config::get('kandy-laravel.user_table');
        $mainUserTablePrimaryKey = $this->getMainUserIdColumn();
        $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');

        $sql = "SELECT $displayNameColumn as displayName FROM $mainUserTable as u, $kandyUserTable as k WHERE u.$mainUserTablePrimaryKey = k.main_user_id AND k.id = $id";
        $data = \DB::select($sql);
        if (!empty($data)) {
            $user = $data[0];
            $result = $user->displayName;
        }
        return $result;
    }

    /**
     * Get display name for specific kandy user from his kandy user_id
     *
     * @param $name
     * @return string
     */
    public function getDisplayNameFromKandyUser($name) {
        $result = "";
        $displayNameColumn = $this->getColumnForDisplayName('u');
        $mainUserTable = \Config::get('kandy-laravel.user_table');
        $mainUserTablePrimaryKey = $this->getMainUserIdColumn();
        $kandyUserTable = \Config::get('kandy-laravel.kandy_user_table');

        $sql = "SELECT $displayNameColumn as displayName FROM $mainUserTable as u, $kandyUserTable as k WHERE u.$mainUserTablePrimaryKey = k.main_user_id AND k.user_id = '" . $name . "'";

        $getDomainNameResponse = $this->getDomain();
        if ($getDomainNameResponse['success']) {
            // Get domain name successfully
            $domainName = $getDomainNameResponse['data'];
            $sql .= " AND domain_name = '" . $domainName . "'";
        }

        $data = \DB::select($sql);
        if (!empty($data)) {
            $user = $data[0];
            $result = $user->displayName;
        } else {
            $result = self::KANDY_UNASSIGNED_USER;
        }
        return $result;
    }

    /**
     * Get main user table id column
     *
     * @return null
     */
    public function getMainUserIdColumn()
    {
        $result = null;
        $mainUserTable = \Config::get('kandy-laravel.user_table');
        $keys = \DB::select('SHOW KEYS FROM ' . $mainUserTable . ' WHERE Key_name = "PRIMARY"');
        if (!empty($keys)) {
            $key = $keys[0];
            $result = $key->Column_name;
        }
        return $result;
    }

    /**
     * Get necessary columns for name display
     *
     * @param $table
     * @return mixed
     */
    public function getColumnForDisplayName($table)
    {
        $columns = array();
        $result = \Config::get('kandy-laravel.user_name_display');

        preg_match_all('/{(.*?)}/', $result, $columns);
        $count = count ($columns[0]);
        for ($i = 0; $i < $count; $i++) {
            $result = str_replace($columns[0][$i], "`,$table.".$columns[1][$i].",`", $result);
        }

        $result = "CONCAT(`$result`)";

        return $result;
    }

    /**
     * Get Kandy user
     *
     * @param $id
     * @param bool $email
     * @return mixed|string
     */
    public function getKandyUser($id, $email = true)
    {
        $result = "";
        $user = KandyUsers::find($id);
        if (!empty($user)) {
            $result = $email ? $user->user_id . "@" . $user->domain_name : $user->user_id;
        }
        return $result;
    }

    /**
     * Get Kandy user
     *
     * @param $main_user_id
     * @param bool $email
     * @return string
     */
    public function getKandyUserFromMainUser($main_user_id, $email = true)
    {
        $result = "";
        $user = KandyUsers::wheremain_user_id($main_user_id)->first();
        if (!empty($user)) {
            $result = $email ? $user->user_id . "@" . $user->domain_name : $user->user_id;
        }
        return $result;
    }
}