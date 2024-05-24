<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/notifications.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');

    use Dotenv\Dotenv;
    use IntaSend\IntaSendPHP\Transfer;
    use IntaSend\IntaSendPHP\Wallet;
     
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
    
    function creditSmsBalance($conn, $net_amount) { 
        //check SMS units balance 
        $sqlSmsUnitBal = $conn->query("SELECT sms FROM wallet");
        $sqlSmsUnitBalR = $sqlSmsUnitBal->fetch_assoc();
        $return = intval($sqlSmsUnitBalR['sms']);
        
        $smsCount = floor($net_amount) * 2;
        
        $addSMS = $return + intval($smsCount);
        
        $conn->query("UPDATE wallet SET sms=$addSMS");
        
        return $addSMS;
    }
    
    function purchaseSMS($net_amount){
        $name = 'MFI-SMS';
        $account3 = $_ENV['ADVANTA_PAYBILL'];
        $accountref3 = $_ENV['ADVANTA_ACCOUNT'];
        $amount3 = intval(floor($net_amount)) - 30;
        $narration3 = 'MFI-SMS BUY';
        $walletID = $_ENV['MFISMS_WALLET'];
        
        $transactions3 = [
            ['name' => $name,'account'=>$account3,'amount'=>$amount3, 'account_type'=>'PayBill', 'account_reference'=>$accountref3, 'narrative'=> $narration3]
        ];
        
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $transfer = new Transfer();
        $transfer->init($credentials);
        
        $response=$transfer->send_money("MPESA-B2B", "KES", $transactions=$transactions3, $callback_url=null,  $wallet_id=$walletID);
        $response = $transfer->approve($response);
        
        
    }
    
    function transferWallet($origin_wallet_id, $destination_wallet_id, $amount, $narrative){
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $wallet = new Wallet();
        $wallet->init($credentials);
        
        
        $wallet->intra_transfer($origin_wallet_id, $destination_wallet_id, $amount, $narrative);
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
        $invoice_id = $data['invoice_id'];
        $state = $data['state'];
        $provider = $data['provider'];
        $charges =  $data['charges'];
        $net_amount =  $data['net_amount'];
        $value = $data['value'];
        $account = $data['account'];
        $api_ref =  $data['api_ref'];
        $api_ref100 = explode("-", $api_ref);
        $api_ref1 = $api_ref100[0];
        
        $clearing_status = null;
        if(isset($data['clearing_status'])){
            $clearing_status = $data['clearing_status'];
        }else {
            $clearing_status = null;
        }
        
        $mpesa_reference = $data['mpesa_reference'];
        $failed_reason = $data['failed_reason'];
        $failed_code =  $data['failed_code'];
        $updated_at = $data['updated_at'];
        
        if($passKey === $_ENV['INTASEND_CALLBACK_KEY'] && $api_ref1 === "MFI" ){
            // Write the received data to a file
            $file = 'received_data.json';
            file_put_contents($file, $jsonData);
            
            //Check whether transaction is already created in table
            $stmt = $conn->prepare("SELECT * FROM mpesa_collections WHERE invoice_id = ?");
            $stmt->bind_param("s", $invoice_id);
            $stmt -> execute();
            $foundInvoice = $stmt->get_result();
            
            if ($foundInvoice->num_rows > 0){
                $stmt = $conn->prepare("UPDATE mpesa_collections SET state=?, clearing_status=?, mpesa_reference=?, failed_reason=?, failed_code=? WHERE invoice_id=?");
                $stmt->bind_param("ssssss", $state, $clearing_status, $mpesa_reference, $failed_reason, $failed_code, $invoice_id);
                $stmt->execute();
                
                
                if($state === "COMPLETE" && $api_ref === "MFI-SMS"){
                    $origin_wallet_id = 'KJ9JN8Y';
                    $destination_wallet_id = $_ENV['MFISMS_WALLET']; 
                    $narrative = 'SMS Purchase';
                    
                    transferWallet($origin_wallet_id, $destination_wallet_id, $net_amount, $narrative);
                    
                    purchaseSMS($net_amount);
                    
                    $unitAmount = floor($net_amount) - 30;
                    
                    creditSmsBalance($conn, $unitAmount);
                    
                    $messageSMS = "Hi, Purchase of SMS units worth Kshs." . (floor($net_amount) - 30) . " by " . $account . " via " . $provider . " is " . $state . " at " . $updated_at . ". Reference: " . $mpesa_reference . " . Thank you!" ;

                    notify($messageSMS);
                    
                } elseif($state === "COMPLETE" && $api_ref === "MFI-CUSTOMERS"){
                    $dateRegistPaid = date("Y-m-d H:i:s");
                    $stmtRegis = $conn->prepare("INSERT INTO customer_registration (invoice_id, state, provider, charges, net_amount, value, account, api_ref, clearing_status, mpesa_reference, failed_reason, failed_code, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmtRegis->bind_param("sssssssssssss", $invoice_id, $state, $provider, $charges, $net_amount, $value, $account, $api_ref, $clearing_status, $mpesa_reference, $failed_reason, $failed_code, $dateRegistPaid);
                    $stmtRegis->execute();
                    
                    $valueNow = intval($value) - ceil((intval($value) * (3 / 100)));
                    
                    $message = "Hi, Customer $account has paid $valueNow for registration.";
                    notify($message);
                }
                
                
                
            } else {
                $stmt = $conn->prepare("INSERT INTO mpesa_collections (invoice_id, state, provider, charges, net_amount, value, account, api_ref, clearing_status, mpesa_reference, failed_reason, failed_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssssssss", $invoice_id, $state, $provider, $charges, $net_amount, $value, $account, $api_ref, $clearing_status, $mpesa_reference, $failed_reason, $failed_code);
                $stmt->execute();
            }
            
            // Respond with a JSON acknowledgment
            $response = json_encode(["status" => "success"]);
            
        } else {
            $response = json_encode(["status" => "error authenticating"]);
        }
    }
    
    // Set the response content type to JSON
    header('Content-Type: application/json');
    
    // Send the JSON response
    echo $response;
    
    
?>