<?php

class FTXWrapper
{
    /**
     * Create an overview of total open positions
     */
    public function load_open_positions($open_positions) {

        $result = array();
        $i = 0;

      

        foreach($open_positions as $position) {
            $invested += $position['contracts'] * $position['entryPrice'];
            $current_worth += $position['notional'];

            if ($position['cost'] != 0) {

                //pr($position);

                if ($position['side'] == 'sell') {
                    $factor = -1;
                } else {
                    $factor = 1;
                }
     

                $result[$i] = array( 
                    'symbol' => $position['future'] , 
                    'totalAsset' => $position['size'] ,
                    'entryPrice' => $position['recentBreakEvenPrice'] , 
                    'avgEntryPrice' => $position['recentAverageOpenPrice'] , 
                    'currentPrice' => $position['entryPrice']  , 
                    'profitPercentage' =>  ( ($position['entryPrice'] ) / ($position['recentBreakEvenPrice']  ) ) * $factor ,
                    'profitPercentage_min' => ( ( ($position['entryPrice'] ) / ($position['recentBreakEvenPrice'] ) - 1) * 100 ) * $factor ,
                    'investedWorth' => ($position['recentBreakEvenPrice'] * $position['size'] ) ,
                    'currentWorth' => ( $position['cost']) * $factor ,
                    'pnl' => ( ( $position['entryPrice'] * $position['size']) - ($position['recentBreakEvenPrice'] * $position['size'] ) ) * $factor ,
                    'liqPrice' => ($position['estimatedLiquidationPrice']) ,
                    'side' => $position['side'] != 'sell'  ? 'Buy' : 'Sell'
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


        if($type == 'unrealized_pnl') {
            $invested = 0;

            foreach($open_positions as $key => $position) {
                $invested += $position['recentPnl'];
            }

            return $invested;
        }

        if($type == 'maintainance_margin') {
            $invested = 0;

            foreach($open_positions as $position) {
                $invested += $position['collateralUsed'];
            }

            return $invested;
        }


        if($type == 'invested') {
            $invested = 0;

            foreach($open_positions as $position) {
                $invested += ($position['recentBreakEvenPrice'] * $position['size']);
            }

            return $invested;
        }

        if($type == 'current_worth') {
            $current_worth = 0;

            foreach($open_positions as $position) {

                
                if ($position['side'] == 'sell') {
                    $factor = -1;
                } else {
                    $factor = 1;
                }

                //pr($position);
                $current_worth += ($position['cost'] * $factor);
            }

            return $current_worth;
        }
    }
}
