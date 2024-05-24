<?php
require_once __DIR__ .'/../vendor/autoload.php';
require_once __DIR__ .'/../templates/notifications.php';
require_once __DIR__ .'/../templates/crypt.php';

use IntaSend\IntaSendPHP\Transfer;
use Dotenv\Dotenv;

// Load the environment variables from .env
$dotenv = Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

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

$credentials = [
    'token' => $_ENV['INTASEND_TOKEN'],
    'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
];


$transfer = new Transfer();
$transfer->init($credentials);

$date = date('Y-m-d H:i:s');
$now = date('d F');

$name = "Customer";

$narrationName = 'Chama';

$account1 = isset($_GET['account1']) ? $_GET['account1'] : '0';
$amount1 = isset($_GET['amount1']) ? $_GET['amount1'] : '0';
$whatFor1 = isset($_GET['reason1']) ? $_GET['reason1'] : '0';
$narration1 = $narrationName;

$account2 = isset($_GET['account2']) ? $_GET['account2'] : '0';
$amount2 = isset($_GET['amount2']) ? $_GET['amount2'] : '0';
$whatFor2 = isset($_GET['reason2']) ? $_GET['reason2'] : '0';
$narration2 = $narrationName;

$account3 = isset($_GET['account3']) ? $_GET['account3'] : '0';
$accountref3 = isset($_GET['accountref3']) ? $_GET['accountref3'] : '0';
$amount3 = isset($_GET['amount3']) ? $_GET['amount3'] : '0';
$whatFor3 = isset($_GET['reason3']) ? $_GET['reason3'] : '0';
$narration3 = $narrationName;

$bankcode4 = isset($_GET['bankcode4']) ? $_GET['bankcode4'] : '0';

//Get bank code from bank name
$sqlBankCodes = "SELECT * FROM bankCodes WHERE bank_name='$bankcode4'";
$resultCodes = $conn->query($sqlBankCodes);

if ($resultCodes->num_rows > 0) {
    $rowCodes = $resultCodes->fetch_assoc();
    $bankcode4= $rowCodes['bank_code'];
} else {
    $bankcode4 = '0';
}

$account4 = isset($_GET['account4']) ? $_GET['account4'] : '0';
$amount4 = isset($_GET['amount4']) ? $_GET['amount4'] : '0';
$whatFor4 =  isset($_GET['reason4']) ? $_GET['reason4'] : '0';
$narration4 = $narrationName;

$amount1='10';
$amount2='10';
$amount3='10';
$amount4='10';

$transactions1 = [
    ['account'=>$account1,'amount'=>$amount1, 'narrative'=>$narration1]
];

$transactions2 = [
    ['name' => $name,'account'=>$account2,'amount'=>$amount2, 'account_type'=>'TillNumber', 'narrative'=> $narration2]
];

$transactions3 = [
    ['name' => $name,'account'=>$account3,'amount'=>$amount3, 'account_type'=>'PayBill', 'account_reference'=>$accountref3, 'narrative'=> $narration3]
];

$transactions4 = [
    ['name' => $name,'account'=>$account4,'amount'=>$amount4, 'bank_code'=>$bankcode4, 'narrative'=> $narration4]
];


if (!empty($account1)) {
    $response=$transfer->mpesa("KES", $transactions1);
    $account = $account1;
    $whatFor = $whatFor1;
    $amount = $amount1;
} elseif (!empty($account2)) {
    $response=$transfer->mpesa_b2b("KES", $transactions2);
    $account = $account2;
    $whatFor = $whatFor2;
    $amount = $amount2;
} elseif (!empty($account3)) {
    $response=$transfer->mpesa_b2b("KES", $transactions3);
    $account = $account3;
    $whatFor = $whatFor3;
    $amount = $amount3;
} elseif (!empty($account4)) {
    $response=$transfer->bank("KES", $transactions4);
    $account = $account4;
    $whatFor = $whatFor4;
    $amount = $amount4;
} else {
    echo "Payment Mode not captured";
    $response=null;
    $account = null;
    $whatFor = null;
    $amount = null;
}

//Check user transaction limit
$username = $_SESSION['username'];
$username1 = encrypt($username);

$sqlGetUserLimit = "SELECT * FROM users WHERE username='$username1' ";
$resultLimit = $conn->query($sqlGetUserLimit);
$resultRowLimit = $resultLimit->fetch_assoc();

$userLimit = $resultRowLimit['transaction_limit'];
$userLimit = decrypt($userLimit);

//Queue it for approval if avove user limit
$trackingInfo = json_encode($response);
$status = 'Not Approved';

$account1 = encrypt($account);
$amount = encrypt($amount);
$whatFor = encrypt($whatFor);
$date = encrypt($date); 
$trackingInfo = encrypt($trackingInfo);
$status = encrypt($status);
$username = $username1;

//Get member name
$phone = '0' . substr($account, -9);
$phone1 = encrypt($phone);

$sqlName = $conn->query("SELECT staff_name FROM staff WHERE staff_phone = '$phone1'");
$sqlNameResult = $sqlName->fetch_assoc();
$name1 = $sqlNameResult['staff_name'];

$updateMpesaPayment = "INSERT INTO `mpesa_payments`(`name`, `phone`, `amount`, `reason`, `date`, `trackingInfo`, `status`, `initiatedBy`) 
VALUES ('$name1','$account1','$amount','$whatFor', '$date', '$trackingInfo', '$status', '$username')";

$message = 'You have a new withdrawal request pending approval.';
$level = 'Admin';
saveNotification($message, $level);

if($conn->query($updateMpesaPayment)){
    //Do nothing
} else {
    echo 'Failed to make payment:' . $conn->error;
}

$conn->close();

?>

