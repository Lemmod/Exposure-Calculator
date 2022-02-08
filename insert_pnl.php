<?php

$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;

ini_set('display_errors', 1);
error_reporting(E_ERROR);

date_default_timezone_set('Europe/Amsterdam');

include (__DIR__.'/app/binance/Connector.php');
include (__DIR__.'/app/binance/Wrapper.php');
include (__DIR__.'/app/bybit/Connector.php');
include (__DIR__.'/app/bybit/Wrapper.php');
include (__DIR__.'/app/ftx/Connector.php');
include (__DIR__.'/app/ftx/Wrapper.php');
include (__DIR__.'/app/Config.php');
include (__DIR__.'/app/Core.php');
include (__DIR__.'/app/DataReader.php');
include (__DIR__.'/app/DataMapper.php');

$dataReader = new DataReader();
$dataMapper = new DataMapper();
$accounts = $dataReader->get_accounts();

foreach ($accounts as $account) {

    $start_time = $dataReader->get_end_time_pnl($account['account_id']);
    
    if(is_null($start_time['time'])) {
        $start_time['time'] = round( (microtime(true) - (60 * 60)) * 1000);
    }

    //echo '<h1>'.$account['account_name'].'</h1>';


    if ($account['exchange'] == 'binance') {
        $binance = new BinanceConnector($account['api_key'] , $account['api_secret']);
        $binance_wrapper = new BinanceWrapper();

        $account_info= $binance->account_info();

        // Total wallet for inserting on wallet_balance
        $total_wallet = $account_info['totalWalletBalance'];
        $total_unrealized = $account_info['totalUnrealizedProfit'];

        //echo 'wallet_balance = '.$total_wallet.'<br />';
        //echo 'unrealized_pnl = '.$total_unrealized.'<br />';

        $transfers = $binance->get_transfers(['asset' => 'USDT' , 'startTime' => $start_time['time']]);

        //Core::pr($transfers);

        $transfer_in = 0;
        $transfer_out = 0;
        foreach($transfers['rows'] as $transfer) {
            // 2 is from USDT to Spot , so transfer out
            if($transfer['type'] == 2 && $transfer['status'] == 'CONFIRMED') {
                $transfer_out += $transfer['amount'];
            }
            // 2 is from USDT to Spot , so transfer out
            if($transfer['type'] == 1  && $transfer['status'] == 'CONFIRMED') {
                $transfer_in += $transfer['amount'];
            }
        }  

        $total_maintainance_margin = $account_info['totalMaintMargin'];
        $invested = $binance_wrapper->load_totals('invested' , $account_info['positions']);

        
        $exposure = number_format(  ($invested+$total_maintainance_margin) / $total_wallet , 2);

        //echo 'transfer_in = '.$transfer_in.'<br />';
        //echo 'transfer_out = '.$transfer_out.'<br />';       
    }

    if ($account['exchange'] == 'bybit') {

        $bybit = new BybitConnector($account['api_key'] , $account['api_secret']);
        $bybit_wrapper = new BybitWrapper();

        $wallet_info = $bybit->wallet_info();
        $positions_info = $bybit->get_positions();

        $total_wallet = $wallet_info['result']['USDT']['wallet_balance'];
        $total_unrealized = $wallet_info['result']['USDT']['unrealised_pnl'];

        //echo 'wallet_balance = '.$total_wallet.'<br />';
        //echo 'unrealized_pnl = '.$total_unrealized.'<br />';

        $transfers = $bybit->get_transfers(['coin' => 'USDT' , 'start_time' => (int) ($start_time['time'] / 1000)]);

        $transfer_in = 0;
        $transfer_out = 0;
        foreach($transfers['result']['list'] as $transfer) {
            // From Contract (Deriv) to spot is out
            if($transfer['from_account_type'] == 'CONTRACT' && $transfer['to_account_type'] == 'SPOT' && $transfer['status'] == 'SUCCESS') {
                $transfer_out += $transfer['amount'];
            }
            // From Spot to Contract is in
            if($transfer['from_account_type'] == 'SPOT' && $transfer['to_account_type'] == 'CONTRACT'  && $transfer['status'] == 'SUCCESS') {
                $transfer_in += $transfer['amount'];
            }
        }  

        $invested = $bybit_wrapper->load_totals('invested' , $positions_info['result']);
        $total_maintainance_margin = $wallet_info['result']['USDT']['position_margin'] + $wallet_info['result']['USDT']['unrealised_pnl'];

        $exposure = number_format(  ($invested+$total_maintainance_margin) / $total_wallet , 2);

        //echo 'transfer_in = '.$transfer_in.'<br />';
        //echo 'transfer_out = '.$transfer_out.'<br />'; 

    }

    if ($account['exchange'] == 'ftx') {

        $ftx = new FTXConnector($account['api_key'] , $account['api_secret'] , $account['subaccount']);
        $ftx_wrapper = new FTXWrapper();

        $account_info = $ftx->get_account_info();
        $fetched_positions = $ftx->get_positions();
        $balances = $ftx->get_balances();

        $total_unrealized = $ftx_wrapper->load_totals('unrealized_pnl' , $fetched_positions['result']);
        $total_maintainance_margin = $account_info['result']['collateral'] - $account_info['result']['freeCollateral'];
        $total_wallet = $account_info['result']['totalAccountValue'] + ( $total_unrealized * - 1);
        $total_margin_balance = $total_wallet + $total_unrealized;

        $deposits = $ftx->get_deposits(['start_time' => (int) ($start_time['time'] / 1000)]);
        $withdrawals = $ftx->get_withdrawals(['start_time' => (int) ($start_time['time'] / 1000)]);

        $transfer_in = 0;
        $transfer_out = 0;

        foreach($deposits['result'] as $desposit) {
            if($desposit['status'] == 'complete' && $desposit['coin'] == 'USD') {
                $transfer_in += $desposit['size'];
            }
        }

        foreach($withdrawals['result'] as $withdrawal) {
            if($withdrawal['status'] == 'complete' && $withdrawal['coin'] == 'USD') {
                $transfer_out += $withdrawal['size'];
            }
        }

        foreach($balances['result'] as $balance) {
            if ($balance['coin'] == 'USD') {
                $total_wallet = $balance['total'];
            }
        }

        $total_wallet_exposure = $account_info['result']['totalAccountValue'] + ( $total_unrealized * - 1);
        $invested = $ftx_wrapper->load_totals('invested' , $fetched_positions['result']);   

        //$exposure = number_format(  ($invested) / $total_wallet , 2);

        $exposure = number_format(  ($invested) / $total_wallet_exposure , 2);

        //echo 'transfer_in = '.$transfer_in.'<br />';
        //echo 'transfer_out = '.$transfer_out.'<br />'; 
    }

    $dataMapper->insert_pnl_record($account['account_id'] , $start_time['time'] , round( (microtime(true) ) * 1000)  , $total_wallet , $total_unrealized , $transfer_in , $transfer_out , $exposure);
}