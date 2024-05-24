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
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    $username = $_SESSION['username'];

    function getProductDetails($productName){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $productName1 = encrypt($productName);
        $status = encrypt('active');
        
        $sqlCheckProduct = $conn->query("SELECT * FROM loan_products WHERE product_name='$productName1' AND product_status='$status'");
        
        if($sqlCheckProduct->num_rows > 0){
            $productInfo = $sqlCheckProduct->fetch_assoc();
            $interestRate = decrypt($productInfo['product_interest']);
            $product_maxAmount = decrypt($productInfo['product_maxAmount']);
            $product_maxTerm = decrypt($productInfo['product_maxTerm']);
            $product_fees = decrypt($productInfo['product_fees']);
            $repayFrequency = decrypt($productInfo['repaymentFrequency']);
            
            if($repayFrequency === "Daily"){
                $installmentNo = 28;
            } else if ($repayFrequency === "Weekly"){
                $installmentNo = 4;
            } else if ($repayFrequency === "Monthly"){
                $installmentNo = 1;
            } else {
                $installmentNo = null;
            }
            
            $response = array(
                "success" => true,
                "message"=>"valid",
                "int_rate"=> $interestRate,
                "prod_max"=>$product_maxAmount,
                "max_term"=>$product_maxTerm,
                "prod_fees"=>$product_fees,
                "repaymtFrequency"=>$repayFrequency,
                "installmentNo" => $installmentNo
                );
            
        } else {
            $response = array("success" => false, "message"=> 'Invalid Product. Select Correctly.');
        }
        
        $conn->close();
        
        return $response;
    }
    
    function getProductDetailsEdit($productNo){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $status = encrypt('active');
        
        $sqlCheckProduct = $conn->query("SELECT * FROM loan_products WHERE product_no='$productNo'");
        
        if($sqlCheckProduct->num_rows > 0){
            $productInfo = $sqlCheckProduct->fetch_assoc();
            $interestRate = decrypt($productInfo['product_interest']);
            $product_maxAmount = decrypt($productInfo['product_maxAmount']);
            $product_maxTerm = decrypt($productInfo['product_maxTerm']);
            $product_fees = decrypt($productInfo['product_fees']);
            $repayFrequency = decrypt($productInfo['repaymentFrequency']);
            
            
            $response = array(
                "success" => true,
                "message"=>"valid",
                "int_rate"=> $interestRate,
                "prod_max"=>$product_maxAmount,
                "max_term"=>$product_maxTerm,
                "prod_fees"=>$product_fees,
                "repayFrequency"=>$repayFrequency,
                );
            
        } else {
            $response = array("success" => false, "message"=> 'Invalid Product. Select Correctly.');
        }
        
        $conn->close();
        
        return $response;
    }
    
    function validateApproverNow($loanNo){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $sqlApprover = $conn->query("SELECT * FROM loan_applications WHERE loan_no='$loanNo'");
        
        if($sqlApprover->num_rows > 0){
            $sqlApproverResult = $sqlApprover->fetch_assoc();
            $loan_reviewer1 = decrypt($sqlApproverResult['loan_reviewer']);
            $product = decrypt($sqlApproverResult['loan_product']);
            $firstDate = $sqlApproverResult['firstRepaymentDate'];
            
            $firstRepayDate = ($firstDate === null) ? null: decrypt($firstDate);
            
            $prodDetails = getProductDetails($product);
            
            $prodDetails = json_encode($prodDetails);
            
            // Decode the JSON string into an associative array
            $data = json_decode($prodDetails, true);
            
            // Extract repayFrequency
            $repayFrequency = $data['repaymtFrequency'];
            
            if($loan_reviewer1 === null){
                $loan_reviewer = null;
                $response = array(
                        "success" => true, 
                        "message"=> "$loan_reviewer",
                        "repayFrequency"=> "$repayFrequency",
                        "firstRepayDate"=> "$firstRepayDate"
                    );
            } else {
                $loan_reviewer = $loan_reviewer1;
                $response = array(
                        "success" => true, 
                        "message"=> "$loan_reviewer",
                        "repayFrequency"=> "$repayFrequency",
                        "firstRepayDate"=> "$firstRepayDate"
                    );
            }
        } else {
            $response = array(
                "success" => false, 
                "message"=> 'Invalid loan details.',
                "repayFrequency"=> null,
                "firstRepayDate"=> null
                );
        }
        
        $conn->close();
        
        return $response;
    }
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $requestData = file_get_contents("php://input");
        $data = json_decode($requestData);
        
        $check = $data->check;
        
        if($check === 'productInfo'){
            $productName = $data->product;
            
            $response = getProductDetails($productName);
            
            echo json_encode($response);
        } elseif($check === 'validateApprover'){
            $data = $data->data;
            $loanDetails = explode(" - " , $data);
            
            $loanNo = $loanDetails[0];
            
            
            $response = validateApproverNow($loanNo);
            
            echo json_encode($response);
            
        }  elseif($check === 'productInfoEdit'){
            $productNo2 = $data->product;
            $productNo1 = explode(" - ", $productNo2);
            $productNo = $productNo1[0];
            
            $response = getProductDetailsEdit($productNo);
            
            echo json_encode($response);
        }
    }


















?>