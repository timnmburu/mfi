<?php    
    require_once __DIR__ . '/../vendor/autoload.php'; 
    require_once __DIR__ . '/../templates/notifications.php';
    
    use Dotenv\Dotenv;
    
    session_start();
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
function sendSMS($recipient, $message) {  

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    date_default_timezone_set('Africa/Nairobi');
    $date = date('Y-m-d H:i:s');
    
    if (intval(smsBalance($conn)) < 3) {
        //Do not send sms
        
    } else {
        
        if(intval(smsBalance($conn)) < 50){
            //notify Admin
            $notifctn = "SMS balance is running low. Consider topping up to avoid service interruption.";
            $level = "Superadmin";
            
            saveNotification($notifctn, $level);
        }
    
        try {
            /*Get credentials for AdvantaSMS */
            $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
            $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
            $senderB = $_ENV['ADVANTASMS_API_SENDER'];
            
            $urlSms = 'https://quicksms.advantasms.com/api/services/sendsms/?';
            $urlSms .= '&apikey=' . urlencode($apiKeyB);
            $urlSms .= '&partnerID=' . urlencode($partnerIDB);
            $urlSms .= '&message=' . urlencode($message);
            $urlSms .= '&messageID=' . urlencode($message);
            $urlSms .= '&shortcode=' . urlencode($senderB);
            $urlSms .= '&mobile=' . urlencode($recipient);
            
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $urlSms);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseURL = curl_exec($ch);
        
                if (curl_errno($ch)) {
                    $return = 'cURL Error: ' . curl_error($ch);
                }
                curl_close($ch);
                
                $responses = json_decode($responseURL, true);
                $response_code = $responses['responses'][0]['response-code'];
                $responseDescription = $responses['responses'][0]['response-description'];
                $messageId = (isset($responses['responses'][0]['messageid']))? $responses['responses'][0]['messageid'] : 'null';
                
            }  catch (Exception $e) {
                //echo 'Caught exception: ', $e->getMessage();
            }
                
            if($response_code !== null){
                $stmt=$conn->prepare("INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated, messageID, status) VALUES (?, ?, ?, ?, ?, ? ,?) ");
                $stmt->bind_param("sssssss", $recipient, $message, $urlSms, $urlSms, $date, $messageId, $responseDescription);
                $stmt->execute();
            }
        } catch (Exception $e) {
            $return = $e->getMessage();
        }
        
        debitSmsBalance($conn, $message);
    
        return $messageId;
    }
}



function sendSMSOtp($recipient, $message) {  

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $date = date('Y-m-d H:i:s');
    
    /*Get credentials for AdvantaSMS */
    $partnerIDB = $_ENV['ADVANTASMS_PARTNERID'];
    $apiKeyB = $_ENV['ADVANTASMS_API_KEY'];
    $senderB = $_ENV['ADVANTASMS_API_SENDER'];
    
    $urlSms = 'https://quicksms.advantasms.com/api/services/sendsms/?';
    $urlSms .= '&apikey=' . urlencode($apiKeyB);
    $urlSms .= '&partnerID=' . urlencode($partnerIDB);
    $urlSms .= '&message=' . urlencode($message);
    $urlSms .= '&messageID=' . urlencode($message);
    $urlSms .= '&shortcode=' . urlencode($senderB);
    $urlSms .= '&mobile=' . urlencode($recipient);
    
    /* Get credentials for MoveSMS */
    $username = $_ENV['MOVESMS_API_USERNAME'];
    $apiKey = $_ENV['MOVESMS_API_API_KEY'];
    $sender = $_ENV['MOVESMS_API_SENDER'];

    // Construct the URL with the necessary parameters
    $urlSms2 = 'https://sms.movesms.co.ke/api/compose';
    $urlSms2 .= '?username=' . urlencode($username);
    $urlSms2 .= '&api_key=' . urlencode($apiKey);
    $urlSms2 .= '&sender=' . urlencode($sender);
    $urlSms2 .= '&to=' . urlencode($recipient);
    $urlSms2 .= '&message=' . urlencode($message);
    $urlSms2 .= '&msgtype=5';
    $urlSms2 .= '&dlr=0';
    
    $stmt=$conn->prepare('INSERT INTO smsQ (recipient, message, sender1, sender2, dateInitiated) VALUES (?, ?, ?, ?, ?) ');
    $stmt->bind_param("sssss", $recipient, $message, $urlSms, $urlSms2, $date);
    $stmt->execute();
    
    $stmtSave = $conn->prepare("INSERT INTO sentSMS (recipient, message, date) VALUES (?, ?, ?)");
    $stmtSave->bind_param("sss", $recipient, $message, $date);
    $stmtSave->execute();
    
    echo "
        <script>
            fetch('$urlSms',
                    {
                        mode: 'no-cors'
                    })
                .then(response => response ? response.text()
                    .then(data => {
                        
                    }) 
                : console.error('Network response was not ok'))
                    .catch(error => 
                            console.error('Error:', error)
                            );
        </script>";
    
    $conn->close();
    
    //return  'sent';
    //return $return;
}

function smsBalance($conn) { 
    //check SMS units balance 
    $sqlSmsUnitBal = $conn->query("SELECT sms FROM wallet");
    $sqlSmsUnitBalR = $sqlSmsUnitBal->fetch_assoc();
    $return = intval($sqlSmsUnitBalR['sms']);
    
    return $return;
}

function debitSmsBalance($conn, $message) { 
    //check SMS units balance 
    $sqlSmsUnitBal = $conn->query("SELECT sms FROM wallet");
    $sqlSmsUnitBalR = $sqlSmsUnitBal->fetch_assoc();
    $return = intval($sqlSmsUnitBalR['sms']);
    
    $optout = 15; // 15 chrs for Opt Out
    $oneSMS = 160; // 160 chars for 1 SMS
    $twoSMS = 306; // 306 chars for 2 SMS
    $threeSMS = 459; // 459 chars for 3 SMS
    $fourSMS = 460; // and above
    
    $length = strlen($message); // SMS length
    
    if ($length < ($oneSMS - $optout)) {
        $smsCount = 1;
    } elseif ($length < ($twoSMS - $optout)) {
        $smsCount = 2;
    } elseif ($length < ($threeSMS - $optout)) {
        $smsCount = 3;
    } elseif ($length < ($fourSMS - $optout)) {
        $smsCount = 4;
    } else {
        $smsCount = 5;
    }
    
    $lessSMS = $return - intval($smsCount);
    
    $conn->query("UPDATE wallet SET sms=$lessSMS");
    
    return $lessSMS;
}

















?>
