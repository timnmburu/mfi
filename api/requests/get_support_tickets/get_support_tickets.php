<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once (__DIR__ . '/../../../templates/sendsms.php');
    require_once (__DIR__ . '/../../../templates/crypt.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if(isset($_GET['data'])){
        
        $supportData = $_GET['data'];
        
        if($supportData <> ''){
            $supportData = explode('^', $supportData);
            
            $description = $supportData[0];        
            $action = $supportData[1]; 
            $actionBy = $supportData[2]; 
            $date = $supportData[3]; 
            $status = $supportData[4]; 
            $selectedTicketID = $supportData[5];
            
            $description = encrypt($description);
            $action = encrypt($action);
            $actionBy = encrypt($actionBy);
            $date = encrypt($date);
            $status = encrypt($status);
            
            $sqlUpdateTickets = "UPDATE support_tickets SET comments='$description', action= '$action', action_by='$actionBy', action_date='$date', status='$status' WHERE ticketID='$selectedTicketID'";
            
            $resultUpdate = $conn->query($sqlUpdateTickets);
            
            if($resultUpdate){
                echo "success";
            } else {
                echo "Error" . $conn->error;
            }
        } else {
            echo "null data";
        }
    }
?>