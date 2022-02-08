<?php

class FTXConnector
{

    protected $api_key = '';
    protected $api_secret = '';
    protected $subaccount = NULL;
    protected $base_url = 'https://ftx.com';

    public function __construct($api_key , $api_secret , $subaccount = NULL) {

        if(empty($api_key)) {
            throw new \Exception("API Key not set");
        }

        if(empty($api_secret)) {
            throw new \Exception("API Secret not set");
        }

        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->subaccount = $subaccount;
    }

    protected function request_info($request , $method , $params = []) {

        $timestamp = time() * 1000;

        $request = '/api'.$request;

        if (!empty($params)) {
            $query = http_build_query($params, '', '&');
            $auth = $timestamp . $method . $request . '?' . $query;
            $signature = hash_hmac('sha256', $auth , $this->api_secret);
            $endpoint = $this->base_url . $request  . '?' . $query;
        } else {    
            $auth = $timestamp . $method . $request;
            $signature = hash_hmac('sha256', $auth , $this->api_secret);
            $endpoint = $this->base_url . $request;
        }

        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, (int) (10000));
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, (int) (10000));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($method == 'GET') {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        } elseif ($method == 'POST') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
        }

        $headers = [
            'FTX-KEY' => $this->api_key,
            'FTX-TS' => $timestamp,
            'FTX-SIGN' => $signature,
            'FTX-SUBACCOUNT' => $this->subaccount
        ];

        if (!$headers) {
            $headers = array();
        } elseif (is_array($headers)) {
            $tmp = $headers;
            $headers = array();
            foreach ($tmp as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
        }

        if ($headers) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $output = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $output = substr($output, $header_size);
        
        curl_close($curl);
        
        $json = json_decode($output, true);

        return $json;
    }

    public function get_positions() {
        return $this->request_info('/positions' , 'GET' , ['showAvgPrice' => TRUE]);
    }

    public function get_account_info() {
        return $this->request_info('/account' , 'GET');
    }

    public function get_balances() {
        return $this->request_info('/wallet/balances' , 'GET');
    }

    public function get_deposits($params = array()) {
        return $this->request_info('/wallet/deposits' , 'GET' , $params);
    }

    public function get_withdrawals($params = array()) {
        return $this->request_info('/wallet/withdrawals' , 'GET' , $params);
    }

    public function markets() {
        return $this->request_info('/fills' , 'GET');
    }
}
