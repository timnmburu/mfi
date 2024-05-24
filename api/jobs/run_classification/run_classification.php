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
    
    function runClassification($conn){
        //first, get all the loans in the loan schedule that have not received a payment
        $today = date("Y-m-d");
        $open1 = encrypt("Open");
        $sqlSchedules = $conn->query("SELECT * FROM loans WHERE loan_status = '$open1' ORDER BY s_no ASC");
        if ($sqlSchedules->num_rows > 0) {
            
            clearArrearsTable($conn);
             
            while ($results = $sqlSchedules->fetch_assoc()){
                $firstRepaymentDate = decrypt($results['firstRepaymentDate']);
                $loan_no = $results['loan_no'];
                
                //get the last date of payment
                $sqlLastDate = $conn->query("SELECT due_date FROM loan_schedules WHERE loan_no = '$loan_no' ORDER BY s_no DESC LIMIT 1");
                $sqlLastDaterow = $sqlLastDate->fetch_assoc();
                $lastDate = decrypt($sqlLastDaterow['due_date']);
                
                if(strtotime($today) > strtotime($firstRepaymentDate) ){
                    //$loan_no = $results['loan_no'];
                
                    $classifiedAlready = checkClassified($conn, $loan_no);
                    
                    if($classifiedAlready){
                        //do nothing
                    } else {
                        
                        //check if last due date is past
                        $dueDatesOverdue = (strtotime($today) > strtotime($lastDate)) ? true : false;
                        
                        //get loan balance
                        $loan_balance = intval(decrypt($results['loan_balance']));
                        
                        //get the number of installments due
                        $numOfInstallmentsDue = noOfInstallmentsDue($conn, $firstRepaymentDate, $loan_no);
                        
                        //calc total amount due
                        $loan_installment = intval(decrypt($results['loan_installment'])); //2600
                        $totalAmountDue = $loan_installment * $numOfInstallmentsDue; //5200
                        
                        //check if total paid is equal to total due
                        $totalPaidToDate = intval(decrypt($results['loan_payments'])); //5000
                        
                        //classify
                        //get amount in arrears
                        $amountInArrears0 = ($dueDatesOverdue) ? $loan_balance : $totalAmountDue - $totalPaidToDate; // 5200 - 5000 = 200
                        $amountInArrears = ($amountInArrears0 == 0 || $amountInArrears0 < 1)? 0 : $amountInArrears0;
                        $amountInArrears1 = encrypt($amountInArrears);
                        
                        //how many full payments have been made
                        $noOfInstPaid = ($totalPaidToDate == 0 ) ? 0 : floor($totalPaidToDate / $loan_installment); //5000 / 2600 = 1.9 which is 1
                        
                        //calculate the number of days in arrears
                        $dayDiff = getDayDiff($conn, $loan_no); // dayDiff = 7
                        $daysInArrears0 = intval(calculateDaysInArrears($noOfInstPaid, $dayDiff, $today, $firstRepaymentDate));
                        $daysInArrears = ($amountInArrears == 0 || $amountInArrears < 1) ? 0 : $daysInArrears0;
                        $daysInArrears1 = encrypt($daysInArrears);
                        
                        //check the classification for the days in arrears for that loan
                        $classification = getClassificationCategory($conn, $loan_no, $daysInArrears);
                        $classification1 = encrypt($classification);
                        
                        //check the worst days in arrears and worst classification for that loan
                        $worstDayClass = getWorstDayClass($conn, $loan_no);
                        if($worstDayClass === null){
                            $worstDay = null;
                            $worstClass = null;
                        } else {
                            $worstDayClassExp = explode(" - ", $worstDayClass);
                            $worstDay = $worstDayClassExp[0];
                            $worstClass = $worstDayClassExp[1];
                        }
                        
                        $worstDay = ($worstDay === null) ? $daysInArrears : $worstDay;
                        $worstClass = ($worstClass === null) ? $classification : $worstClass;
                        
                        if($daysInArrears > $worstDay){
                            $worstDay0 =  $daysInArrears;
                            $worstClass0 =  $classification;
                        } else {
                            $worstDay0 = $worstDay;
                            $worstClass0 = $worstClass;
                        }
                        
                        //update the loans table accordingly
                        $worstDay1 = encrypt($worstDay0);
                        $worstClass1 = encrypt($worstClass0);
                        
                        $sqlUpdateLoan = ("UPDATE loans SET loan_classification='$classification1', days_inArrears='$daysInArrears1', amount_inArrears='$amountInArrears1', worst_classification='$worstClass1', worst_daysInArrears='$worstDay1' WHERE loan_no='$loan_no'");
                        
                        $conn->query($sqlUpdateLoan);
                        
                        if($amountInArrears > 1){
                            sendLoanToArrearsTable($conn, $loan_no); 
                        }
                        
                        markAsClassified($conn, $loan_no);
                        
                        //echo "done\n";
                     
                    }
                    
                } else {
                    $classification1 = encrypt("Normal");
                    $daysInArrears1 = encrypt("0");
                    $amountInArrears1 = encrypt("0");
                    $worstClass1 = encrypt("Normal");
                    $worstDay1 = encrypt("0");
                    
                    $sqlUpdateLoan = ("UPDATE loans SET loan_classification='$classification1', days_inArrears='$daysInArrears1', amount_inArrears='$amountInArrears1', worst_classification='$worstClass1', worst_daysInArrears='$worstDay1' WHERE loan_no='$loan_no'");
                    
                    $conn->query($sqlUpdateLoan);
                    
                    //echo "not due\n";
                }
            }
        } else {
            //echo "no loan";
        }
        
        clearClassifiedTable($conn);
    }
    
    function noOfInstallmentsDue($conn, $firstRepaymentDate, $loan_no){
        
        //get day diff
        $dayDiff = getDayDiff($conn, $loan_no);
        
        $todayDate = new DateTime();
        $date2 = new DateTime($firstRepaymentDate);
        
        $diff = $todayDate->diff($date2);
        
        $totalDays = $diff->d ;
        
        //how many cycles
        $cycles = ceil($totalDays / $dayDiff);
        
        return $cycles;
    }
    
    function getDayDiff($conn, $loan_no){
        //check the repayment frquency of the loan
        $repaymentFreq11 = $conn->query("SELECT repaymentFrequency FROM loans WHERE loan_no='$loan_no'");
        $repaymentFreqResult = $repaymentFreq11->fetch_assoc();
        $repaymentFreq = $repaymentFreqResult['repaymentFrequency'];
        $repaymentFreq1 = decrypt($repaymentFreq);
        
        if($repaymentFreq1 == "Weekly"){
            $dayDiff = 7;
        } else if($repaymentFreq1 == "Daily"){
            $dayDiff = 1;
        } else if($repaymentFreq1 == "Monthly"){
            $dayDiff = 30;
        } else {
            $dayDiff = 7;
        }
        
        return $dayDiff;
    }
    
    function markAsClassified($conn, $loan_no){
        $conn->query("INSERT INTO counter (classified) VALUES ('$loan_no')");
    }
    
    function checkClassified($conn, $loan_no){
        $sqlClassified = $conn->query("SELECT * FROM counter WHERE classified ='$loan_no'");
        if($sqlClassified->num_rows > 0){
            return true;
        } else {
            return false;
        }
    }
    
    function getFirstRepaymentDate($conn, $loan_no){
        $sqlBal = $conn->query("SELECT * FROM loans WHERE loan_no='$loan_no'");
        if($sqlBal->num_rows > 0){
            $sqlBalResult = $sqlBal->fetch_assoc();
            
            $firstDate = strtotime(decrypt($sqlBalResult['firstRepaymentDate']));
            $today = strtotime(date("Y-m-d"));
            
            if($firstDate > $today){
                return false;
            } else {
                return $firstDate;
            }
        } else {
            return false;
        }
        
    }
    
    function calculateDaysInArrears($noOfInstPaid, $dayDiff, $today, $firstRepaymentDate){
        // Days settled
        $daysSettled = $noOfInstPaid * $dayDiff; // 1 * 7 = 7
        
        // Calculate the date supposed to be current
        $dateSupposedToBeCurrent = new DateTime($firstRepaymentDate); //2024-04-18
        $dateSupposedToBeCurrent->add(new DateInterval("P{$daysSettled}D")); //2024-04-25
    
        // Calculate the difference in days between $today and $dateSupposedToBeCurrent
        $todayDate = new DateTime($today);
        $diff_days = $todayDate->diff($dateSupposedToBeCurrent)->days; // Get the total number of days
    
        return $diff_days;
    }



    
    function getClassificationCategory($conn, $loan_no, $daysInArrears0){
        
        //use the product name to get the classification details of that product name
        $daysInArrears = intval($daysInArrears0);
        
        if ($daysInArrears < 7) {
            $daysClass = 1;
        } elseif ($daysInArrears < 15) {
            $daysClass = 7;
        } elseif ($daysInArrears < 30) {
            $daysClass = 15;
        } elseif ($daysInArrears < 60) {
            $daysClass = 30;
        } elseif ($daysInArrears < 90) {
            $daysClass = 60;
        } else {
            $daysClass = 90;
        }

        
        //get the product name
        $sqlGetLoanProduct = $conn->query("SELECT loan_product FROM loans WHERE loan_no='$loan_no'");
        $sqlGetLoanProductRow = $sqlGetLoanProduct->fetch_assoc();
        $loanProduct = (empty($sqlGetLoanProductRow['loan_product']))? null : $sqlGetLoanProductRow['loan_product'];
        
        $daysClass1 = encrypt($daysClass);
        
        $sqlGetLoanProductClassf = $conn->query("SELECT * FROM loan_classification WHERE product_name='$loanProduct' AND days_inArrears='$daysClass1'");
        
        if($sqlGetLoanProductClassf->num_rows > 0){
            $sqlGetLoanProductRowf = $sqlGetLoanProductClassf->fetch_assoc();
            $classification = $sqlGetLoanProductRowf['classification'];
                
            return decrypt($classification);
        } else {
            //default classfication
            /*
            switch ($daysInArrears) {
                case ($daysInArrears < 7):
                    $classification = 'Normal';
                    break;
                case ($daysInArrears > 6 && $daysInArrears < 15):
                    $classification = 'Watch';
                    break;
                case ($daysInArrears > 14 && $daysInArrears < 30):
                    $classification = 'Substandard';
                    break;
                case ($daysInArrears > 29 && $daysInArrears < 60):
                    $classification = 'Doubtful';
                    break;
                case ($daysInArrears > 59 && $daysInArrears < 90):
                    $classification = 'Loss';
                    break;
                case ($daysInArrears > 89):
                    $classification = 'Loss';
                    break;
                default:
                    $classification = 'Normal';
                    break;
            }
            */
            
            switch ($daysClass) {
                case 1:
                    $classification = 'Normal';
                    break;
                case 7:
                case 15:
                    $classification = 'Watch';
                    break;
                case 30:
                    $classification = 'Substandard';
                    break;
                case 60:
                    $classification = 'Doubtful';
                    break;
                case 90:
                    $classification = 'Loss';
                    break;
                default:
                    $classification = 'Normal';
                    break;
            }
            
            return $classification;
            //return 'Here.';
        }
    }
    
    function getWorstDayClass($conn, $loan_no){
        $sqlGetWorst = $conn->query("SELECT * FROM loans WHERE loan_no='$loan_no'");
        $rowWorst = $sqlGetWorst->fetch_assoc();
        if(empty($rowWorst['worst_daysInArrears'])){
            return null;
        } else {
            $worstDays = decrypt($rowWorst['worst_daysInArrears']);
            $worstClass = decrypt($rowWorst['worst_classification']);
            
            $worstDayClass = "$worstDays - $worstClass";
            
            return $worstDayClass;
        }
    }
    
    function calcAmountInArrears($conn, $loan_no){
        //get total paid from loan schedules
        $sqlTotalPaid = $conn->query("SELECT paid FROM loan_schedules WHERE loan_no='$loan_no'");
        $amountPaid = 0;
        while($amountPaidRow = $sqlTotalPaid->fetch_assoc()){
            $amountPaidX = intval(decrypt($amountPaidRow['paid']));
            $amountPaid += $amountPaidX;
        }
        
        //get total amount due for the unpaid period
        $sqlTotalUnPaid = $conn->query("SELECT * FROM loan_schedules WHERE loan_no='$loan_no'");
        $amountUnPaid = 0;
        while($amountUnPaidRow = $sqlTotalUnPaid->fetch_assoc()){
            $firstRepaymentDate = decrypt($amountUnPaidRow['due_date']);
            $today = date('Y-m-d');
            
            if($firstRepaymentDate < $today){
                $installment = intval(decrypt($amountUnPaidRow['loan_installment']));
            
                $amountUnPaid += $installment;
            }
        }
        
        $amountInArrears = intval($amountPaid) - intval($amountUnPaid);
        
        return $amountInArrears;
    }
    
    function clearArrearsTable($conn){
        //clear the loan arrears table
        $conn->query("TRUNCATE loan_arrears");
    }
    
    function clearClassifiedTable($conn){
        $conn->query("TRUNCATE counter");
    }
    
    function sendLoanToArrearsTable($conn, $loan_no){
        //check if loan is already in arrears table
        $sqlLoanArrears = $conn->query("SELECT * FROM loan_arrears WHERE loan_no='$loan_no'");
        if($sqlLoanArrears->num_rows > 0){
            //do nothing
        } else {
            //send to the loan arrears table
            
            $conn->query("INSERT INTO loan_arrears SELECT * FROM loans WHERE loan_no='$loan_no'");
        }   
    }
    
    
    runClassification($conn);
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>