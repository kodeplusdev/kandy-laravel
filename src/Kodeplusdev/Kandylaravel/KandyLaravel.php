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
     * @param string|integer $mainUserId
     *
     * @return array
     * @throws RestClientException
     */
    public function createUser($username, $mainUserId = null)
    {
        $result = $this->getDomainAccessToken();
        if ($result['success'] == true) {
            $this->domainAccessToken = $result['data'];
        } else {
            // Catch errors
        }

        $userIdColumn   = \Config::get('kandylaravel::user_id_column');
        $kandyPwdColumn = \Config::get('kandylaravel::password_column');

        $params     = array(
            'key' => $this->domainAccessToken
        );
        $postFields = array(
            'user_id' => $username,
        );

        $postFieldsString = json_encode($postFields);

        $fieldsString = http_build_query($params);
        $url = Kandylaravel::API_BASE_URL . 'domains/users' . '?' . $fieldsString;

        try {
            $response = (new RestClient())->post($url, $postFieldsString)->getContent();
        } catch (Exception $ex) {
            return array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }

        $response = json_decode($response);
        if ($response) {
            $user = new KandyUsers();
            $user->$userIdColumn = 'demo';  // $response->result->user_id
            $user->$kandyPwdColumn = 'a1234567'; // $response->result->user_password
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