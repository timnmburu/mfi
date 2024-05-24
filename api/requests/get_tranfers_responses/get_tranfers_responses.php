<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/notifications.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');
    require_once(__DIR__ . '/../../../templates/sendsms.php');

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
    
    // Read the raw POST data from the callback
    $jsonData = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($jsonData, true);
    
    if ($data === null) {
        // JSON decoding failed; handle the error
        $response = json_encode(["status" => "error with json response"]);
    } else {
        $passKey = $data['challenge'];
        $file_id = $data['file_id'];
        $tracking_id = $data['tracking_id'];
        $status = $data['status'];
        $status_code = $data['status_code'];
        
        // Access transactions array by index 0
        $transaction = $data['transactions'][0];
        $transaction_id = $transaction['transaction_id'];
        $transaction_status = $transaction['status'];
        $transaction_status_code = $transaction['status_code'];
        $provider = $transaction['provider'];
        $bank_code = $transaction['bank_code'];
        $name=$transaction['name'];
        $account = $transaction['account'];
        $account_type = $transaction['account_type'];
        $account_reference = $transaction['account_reference'];
        $provider_reference = $transaction['provider_reference'];
        $provider_account_name = $transaction['provider_account_name'];
        $amount = $transaction['amount'];
        $charge = $transaction['charge'];
        $narrative = $transaction['narrative'];
        
        $failed_amount = $data['failed_amount'];
        
        // Access wallet array by key 'wallet'
        $wallet = $data['wallet'];
        $wallet_available_balance = $wallet['available_balance'];
        
        $updated_at = $transaction['updated_at'];
        
        if($passKey === $_ENV['INTASEND_CALLBACK_KEY'] && $narrative === "MFI-SMS BUY") {
            // Write the received data to a file
            $file = 'received_data.json';
            file_put_contents($file, $jsonData);
            
            if($transaction_status === 'Completed'){
                //
            }
            
            //Check whether transaction is already created in table
            $stmt = $conn->prepare("SELECT * FROM mpesa_transfers WHERE tracking_id = ?");
            $stmt->bind_param("s", $tracking_id);
            $stmt -> execute();
            $foundInvoice = $stmt->get_result();
            
            if ($foundInvoice->num_rows > 0){
                $stmt1 = $conn->prepare("UPDATE mpesa_transfers SET status=?, status_code=?, transaction_status=?, transaction_status_code=?, provider_reference=?, provider_account_name=?, failed_amount=?, wallet_available_balance=?, updated_at=? WHERE tracking_id=?");
                $stmt1->bind_param("ssssssssss", $status, $status_code, $transaction_status, $transaction_status_code, $provider_reference, $provider_account_name, $failed_amount, $wallet_available_balance, $updated_at, $tracking_id);
                $stmt1->execute();
                $stmt1->close();
                
                if($transaction_status !== 'Pending'){
                    $notification = 'Hi, initiated payment of Kshs.' . $amount . ' to '. $account .' was ' .  $transaction_status . ' at '. $updated_at. '. Payment reference: ' . $provider_reference . '. Thank you.';
                    notify($notification);
                }
                
                
            } else {
                $stmt2 = $conn->prepare("INSERT INTO mpesa_transfers (file_id, tracking_id, status, status_code, transaction_id, transaction_status, transaction_status_code, provider, bank_code, name, account, account_type, account_reference, provider_reference, provider_account_name, amount, charge, narrative, failed_amount, wallet_available_balance, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param("sssssssssssssssssssss", $file_id, $tracking_id, $status, $status_code, $transaction_id, $transaction_status, $transaction_status_code, $provider, $bank_code, $name, $account, $account_type, $account_reference, $provider_reference, $provider_account_name, $amount, $charge, $narrative, $failed_amount, $wallet_available_balance, $updated_at);
                $stmt2->execute();
                $stmt2->close();
            }
            
            // Respond with a JSON acknowledgment
            $response = json_encode(["status" => "success"]);
        } else {
            $response = json_encode(["status" => "failed to authenticate."]);
        }
    }
    
    // Set the response content type to JSON
    header('Content-Type: application/json');
    
    // Send the JSON response
    echo $response;
    
    $conn->close();
    
?>
