<?php
    require_once __DIR__ . '/../vendor/autoload.php'; 
    require_once __DIR__ . '/../templates/crypt.php'; 
    
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    function logAction($action){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = mysqli_connect($db_servername, $db_username, $db_password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $username = $_SESSION['username'];
        $username = encrypt($username);
        
        $date = date('Y-m-d H:i:s');
        $date = encrypt($date);
        
        $action = encrypt($action);
        
        $log = "INSERT INTO `userlogs`(`username`, `user_activity`, `date`) VALUES ('$username', '$action', '$date')";
        mysqli_query($conn, $log);
        
    }


?>