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
        $sqlSchedules = "SELECT * FROM loan_schedules ORDER BY s_no ASC";
        $resultSchedules = $conn->query($sqlSchedules);        
        if ($resultSchedules->num_rows > 0) {
            
            clearArrearsTable($conn);
             
            while ($results = $resultSchedules->fetch_assoc()){
                $due_date = decrypt($results['due_date']);
                $paid = intval(decrypt($results['paid']));
                $installmentDue = intval(decrypt($results['loan_installment']));
                
                if(strtotime($today) > strtotime($due_date) && $paid != $installmentDue){
                    $loan_no = $results['loan_no'];
                    
                    $isOpen = checkIfLoanIsOpen($conn, $loan_no);
                    
                    if($isOpen){
                        
                        $dueDateReached = getFirstRepaymentDate($conn, $loan_no);
                        
                        if($dueDateReached === false){
                            //do nothing
                        } else {
                            $classifiedAlready = checkClassified($conn, $loan_no);
                            
                            if($classifiedAlready){
                                //do nothing
                            } else {
                                //calculate the number of days in arrears
                                $daysInArrears = intval(calculateDaysInArrears($today, $due_date));
                                $daysInArrears1 = encrypt($daysInArrears);
                                
                                //get amount in arrears
                                $amountInArrears = calcAmountInArrears($conn, $loan_no);
                                $amountInArrears1 = encrypt($amountInArrears);
                                
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
                                
                                if($amountInArrears < 1){
                                    sendLoanToArrearsTable($conn, $loan_no); 
                                }
                                
                                markAsClassified($conn, $loan_no);
                            }
                        }
                    }
                }
            }
        }
        
        clearClassifiedTable($conn);
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
    
    function checkIfLoanIsOpen($conn, $loan_no){
        $isOpen = encrypt("Open");
        $sqlBal = $conn->query("SELECT * FROM loans WHERE loan_no='$loan_no' AND loan_status='$isOpen'");
        if($sqlBal->num_rows > 0){
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
                return true;
            }
        } else {
            return false;
        }
        
    }
    
    function calculateDaysInArrears($today, $due_date){
        $date1 = strtotime($today);
        $date2 = strtotime($due_date);
        $diff_days = ($date1 - $date2) / (60 * 60 * 24);
        return $diff_days;
    }
    
    function getClassificationCategory($conn, $loan_no, $daysInArrears){
        
        //use the product name to get the classification details of that product name
        switch ($daysInArrears) {
            case ($daysInArrears < 7):
                $daysClass = 1;
                break;
            case ($daysInArrears > 6 && $daysInArrears < 15):
                $daysClass = 7;
                break;
            case ($daysInArrears > 14 && $daysInArrears < 30):
                $daysClass = 15;
                break;
            case ($daysInArrears > 29 && $daysInArrears < 60):
                $daysClass = 30;
                break;
            case ($daysInArrears > 59 && $daysInArrears < 90):
                $daysClass = 60;
                break;
            case ($daysInArrears > 89):
                $daysClass = 90;
                break;
            default:
                $daysClass = 1;
                break;
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
            
            return $classification;
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
            $due_date = decrypt($amountUnPaidRow['due_date']);
            $today = date('Y-m-d');
            
            if($due_date < $today){
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