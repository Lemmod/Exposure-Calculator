<?php

class BinanceConnector
{

    protected $api_key = '';
    protected $api_secret = '';
    protected $base_url = 'https://fapi.binance.com/fapi'; // Endpoint for binance futures , differs from SPOT API URL
    protected $base_url_sapi = 'https://api.binance.com/sapi'; 

    public function __construct($api_key , $api_secret) {

        if(empty($api_key)) {
            throw new \Exception("API Key not set");
        }

        if(empty($api_secret)) {
            throw new \Exception("API Secret not set");
        }

        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }

    protected function request_info($url , $params = [] , $sapi = FALSE /* Only for transfers */) {

        $timestamp = (microtime(true) * 1000);
        $params['timestamp'] = number_format($timestamp, 0, '.', '');

        $query = http_build_query($params, '', '&');
        $signature = hash_hmac('sha256', $query, $this->api_secret);

        // For futures we need another base_url
        if ($sapi) {
            $endpoint = $this->base_url_sapi . $url . '?' . $query . '&signature=' . $signature;
        } else {
            $endpoint = $this->base_url . $url . '?' . $query . '&signature=' . $signature;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'X-MBX-APIKEY: ' . $this->api_key,
        ));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        $output = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $output = substr($output, $header_size);
        
        curl_close($curl);
        
        $json = json_decode($output, true);

        return $json;

    }

    /**
     * Get the account info , contains all the info we need to get the required information
     */
    public function account_info() {
        return $this->request_info('/v2/account');
    }

    /**
     * Get the account info , contains all the info we need to get the required information
     */
    public function income($params) {
        return $this->request_info('/v1/income' , $params );
    }

    /**
     * Get the current risk
     */

    public function get_risk() {
        return $this->request_info('/v2/positionRisk');
    }

    /**
     * Get transfer information , needs another endpoint
     */

     public function get_transfers($params) {
        return $this->request_info('/v1/futures/transfer' , $params , true);
     }
}
?>