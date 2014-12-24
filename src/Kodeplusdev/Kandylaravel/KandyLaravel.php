<?php
namespace Kodeplusdev\Kandylaravel;

class Kandylaravel
{
    /**
     * Base URL of the API
     */
    const API_BASE_URL = 'https://api.kandy.io/v1/';

    const KANDY_CSS = 'packages/kodeplusdev/kandylaravel/assets/css/kandylaravel.css';
    const KANDY_JS_CUSTOM = 'packages/kodeplusdev/kandylaravel/assets/js/kandylaravel.js';

    // Default KANDY JS from clound
    const KANDY_JS_FCS = 'https://kandy-portal.s3.amazonaws.com/public/javascript/fcs/3.0.0/fcs.js';
    const KANDY_JS = 'https://kandy-portal.s3.amazonaws.com/public/javascript/kandy/1.1.4/kandy.js';
    const KANDY_JQUERY = 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js';

    // KANDY USER FILTERING STATUS
    const KANDY_USER_ALL = 1;
    const KANDY_USER_ASSIGNED = 2;
    const KANDY_USER_UNASSIGNED = 3;

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
            'key'               => \Config::get('kandylaravel::key'),
            'domain_api_secret' => \Config::get(
                'kandylaravel::domain_api_secret'
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
                        $model = KandyUsers::whereuser_id($kandyUser->user_id)
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
                    if ($type == self::KANDY_USER_UNASSIGNED) {
                        $models = KandyUsers::whereNull('main_user_id')->get();
                    }
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
        $params = array(
            'key'               => \Config::get('kandylaravel::key'),
            'domain_api_secret' => \Config::get(
                'kandylaravel::domain_api_secret'
            )
        );

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/details' . '?'
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
     * Assign an application user to a Kandy user
     *
     * @param int $mainUserId Application User Id
     * @param int $user_id Kandy user id, null in case random assignment.
     *
     * @return bool True if success, false if fail
     */
    public function assignUser($mainUserId, $user_id)
    {
        KandyUsers::wheremain_user_id($mainUserId)->update(array('main_user_id' => null));
        $kandyUser = is_null($user_id) ? KandyUsers::whereNull('main_user_id')->first() : KandyUsers::find($user_id);
        if (empty($kandyUser)) {
            $result = array('success' => false, 'message' => 'Cannot find the Kandy user.');
        } else {
            $kandyUser->main_user_id = $mainUserId;
            if ($kandyUser->save()) {
                $result = array('success' => true, 'message' => 'Assign successfully', 'user' => $kandyUser->user_id);
            } else {
                $result = array('success' => false, 'message' => 'Cannot assign the Kandy user.');
            }
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
        $this->apiKey = \Config::get('kandylaravel::key');

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
        return $return;
    }

    /**
     *
     * @return string
     */
    public function js()
    {
        $return = "";
        $jqueryReload = \Config::get('kandylaravel::key');
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
        $jqueryReload = \Config::get('kandylaravel::key');
        if ($jqueryReload) {
            $return .= $this->add('script', asset(self::KANDY_JQUERY));
        }
        $return .= $this->add('script', self::KANDY_JS_FCS);
        $return .= $this->add('script', self::KANDY_JS);
        $return .= $this->add('script', asset(self::KANDY_JS_CUSTOM));
        $return .= "<script>
                       window.kandy_logout = function() {
                                KandyAPI.Phone.logout();
                            };
                    </script>";
        return $return;
    }
}