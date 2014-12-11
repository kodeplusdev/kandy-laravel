<?php namespace Kodeplusdev\Kandylaravel;

use Kodeplusdev\Kandylaravel\Models;

class Kandylaravel
{
    const API_BASE_URL = 'https://api.kandy.io/v1/';
    public $domainAccessToken;

    public function __construct()
    {
        // TODO: Call request get domain access token
        $result = $this->getDomainAccessToken();
        if ($result['success'] == true) {
            $this->domainAccessToken = $result['data'];
        } else {
           // Catch errors
        }
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
     * @param null $mainUserId
     *
     * @return array
     * @throws RestClientException
     */
    public function createUser($username, $mainUserId = null)
    {
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

    public function getUser()
    {

    }

}