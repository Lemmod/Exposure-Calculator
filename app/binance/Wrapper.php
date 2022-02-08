<?php

class BinanceWrapper
{
    /**
     * Create an overview of total open positions
     */
    public function load_open_positions($open_positions) {

        $result = array();
        $i = 0;

        foreach($open_positions as $position) {

            if ($position['positionAmt'] < 0) {
                $factor = -1;
            } else {
                $factor = 1;
            }

            $invested += (($position['positionAmt'] * $position['entryPrice']) * $factor);
            $current_worth += $position['notional'];

            if ($position['entryPrice'] > 0) {

                 $result[$i] = array( 
                    'symbol' => $position['symbol'] , 
                    'totalAsset' => $position['positionAmt'] ,
                    'entryPrice' => $position['entryPrice'] , 
                    'currentPrice' => $position['notional'] / $position['positionAmt'] , 
                    'profitPercentage' =>  ($position['notional'] ) / ($position['entryPrice'] * $position['positionAmt'] ) ,
                    'profitPercentage_min' =>  ( ($position['notional'] ) / ($position['entryPrice'] * $position['positionAmt'] ) - 1) * 100 ,
                    'investedWorth' => ($position['entryPrice'] * $position['positionAmt'] ) ,
                    'currentWorth' => $position['notional'] ,
                    'pnl' => $position['notional'] - ($position['entryPrice'] * $position['positionAmt'] )  ,
                    'liqPrice' => $risk[array_search($position['symbol'] , array_column($risk, 'symbol'))]['liquidationPrice'] ,
                    'side' => ($position['positionAmt'] * $position['entryPrice']) > 0 ? 'Buy' : 'Sell'
                );                  
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

                if ($position['positionAmt'] < 0) {
                    $factor = -1;
                } else {
                    $factor = 1;
                }

                $invested += (($position['positionAmt'] * $position['entryPrice']) * $factor);
            }

            return $invested;
        }

        if($type == 'current_worth') {
            $current_worth = 0;

            foreach($open_positions as $position) {
                $current_worth += $position['notional'];
            }

            return $current_worth;
        }
    }
}
