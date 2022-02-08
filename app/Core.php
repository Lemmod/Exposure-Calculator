<?php

class Core
{
    public $dbh; // handle of the db connection

    public function __construct()
    {
		// Connection string to database
        $dsn = 'mysql:host=' . DB_HOST .
               ';dbname='    . DB_DBNAME .
               ';charset='   . DB_CHARSET .
               ';connect_timeout=15';
			   
        // Get user credentials from config for database               

        $this->dbh = new PDO($dsn, DB_USERNAME , DB_PASSWORD);
        $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
    }

    public function close_connection() {
        $this->dbh = null;
    }

    public function pr($data) {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }
 
}


