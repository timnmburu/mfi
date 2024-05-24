<?php    
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    
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
    
    //get SMS MessageID that has no delivery status
    function getSMSMessageIDWithoutStatus($conn){
        $sqlGetMessageID = $conn->query("SELECT * FROM smsQ WHERE delivery IS NULL OR delivery = 'Scheduled' ORDER BY s_no ASC");
        if($sqlGetMessageID->num_rows > 0){
            
            while($sqlGetMessageIDResult = $sqlGetMessageID->fetch_assoc()){
                $messageID = $sqlGetMessageIDResult['messageID'];
                
                $deliveryStatusResult = getSMSdeliveryReport($conn, $messageID);
                $deliveryStatus = $deliveryStatusResult['status'];
                $deliveryTime = $deliveryStatusResult['timing'];
        
                //Update the delivery status 
                updateStatus($conn, $messageID, $deliveryStatus, $deliveryTime);
                
                echo "Updated delivery successfully.";
            }
        } else {
            echo "Updated all delivery reports.";
        }
    }
    
    function getSMSdeliveryReport($conn, $messageID){
        
        $getDelvUrl = "https://quicksms.advantasms.com/api/services/getdlr/";
        
        // API Key, Partner ID, and Message ID
        $apikey = $_ENV['ADVANTASMS_API_KEY'];
        $partnerID = $_ENV['ADVANTASMS_PARTNERID'];
        
        // Create a cURL handle
        $ch = curl_init($getDelvUrl);
        
        // Set the cURL options
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'apikey' => $apikey,
                'partnerID' => $partnerID,
                'messageID' => $messageID
            )),
            CURLOPT_RETURNTRANSFER => true
        ));
        
        // Execute the request
        $json_response = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
        curl_close($ch);
        
        // Decode JSON into an associative array
        $response_array = json_decode($json_response, true);
        
        // Access the values
        $response_code = $response_array['response-code'];
        $message_id = $response_array['message-id'];
        $response_description = $response_array['response-description'];
        $delivery_status = $response_array['delivery-status'];
        $delivery_description = $response_array['delivery-description'];
        $delivery_tat = $response_array['delivery-tat'];
        $delivery_networkid = $response_array['delivery-networkid'];
        $delivery_time = $response_array['delivery-time'];
        
        // Output the response
        //DeliveredToTerminal
        //SenderName Blacklisted
        
        //if($delivery_description === 'DeliveredToTerminal'){
            //return 'Delivered';
        //} else {
            //return 'Blocked';
        //}
        $return = array("status"=>$delivery_description, "timing"=>$delivery_time);
        
        return $return;
    
    }
    
    function updateStatus($conn, $messageID, $deliveryStatus, $deliveryTime){
        //update the delivery status
        $sqlUpdate = $conn->query("UPDATE smsQ SET delivery='$deliveryStatus', dateDelivered='$deliveryTime' WHERE messageID ='$messageID' ");
        
        if($sqlUpdate){
            echo "Updated successfully";
        } else {
            echo "Error updating";
        }
    }
    
    getSMSMessageIDWithoutStatus($conn);
    
?>


