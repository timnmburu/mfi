<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/sendsms.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');
    require_once(__DIR__ . '/../../../templates/loanActions.php');

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
    
    function dueToday($conn){
        //notify customers whose loans are due today, cron-job created
        $today1 = encrypt(date("Y-m-d"));
        $sqlSchedules1 = "SELECT * FROM loan_schedules WHERE due_date = '$today1' AND paid IS NULL ORDER BY s_no DESC";
    
        $resultSchedules1 = $conn->query($sqlSchedules1);
    
        // Loop through the table data and generate HTML code for each row
        if ($resultSchedules1->num_rows > 0) {
            
            while ($rowSchedules1 = $resultSchedules1->fetch_assoc()) {

                $phone = decrypt($rowSchedules1["customer_phone"]) ;
                $installment = decrypt($rowSchedules1["loan_installment"]);
                $dueWhen = decrypt($rowSchedules1["due_date"]) ;
                $loan_no = $rowSchedules1["loan_no"];
                
                $hasBal = intval(getCustomerLoanBalance($conn, $loan_no));
                
                if($hasBal > 0){
                    $recipient = $phone;
                    $message = "Dear customer, your loan is due TODAY. Please pay a total of Kshs." . $installment . " to remain in good standing in our books. Ignore if already paid. Thank you. Truesales Credit Ltd.";
                    
                    sendSMS($recipient, $message);
                    //echo $message;
                }
            }
        } else {
            exit;
        }
    }
    
    
    dueToday($conn);
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>