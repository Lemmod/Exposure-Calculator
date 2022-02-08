<?php

class BybitWrapper
{

     /**
     * Create an overview of total open positions
     */
    public function load_open_positions($open_positions) {

        $result = array();
        $i = 0;

        foreach ($open_positions as $position) {
           
            if ($position['data']['size'] > 0) {

                //pr($position);

                if ($position['data']['side'] == 'Sell') {
                    $factor = -1;
                } else {
                    $factor = 1;
                }

                $bybit = new BybitConnector('a','b');

                $ticker = $bybit->get_ticker($position['data']['symbol']);

                $current_price = $ticker['result'][0]['bid_price'];

                $result[$i] = array( 
                    'symbol' => $position['data']['symbol'] , 
                    'totalAsset' => $position['data']['size'] ,
                    'entryPrice' => $position['data']['entry_price'] , 
                    'currentPrice' => $current_price ,
                    'profitPercentage' =>  ( ($$current_price * $position['data']['size']) / ($position['data']['entry_price'] * $position['data']['size']) ) * $factor ,
                    'profitPercentage_min' =>  ( ( ($current_price * $position['data']['size']) / ($position['data']['entry_price'] * $position['data']['size'] ) - 1) * 100 ) * $factor ,
                    'investedWorth' => ($position['data']['entry_price'] * $position['data']['size'] ) ,
                    'currentWorth' => ($current_price * $position['data']['size']) ,
                    'pnl' =>  ( ($current_price * $position['data']['size']) - ($position['data']['entry_price'] * $position['data']['size']) ) * $factor  ,
                    'side' => $position['data']['side'] ,
                    'liqPrice' => $position['data']['liq_price']);
                $i++;
            }
        }

        array_multisort(array_column($result, 'profitPercentage'),  SORT_DESC , $result);
        return $result;
    }

     /**
     * Get totals from the open positions
     */
    public function load_totals($type , $open_positions) {

        if($type == 'invested') {
            $invested = 0;

            foreach($open_positions as $position) {
                $invested += $position['data']['position_value'];
            }

            return $invested;
        }

        if($type == 'current_worth') {
            $current_worth = 0;

            foreach($open_positions as $position) {
                $current_worth += $position['data']['position_value'] + $position['data']['unrealised_pnl'];
            }

            return $current_worth;
        }
    }
}
