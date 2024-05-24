<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    require_once __DIR__ .'/../templates/crypt.php';

    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);

    function checkIfMemberExists($checkPhone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $checkPhone1 = encrypt($checkPhone);
        
        $sqlCheckMember = $conn->query("SELECT * FROM staff WHERE staff_phone='$checkPhone1'");
        
        if($sqlCheckMember->num_rows > 0){
            $sqlCheckMemberStatus = $sqlCheckMember->fetch_assoc()['status'];
            $sqlCheckMemberStatus = decrypt($sqlCheckMemberStatus);
            
            if($sqlCheckMemberStatus === 'active'){
                $response = array("success" => true, "message"=> 'active');
            } else {
                $response = array("success" => true, "message"=> 'exited');
            }
            
        } else {
            $response = array("success" => false, "message"=> 'null');
        }
        
        $conn->close();
        
        return $response;
    }
    
    function checkIfCustomerExists($checkPhone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $checkPhone1 = encrypt($checkPhone);
        
        $sqlCheckMember = $conn->query("SELECT * FROM customers WHERE customer_phone='$checkPhone1'");
        
        if($sqlCheckMember->num_rows > 0){
            $sqlCheckMemberStatus = $sqlCheckMember->fetch_assoc()['status'];
            $sqlCheckMemberStatus = decrypt($sqlCheckMemberStatus);
            
            if($sqlCheckMemberStatus === 'active'){
                $response = array("success" => true, "message"=> 'active');
            } else {
                $response = array("success" => true, "message"=> $sqlCheckMemberStatus);
            }
            
        } else {
            $response = array("success" => false, "message"=> 'null');
        }
        
        $conn->close();
        
        return $response;
    }
    
    function checkMemberBalances($staffNo){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $sqlCheckMember = $conn->query("SELECT * FROM member WHERE staff_no='$staffNo'");
        
        if($sqlCheckMember->num_rows > 0){
            $sqlCheckMemberStatus = $sqlCheckMember->fetch_assoc();
            
            $message = '';
            $success = 0;
            //Check Savings Balance
            $savingsBal = decrypt($sqlCheckMemberStatus['savings_bal']);
            if($savingsBal > 0){
                $message .= 'Savings,';
                $success += 1;
            }
            
            //Check Shares Balance
            $shares_bal = decrypt($sqlCheckMemberStatus['shares_bal']);
            if($shares_bal > 0){
                $message .= ', Shares ,';
                $success += 1;
            }
            
            //Check Dividends Balance
            $dividends_bal = decrypt($sqlCheckMemberStatus['dividends_bal']);
            if($dividends_bal > 0){
                $message .= ', Dividends ,';
                $success += 1;
            }
            
            //Check Loan Balance
            $loan_bal = decrypt($sqlCheckMemberStatus['loan_bal']);
            if($loan_bal > 0){
                $message .= ', Loan ,';
                $success += 1;
                
            }
            
            if($success > 0){
                $response = array("success" => true, "message"=> $message);
            } else {
                $response = array("success" => false, "message"=> 'No balances');
            }
            
        } else {
            
            $response = array("success" => false, "message"=> 'No balances');
        }
        
        $conn->close();
        
        return $response;
    }
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $requestData = file_get_contents("php://input");
        $data = json_decode($requestData);
        
        $check = $data->check;
        
        if($check === 'memberExists'){
            $memberPhone = $data->phone;
            
            $checkPhone = '0' . substr($memberPhone, -9);
            
            $response = checkIfMemberExists($checkPhone);
            
            echo json_encode($response);
        } elseif($check === 'memberHasBalance'){
            $memberPhone = $data->phone;
            
            $memberData = explode(' - ', $memberPhone);
            $memberPhone1 = $memberData[2]; 
            
            $checkPhone = '0' . substr($memberPhone1, -9);
            $checkPhone1 = encrypt($checkPhone);
            
            $sqlGetStaffNo = $conn->query("SELECT staff_no FROM staff WHERE staff_phone='$checkPhone1'");
            $sqlGetStaffNoResult = $sqlGetStaffNo->fetch_assoc();
            $staffNo = $sqlGetStaffNoResult['staff_no'];
            
            $response = checkMemberBalances($staffNo);
            
            echo json_encode($response);
        } elseif($check === 'customerExists'){
            $memberPhone = $data->phone;
            
            $checkPhone = '0' . substr($memberPhone, -9);
            
            $response = checkIfCustomerExists($checkPhone);
            
            echo json_encode($response);
        }
    }


?>