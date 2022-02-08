<?php

class DataMapper extends Core
{

     /**
     * insert_pnl_records
     *
     * @param  string $tv_input
     * @return void
     */
    public function insert_pnl_record($account_id , $start_time , $end_time , $wallet_balance , $unrealized_pnl , $transfer_in , $transfer_out , $exposure) {

        try{
               
            $stmt = $this->dbh->prepare('
                INSERT INTO pnl 
                    (account_id , start_time , end_time , wallet_balance , unrealized_pnl , transfer_in , transfer_out , exposure) 
                    VALUES 
                    (:account_id , :start_time , :end_time , :wallet_balance , :unrealized_pnl , :transfer_in , :transfer_out , :exposure) ');
            $stmt->bindParam(':account_id', $account_id);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':wallet_balance', $wallet_balance);
            $stmt->bindParam(':unrealized_pnl', $unrealized_pnl);
            $stmt->bindParam(':transfer_in', $transfer_in);
            $stmt->bindParam(':transfer_out', $transfer_out);
            $stmt->bindParam(':exposure', $exposure);
            $stmt->execute();

            $stmt = null;
            
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

}