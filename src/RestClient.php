<?php
namespace Kodeplus\Kandylaravel;

use Config;
use Exception;

class RestClientException extends Exception
{
}

class RestClient
{
    protected $_submitted = false;
    protected $_headers = array();
    protected $_body = '';

    public function get($uri, $headers = array(), $timeout = 30)
    {
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            \Config::get('kandy-laravel.ssl_verify')
        );
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (curl_errno($ch)) {
            throw new RestClientException(curl_errno($ch));
        }
        $this->_submitted = true;
        $this->_body = curl_exec($ch);
        $this->_headers = curl_getinfo($ch);

        curl_close($ch);
        return $this;
    }

    public function post($uri, $payload, $headers = array(), $timeout = 30)
    {
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            \Config::get('kandy-laravel.ssl_verify')
        );
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (curl_errno($ch)) {
            throw new RestClientException(curl_errno($ch));
        }

        $this->_submitted = true;
        $this->_body = curl_exec($ch);
        $this->_headers = curl_getinfo($ch);
        curl_close($ch);
        return $this;
    }

    public function put($uri, $payload, $headers = array(), $timeout = 30)
    {
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            \Config::get('kandy-laravel.ssl_verify')
        );
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (curl_errno($ch)) {
            throw new RestClientException(curl_errno($ch));
        }

        $this->_submitted = true;
        $this->_body = curl_exec($ch);
        $this->_headers = curl_getinfo($ch);

        curl_close($ch);
        return $this;
    }

    public function delete($uri, $headers = array(), $timeout = 30)
    {
        $ch = curl_init($uri);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_SSL_VERIFYPEER,
            \Config::get('kandy-laravel.ssl_verify')
        );
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (curl_errno($ch)) {
            throw new RestClientException(curl_errno($ch));
        }
        $this->_submitted = true;
        $this->_body = curl_exec($ch);
        $this->_headers = curl_getinfo($ch);

        curl_close($ch);
        return $this;
    }

    /**
     * After the request - functions to return data
     * @return int|string
     */

    public function getStatusText()
    {
        if ($this->_submitted) {
            return $this->getStatusCode();
        }
        return 'UNKNOWN';
    }

    /**
     * Get Status Code.
     *
     * @return int|string
     */
    public function getStatusCode()
    {
        if ($this->_submitted) {
            return $this->getHeader('http_code');
        }
        return 0;
    }

    /**
     * Get Header.
     *
     * @param $index
     * @return string
     */
    public function getHeader($index)
    {
        if (isset($this->_headers[$index])) {
            return $this->_headers[$index];
        }
        return 'N/A';
    }

    /**
     * Get content.
     * @return string
     */
    public function getContent()
    {
        return $this->_body;
    }

    /**
     * Get Headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get Time
     * @return string
     */
    public function getTime()
    {
        return $this->getHeader('total_time');
    }
}