<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    require_once __DIR__ .'/../templates/crypt.php';

    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);

    function getMemberBalances($tableColumn){
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $admin = $_SESSION['admin'];
        $member_no = $_SESSION['member_no'];
        
        if($member_no > 0){
            if(!$admin){
                //Get the user's details only
                $sqlSavingsBal = $conn->query("SELECT $tableColumn FROM member WHERE staff_no='$member_no' ");
                $sqlSavingsBalResult = $sqlSavingsBal->fetch_assoc();
                $savingsBal = 0;
                $savingsBalAmount = decrypt($sqlSavingsBalResult[$tableColumn]);
                $savingsBal += $savingsBalAmount;
                
            } else {
                //Calculate the total for that column to get for all members
                $sqlSavingsBal = $conn->query("SELECT $tableColumn FROM member");
                if($sqlSavingsBal->num_rows > 0){
                    
                    $savingsBal = 0;
                    
                    while($sqlSavingsBalRows = $sqlSavingsBal->fetch_assoc()){
                        $savingsBalAmount = decrypt($sqlSavingsBalRows[$tableColumn]);
                        
                        $savingsBal += $savingsBalAmount;
                    }
                } else {
                    $savingsBal = 0;
                }
                
            }
        } else {
            //Calculate the total for that column to get for all members
            $sqlSavingsBal = $conn->query("SELECT $tableColumn FROM member");
            if($sqlSavingsBal->num_rows > 0){
                
                $savingsBal = 0;
                
                while($sqlSavingsBalRows = $sqlSavingsBal->fetch_assoc()){
                    $savingsBalAmount = decrypt($sqlSavingsBalRows[$tableColumn]);
                    
                    $savingsBal += $savingsBalAmount;
                }
            } else {
                $savingsBal = 0;
            }
        }
        
        return $savingsBal;
    }
    
    function getMemberBalances1($tableColumn){
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $admin = $_SESSION['admin'];
        $member_no = $_SESSION['member_no'];
        
        if($member_no > 0){
            //Get the user's details only
            $sqlSavingsBal = $conn->query("SELECT $tableColumn FROM member WHERE staff_no='$member_no' ");
            if($sqlSavingsBal->num_rows > 0){
                $sqlSavingsBalResult = $sqlSavingsBal->fetch_assoc();
                $savingsBal = decrypt($sqlSavingsBalResult[$tableColumn]);
                
                if($savingsBal === null){
                    $savingsBal = 0;
                } else {
                    $savingsBal = $savingsBal;
                }
            } else {
                $savingsBal = 0;
            }
        } else {
            $savingsBal = '0';
        }
        
        return $savingsBal;
    }
    
    
    function getMembershipInfo($tableColumn){
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $admin = $_SESSION['admin'];
        $member_no = $_SESSION['member_no'];
        
        if($member_no > 0){
            //Get the user's details only
            $sqlSavingsBal = $conn->query("SELECT $tableColumn FROM member WHERE staff_no='$member_no' ");
            if($sqlSavingsBal->num_rows > 0){
                $sqlSavingsBalResult = $sqlSavingsBal->fetch_assoc();
                $savingsBal = decrypt($sqlSavingsBalResult[$tableColumn]);
                
                if($savingsBal === null){
                    $savingsBal = 0;
                } else {
                    $savingsBal = $savingsBal;
                }
            } else {
                $savingsBal = 0;
            }
        } else {
            $savingsBal = '0';
        }
        
        return $savingsBal;
    }


?>