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

    function getStaffDetailsNow($staffID){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $staffID1 = explode(" - ", $staffID);
        $staffID = $staffID1[0];
        
        $sqlCheckStaff = $conn->query("SELECT * FROM staff WHERE staff_no='$staffID'");
        
        $staffInfo = $sqlCheckStaff->fetch_assoc();
        $staff_name = decrypt($staffInfo['staff_name']);
        $staff_phone = decrypt($staffInfo['staff_phone']);
        $staff_email = decrypt($staffInfo['staff_email']);
        $rate = decrypt($staffInfo['rate']);
        $joinDate = decrypt($staffInfo['joinDate']);
        $location_name = $staffInfo['location_name'];
        
        
        $response = array(
            "success" => true,
            "name"=>$staff_name,
            "phone"=> $staff_phone,
            "email"=>$staff_email,
            "joined"=>$joinDate,
            "rate"=>$rate,
            "location1"=>$location_name
            );
            
        $conn->close();
        
        return $response;
    }
    
    function getCustomerDetailsNow($customerID){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $staffID1 = explode(" - ", $customerID);
        $customerID = $staffID1[0];
        
        $sqlCheckStaff = $conn->query("SELECT * FROM customers WHERE customer_no='$customerID'");
        
        $staffInfo = $sqlCheckStaff->fetch_assoc();
        $staff_name = decrypt($staffInfo['customer_name']);
        $staff_phone = decrypt($staffInfo['customer_phone']);
        $customer_idno = decrypt($staffInfo['customer_idno']);
        $staff_email = decrypt($staffInfo['customer_email']);
        $joinDate = decrypt($staffInfo['joinDate']);
        $location_name = $staffInfo['location_name'];
        $status = decrypt($staffInfo['status']);
        
        
        $response = array(
            "success" => true,
            "name"=>$staff_name,
            "phone"=> $staff_phone,
            "idno"=> $customer_idno,
            "email"=>$staff_email,
            "joined"=>$joinDate,
            "location1"=>$location_name,
            "statusNow"=>$status
            );
            
        $conn->close();
        
        return $response;
    }
    
    function getMemberInfo($memberPhone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $memberPhone1 = encrypt($memberPhone);
        
        //check whether customer has any pending application        
        $approvedStat = encrypt("Not Approved");
        $declinedStat = encrypt("Reviewed");
        $declinedStat1 = encrypt("Resubmitted");
        
        //check whether customer has any pending application        
        $approvedStat = encrypt("Not Approved");
        $declinedStat = encrypt("Reviewed");
        $declinedStat1 = encrypt("Resubmitted");
        
        $sqlCheckMemberApplications = $conn->query("SELECT * FROM loan_applications WHERE customer_phone='$memberPhone1' AND (loan_status = '$approvedStat' OR loan_status = '$declinedStat' OR loan_status = '$declinedStat1')");
        
        if($sqlCheckMemberApplications->num_rows > 0){
            $response = array(
                "success" => false,
                "message"=>'Customer has a pending loan application.',
                );
            return $response;
            exit;
        }
        
        //check whether customer has any outstanding loans
        $sqlCheckMember = $conn->query("SELECT * FROM loans WHERE customer_phone='$memberPhone1'");
        
        if($sqlCheckMember->num_rows > 0){
            $loan_balance = 0;
            
            while($memberInfoFetch = $sqlCheckMember->fetch_assoc()){
                $loan_balance += intval(decrypt($memberInfoFetch['loan_balance']));
            }
            
            if(intval($loan_balance) > 0){
                $response = array(
                    "success" => false,
                    "message"=>'Customer has an Outstanding loan balance of ' . $loan_balance,
                    );
                return $response;
                exit;
            }
        }
        
        //check whether customer is active or blacklisted
        $sqlCheckMemberActive = $conn->query("SELECT * FROM customers WHERE customer_phone='$memberPhone1'");
        
        if($sqlCheckMemberActive->num_rows > 0){
            $memberInfoFetchActive = $sqlCheckMemberActive->fetch_assoc();
            $status = decrypt($memberInfoFetchActive['status']);
            
            if($status !== 'active'){
                $response = array(
                    "success" => false,
                    "message"=>'Customer is ' . $status
                    );
                return $response;
                exit;
            }
        } else {
            $response = array(
                "success" => false,
                "message"=>'Customer does not exist. Check the number again.',
                );
            return $response;
            exit;
        }
            
        
        
        $response = checkPaymentCode($conn, $memberPhone);
        
        $conn->close();
        
        return $response;
        
    }
    
    function getTopupInfo($memberPhone, $loanNum){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $memberPhone1 = encrypt($memberPhone);
        
        //check whether customer is active or blacklisted
        $sqlCheckMemberActive = $conn->query("SELECT * FROM customers WHERE customer_phone='$memberPhone1'");
        
        if($sqlCheckMemberActive->num_rows > 0){
            $memberInfoFetchActive = $sqlCheckMemberActive->fetch_assoc();
            $status = decrypt($memberInfoFetchActive['status']);
            
            if($status !== 'active'){
                $response = array(
                    "success" => false,
                    "message"=>'Customer is ' . $status
                    );
                return $response;
                exit;
            }
        } else {
            $response = array(
                "success" => false,
                "message"=>'Customer does not exist. Check the number again.',
                );
            return $response;
            exit;
        }
        
        //check whether customer has any pending application        
        $approvedStat = encrypt("Not Approved");
        $declinedStat = encrypt("Reviewed");
        $declinedStat1 = encrypt("Resubmitted");
        
        $sqlCheckMemberApplications = $conn->query("SELECT * FROM loan_applications WHERE customer_phone='$memberPhone1' AND (loan_status = '$approvedStat' OR loan_status = '$declinedStat' OR loan_status = '$declinedStat1')");
        
        if($sqlCheckMemberApplications->num_rows > 0){
            $response = array(
                "success" => false,
                "message"=>'Customer has a pending loan application.',
                );
            return $response;
            exit;
        } else {
            
            $responsePaid = checkPaymentCode($conn, $memberPhone); 
            
            if($responsePaid['success'] == true){
                return getLoanInfo($loanNum);
            } else {
                return checkPaymentCode($conn, $memberPhone);
            }
        }
        
    }
    
    function getLoanInfo($loanNum){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        
        //check whether customer has any outstanding loans
        $sqlCheckMember = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNum' ORDER BY s_no DESC");
        
        if($sqlCheckMember->num_rows > 0){
            $memberInfoFetch = $sqlCheckMember->fetch_assoc();
            $loan_product = decrypt($memberInfoFetch['loan_product']);
            $topupBal = decrypt($memberInfoFetch['loan_balance']);
            
            $response = array(
                "success" => true,
                "message"=> 'Okay',
                "topupLoanType"=>$loan_product,
                "topupBal"=>$topupBal
                );
            return $response;
            exit;
            
        }
            
        $conn->close();
        
        $response = array(
            "success" => false,
            "message"=>'Loan not found.',
            "topupLoanType"=>null,
            "topupBal"=>null
            );
        
        return $response;
        
    }
    
    function pullFilter($conn, $column){ 
        $result = $conn->query("SELECT DISTINCT $column FROM customers");
        $options = ""; // Initialize an empty string to store options
    
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $colValues = ($column === 'location_name') ? $row[$column] : decrypt($row[$column]);
                $options .= "<option value=\"$colValues\">"; // Append each option to the string
            }
        } else {
            $options = "<option value='No results found'>"; // Set default option if no results found
        }
    
        return $options; // Return the accumulated options
    }
    
    function checkPaymentCode($conn, $phonePaid){ 
        $mobile = '254' . substr($phonePaid, -9);
        
        $today = date('Y-m-d');
        
        $result = $conn->query("SELECT * FROM customer_registration WHERE account = '$mobile' ORDER BY s_no DESC LIMIT 1");

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                $date = $row['date'];
                $dateOnly = date('Y-m-d', strtotime($date));
                
                if($dateOnly === $today){
                    
                    $mpesa_reference = $row['mpesa_reference'];
                    
                    $response = array(
                        "success" => true,
                        "message"=>$mpesa_reference
                    );
                } else {
                    $response = array(
                        "success" => true,
                        "message"=>'Advice customer to pay afresh for new loan application.'
                    );
                }
            }
        } else {
            $response = array(
                "success" => true,
                "message"=>'Loan application fee not paid.'
            );
            
        }
    
        return $response; // Return the accumulated options
    }
    
    function checkValidity($conn, $loanNo){
        //check whether customer has any outstanding loans
        $sqlCheckMember = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo' ");
        
        if($sqlCheckMember->num_rows > 0){
            $response = array(
                "success" => true,
                "message"=> 'Okay',
            );
            return $response;
            exit;
            
        }
        
        $conn->close();
        
        $response = array(
            "success" => false,
            "message"=>'Loan not found. Select correctly.',
        );
        
        return $response;
    }
    
    function getCustomerOwner($conn, $customerNo){
        //check whether customer has any outstanding loans
        $sqlCheckMember = $conn->query("SELECT * FROM customers WHERE customer_no='$customerNo' ");
        
        if($sqlCheckMember->num_rows > 0){
            $ownerRow = $sqlCheckMember->fetch_assoc();
            $owner = $ownerRow['staff_phone'];
            $owner1 = decrypt($owner);
            
            //get staff name
            $sqlGetStaffName = $conn->query("SELECT * FROM staff WHERE staff_phone = '$owner' ");
            $sqlGetStaffNameRow = $sqlGetStaffName->fetch_assoc();
            $staffName1 = decrypt($sqlGetStaffNameRow['staff_name']);
            
            $staffDetails = "$staffName1 - $owner1";
            
            $response = array(
                "success" => true,
                "message"=> $staffDetails,
            );
            return $response;
            exit;
            
        }
        
        $conn->close();
        
        $response = array(
            "success" => false,
            "message"=>'Customer not found. Select correctly.',
        );
        
        return $response;
    }

    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $requestData = file_get_contents("php://input");
        $data = json_decode($requestData);
        
        $check = $data->check;
        
        if($check === 'getMemberDetails'){
            $staffID1 = $data->data;
            
            $response = getStaffDetailsNow($staffID1);
            
            echo json_encode($response);
            
        } else if($check === 'memberInfo'){
            $memberPhone = $data->data;
            
            $response = getMemberInfo($memberPhone);
            
            echo json_encode($response);
        } else if($check === 'loanInfo'){
            $loanInfo = $data->data;
            
            $loanNum1 = explode(" - ", $loanInfo);
            
            $memberPhone = (isset($loanNum1[2]))? $loanNum1[2] : $loanInfo;
            $loanNum = (isset($loanNum1[0]))? $loanNum1[0] : $loanInfo;
            
            $response = getTopupInfo($memberPhone, $loanNum);
            
            echo json_encode($response);
        } else if($check === 'getCustomerDetails'){
            $customerID1 = $data->data;
            
            $response = getCustomerDetailsNow($customerID1);
            
            echo json_encode($response);
            
        }  else if($check === 'getFilters'){ 
            $filterBy = $data->data;
            
            $response = pullFilter($conn, $filterBy);
            
            echo $response;
            
        } else if($check === 'getCustRegistPayment'){ 
            $phonePaid = $data->data;
            
            $response = checkPaymentCode($conn, $phonePaid);
            
            echo json_encode($response);
            
        }  else if($check === 'memberValid'){  
            $dataNow = $data->data;
            $dataNow1 = explode("-", $dataNow);
            $loanNo = $dataNow1[0];
            
            $response = checkValidity($conn, $loanNo);
            
            echo json_encode($response);
            
        } else if($check === 'getCustomerOwner'){  
            $dataNow = $data->data;
            $dataNow1 = explode("-", $dataNow);
            $customerNo = $dataNow1[0];
            
            $response = getCustomerOwner($conn, $customerNo);
            
            echo json_encode($response);
            
        }
        
    }


















?>