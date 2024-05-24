<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/sendsms.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');

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
    
    function arrearsToday($conn){
        
        clearLoanArrearsSmsQ($conn);
        
        $open = encrypt('Open');
        
        //notify customers whose loans are due today, cron-job created
        $sqlSchedules1 = "SELECT * FROM loans WHERE loan_status = '$open' ORDER BY s_no ASC";
        
        $resultSchedules1 = $conn->query($sqlSchedules1);
        
        // Loop through the table data and generate HTML code for each row
        if ($resultSchedules1->num_rows > 0) {
            
            while ($rowSchedules1 = $resultSchedules1->fetch_assoc()) {
                
                $phone = decrypt($rowSchedules1["customer_phone"]) ;
                $amount_inArrears = intval(decrypt($rowSchedules1["amount_inArrears"]));
                $days_inArrears = intval(decrypt($rowSchedules1["days_inArrears"]));
                $s_no = $rowSchedules1["s_no"];
                $loan_no = $rowSchedules1["loan_no"];
                
                if($amount_inArrears == 0 || empty($amount_inArrears)){
                    //do nothing
                } else {
                    $smsSentAlready = checkIfSMSIsSent($conn, $loan_no);
                    
                    if($smsSentAlready){
                        //do not send twice, do nothing
                    } else {
                        $recipient = $phone;
                        $message = "Dear customer, your loan is $days_inArrears days in ARREARS. Please pay a total of Kshs." . $amount_inArrears . " to remain in good standing in our books. Ignore if already paid. Thank you. Truesales Credit Ltd.";
                        
                        //sendSMS($recipient, $message);
                        
                        markSMSAsSent($conn, $loan_no);
                    }
                }
            }
        }
    }
    
    
    arrearsToday($conn);
    
    function clearLoanArrearsSmsQ($conn){
        $conn->query("TRUNCATE loan_arrears_smsQ");
    }
    
    function checkIfSMSIsSent($conn, $loan_no){
        $sqlSmsSent = $conn->query("SELECT * FROM loan_arrears_smsQ WHERE loan_no = '$loan_no'");
        if($sqlSmsSent->num_rows > 0){
            return true;
        } else {
            return false;
        }
    }
    
    function markSMSAsSent($conn, $loan_no){
        $conn->query("INSERT INTO loan_arrears_smsQ (loan_no) VALUES ($loan_no)");
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>