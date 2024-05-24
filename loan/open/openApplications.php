<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

    require_once __DIR__.'/../../vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/../../templates/pay-process2.php';
    require_once __DIR__.'/../../templates/crypt.php';
    require_once __DIR__.'/../../templates/counter.php';
    require_once __DIR__.'/../../templates/checkMembersBalances.php';
    require_once __DIR__.'/../../templates/notifications.php';
    require_once __DIR__.'/../../templates/sendsms.php';
    require_once __DIR__.'/../../templates/upload_docs.php';
    require_once __DIR__.'/../../templates/loanActions.php';
    require_once __DIR__.'/../../templates/loanRepayment.php';
    require_once __DIR__.'/../../templates/ledgerActions.php';

    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();

    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: /login'); // Redirect to the login page
        exit;
    }
    
    if (!isset($_GET['lno']) || $_GET['lno'] === "" || $_GET['lno'] < 1) {
        header('Location: /loans'); // Redirect to the login page
        exit;
    } else {
        $loanNo = $_GET['lno'];
    }
    
    $username = $_SESSION['username'];
    $admin = $_SESSION['admin'];
    $member_no = $_SESSION['member_no'];
    $userphone = $_SESSION['userphone'];
    if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
        $access = false;
    } else {
        $access = true;
    }
    
    $loansAdvancedLedger = '101100';
    $bankLedger = '100100';
    $loanFeesLedger = '501100';
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //pull customer no for use
    $sqlGetCustNo = $conn->query("SELECT * FROM loan_applications WHERE loan_no='$loanNo'");
    $getCustNoRow = $sqlGetCustNo->fetch_assoc();
    $custNo = $getCustNoRow['customer_no'];
    $customerOwner = decrypt($getCustNoRow['staff_phone']);
    
    //get customer info
    $sqlGetCustInfo = $conn->query("SELECT * FROM customers WHERE customer_no='$custNo'");
    $custNoRow = $sqlGetCustInfo->fetch_assoc();
    
    $loanStatusThis = decrypt($getCustNoRow['loan_status']);
    $loanTypeXX = decrypt($getCustNoRow['loan_type']);
    
    //get loan application fee details
    $sqlGetLoanFees = $conn->query("SELECT * FROM customer_registration WHERE loan_no='$loanNo'");
    $loanFees = $sqlGetLoanFees->fetch_assoc();
    
    
    //make loan application resubmission
    if(isset($_POST['submitLoan'])){
        $productSelectLoan1 = $_POST['productSelectLoan'];
        $pymtFrequency = $_POST['pymtFrequency'];
        $interestRates = $_POST['interest-rateH'];
        $loanFees = $_POST['loan-feeH'];
        $amount = $_POST['amountApplied'];
        $noOfInstallments1 = $_POST['no-of-installments'];
        $loanInstallment = $_POST['loan-installmentH'];
        $takeHome = $_POST['takeHomeH'];
        $grossLoanAmt = $_POST['grossLoanH'];
        
        //$installment = (int) str_replace(',', '', $loanInstallment); //remove commas and spaces  
        $date = date('Y-m-d H:i:s');
        
        $productSelectLoan = encrypt($productSelectLoan1);
        $interestRates = encrypt($interestRates);
        $loanFees = encrypt($loanFees);
        $amount = encrypt($amount);
        $noOfInstallments = encrypt($noOfInstallments1);
        $loanInstallment = encrypt($loanInstallment);
        $status = encrypt('Resubmitted');
        $date = encrypt($date);
        $takeHome = encrypt($takeHome);
        $grossLoanAmt = encrypt($grossLoanAmt);
        $repaymentFrequency1 = encrypt($pymtFrequency);
        
        //get applicant phone number
        $sqlGetApplicant = $conn->query("SELECT customer_phone FROM loan_applications WHERE loan_no = '$loanNo'");
        $getApplicant = $sqlGetApplicant->fetch_assoc();
        $applicant = decrypt($getApplicant['customer_phone']);
        
        $locality = 'customer_docs';
        
        $documentName='loanForm';
        $loanFormName = $documentName . $applicant . 'No' . $loanNo . 're';
        $loanForm = uploadDocs($documentName, $locality, $loanFormName);
        
        $loanForm1 = encrypt($loanForm);
        
        //Calculate interest due and principal due
        if($pymtFrequency === 'Daily'){
            $divideBy = 28;
        } else if ($pymtFrequency === 'Weekly'){
            $divideBy = 4;
        } else if ($pymtFrequency === 'Monthly'){
            $divideBy = 1;
        } else {
            $divideBy = 0;
        }
        
        $principalBal = encrypt($amount);
        $interestDue1 = intval($amount) * (intval($interestRates)/100) * (intval($noOfInstallments1) / $divideBy);
        $interestDue = encrypt($interestDue1);
        
        $loanType = encrypt($loanTypeXX);
        
        //insert into table loans
        $sqlLoanApp = $conn->prepare("UPDATE loan_applications SET loan_product=?, loan_amount=?, no_of_installments=?, loan_interest=?, loan_installment=?, principalBal=?, interestBal=?, take_home=?, loan_status=?,  gross_loan=?, loan_balance=?, loan_form=?, repaymentFrequency=?  WHERE loan_no=?");
        $sqlLoanApp->bind_param("ssssssssssssss", $productSelectLoan,  $amount, $noOfInstallments, $interestRates, $loanInstallment, $principalBal, $interestBal,  $takeHome, $status, $grossLoanAmt, $grossLoanAmt, $loanForm1, $repaymentFrequency1, $loanNo);
        
        if($sqlLoanApp->execute()){
            //update appraisal
            $review = 'Resubmitted';
            $actionBy = $username;
            $description = 'Resubmitted the loan for approval.';
            
            $addAppraisal = addLoanAppraisal($conn, $loanNo, $review, $actionBy, $description);
            
            $notification = "You have a newly resubmitted loan application.";
            $role = 'Admin';
            saveNotification($notification, $role);
            
            header("Location: /loan/open/?lno=$loanNo");
        } else {
            header("Location: /loan/open/?lno=$loanNo");
        }
        
    }
    
    //approve loan application
    if(isset($_POST['approveLoan'])){
        $loanData = $_POST['loanSelect'];
        $loanData1 = explode(" - ", $loanData);
        $loan_no = $loanData1[0];
        $loan_status = $loanData1[4];
        $repaymentDate = $_POST['repaymentDate'];
        $repaymentDate1 = encrypt($repaymentDate);
        $repaymentFrquency = $_POST['frequencySelect'];
        $repaymentFrquency1 = encrypt($repaymentFrquency);
        $loanNarration = $_POST['loanNarration'];
        
        if($loan_status === 'Not Approved'){
            $state = encrypt('Reviewed');
            $reviewer = encrypt($username);
            $date = encrypt(date("Y-m-d H:i:s"));
            $sqlUpdateLoansTable = "UPDATE loan_applications SET repaymentFrequency='$repaymentFrquency1', firstRepaymentDate='$repaymentDate1', loan_status='$state', loan_reviewer='$reviewer', loan_reviewDate='$date' WHERE loan_no=$loanNo";
            $reviewLoan = $conn->query($sqlUpdateLoansTable);
            
            if($reviewLoan){
                
                //update appraisal
                $review = 'Reviewed';
                $actionBy = $username;
                $description = $loanNarration;
                
                $addAppraisal = addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
                
                if($addAppraisal){
                    $notification = "You have a new loan application pending approval.";
                    $role = 'Admin';
                    saveNotification($notification, $role);
                }
            }
            
            header("Location: /loans");
            
        } elseif ($loan_status === 'Reviewed' || $loan_status === 'Resubmitted'){
            $state = encrypt('Approved');
            $reviewer = encrypt($username);
            $date = encrypt(date("Y-m-d H:i:s"));
            $sqlUpdateLoansTable = "UPDATE loan_applications SET repaymentFrequency='$repaymentFrquency1', firstRepaymentDate='$repaymentDate1', loan_status='$state', loan_approver='$reviewer', loan_approvalDate='$date' WHERE loan_no=$loanNo";
            
            $approvedLoan = $conn->query($sqlUpdateLoansTable);
        
            if($approvedLoan){
                
                //Take the loan to the loans table
                $sqlCheckIfLoanCreated = $conn->query("SELECT * FROM loans WHERE loan_no=$loanNo");
                
                if($sqlCheckIfLoanCreated->num_rows > 0){
                    //do nothing
                    echo "Loan already created.";
                } else {
                    $copyLoan = $conn->query("INSERT INTO loans SELECT * FROM loan_applications WHERE loan_no=$loanNo");
                    
                    if($copyLoan){
                        //Update status to Open
                        $openStatus = encrypt("Open");
                        
                        $sqlUpdateStatusOpen = $conn->query("UPDATE loans SET loan_status='$openStatus' WHERE loan_no=$loanNo");
                        
                        if($sqlUpdateStatusOpen){
                            
                            //insert into loan transactions table as debit
                            //get loan details
                            $sqlGetLoanDetails = $conn->query("SELECT * FROM loans WHERE loan_no=$loanNo");
                            $sqlGetLoanDetailsResults = $sqlGetLoanDetails->fetch_assoc();
                            $customerN02 = $sqlGetLoanDetailsResults['customer_no'];
                            $customerName2 = $sqlGetLoanDetailsResults['customer_name'];
                            $customerPhone2 = $sqlGetLoanDetailsResults['customer_phone'];
                            $loan_product2 = $sqlGetLoanDetailsResults['loan_product'];
                            $loan_amount2 = $sqlGetLoanDetailsResults['loan_amount'];
                            $loan_installment2 = $sqlGetLoanDetailsResults['loan_installment'];
                            $loan_balance2 = $sqlGetLoanDetailsResults['loan_balance'];
                            $location_name = $sqlGetLoanDetailsResults['location_name'];
                            $take_home = $sqlGetLoanDetailsResults['take_home'];
                            $loan_term2 = $sqlGetLoanDetailsResults['no_of_installments'];
                            $loan_interest2 = $sqlGetLoanDetailsResults['loan_interest'];
                            $firstRepaymentDate2 = $sqlGetLoanDetailsResults['firstRepaymentDate']; 
                            $repaymentFrequency2 = $sqlGetLoanDetailsResults['repaymentFrequency'];
                            $loan_type = decrypt($sqlGetLoanDetailsResults['loan_type']);
                            
                            $posting_date = encrypt(date('Y-m-d H:i:s'));
                            $posting_description = encrypt($loan_type);
                            $description_no = encrypt($loanNo);
                            $debit2 = $sqlGetLoanDetailsResults['loan_balance'];
                            $running_balance = $debit2;
                            $transaction_by = encrypt($username);
                            
                            //check if loan is posted in loan transactions table
                            $sqlLoanTrans = $conn->query("SELECT * FROM loan_transactions WHERE loan_no=$loanNo");
                            
                            if($sqlLoanTrans->num_rows > 0){
                                //do nothing
                                echo "Loan already posted in trans table";
                            } else {
                                $sqlInsertTransactions = $conn->query("INSERT INTO loan_transactions(loan_no, customer_no, posting_date, posting_description, description_no, debit, transaction_by, running_balance) 
                                VALUES ('$loanNo','$customerN02','$posting_date','$posting_description','$description_no','$debit2','$transaction_by', '$debit2')");
                                
                                if($sqlInsertTransactions){
                                    
                                    //Close topup account
                                    if($loan_type <> 'New Loan'){
                                        $loanNoTopupEx = explode("-", $loan_type);
                                        $loanNoTopup = $loanNoTopupEx[1];
                                        $loan_type2 = $loanNoTopupEx[0];
                                        
                                        //close account in loans table
                                        $sqlGetLoanDetailsTopup = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNoTopup' ");
                                        $sqlTopupResults = $sqlGetLoanDetailsTopup->fetch_assoc();
                                        $gross_loan = $sqlTopupResults['gross_loan'];
                                        $loan_balance = $sqlTopupResults['loan_balance'];
                                        $loan_status = encrypt('Closed');
                                        $zero = encrypt("0");
                                        
                                        $conn->query("UPDATE loans SET loan_payments = '$gross_loan', loan_status='$loan_status', loan_balance = '$zero' WHERE loan_no='$loanNoTopup'");
                                        
                                        //add transaction in transaction table
                                        $description_no = encrypt($loanNoTopup);
                                        $posting_description = encrypt($loan_type2);
                                        
                                        $sqlInsertTransactions = $conn->query("INSERT INTO loan_transactions(loan_no, customer_no, posting_date, posting_description, description_no, credit, transaction_by) 
                                            VALUES ('$loanNoTopup','$customerN02','$posting_date','$posting_description','$description_no','$loan_balance','$transaction_by')");
                                        
                                        //add transaction in loan schedules
                                        $sqlGetInstallment = $conn->query("SELECT * FROM loan_schedules WHERE loan_no='$loanNoTopup' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                                        $installmentRow = $sqlGetInstallment->fetch_assoc();
                                        $installmentDue = $installmentRow['loan_installment'];
                                        
                                        $conn->query("UPDATE loan_schedules SET paid = '$installmentDue' WHERE loan_no = '$loanNoTopup' AND paid IS NULL ");
                                        
                                    }
                                    
                                    //Generate loan schedules
                                    $term = intval(decrypt($loan_term2));
                                    
                                    $generateSchedules = generateLoanScheduleAndInsert($conn, $loanNo, $customerN02, $customerPhone2, $loan_amount2, $term, $loan_interest2, $loan_installment2, $firstRepaymentDate2, $repaymentFrequency2);
                                    
        
                                    if($generateSchedules){
                                        
                                        //update loan appraisal
                                        $approve = 'Approved';
                                        $actionBy = $username;
                                        $descriptionNar = $loanNarration;
                                        
                                        $addAppraisal = addLoanAppraisal($conn, $loan_no, $approve, $actionBy, $descriptionNar);
                                        
                                        if($addAppraisal){
                                            //update customer via SMS
                                            $customerPhone3 = decrypt($customerPhone2);
                                            $customerName3 = decrypt($customerName2);
                                            
                                            $message3 = "Dear " . $customerName3 . ", your loan application was approved successfully. Please wait for disbursement to be done to your account. \nTruesales Credit Ltd.";
                                            
                                            //sendSMS($customerPhone3, $message3);
                                            
                                            //Add the loan amount to the Total Loans Advanced Ledger
                                            $subledger_amount0 = decrypt($take_home);
                                            addSubLedgerBalanceCredit($conn, $loansAdvancedLedger, $subledger_amount0);
                                            
                                            //deduct loan amount from the bank ledger
                                            addSubLedgerBalanceDebit($conn, $bankLedger, $subledger_amount0);
                                            
                                            //Add the loan fees amount to the Loan Fees Income Ledger
                                            $subledger_amount01 = '700';
                                            addSubLedgerBalanceCredit($conn, $loanFeesLedger, $subledger_amount01);
                                            
                                            header("Location: /loans");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    //defer loan application
    if(isset($_POST['deferLoan'])){
        $loanData = $_POST['loanSelect'];
        $loanData1 = explode(" - ", $loanData);
        $loan_no = $loanData1[0];
        $loan_status = $loanData1[4];
        $loanNarration = $_POST['loanNarration'];
        
        $state = encrypt('Deferred');
        $reviewer = encrypt($username);
        $date = encrypt(date("Y-m-d H:i:s"));
        $sqlUpdateLoansTable = "UPDATE loan_applications SET loan_status='$state', loan_reviewer='$reviewer', loan_reviewDate='$date' WHERE loan_no=$loan_no";
        
        $deferred = $conn->query($sqlUpdateLoansTable);
        
        if($deferred){
            
            //update appraisal
            $review = 'Deferred';
            $actionBy = $username;
            $description = $loanNarration;
            
            addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
            
            header("Location: /loans");
        }
            
    }
    
    //decline loan application
    if(isset($_POST['declineLoan'])){
        $loanData = $_POST['loanSelect'];
        $loanData1 = explode(" - ", $loanData);
        $loan_no = $loanData1[0];
        $loan_status = $loanData1[4];
        $loanNarration = $_POST['loanNarration'];
        
        if($loan_status === 'Not Approved'){
            $state = encrypt('Declined');
            $reviewer = encrypt($username);
            $date = encrypt(date("Y-m-d H:i:s"));
            $sqlUpdateLoansTable = "UPDATE loan_applications SET loan_status='$state', loan_reviewer='$reviewer', loan_reviewDate='$date', loan_approver='$reviewer', loan_approvalDate='$date' WHERE loan_no=$loan_no";
            
        } elseif ($loan_status === 'Reviewed' || $loan_status === 'Resubmitted'){
            $state = encrypt('Declined');
            $reviewer = encrypt($username);
            $date = encrypt(date("Y-m-d H:i:s"));
            $sqlUpdateLoansTable = "UPDATE loan_applications SET loan_status='$state', loan_approver='$reviewer', loan_approvalDate='$date' WHERE loan_no=$loan_no";
        }
        
        $decline = $conn->query($sqlUpdateLoansTable);
        
        if($decline){
            //update appraisal
            $review = 'Declined';
            $actionBy = $username;
            $description = $loanNarration;
            
            addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
            
            header("Location: /loans");
        }
            
    }
    
    //add loan repayment
    if(isset($_POST['submitRepayment'])){
        $session_token = $_POST['session_token_submitRepayment'];
        
        $formSent = checkSessionToken($conn, $session_token, 'loan_transactions');
        
        if($formSent){
            //do not send data again
        } else {
        
            $paymentDetails = $_POST['memberSelect1'];
            $paymentDetails1 = explode(" - ", $paymentDetails);
            $loanNo3 = $paymentDetails1[0];
            $memberName = $paymentDetails1[1];
            $memberPhone = $paymentDetails1[2];
            $loanBalance3 = $paymentDetails1[3];
            
            $repaymentAmt = $_POST['repaymentAmt'];
            $repaymentAmt1 = encrypt($repaymentAmt);
            $payMode1 = $_POST['payment-mode1'];
            $posting_date1 = $_POST['paidDate'];
            $posting_description1 = ("Repayment");
            $descriptionNo1 = $payMode1;
            $transactionBy1 = $username;
            
            repayLoan($conn, $loanNo3, $repaymentAmt, $payMode1, $posting_date1, $posting_description1, $descriptionNo1, $transactionBy1, $session_token);
        }
    }

    //add loan debit
    if(isset($_POST['submitDebit'])){
        $paymentDetails = $_POST['memberSelect11'];
        $paymentDetails1 = explode(" - ", $paymentDetails);
        $loanNo3 = $paymentDetails1[0];
        $memberName = $paymentDetails1[1];
        $memberPhone = $paymentDetails1[2];
        $loanBalance3 = $paymentDetails1[3];
        
        $debitAmt = $_POST['debitAmt'];
        $debitAmt1 = encrypt($debitAmt);
        $debitMode = encrypt($_POST['debit-mode1']);
        $posting_date = encrypt(date("Y-m-d H:i:s"));
        $posting_description = $debitMode;
        $descriptionNo = encrypt($loanNo3);
        $transactionBy = $username;
        $reason = $_POST['reason'];
        
        //Get customer no.
        $sqlGetLastLoanDetails = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo3' ");
        $sqlGetLastLoanDetailsResult = $sqlGetLastLoanDetails->fetch_assoc();
        $customer_no = $sqlGetLastLoanDetailsResult['customer_no'];
        $customer_phone = $sqlGetLastLoanDetailsResult['customer_phone'];
        $loan_payments = decrypt($sqlGetLastLoanDetailsResult['loan_payments']);
        $productName31 = decrypt($sqlGetLastLoanDetailsResult['loan_product']);
        $loan_balance31 = decrypt($sqlGetLastLoanDetailsResult['loan_balance']); 
        $repaymentFrequency = decrypt($sqlGetLastLoanDetailsResult['repaymentFrequency']); 
        
        //add loan balance in loans table
        $newBalance = intval($loanBalance3) + intval($debitAmt);
        $newBalance1 = encrypt($newBalance);
        
        $sqlAddBalance = $conn->query("UPDATE loans SET loan_balance='$newBalance1' WHERE loan_no='$loanNo3'");
        
        //add debit to loan transactions table
        if($sqlAddBalance){
            
            $sqlUpdateLoanTransaction = $conn->query("INSERT INTO loan_transactions (loan_no, customer_no, posting_date, posting_description, description_no, debit, payment_mode, transaction_by, running_balance) 
            VALUES ('$loanNo3', '$customer_no', '$posting_date', '$posting_description', '$descriptionNo', '$debitAmt1', '$posting_description', '$transactionBy', '$newBalance1') ");
            
            //add to loan schedules table as final payment
            if($sqlUpdateLoanTransaction){
                
                if(decrypt($debitMode) === "Closure"){
                    //update loan appraisal
                    $approve = 'Debited';
                    $actionBy = $username;
                    $descriptionNar = "Debited customer with Kshs. " . number_format($debitAmt, 0, '.', ',') . " as " . decrypt($debitMode) . ". Reason: " . $reason; 

                    $addAppraisal = addLoanAppraisal($conn, $loanNo3, $approve, $actionBy, $descriptionNar);
                    
                    header("Location: /loans");
                } else {
                    //get last date and payment frequency
                    $sqlGetLastDate = $conn->query("SELECT due_date FROM loan_schedules WHERE loan_no='$loanNo3' ORDER BY s_no DESC LIMIT 1");
                    $sqlGetLastDateRow = $sqlGetLastDate->fetch_assoc();
                    $lastDueDate = decrypt($sqlGetLastDateRow['due_date']);
                    
                    if ($repaymentFrequency === 'Daily') {
                        $adding = '1 Day';
                    } elseif ($repaymentFrequency === 'Weekly') {
                        $adding = '1 Week';
                    } elseif ($repaymentFrequency === 'Monthly') {
                        $adding = '1 Month';
                    }
                    
                    $lastDate = date('Y-m-d', strtotime($lastDueDate));
                    $nextPaymentDate1 = date('Y-m-d', strtotime($lastDate . " + $adding"));
                    $nextPaymentDate12 = encrypt($nextPaymentDate1);
                    $principal1 = encrypt('0');
                    
                    // Insert data into the loan schedules table 
                    $stmt = $conn->prepare("INSERT INTO loan_schedules (loan_no, customer_no, customer_phone, loan_amount, principal, interest, loan_installment, due_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssss", $loanNo3, $customer_no, $customer_phone, $debitAmt1, $principal1, $debitAmt1, $debitAmt1, $nextPaymentDate12);
                    $result = $stmt->execute();
                    
                    if($result){
                        //update loan appraisal
                        $approve = 'Debited';
                        $actionBy = $username;
                        $descriptionNar = "Debited customer with Kshs. " . number_format($debitAmt, 0, '.', ',') . " as " . decrypt($debitMode) . ". Reason: " . $reason; 
    
                        $addAppraisal = addLoanAppraisal($conn, $loanNo3, $approve, $actionBy, $descriptionNar);
                        
                        header("Location: /loans");
                    }
                }
            }
        }
    }

        
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Process Loan</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/../../templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/../../templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <div class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark d-flex flex-row">
                <h1 class="mr-3">Process Loan <?php if($loanNo !== null){ echo 'No. ' . $loanNo; } ?></h1>
                <a class="btn btn-sm btn-info align-self-center " onclick="window.history.back()">Go Back</a>
            </div>
            <div class="card-body">
                <div class="container-fluid col-12">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu" <?php if(!$admin || $loanStatusThis === 'Declined'){ echo 'hidden';} ?>>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button <?php if($loanStatusThis === 'Deferred' ){ echo 'hidden';} else if($admin !== 2 && $loanStatusThis === 'Reviewed'){ echo 'hidden';} else if ($loanStatusThis === 'Approved'){ echo 'hidden';} ?> type="button" id="approveLoanModalBtn" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#approveLoanModal">Appraise Loan</button>
                            </li>
                            <li>
                                <button <?php if($loanStatusThis !== 'Deferred'){ echo 'hidden';} ?> type="button" id="editLoanModalBtn" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#editLoanModal">Resubmit Loan</button>
                            </li>
                            <li <?php if(!$admin || $loanStatusThis !== 'Approved'){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#recordRepaymentModal">Record Repayment</button>
                            </li>
                            <li <?php if(!$admin || !$access || $loanStatusThis !== 'Approved'){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#debitModal">Add Debit</button>
                            </li>
                            <li <?php if(!$access || $loanStatusThis !== 'Approved'){ echo 'hidden'; } ?> >
                                <a type="button" class='btn btn-sm btn-info dropdown-item border-bottom' href='/statement?lno=<?php echo $loanNo; ?>' target='_blank' > Loan Statement</a>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Customer Information</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Customer Status: <?php echo strtoupper(decrypt($custNoRow['status']));   ?></span>
                                        
                                        <span class="d-block mb-3 col-md-6"> Registration Date: <?php echo strtoupper(decrypt($custNoRow['joinDate']));   ?></span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Customer Name: <?php echo decrypt($custNoRow['customer_name']);   ?></span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> Customer Phone: <?php echo decrypt($custNoRow['customer_phone']);   ?></span> 
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Customer Email: <?php echo decrypt($custNoRow['customer_email']);   ?></span>
                                        
                                        <span class="d-block mb-3 col-md-6"> Branch: <?php echo ($custNoRow['location_name']);   ?></span>
                                    </div>
                                    
                                    <span class="d-block mb-3"> ID Front: <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['ID_front']); ?> . ">View</a></span>
                                    
                                    <span class="d-block mb-3"> ID Back: <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['ID_back']); ?> . ">View</a></span>
                                    
                                    <span class="d-block mb-3"> Passport Photo:  <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['passport_pic']); ?> . ">View</a></span>

                                    <span class="d-block mb-2 col-md-6 fw-bold"> Portfolio Owner: <?php echo decrypt($custNoRow['staff_phone']);   ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Loan Application Information</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here loan_applicationDate-->
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Status: <?php echo strtoupper(decrypt($getCustNoRow['loan_status']));   ?></span>
                                        
                                        <span class="d-block mb-3 col-md-6"> Application Date: <?php echo decrypt($getCustNoRow['loan_applicationDate']);   ?></span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Loan Product: <?php echo decrypt($getCustNoRow['loan_product']);   ?></span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> Loan Type: <?php echo decrypt($getCustNoRow['loan_type']);   ?></span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Loan Amount: <?php echo decrypt($getCustNoRow['loan_amount']);   ?></span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> No. of Installments: <?php echo decrypt($getCustNoRow['no_of_installments']);   ?></span> 
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Loan Interest: <?php echo decrypt($getCustNoRow['loan_interest']);   ?>% p.m.</span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> Loan Installments: <?php echo decrypt($getCustNoRow['loan_installment']);   ?></span> 
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Gross Loan : <?php echo decrypt($getCustNoRow['gross_loan']);   ?></span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> Take Home: <?php echo decrypt($getCustNoRow['take_home']);   ?></span> 
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Loan Form: <a class="btn btn-sm btn-info" href=" <?php echo decrypt($getCustNoRow['loan_form']); ?>">View</a></span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> First Repayment Date: <?php echo decrypt($getCustNoRow['firstRepaymentDate']);   ?></span> 
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Loan Application Fee: <?php if(empty($loanFees['value'])) { echo '';} else { echo (floor(intval($loanFees['value']) * (97/100))); }?> </span> 
                                        
                                        <span class="d-block mb-3 col-md-6"> Mpesa Ref. Code: <?php if(empty($loanFees['value'])) { echo '';} else { echo $loanFees['mpesa_reference'];} ?></span> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan Appraisal Activity</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button <?php if(!$admin){ echo 'hidden'; } ?> type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('appraisal-table', 'appraisals')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="payments-search1" onkeyup="searchTable1()" placeholder="Search by name or phone number"  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size1">Rows per page:</label>
                                <select id="page-size1">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="appraisal-table" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action By</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        $sqlLoanAppraisals = "SELECT * FROM loan_appraisals WHERE loan_no='$loanNo' ORDER BY s_no DESC";
                                
                                        $resultLoanAppraisals = $conn->query($sqlLoanAppraisals);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultLoanAppraisals->num_rows > 0) {
                                            while ($rowAppraisal = $resultLoanAppraisals->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . decrypt($rowAppraisal["date"]) . "</td>
                                                    <td>" . decrypt($rowAppraisal["action_by"]) . "</td>
                                                    <td>" . decrypt($rowAppraisal["action"]) . "</td>
                                                    <td>" . decrypt($rowAppraisal["description"]) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'>No results found.</td></tr>";
                                        }
                                
                                        //$conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                <button id="prev-page1">Previous Page</button>
                                <span id="page-info1"></span>
                                <button id="next-page1">Next Page</button>
                            </div>
                        </div>
                    </div>
                    <br>
    
                    <div class="card bg-info shadow"  <?php if($loanStatusThis !== "Approved"){ echo 'hidden';}?> >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loans Details</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button  type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('loan-details', 'loan-details')" >Export to Excel</button>
                            
                            <div class="table table-responsive">
                                <table id="loan-details" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Loan No.</th>
                                            <th>Customer Number</th>
                                            <th>Customer Name</th>
                                            <th>Customer Phone</th>
                                            <th>Loan Product</th>
                                            <th>Loan Type</th>
                                            <th>Loan Amount</th>
                                            <th>Loan Term</th>
                                            <th>Loan Installment</th>
                                            <th>Application Date</th>
                                            <th>Gross Loan</th>
                                            <th>Total Paid</th>
                                            <th>Loan Balance</th>
                                            <th>Last Pay Date</th>
                                            <th>Status</th>
                                            <th>Loan Classification</th>
                                            <th>Days In Arrears</th>
                                            <th>Amount In Arrears</th>
                                            <th>Worst Classification</th>
                                            <th>Worst Days In Arrears</th>
                                            <th>Branch</th>
                                            <th>Statement</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                            $sqlGetLoan1 = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo'");
                                                
                                            if($sqlGetLoan1->num_rows > 0){
                                                while ($rowLoans1 = $sqlGetLoan1->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans1['loan_no']}'>{$rowLoans1['loan_no']}</a></td>
                                                        <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowLoans1['customer_no']}'>{$rowLoans1['customer_no']}</a></td>
                                                        <td>" . decrypt($rowLoans1["customer_name"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["customer_phone"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_product"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_type"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_amount"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_term"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_installment"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_applicationDate"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["gross_loan"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_payments"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_balance"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["last_paymentDate"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_status"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_classification"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["days_inArrears"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["amount_inArrears"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["worst_classification"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["worst_daysInArrears"]) . "</td>
                                                        <td>" . $rowLoans1["location_name"] . "</td>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/statement?lno={$rowLoans1['loan_no']}'>Statement</a></td>
                                                        </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='9'>No results found.</td></tr>";
                                            }
                                        //$conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow"  <?php if($loanStatusThis !== "Approved"){ echo 'hidden';}?> >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan Transactions</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button <?php if(!$admin){ echo 'hidden'; } ?> type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('transactions-table', 'loan_transactions')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="payments-search3" onkeyup="searchTable3()" placeholder="Search by name or phone number"  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size3">Rows per page:</label>
                                <select id="page-size3">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="transactions-table" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Loan No.</th>
                                            <th>Customer Number</th>
                                            <th>Posting Date</th>
                                            <th>Description</th>
                                            <th>Description No.</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Balance</th>
                                            <th>Payment Mode</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        
                                        $sqlPaid = "SELECT * FROM loan_transactions WHERE loan_no='$loanNo' ORDER BY s_no DESC";
                                
                                        $resultPaid = $conn->query($sqlPaid);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultPaid->num_rows > 0) {
                                            while ($rowLoans2 = $resultPaid->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans2['loan_no']}'>{$rowLoans2['loan_no']}</a></td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowLoans2['customer_no']}'>{$rowLoans2['customer_no']}</a></td>
                                                    <td>" . decrypt($rowLoans2["posting_date"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["posting_description"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["description_no"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["debit"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["credit"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["running_balance"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["payment_mode"]) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'>No results found.</td></tr>";
                                        }
                                
                                        //$conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                <button id="prev-page3">Previous Page</button>
                                <span id="page-info3"></span>
                                <button id="next-page3">Next Page</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow" <?php if($loanStatusThis !== "Approved"){ echo 'hidden';}?> >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan Repayment Schedules</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button <?php if(!$admin){ echo 'hidden'; } ?> type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('repayment-schedules', 'repayment-schedules')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="payments-search4" onkeyup="searchTable4()" placeholder="Search .."  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size4">Rows per page:</label>
                                <select id="page-size4">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="repayment-schedules" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Loan No.</th>
                                            <th>Customer Number</th>
                                            <th>Customer Phone</th>
                                            <th>Amount</th>
                                            <th>Principal</th>
                                            <th>Interest</th>
                                            <th>Principal Paid</th>
                                            <th>Interest Paid</th>
                                            <th>Installment</th>
                                            <th>Due Date</th>
                                            <th>Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        
                                        $sqlSchedules = "SELECT * FROM loan_schedules WHERE loan_no='$loanNo' ORDER BY s_no DESC";
                                
                                        $resultSchedules = $conn->query($sqlSchedules);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultSchedules->num_rows > 0) {
                                            while ($rowSchedules = $resultSchedules->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowSchedules['loan_no']}'>{$rowSchedules['loan_no']}</a></td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowSchedules['customer_no']}'>{$rowSchedules['customer_no']}</a></td>
                                                    <td>" . decrypt($rowSchedules["customer_phone"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["loan_amount"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["principal"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["interest"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["principalPaid"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["interestPaid"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["loan_installment"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["due_date"]) . "</td>
                                                    <td>" . decrypt($rowSchedules["paid"]) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9'>No results found.</td></tr>";
                                        }
                                
                                        //$conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                <button id="prev-page4">Previous Page</button>
                                <span id="page-info4"></span>
                                <button id="next-page4">Next Page</button>
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Modal -->
                    <div class="modal fade" id="editLoanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Resubmit Loan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="form" method="POST" action="" enctype="multipart/form-data">
                                        <div class=" mb-2">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="loanTypeX" class="form-label">Loan Type</label>
                                            <input class="form-control" list="datalistOptions811" id="loanTypeX" name="loanTypeX" placeholder="Loan Type" autocomplete="off" required value="<?php echo $loanTypeXX; ?>">
                                            <datalist id="datalistOptions811">
                                                <option value="New Loan"></option>';
                                                <option value="Topup Loan"></option>';
                                            </datalist>
                                        </div>
                                        <div class=" mb-3 productSelectLoan1">
                                            <label for="productSelectLoan" class="form-label">Loan Product</label>
                                            <input class="form-control" list="datalistOptions12" id="productSelectLoan" name="productSelectLoan" placeholder="Type to search Product.." autocomplete="off" >
                                            <datalist id="datalistOptions12">
                                                <?php
                                                    $activeStatus = encrypt('active');
                                                    $sqlproductSelect = "SELECT * FROM loan_products WHERE product_status='$activeStatus'";
                                                    $resultproductSelect = $conn->query($sqlproductSelect);
                                                    
                                                    if ($resultproductSelect->num_rows > 0) {
                                                        while ($rowproductSelect = $resultproductSelect->fetch_assoc()) {
                                                            echo '<option value="' . decrypt($rowproductSelect['product_name']) . '"></option>';
                                                        }
                                                    } else {
                                                        echo "No Customer found";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start interest">
                                            <input disabled class="form-control" type="text" id="interest-rate" name="interest-rate" value="" required>
                                            <input type="hidden" name="interest-rateH" id="interest-rateH" value="">
                                            <label for="interest-rate">Interest rate</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start loanFees">
                                            <input disabled class="form-control" type="text" id="loan-fee" name="loan-fee" value="" required>
                                            <input type="hidden" name="loan-feeH" id="loan-feeH" value="">
                                            <label for="loan-fee">Loan Fees</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start ">
                                            <input class="form-control" type="text" id="max-loan" name="max-loan" value="" required>
                                            <label for="max-loan">Maximum Amount</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start ">
                                            <input class="form-control" type="text" id="max-period" name="max-period" value="" required>
                                            <label for="max-period">Maximum Term</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start loanAmount">
                                            <input class="form-control" type="number" id="amountApplied" name="amountApplied" placeholder="Amount (Kes.):" value="" required> 
                                            <label for="amountApplied">Amount (Kes.):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start loanPeriod">
                                            <input class="form-control" type="number" id="loan-term" name="loan-term" placeholder="Loan Period (months):" > 
                                            <label for="loan-term">Loan Period (months):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start no-of-installments">
                                            <input class="form-control" type="number" id="no-of-installments" name="no-of-installments" placeholder="No of Installments" required> 
                                            <label for="no-of-installments">No of Installments</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start ">
                                            <input class="form-control" type="text" id="pymtFrequency" name="pymtFrequency" placeholder="Repayment Frequency:" > 
                                            <label for="pymtFrequency">Repayment Frequency:</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start installment">
                                            <input disabled class="form-control" type="number" id="loan-installment" name="loan-installment" value="" required> 
                                            <input type="hidden" name="loan-installmentH" id="loan-installmentH" value="">
                                            <label for="loan-installment">Loan Installment (Kshs):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start topupBalC">
                                            <input disabled class="form-control" type="text" id="topupBal" name="topupBal" value="" required> 
                                            <input type="hidden" name="topupBalH" id="topupBalH" value="">
                                            <label for="topupBal">Topup Balance</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start takeHome">
                                            <input disabled class="form-control" type="number" id="takeHome1" name="takeHome" value="" required> 
                                            <input type="hidden" name="takeHomeH" id="takeHomeH" value="">
                                            <label for="takeHome1">Take Home Amount (Kshs.):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start grossLoan">
                                            <input disabled class="form-control" type="number" id="grossLoan" name="grossLoan" value="" required> 
                                            <input type="hidden" name="grossLoanH" id="grossLoanH" value="">
                                            <label for="grossLoan">Gross Loan (Kshs.):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start loanForm">
                                            <input class="form-control" type="file" id="loanForm" name="loanForm" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                            <label for="loanForm">Loan Application Form:</label>
                                        </div>
                                        <input hidden class="btn btn-success btn-bg " type="submit" id="submitLoanId" name="submitLoan" value="" >
                                    
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button disabled="disabled" type="button" class="btn btn-primary submission" onclick="btnClick('submitLoanId')">Resubmit Application</button>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="approveLoanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Appraise Loan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="" method="POST" action="">
                                        <div hidden class=" mb-3">
                                                <?php
                                                    $customer_name = decrypt($custNoRow['customer_name']);
                                                    $loanAmount = decrypt($getCustNoRow['loan_amount']);
                                                    $dateApplied = decrypt($getCustNoRow['loan_applicationDate']);
                                                    $loanStatus = decrypt($getCustNoRow['loan_status']);
                                                    
                                                    $approvalData = "$loanNo - $customer_name - $loanAmount - $dateApplied - $loanStatus";
                                                ?>
                                            <input class="form-control"  id="loanSelect" name="loanSelect" value="<?php echo $approvalData;?>" >
                                            
                                        </div>
                                        <div  class="form-floating mb-2 repayDate">
                                            <input  class="form-control" placeholder="First Repayment Date" type="date" id="repaymentDate" name="repaymentDate"  required>
                                            <label for="repaymentDate"> First Repayment Date</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-2 loanNarration">
                                            <textarea  class="form-control" placeholder="Narration" type="text" rows="3" id="loanNarration" name="loanNarration" required></textarea>
                                            <label for="loanNarration">Narration</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-2 ">
                                            <input  class="form-control" placeholder="Repayment Frequency" type="text" id="frequencySelect" name="frequencySelect" value=""  >
                                            <label for="frequencySelect">Repayment Frequency</label>
                                        </div>
                                        
                                        <input class="btn btn-danger" type="submit" value="Decline Loan" name="declineLoan" >
                                        <input hidden="hidden" class="btn btn-warning deferLoan" type="submit" value="Defer Loan" name="deferLoan" id="deferLoan"  >
                                        <input class="btn btn-info" type="submit" value="Approve Loan" name="approveLoan" id="approveLoan">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="recordRepaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Record Repayment</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="" method="POST" action="">
                                        <input type="hidden" name="session_token_submitRepayment" value="<?php echo date("YmdHis"); ?>">
                                        <div hidden class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                <?php
                                                    $closed1 = encrypt('Closed');
                                                    $sqlLoans11 = "SELECT * FROM loans WHERE loan_no = '$loanNo'" ;
                                                    $resultLoans11 = $conn->query($sqlLoans11);
                                                    
                                                    if ($resultLoans11->num_rows > 0) {
                                                        while ($rowLoans11 = $resultLoans11->fetch_assoc()) {
                                                            $loan_No1 = $rowLoans11['loan_no'];
                                                            $customer_name1 = decrypt($rowLoans11['customer_name']);
                                                            $customer_phone1 = decrypt($rowLoans11['customer_phone']);
                                                            $loan_balance1 = decrypt($rowLoans11['loan_balance']);
                                                            
                                                            $loanDetails = "$loan_No1 - $customer_name1 - $customer_phone1 - $loan_balance1";
                                                        }
                                                    } else {
                                                        $loanDetails = 0;
                                                    }
                                                ?>
                                            <label for="memberSelect1" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions9" id="memberSelect1"  name="memberSelect1" placeholder="Type to search Customer.." autocomplete="off" required value="<?php echo $loanDetails; ?>" >
                                        </div>
                                        <div class="form-floating mb-2">
                                            <input  class="form-control" placeholder="Amount Paid" type="number" id="repaymentAmt" name="repaymentAmt"  required>
                                            <label for="repaymentAmt"> Amount Paid</label>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label"> Select Payment Mode </label>
                                            <select class="form-select form-select-sm" id="payment-mode1" name="payment-mode1" required>
                                                <option id="kcb" name="kcb" >Cash</option> 
                                                <option id="mpesa" name="mpesa" >Bank</option>
                                            </select>
                                        </div>
                                        <div class="form-floating mb-2">
                                            <input  class="form-control" placeholder="Paid Date" type="date" id="paidDate" name="paidDate"  required>
                                            <label for="paidDate">Paid Date</label>
                                        </div>
                                        <br>
                                        <input hidden class="btn btn-primary btn-sm" id="submitRepayment" name="submitRepayment" type="submit" value="Record Deposit" />
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('submitRepayment')">Record Repayment</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="debitModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Debit to Loan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="" method="POST" action="">
                                        <div hidden class=" mb-3">
                                                <?php
                                                    $closed1 = encrypt('Closed');
                                                    $sqlLoans111 = "SELECT * FROM loans WHERE loan_no = '$loanNo'" ;
                                                    $resultLoans111 = $conn->query($sqlLoans111);
                                                    
                                                    if ($resultLoans111->num_rows > 0) {
                                                        while ($rowLoans111 = $resultLoans111->fetch_assoc()) {
                                                            $loan_No11 = $rowLoans111['loan_no'];
                                                            $customer_name11 = decrypt($rowLoans111['customer_name']);
                                                            $customer_phone11 = decrypt($rowLoans111['customer_phone']);
                                                            $loan_balance11 = decrypt($rowLoans111['loan_balance']);
                                                            
                                                            $loanDetails1 = "$loan_No11 - $customer_name11 - $customer_phone11 - $loan_balance11";
                                                        }
                                                    } else {
                                                        $loanDetails1 = 0;
                                                    }
                                                ?>
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="memberSelect11" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions91" id="memberSelect11"  name="memberSelect11" placeholder="Type to search Customer.." autocomplete="off" required value="<?php echo $loanDetails1; ?>" >
                                        </div>
                                        <div class="form-floating mb-2">
                                            <input  class="form-control" placeholder="Debit Amount" type="number" id="debitAmt" name="debitAmt"  required>
                                            <label for="debitAmt"> Debit Amount</label>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"> Select Debit Category </label>
                                            <select class="form-select form-select-sm" id="debit-mode1" name="debit-mode1" required>
                                                <option id="Interest" name="Interest" >Interest</option>
                                                <option id="Penalty" name="Penalty" >Penalty</option> 
                                                <option id="Other Charges" name="Other Charges" >Other Charges</option>
                                                <option id="Reversal" name="Reversal" >Reversal</option>
                                                <option id="Closure" name="Closure" >Closure</option>
                                            </select>
                                        </div>
                                        <div class="form-floating mb-2">
                                            <textarea  class="form-control" rows="3" placeholder="Reason for Debit" type="text" id="reason" name="reason"  required></textarea>
                                            <label for="reason"> Reason for Debit</label>
                                        </div>
                                        <br>
                                        <input hidden class="btn btn-primary btn-sm" id="submitDebit" name="submitDebit" type="submit" value="Record Deposit" />
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('submitDebit')">Submit Debit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>  <!--End of container fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div> 
        
        <script>
            //Loan application handler
            document.addEventListener('DOMContentLoaded', function() {
                
                function getCustomerInfo(){
                    var loanType = document.getElementById('loanTypeX').value;
                    
                    if(loanType === 'New Loan'){
                        var customer = document.getElementById('memberSelectLoan').value;
                        
                        const newData = {
                                data: customer,
                                check:"memberInfo"
                            };
            
                        // Send an AJAX request to a PHP script to update the data
                        fetch('/templates/checkMemberDetails.php', {
                            method: 'POST',
                            body: JSON.stringify(newData),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success === false) {
                                var response_message = data.message;
                                alert(response_message);
                                document.getElementById('memberSelectLoan').value = '';
                            } else {
                                
                                document.querySelector('.productSelectLoan1').removeAttribute('hidden');
                                
                            }
                        });
                    } else if(loanType === 'Topup Loan'){
                        var customerLoan = document.getElementById('loanSelectX').value;
                        
                        
                        const newData = {
                                data: customerLoan,
                                check:"loanInfo"
                            };
            
                        // Send an AJAX request to a PHP script to update the data
                        fetch('/templates/checkMemberDetails.php', {
                            method: 'POST',
                            body: JSON.stringify(newData),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success === true) {
                                var response_message = data.message;
                                
                                document.getElementById('topupLoanType').value = data.topupLoanType;
                                document.getElementById('topupLoanTypeH').value = data.topupLoanType;
                                
                                document.querySelector('.topupLoanTypeC').removeAttribute('hidden');
                                
                                document.getElementById('topupBal').value = data.topupBal;
                                document.getElementById('topupBalH').value = data.topupBal;
                                
                                getProductInfo();
                            } else {
                                var response_message = data.message;
                                alert(response_message);
                            }
                        });

                    } 
                }
                
                function getProductInfo(){
                    var prod = document.getElementById('productSelectLoan').value;

                    const newData = {
                            product: prod,
                            check:"productInfo"
                        };
        
                    // Send an AJAX request to a PHP script to update the data
                    fetch('/templates/checkProductDetails.php', {
                        method: 'POST',
                        body: JSON.stringify(newData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success === false) {
                            var response_message = data.message;
                            alert(response_message);
                            document.getElementById('productSelectLoan').value = '';
                        } else {
                            var response_message = data.message;
                            var int = data.int_rate;
                            var prod_max = data.prod_max;
                            var max_term = data.max_term;
                            var prod_fees = data.prod_fees;
                            var repayFrequency = data.repaymtFrequency;

                            document.getElementById('pymtFrequency').value = repayFrequency;
                            
                            document.getElementById('interest-rate').value = int;
                            document.getElementById('interest-rateH').value = int;
                            document.querySelector('.interest').removeAttribute('hidden');
                            
                            document.getElementById('loan-fee').value = prod_fees;
                            document.getElementById('loan-feeH').value = prod_fees;
                            document.querySelector('.loanFees').removeAttribute('hidden');
                            
                            document.getElementById('max-loan').value = prod_max;
                            document.getElementById('max-period').value = max_term; 
                            document.getElementById('no-of-installments').value = max_term;
                            
                            document.querySelector('.loanAmount').removeAttribute('hidden');
                            
                        }
                    });
                }
                
                function checkAmount(){
                    var amountApplied = document.getElementById('amountApplied').value;
                    var maxApplied = document.getElementById('max-loan').value;
                    
                    if(parseInt(maxApplied) > 0){
                        if(parseInt(amountApplied) > parseInt(maxApplied)){
                            alert("You can not apply more than product maximum of Kshs. " + maxApplied);
                            document.getElementById('amountApplied').value = '';
                        } else {
                            document.querySelector('.no-of-installments').removeAttribute('hidden');
                            checkLoanTerm();
                        }
                    } else {
                        document.querySelector('.no-of-installments').removeAttribute('hidden');
                        checkLoanTerm();
                    }
                }
                
                function checkLoanTerm(){
                    //var termEntered = document.getElementById('loan-term').value;
                    var maxTerm = document.getElementById('max-period').value;
                    var noOfInstallments = document.getElementById('no-of-installments').value;
                    
                    if(parseInt(maxTerm) > 0){
                        if(parseInt(noOfInstallments) > parseInt(maxTerm)){
                            alert("Cannot apply more than product maximum no of installments of  " + maxTerm + ".");
                            document.getElementById('no-of-installments').value = maxTerm;
                            checkLoanTerm();
                        } else {
                            
                            var installmentS = calcInstallment();
                            
                            var installmentS1 = Math.ceil(installmentS);
                            //var repaymentAmt = parseInt(installmentS1).toLocaleString();
                            
                            document.getElementById('loan-installment').value = installmentS1;
                            document.getElementById('loan-installmentH').value = installmentS1;
                            document.querySelector('.installment').removeAttribute('hidden');
                            
                            var amountApplied = document.getElementById('amountApplied').value;
                            var loanFees = document.getElementById('loan-fee').value;
                            
                            var loanType = document.getElementById('loanTypeX').value;
                    
                            if(loanType === 'New Loan'){
                                var takeHomeAmount = parseInt(amountApplied) - parseInt(loanFees);
                            } else {
                                var topupBal = document.getElementById('topupBal').value;
                                
                                var takeHomeAmount = parseInt(amountApplied) - parseInt(loanFees) - parseInt(topupBal);
                                
                                document.querySelector('.topupBalC').removeAttribute('hidden');
                            }
    
                            document.getElementById('takeHome1').value = takeHomeAmount;
                            document.getElementById('takeHomeH').value = takeHomeAmount;
                            document.querySelector('.takeHome').removeAttribute('hidden');
                            document.querySelector('.grossLoan').removeAttribute('hidden');
                            document.querySelector('.loanForm').removeAttribute('hidden');
                            
                        }
                    } else {
                        var installmentS = calcInstallment();
                        
                        var installmentS1 = Math.ceil(installmentS);
                        //var repaymentAmt = parseInt(installmentS1).toLocaleString();
                        
                        document.getElementById('loan-installment').value = installmentS1;
                        document.getElementById('loan-installmentH').value = installmentS1;
                        document.querySelector('.installment').removeAttribute('hidden');
                        
                        var amountApplied = document.getElementById('amountApplied').value;
                        var loanFees = document.getElementById('loan-fee').value;
                        
                        var loanType = document.getElementById('loanTypeX').value;
                
                        if(loanType === 'New Loan'){
                            var takeHomeAmount = parseInt(amountApplied) - parseInt(loanFees);
                        } else {
                            var topupBal = document.getElementById('topupBal').value;
                            
                            var takeHomeAmount = parseInt(amountApplied) - parseInt(loanFees) - parseInt(topupBal);
                            
                            document.querySelector('.topupBalC').removeAttribute('hidden');
                        }

                        document.getElementById('takeHome1').value = takeHomeAmount;
                        document.getElementById('takeHomeH').value = takeHomeAmount;
                        document.querySelector('.takeHome').removeAttribute('hidden');
                        document.querySelector('.grossLoan').removeAttribute('hidden');
                        document.querySelector('.loanForm').removeAttribute('hidden');
                    }
                }
                
                function calcInstallment(){
                    var amountApplied = document.getElementById('amountApplied').value;
                    var noOfInstallments = document.getElementById('no-of-installments').value;
                    var intrst = document.getElementById('interest-rate').value;
                    var repayFrequency = document.getElementById('pymtFrequency').value;
                    
                    if(repayFrequency === 'Daily'){
                        var divideBy = 28;
                    } else  if(repayFrequency === 'Weekly'){
                        var divideBy = 4;
                    } else if(repayFrequency === 'Monthly'){
                        var divideBy = 1;
                    } else {
                        var divideBy = 0;
                    }
                    
                    var totalInterest = (parseInt(amountApplied) * (intrst/100) * (parseInt(noOfInstallments) / parseInt(divideBy)));
                    var grosLoan = (parseInt(amountApplied) + parseInt(totalInterest));
                    var installments = (parseInt(grosLoan) / parseInt(noOfInstallments));
                    
                    //console.log(grosLoan);
                    document.getElementById('grossLoan').value = grosLoan;
                    document.getElementById('grossLoanH').value = grosLoan;
                    
                    return installments;
                }
                
                //check the files being uploaded are okay
                function validateFormNow(){
                    var fileInput = document.getElementById('loanForm');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('loanForm').value = '';
                    } else {
                        document.querySelector('.submission').removeAttribute('disabled');
                    }
                }
                
                function validateForm(fileInput) {
                    // Check file types
                    if (!validateFileType(fileInput)) {
                        alert("Please select only JPG, JPEG, PNG, GIF, PDF, DOC, or DOCX files.");
                        return false;
                    }
                
                    // Check file sizes
                    if (!validateFileSize(fileInput)) {
                        alert("File size exceeds the limit of 500KB.");
                        return false;
                    }
                
                    return true;
                }
                
                function validateFileType(input) {
                    var validExtensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];
                    var fileExtension = input.value.split('.').pop().toLowerCase();
                    return validExtensions.includes(fileExtension);
                }
                
                function validateFileSize(input) {
                    //return input.files[0].size <= 500000; //500KB = 500000
                    return true;
                }
                
                
                function checkReviewer(){
                    
                    var reviewerData = document.getElementById('loanSelect').value;
                    var reviewerThis = "<?php echo $username; ?>";
                    
                    const newData2 = {
                        data: reviewerData,
                        check: "validateApprover"
                    };
                
                    // Send an AJAX request to a PHP script to update the data
                    return fetch('/templates/checkProductDetails.php', {
                        method: 'POST',
                        body: JSON.stringify(newData2),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success === true) {
                            //get the repayment frequency
                            var repaymentFrequency = data.repayFrequency; 
                            var firstRepaymentDate1 = data.firstRepayDate;
                            
                            document.getElementById('frequencySelect').value = repaymentFrequency;
                            if(firstRepaymentDate1 === ""){
                                //loan has not been reviewed yet
                                document.getElementById('approveLoan').value = 'Review Loan';
                            } else {
                                //fill first repayment date for approval
                                document.getElementById('repaymentDate').value = firstRepaymentDate1;
                                
                                var admin = <?php if(!$admin){ echo '0'; } else { echo $admin;}?>;
                                if(admin != 2){
                                    //do nothing
                                } else {
                                    document.getElementById('approveLoan').value = 'Approve Loan';
                                    document.querySelector('.deferLoan').removeAttribute('hidden');
                                }
                            }
                                
                            if(data.message === null || data.message === reviewerThis){
                                //alert("You can not review and approve same application.");
                                //document.getElementById('loanSelect').value = ''; 
                                document.querySelector('.repayDate').removeAttribute('hidden');
                                document.querySelector('.loanNarration').removeAttribute('hidden');

                            } else {
                                //do nothing
                                document.querySelector('.repayDate').removeAttribute('hidden');
                                document.querySelector('.loanNarration').removeAttribute('hidden');
                            }
                        } else {
                            throw new Error(data.message); // Use throw to reject the promise with an error
                            alert(data.message);
                        }
                    });
                }
                
                document.getElementById('productSelectLoan').addEventListener('change', getProductInfo);
                document.getElementById('amountApplied').addEventListener('change', checkAmount);
                document.getElementById('no-of-installments').addEventListener('change', checkLoanTerm);
                document.getElementById('approveLoanModalBtn').addEventListener('click', checkReviewer);
                document.getElementById('loanForm').addEventListener('change', validateFormNow);
            });
            
        
            //Btn click function
            function btnClick(btnId){
                document.getElementById(btnId).click();
            }
            
            //Add a script to search the table -->
            function searchTable1() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search1');
                filter = input.value.toUpperCase();
                table = document.getElementById('appraisal-table');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName('td'); // Get all elements
            
                    for (j = 0; j < td.length; j++) {
                        txtValue = td[j].value || td[j].innerText || td[j].textContent;
            
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = '';
                            break;  // Stop further checks if a match is found in this row
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                }
            }
            
            //Add a script to search the table -->
            function searchTable3() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search3');
                filter = input.value.toUpperCase();
                table = document.getElementById('transactions-table');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName('td'); // Get all elements
            
                    for (j = 0; j < td.length; j++) {
                        txtValue = td[j].value || td[j].innerText || td[j].textContent;
            
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = '';
                            break;  // Stop further checks if a match is found in this row
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                }
            }
            
            //Add a script to search the table -->
            function searchTable4() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search4');
                filter = input.value.toUpperCase();
                table = document.getElementById('repayment-schedules');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName('td'); // Get all elements
            
                    for (j = 0; j < td.length; j++) {
                        txtValue = td[j].value || td[j].innerText || td[j].textContent;
            
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = '';
                            break;  // Stop further checks if a match is found in this row
                        } else {
                            tr[i].style.display = 'none';
                        }
                    }
                }
            }
            

            //Pagination1
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('appraisal-table');
                const tbody = table.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');
                let currentPage = 1;
                let pageSize = 5;
                
                const totalPages = Math.ceil(rows.length / pageSize);
                
                function showPage(page) {
                    rows.forEach((row, index) => {
                        if (index >= (page - 1) * pageSize && index < page * pageSize) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    document.getElementById('page-info1').textContent = `Page ${currentPage} of Page ${totalPages}`;
                }
                
                function updateButtons() {
                    document.getElementById('prev-page1').disabled = currentPage === 1;
                    document.getElementById('next-page1').disabled = currentPage === Math.ceil(rows.length / pageSize);
                }
    
                function nextPage() {
                    if (currentPage < Math.ceil(rows.length / pageSize)) {
                        currentPage++;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function prevPage() {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function changePageSize() {
                    const selectedValue = document.getElementById('page-size1').value;

                    if (selectedValue === 'all') {
                        pageSize = rows.length;
                    } else {
                        pageSize = parseInt(selectedValue, 10);
                    }
                    
                    currentPage = 1;
                    showPage(currentPage);
                    updateButtons();
                }
    
                showPage(currentPage);
                updateButtons();
    
                document.getElementById('next-page1').addEventListener('click', nextPage);
                document.getElementById('prev-page1').addEventListener('click', prevPage);
                document.getElementById('page-size1').addEventListener('change', changePageSize);
            });
            
            //Pagination3
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('transactions-table');
                const tbody = table.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');
                let currentPage = 1;
                let pageSize = 5;
                
                const totalPages = Math.ceil(rows.length / pageSize);
                
                function showPage(page) {
                    rows.forEach((row, index) => {
                        if (index >= (page - 1) * pageSize && index < page * pageSize) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    document.getElementById('page-info3').textContent = `Page ${currentPage} of Page ${totalPages}`;
                }
                
                function updateButtons() {
                    document.getElementById('prev-page3').disabled = currentPage === 1;
                    document.getElementById('next-page3').disabled = currentPage === Math.ceil(rows.length / pageSize);
                }
    
                function nextPage() {
                    if (currentPage < Math.ceil(rows.length / pageSize)) {
                        currentPage++;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function prevPage() {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function changePageSize() {
                    const selectedValue = document.getElementById('page-size3').value;

                    if (selectedValue === 'all') {
                        pageSize = rows.length;
                    } else {
                        pageSize = parseInt(selectedValue, 10);
                    }
                    
                    currentPage = 1;
                    showPage(currentPage);
                    updateButtons();
                }
    
                showPage(currentPage);
                updateButtons();
    
                document.getElementById('next-page3').addEventListener('click', nextPage);
                document.getElementById('prev-page3').addEventListener('click', prevPage);
                document.getElementById('page-size3').addEventListener('change', changePageSize);
            });
            
            //Pagination4
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('repayment-schedules');
                const tbody = table.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');
                let currentPage = 1;
                let pageSize = 5;
                
                const totalPages = Math.ceil(rows.length / pageSize);
                
                function showPage(page) {
                    rows.forEach((row, index) => {
                        if (index >= (page - 1) * pageSize && index < page * pageSize) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                    document.getElementById('page-info4').textContent = `Page ${currentPage} of Page ${totalPages}`;
                }
                
                function updateButtons() {
                    document.getElementById('prev-page4').disabled = currentPage === 1;
                    document.getElementById('next-page4').disabled = currentPage === Math.ceil(rows.length / pageSize);
                }
    
                function nextPage() {
                    if (currentPage < Math.ceil(rows.length / pageSize)) {
                        currentPage++;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function prevPage() {
                    if (currentPage > 1) {
                        currentPage--;
                        showPage(currentPage);
                        updateButtons();
                    }
                }
    
                function changePageSize() {
                    const selectedValue = document.getElementById('page-size4').value;

                    if (selectedValue === 'all') {
                        pageSize = rows.length;
                    } else {
                        pageSize = parseInt(selectedValue, 10);
                    }
                    
                    currentPage = 1;
                    showPage(currentPage);
                    updateButtons();
                }
    
                showPage(currentPage);
                updateButtons();
    
                document.getElementById('next-page4').addEventListener('click', nextPage);
                document.getElementById('prev-page4').addEventListener('click', prevPage);
                document.getElementById('page-size4').addEventListener('change', changePageSize);
            });

        </script>
        
        <?php //include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include __DIR__ .'/../../templates/scrollUp.php'; ?>
    </body>
</html>