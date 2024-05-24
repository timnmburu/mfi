<?php
require_once __DIR__ .'/../vendor/autoload.php';

use IntaSend\IntaSendPHP\Transfer;
use Dotenv\Dotenv;
use IntaSend\IntaSendPHP\Wallet;
use IntaSend\IntaSendPHP\Chagebacks;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();



$credentials = [
    'token' => $_ENV['INTASEND_TOKEN'],
    'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
];


function getWallets($credentials){
    $wallet = new Wallet();
    $wallet->init($credentials);
      
    $response = $wallet->retrieve();
    print_r($response);
}


function createWallet($credentials, $title){
    $wallet = new Wallet();
    $wallet->init($credentials);
    
    $response = $wallet->create($currency='KES', $label=$title, $can_disburse=true);
    print_r($response);
    
    //stdClass Object ( [wallet_id] => KJ9JN8Y [label] => MAIN 
    //stdClass Object ( [wallet_id] => 0LQJ5EY [label] => MFI-SMS [can_disburse] => 1 [currency] => KES [wallet_type] => WORKING [current_balance] => 0.00 [available_balance] => 0.00 [updated_at] => 2024-03-16T20:22:54.031404+03:00 )
    
}

function transferWallet($origin_wallet_id, $destination_wallet_id, $amount, $narrative){
    $credentials = [
        'token' => $_ENV['INTASEND_TOKEN'],
        'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
    ];
    
    $wallet = new Wallet();
    $wallet->init($credentials);
    
    $wallet->intra_transfer($origin_wallet_id, $destination_wallet_id, $amount, $narrative);
    //$response = $wallet->intra_transfer($origin_wallet_id, $destination_wallet_id, $amount, $narrative);
    print_r("Sent " . $amount);
}

function transferMobile(){
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
        
    //Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $credentials = [
        'token' => $_ENV['INTASEND_TOKEN'],
        'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
    ];
    
    $transfer = new Transfer();
    $transfer->init($credentials);
    
    if(isset($_GET['reason1']) && $_GET['reason1'] === "Commission"){
        $account = $_GET['account1'];
        $amount = $_GET['amount1'];
        $name = $_GET['name'];
        $availableMpesaBalance = null;
        $date = date('Y-m-d H:i:s');
        
        $transactions = [
                [
                    'account'=>$account,
                    'amount'=>$amount
                ]
            ];
            
        $walletID = $_ENV['COMMISSION_WALLET_ID'];
        
        $response=$transfer->send_money("MPESA-B2C", "KES", $transactions=$transactions, $callback_url=null,  $wallet_id=$walletID);
        $transfer->approve($response);
        //print_r($response);
        
        $sqlUpdatePaidStatus = "UPDATE payments SET commission_paid = 'Paid' WHERE staff_phone= '$account'";
        $conn->query($sqlUpdatePaidStatus);
        
        $updateCommissionPayment = "INSERT INTO `commission_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";
        $conn->query($updateCommissionPayment);
        
        exit;
        
    }
}

function buyAirtime(){
    
    $credentials = [
        'token' => $_ENV['INTASEND_TOKEN'],
        'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
    ];
    
    $transfer = new Transfer();
    $transfer->init($credentials);
    
    $account = '254724';
    $amount = '0';
    
    $transactions = [
            [
                'account'=>$account,
                'amount'=>$amount
            ]
        ];
        
    //$walletID = $_ENV['COMMISSION_WALLET_ID'];
    
    $response=$transfer->airtime("KES", $transactions=$transactions);
    $transfer->approve($response);
    print_r($response);
    
    exit;
}

function transferMobileDirect($param){
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
        
    //Database connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $credentials = [
        'token' => $_ENV['INTASEND_TOKEN'],
        'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
    ];
    
    $transfer = new Transfer();
    $transfer->init($credentials);
    
    $account = '0725887269';
    $amount = '50';
    $name = 'Tim';
    $availableMpesaBalance = null;
    $date = date('Y-m-d H:i:s');
    
    $transactions = [
            [
                'account'=>$account,
                'amount'=>$amount
            ]
        ];
        
    $walletID = $_ENV['COMMISSION_WALLET_ID'];
    
    $checkParam = date('Y-m-d');
    $checkParam = strtotime('+1 day', strtotime($checkParam));
    $checkParam = date('Y-m-d', $checkParam);
    
    if($param === $checkParam){
        $response=$transfer->send_money("MPESA-B2C", "KES", $transactions=$transactions, $callback_url=null,  $wallet_id=$walletID);
        $response = $transfer->approve($response);
        //$response = $transfer->approve('stdClass Object ( [file_id] => Y3DP5MK [device_id] => [tracking_id] => e0b08d5c-0b26-4003-bee2-5c7fc32098eb [batch_reference] => [status] => Preview and approve [status_code] => BP103 [nonce] => 6f179d [wallet] => stdClass Object ( [wallet_id] => YRLNERY [label] => SAVINGS [can_disburse] => 1 [currency] => KES [wallet_type] => WORKING [current_balance] => 327.00 [available_balance] => 327.00 [updated_at] => 2023-12-24T10:51:50.307918+03:00 ) [transactions] => Array ( [0] => stdClass Object ( [status] => Pending [status_code] => TP101 [request_reference_id] => d8a47800-728c-44b0-8586-98f954da2029 [name] => [account] => 254725887269 [id_number] => [bank_code] => [amount] => 10.00 [narrative] => ) ) [charge_estimate] => 10 [total_amount_estimate] => 20 [total_amount] => 10 [transactions_count] => 1 [created_at] => 2023-12-27T13:45:37.630473+03:00 [updated_at] => 2023-12-27T13:45:37.951062+03:00)');
        print_r($response);
        
        //$updateCommissionPayment = "INSERT INTO `commission_payments`(`name`, `phone`, `amount`, `accBal`, `date`) VALUES ('$name','$account','$amount','$availableMpesaBalance', '$date')";
        
        //$response = json_encode($response);
        //$updateCommissionPayment = "UPDATE wallet SET mpesa='$response'";
        //$conn->query($updateCommissionPayment);
        
        exit;
    } else {
        exit;
    }
}


//getWallets($credentials);
$title = "MFI-SMS";

createWallet($credentials, $title);

$origin_wallet_id = 'KZBXX6K';
$destination_wallet_id = '0WD2ZPY'; 
$amount = '50';

$narrative = 'test';

//transferWallet($origin_wallet_id, $destination_wallet_id, $amount, $narrative);

//transferMobile();

$param = '2024-03-08';

//transferMobileDirect($param);

//buyAirtime();


?>

