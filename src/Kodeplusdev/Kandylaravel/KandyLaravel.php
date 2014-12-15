<?php namespace Kodeplusdev\Kandylaravel;

class Kandylaravel
{
    const API_BASE_URL = 'https://api.kandy.io/v1/';
    const KANDY_CSS = 'packages/kodeplusdev/kandylaravel/assets/css/kandylaravel.css';
    const KANDY_JS = 'packages/kodeplusdev/kandylaravel/assets/js/kandylaravel.js';

    public $domainAccessToken;

    public function __construct()
    {

    }

    public function getDomainAccessToken()
    {
        $params     = array(
            'key' => \Config::get('kandylaravel::key'),
            'domain_api_secret' => \Config::get('kandylaravel::domain_api_secret')
        );

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/accesstokens' . '?' . $fieldsString;

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
                'data' => $response->result->domain_access_token,
            );
        } else {
            return array(
                'success' => false,
                'message' => $response->message
            );
        }
    }

    /**
     * @param      $username
     * @param      $email
     * @param null $mainUserId
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

        $params     = array(
            'key' => $this->domainAccessToken
        );
        $postFields = array(
            'user_id' => $username,
            'user_email' => $email
        );

        $postFieldsString = json_encode($postFields);

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/users/user_id' . '?' . $fieldsString;
        $headers = array(
            'Content-Type: application/json'
        );

        try {
            $response = (new RestClient())->post($url, $postFieldsString, $headers)->getContent();
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
     * HTML
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    public $html;

    /**
     * @return mixed
     */
    public function css()
    {
        $return = $this->add('style', asset(self::KANDY_CSS));
        return $return;
    }

    /**
     * @return mixed
     */
    public function js()
    {
        $return = $this->add('script', asset(self::KANDY_JS));
        return $return;
    }

    /**
     * @param $type
     * @param $location
     *
     * @return mixed
     */
    protected function add($type, $location)
    {
        return $this->html->$type($location);
    }
}