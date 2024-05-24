<?php

    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../templates/crypt.php';

    use IntaSend\IntaSendPHP\Collection;
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    

    function notify($msg){

        // Your bot's API token
        $botToken = $_ENV['TELEGRAM_BOT_API_TOKEN'];

        // Your chat ID
        $chatID = $_ENV['TELEGRAM_CHAT_ID'];
        

        // Error details
        //$msg = 'An error occurred on the server: Something went wrong!';

        // Telegram API endpoint
        $telegramAPI = "https://api.telegram.org/bot{$botToken}/sendMessage";

        // Prepare the message data
        $messageData = [
            'chat_id' => $chatID,
            'text' => $msg,
        ];

        // Use cURL to send the message
        $ch = curl_init($telegramAPI);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $messageData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Check the response if needed
        if ($response === false) {
            // Handle cURL error
        } else {
            $responseData = json_decode($response, true);
            // Handle the Telegram API response if needed
        }
        
    }
    
    function saveNotification($message, $level){
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
        
        $date = date('Y-m-d H:i:s');
        $date = encrypt($date);
        $message = encrypt($message);
        $level = encrypt($level);
        $action = 'new';
        $action = encrypt($action);
        
        
        $log = "INSERT INTO `notifications`(`message`, `date`, `level`, `action`) VALUES ('$message', '$date', '$level', '$action')";
        mysqli_query($conn, $log);
    }


?>