<?php
/*****

(c) 2021 - Lemmod

Only for first installation

*/
error_reporting(E_ALL);


include ('app/Config.php');
include ('app/Core.php');
include ('app/DataMapper.php');
//include ('app/functions.php');

if (empty(DB_DBNAME) OR DB_DBNAME == 'your_database_name') {
    echo '<h1> Database name not set , please change Config.php to correct settings.</h1>';
    die();
}

$dataMapper = new DataMapper();

// Steps
// 1 - Setup database
// 2 - Add user
// 3 - Forward user to front end

$step = $_REQUEST['step'];
$action = $_REQUEST['action'];

if(empty($step)) {
    $step = 1;
}

// Built-in checks

$accounts_exist = $dataMapper->dbh->prepare('SHOW tables like "account"');
$accounts_exist->execute();
$accounts_exist_res = $accounts_exist->fetch(PDO::FETCH_ASSOC);

$user_exists = $dataMapper->dbh->prepare('SELECT COUNT(*) AS total FROM users;');
$user_exists->execute();
$user_exists_res = $user_exists->fetch(PDO::FETCH_ASSOC);





// Step 1 - Setup the database. Check if the accounts table allready exist (probably allready installed)
if ($step == 1) {

    echo '<h1> Step 1/3 - Setup database</h1>';

    if (!$accounts_exist_res) {
        echo '<h2> <a href="install.php?action=create_tables">Setup the database.</a></h2>';
    } else {
        echo '<h2> Seems the database is allready set. Click <a href="install.php?step=2">here</a> if you want to add an user</h2>';
    }
}


if ($step == 2) {

    echo '<h1> Step 2/3 - Add user</h1>';

    // If user attempts to go step 2 but the database isn't installed yet return to step 1
    if (!$accounts_exist_res) {
        header('Location: install.php?step=1');
    }

    echo 'Set up a username and password :
    <form action="install.php" method="post">
        <input type="hidden" name="action" value="insert_user">

        
        <label for="user_name">
            Username :
        </label>
        <input type="text" name="user_name" placeholder="Username" id="user_name" required>
        <label for="password">
            Password :
        </label>
        <input type="password" name="password" placeholder="Password" id="password" required>
        <input type="submit" value="Add user">
    </form>';
}


if ($step == 3) {

    echo '<h1> Step 3/3 - Setup complete</h1>';

    // If user attempts to go step 3 but there are no users return to step 2. Maybe even go to step 1 if the database isn't set.
    if ($user_exists_res['total'] == 0) {
        header('Location: install.php?step=2');
    }

    echo '<h2> Setup completed. Go <a href="index.php">to the admin homepage.</a> </h2>';
}



if($action == "create_tables") {

    $create_tables = '

    CREATE TABLE account (
            account_id int(11) NOT NULL,
            user_id int(20) NOT NULL,
            account_name varchar(100) NOT NULL,
            exchange enum(\'binance\',\'bybit\',\'ftx\') NOT NULL,
            api_key varchar(250) NOT NULL,
            api_secret varchar(250) NOT NULL,
            subaccount varchar(250) DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          
          CREATE TABLE pnl (
            pnl_id int(11) NOT NULL,
            account_id int(11) NOT NULL,
            start_time bigint(20) NOT NULL,
            end_time bigint(20) NOT NULL,
            wallet_balance decimal(20,8) NOT NULL,
            unrealized_pnl decimal(20,8) NOT NULL,
            transfer_in decimal(20,8) NOT NULL,
            transfer_out decimal(20,8) NOT NULL,
            exposure decimal(18,2) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          
          CREATE TABLE users (
            user_id int(11) NOT NULL,
            user_name varchar(50) NOT NULL,
            password varchar(255) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
          
          
          ALTER TABLE account
            ADD PRIMARY KEY (account_id);
          
          ALTER TABLE pnl
            ADD PRIMARY KEY (pnl_id);
          
          ALTER TABLE users
            ADD PRIMARY KEY (user_id);
          
          
          ALTER TABLE account
            MODIFY account_id int(11) NOT NULL AUTO_INCREMENT;
          
          ALTER TABLE pnl
            MODIFY pnl_id int(11) NOT NULL AUTO_INCREMENT;
          
          ALTER TABLE users
            MODIFY user_id int(11) NOT NULL AUTO_INCREMENT;
    ';

    // Create the tables
    $stmt = $dataMapper->dbh->prepare($create_tables);
    $stmt->execute();

    $stmt = null;

    header('Location: install.php?step=2');
}

if($action == "insert_user") {
    $insert_user = $dataMapper->dbh->prepare("INSERT INTO users (user_name , password) VALUES (:user_name , :password)");
    $insert_user->bindParam(':user_name', $_POST['user_name']);
    $insert_user->bindParam(':password', password_hash($_POST['password'] , PASSWORD_DEFAULT));
    $insert_user->execute();

    $insert_user = null;

    header('Location: install.php?step=3');
}

