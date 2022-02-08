<?php

class DataReader extends Core
{

    public function get_accounts() {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM account ORDER BY user_id ,  account_name');
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_pnl_records($account_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT * FROM pnl WHERE account_id = :account_id');
            $stmt->bindParam(':account_id', $account_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }

    public function get_end_time_pnl($account_id) {

        try{

            $stmt = $this->dbh->prepare('SELECT MAX(end_time) as time FROM pnl WHERE account_id = :account_id');
            $stmt->bindParam(':account_id', $account_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = null;

            return $result;
        }
        catch (PDOExecption $e){
            echo $e->getMessage();
        }    
    }
}