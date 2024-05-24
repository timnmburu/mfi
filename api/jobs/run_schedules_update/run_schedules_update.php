<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/sendsms.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
    $dotenv->load();
    
    function run_schedules_update(){
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        //check all loans whose balance is zero and settle their schedules
        $zero = encrypt("0");
        //$conn->query("UPDATE loan_schedules SET interestPaid = interest, principalPaid = principal WHERE loan_balance = '$zero'");
        
        //check all loans whose balance is not zero
        $sqlGetZeroLoans = $conn->query("SELECT loan_no FROM loans WHERE loan_balance = '$zero' ");
        if($sqlGetZeroLoans->num_rows > 0){
            while($sqlGetZeroLoansRow = $sqlGetZeroLoans->fetch_Assoc()){
                $lNo = $sqlGetZeroLoansRow['loan_no'];
                $conn->query("UPDATE loan_schedules SET paid = loan_installment, interestPaid = interest, principalPaid = principal WHERE loan_no = '$lNo'");
            }
        }
        
        //check all loans whose balance is zero and settle their schedules
        $sqlGetNotZeroLoans = $conn->query("SELECT loan_no FROM loans WHERE loan_balance <> '$zero' ");
        if($sqlGetNotZeroLoans->num_rows > 0){
            while($sqlGetZeroLoansRows = $sqlGetNotZeroLoans->fetch_assoc()){
                $loanNo = $sqlGetZeroLoansRows['loan_no'];
                
                //reset all the schedules
                $conn->query("UPDATE loan_schedules SET paid = NULL, interestPaid = NULL, principalPaid = NULL WHERE loan_no='$loanNo'");
                
                //check the schedule balances for that loan number
                $sqlGetLoanSchedule = $conn->query("SELECT * FROM loan_schedules WHERE loan_no='$loanNo'");
                $sqlGetLoanScheduleRows = $sqlGetLoanSchedule->fetch_assoc();
                $principal = intval(decrypt($sqlGetLoanScheduleRows['principal']));
                $interest = intval(decrypt($sqlGetLoanScheduleRows['interest']));
                $loan_installment = intval(decrypt($sqlGetLoanScheduleRows['loan_installment']));
                
                //pget total paid amount for that loan
                $sqlGetPaidAmount = $conn->query("SELECT loan_payments FROM loans WHERE loan_no='$loanNo'");
                $sqlGetPaidAmountRow = $sqlGetPaidAmount->fetch_assoc();
                $paidAmount = intval(decrypt($sqlGetPaidAmountRow['loan_payments']));
                
                //divide the loan repayment by the installment to get the number of installments paid
                $numOfPaidInst = floor($paidAmount / $loan_installment);
                
                $excessOfInst = $paidAmount % $loan_installment;
                
                $times = intval($numOfPaidInst);
                
                for ($i = 1; $i <= $times; $i++) {
                    $loan_installment1 = encrypt($loan_installment);
                    $principal1= encrypt($principal);
                    $interest1 = encrypt($interest);
                    $conn->query("UPDATE loan_schedules SET paid = '$loan_installment1', principalPaid='$principal1', interestPaid='$interest1' WHERE loan_no = '$loanNo' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                }
                
                if(intval($excessOfInst) > 0){
                    //post excess payment above installment
                    if(intval($excessOfInst) < intval($interest)){
                        $interest10 = encrypt($excessOfInst);
                        $principal10 = encrypt("0");
                        $conn->query("UPDATE loan_schedules SET paid = '$interest1', principalPaid='$principal10', interestPaid='$interest10' WHERE loan_no = '$loanNo' AND (paid IS NULL OR principalPaid IS NULL OR interestPaid IS NULL) ORDER BY s_no ASC LIMIT 1");
                    } else if (intval($excessOfInst) > intval($interest)){
                        $interest10 = encrypt($interest);
                        $principal10 = encrypt(intval($excessOfInst - $interest));
                        $paid0 = encrypt($excessOfInst);
                        $conn->query("UPDATE loan_schedules SET paid = '$paid0', principalPaid='$principal10', interestPaid='$interest10' WHERE loan_no = '$loanNo' AND (paid IS NULL OR principalPaid IS NULL OR interestPaid IS NULL) ORDER BY s_no ASC LIMIT 1");
                    }
                }
            }
        }
    }
    
    function run_schedules_update1() {
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Fetch all loans and their schedules
            $sqlGetZeroLoans = $conn->query("SELECT loan_no, loan_payments FROM loans");
            
            while ($sqlGetZeroLoansRows = $sqlGetZeroLoans->fetch_assoc()) {
                $loanNo = $sqlGetZeroLoansRows['loan_no'];
                $paidAmount = intval(decrypt($sqlGetZeroLoansRows['loan_payments']));
                
                // Fetch the loan schedule for the current loan
                $sqlGetLoanSchedule = $conn->query("SELECT * FROM loan_schedules WHERE loan_no='$loanNo'");
                
                // Store the schedules in an array
                $loanSchedules = [];
                while ($sqlGetLoanScheduleRows = $sqlGetLoanSchedule->fetch_assoc()) {
                    $loanSchedules[] = [
                        's_no' => $sqlGetLoanScheduleRows['s_no'],
                        'principal' => intval(decrypt($sqlGetLoanScheduleRows['principal'])),
                        'interest' => intval(decrypt($sqlGetLoanScheduleRows['interest'])),
                        'loan_installment' => intval(decrypt($sqlGetLoanScheduleRows['loan_installment']))
                    ];
                }
                
                // Reset all schedules for the current loan
                $conn->query("UPDATE loan_schedules SET paid = NULL, interestPaid = NULL, principalPaid = NULL WHERE loan_no='$loanNo'");
                
                foreach ($loanSchedules as $schedule) {
                    $principal = $schedule['principal'];
                    $interest = $schedule['interest'];
                    $loan_installment = $schedule['loan_installment'];
                    
                    // Calculate number of installments paid and excess installment
                    $numOfPaidInst = floor($paidAmount / $loan_installment);
                    $excessOfInst = $paidAmount % $loan_installment;
                    
                    // Update the schedules based on paid installments
                    for ($i = 0; $i < $numOfPaidInst; $i++) {
                        $loan_installment_encrypted = encrypt($loan_installment);
                        $principal_encrypted = encrypt($principal);
                        $interest_encrypted = encrypt($interest);
                        
                        $conn->query("UPDATE loan_schedules SET paid = '$loan_installment_encrypted', principalPaid='$principal_encrypted', interestPaid='$interest_encrypted' WHERE loan_no = '$loanNo' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                    }
                    
                    // Handle excess installment
                    if ($excessOfInst > 0) {
                        if ($excessOfInst < $interest) {
                            $interest10 = encrypt($excessOfInst);
                            $principal10 = encrypt(0);
                        } else {
                            $interest10 = encrypt($interest);
                            $principal10 = encrypt($excessOfInst - $interest);
                        }
                        $paid0 = encrypt($excessOfInst);
                        $conn->query("UPDATE loan_schedules SET paid = '$paid0', principalPaid='$principal10', interestPaid='$interest10' WHERE loan_no = '$loanNo' AND (paid IS NULL OR principalPaid IS NULL OR interestPaid IS NULL) ORDER BY s_no ASC LIMIT 1");
                    }
                }
            }
            
            // Commit transaction
            $conn->commit();
        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            $conn->rollback();
            echo "Failed: " . $e->getMessage();
        }
        
        $conn->close();
    }


    
    run_schedules_update();
    
    
    
    
    
 
    
    
?>