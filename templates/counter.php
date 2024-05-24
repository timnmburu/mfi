<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    require_once __DIR__ .'/../templates/crypt.php';

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
    
    function addCount($column, $location_name){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        // Replace this with your own logic to retrieve payment details from the database
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $date = date("F Y");
        
        $encryptedDate = encrypt($date);
        
        
        $sqlLastCount = $conn->query("SELECT $column FROM counter WHERE location_name='$location_name' AND month_year='$encryptedDate'");
        
        $newCount = 0;
        
        if($sqlLastCount->num_rows > 0){
            $resultCount = $sqlLastCount->fetch_assoc();
            $count = intval(decrypt($resultCount[$column]));
            
            $newCount = $count + 1;
            
            $newCount1 = encrypt($newCount);
            
            $conn->query("UPDATE counter SET $column='$newCount1' WHERE location_name='$location_name' AND month_year='$encryptedDate'");
        } else {
            $newCount = 1;
            
            $newCount1 = encrypt($newCount);
            
            $conn->query("INSERT INTO counter ($column, location_name, month_year ) VALUES ('$newCount1', '$location_name', '$encryptedDate')") ;
        }
    }























?>