<?php

class BybitConnector
{

    protected $api_key = '';
    protected $api_secret = '';
    protected $base_url = 'https://api.bybit.com'; // Endpoint for binance futures , differs from SPOT API URL

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

    protected function request_info($url , $params = []) {

        $timestamp = (microtime(true) * 1000);

        if (!strpos($url , 'publid')) {
            $params['api_key'] = $this->api_key;
            $params['timestamp'] = number_format($timestamp, 0, '.', '');
        }

        ksort($params); // Paramaters need to be sorted in alphabetical order

        $query = http_build_query($params, '', '&');
        $signature = hash_hmac('sha256', urldecode(http_build_query($params)) , $this->api_secret);

        $endpoint = $this->base_url . $url . '?' . $query . '&sign=' . $signature;

        

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $endpoint);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $output = curl_exec($curl);

        //$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        //$output = substr($output, $header_size);
        
        curl_close($curl);
        
        $json = json_decode($output, true);

        return $json;
    }

    /**
     * Get the account info , contains all the info we need to get the required information
     */
    public function wallet_info() {
        return $this->request_info('/v2/private/wallet/balance');
    }

    /**
     * Get the account info , contains all the info we need to get the required information
     */
    public function get_positions() {
        return $this->request_info('/private/linear/position/list' , $params );
    }

    /**
     * Get the current risk
     */

    public function get_risk() {
        return $this->request_info('/v2/positionRisk');
    }

    /**
     * Get the lastes price of the symbol
     */
    public function get_symbol_value($symbol) {
        return $this->request_info('/public/linear/recent-trading-records' , ['symbol' => $symbol , 'limit' => 1]);
    }

    public function get_closed_pnl($symbol) {
        return $this->request_info('/private/linear/trade/closed-pnl/list' , ['symbol' => $symbol , 'limit' => 50]);
    }

    public function get_symbols() {
        return $this->request_info('/v2/public/symbols');
    }

    public function get_funding_fee($symbol) {
        return $this->request_info('/private/linear/trade/execution/list' , ['symbol' => $symbol , 'exec_type' => 'Funding']);
    }

    public function get_transfers($params = array()) {
        return $this->request_info('/asset/v1/private/transfer/list' , $params);
    }

    public function get_key_permissions() {        
        return $this->request_info('/v2/private/account/api-key');        
    }

    public function get_deposits() {
        return $this->request_info('/v2/private/wallet/withdraw/list');     
    }

    public function get_withdrawals() {
        return $this->request_info('/v2/private/wallet/deposit/list');     
    }

    public function get_open_orders($symbol) {
        return $this->request_info('/private/linear/order/list' , ['symbol' => $symbol]);  
    }

    public function get_last_candle($symbol) {
        return $this->request_info('/public/linear/index-price-kline' , ['symbol' => $symbol , 'interval' => 1 , 'from' => strtotime(date('Y-m-d H:i:00')) /* time() */, 'limit' => 1]);
    }

    public function get_api_info() {
        return $this->request_info('/v2/private/account/api-key');         
    }

    public function get_ticker($symbol) {
        return $this->request_info('/v2/public/tickers' , ['symbol' => $symbol]);
    }
}
?>
