<?php
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/pay-process2.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/counter.php';
    require_once __DIR__.'/templates/checkMembersBalances.php';
    require_once __DIR__.'/templates/notifications.php';
    require_once __DIR__.'/templates/sendsms.php';
    require_once __DIR__.'/templates/upload_docs.php';
    require_once __DIR__.'/templates/loanActions.php';
    require_once __DIR__.'/templates/loanRepayment.php';
    require_once __DIR__.'/templates/ledgerActions.php';
    
    use Dotenv\Dotenv;
    use IntaSend\IntaSendPHP\Collection;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    }
    
    $username = $_SESSION['username'];
    $username1 = encrypt($username);
    
    $admin = $_SESSION['admin'];
    $limit = (!$admin) ? " AND staff_phone = '$username1' " : "";
    $limit1 = (!$admin) ? " AND c.staff_phone = '$username1' " : "";
    
    $member_no = $_SESSION['member_no'];
    if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
        $access = false;
    } else {
        $access = true;
    }
    
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //STK PUSH
    function initCollection() {
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $collection = new Collection();
        $collection->init($credentials);
        
        return $collection;
    }
    
    function getInvoiceStatus($invoice_id) {
        // Database credentials
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        //sleep(2);
        
        $status = "SELECT * FROM mpesa_collections WHERE invoice_id='$invoice_id'";
        $resultStatus = $conn->query($status);
        $resultStatus = $resultStatus->fetch_assoc();
        $responseS = $resultStatus['state'];
        $responseR = $resultStatus['failed_reason'];
        $response = [
                'state'=>$responseS,
                'failed_reason' =>$responseR,
            ];
            
        return $response;
    }
    
    function performPaymentRequest($amount, $formatted_phone_number, $api_ref) {
        $collection = initCollection();
        $response = $collection->mpesa_stk_push($amount, $formatted_phone_number, $api_ref);
        return $response;
    }
    
    if (isset($_POST['getInvoiceStatus'])) {
        $invoice_id = $_POST['invoice_idT']; // Retrieve the invoice ID from the form input
        
        // Get the payment status
        $response = getInvoiceStatus($invoice_id);
        
        // Send the JSON-encoded response back to the client
        echo json_encode($response);
        exit;
    }
    
    if (isset($_POST['stkPushed'])) {
        // Retrieve the form data
        $amount = $_POST['amount'];
        $phone_number = $_POST['phone_number'];
        
        // Extract the last 9 digits from the phone number
        $standardizedInput = standardizePhoneNumber($phone_number);
        
        // Add the prefix "254" to the phone number
        $formatted_phone_number = '254' . $standardizedInput;
        
        $api_ref = "MFI-CUSTOMERS"; // You can generate a unique reference for each transaction
        
        // Perform the payment request
        $response = performPaymentRequest($amount, $formatted_phone_number, $api_ref);
        
        // Get the invoice ID from the response
        $invoice = $response->invoice;
        $invoice_id = $invoice->invoice_id;
    }
    
    
    //add loan product
    if(isset($_POST['addProduct'])){
        $productName = encrypt($_POST['product-name']);
        $maxTerm = encrypt($_POST['max-term']);
        $interest = encrypt($_POST['loan-interest']);
        $loanFees = encrypt($_POST['loan-fees']);
        $loanMax = encrypt($_POST['loan-max']);
        $repayFrequency = encrypt($_POST['repayFrequency']);
        
        //Get the max product No
        $sqlMaxProdNo = $conn->query("SELECT MAX(product_no) as max_no FROM loan_products");
        if($sqlMaxProdNo->num_rows > 0){
            $sqlMaxProdNoResult = $sqlMaxProdNo->fetch_assoc();
            $maxNo = $sqlMaxProdNoResult['max_no'];
        } else {
            $maxNo = 0;
        }
        
        $nextProductNo = $maxNo + 1;
        
        $status = encrypt('active');
        
        //Insert product
        $sqlAddProduct = $conn->query("INSERT INTO loan_products (product_no, product_name, product_maxAmount, product_maxTerm, product_interest,  product_fees, product_status, repaymentFrequency) 
        VALUES ('$nextProductNo', '$productName', '$loanMax', '$maxTerm', '$interest', '$loanFees', '$status', '$repayFrequency')"); 
        
        if($sqlAddProduct){
            //add new product as a subledger under Total Loans Advanced
            $parent_ledgerSno = '101100';
            $sub_account_name = decrypt($productName);
            $sub_account_bal0 = '0';
            addSubLedger($conn, $parent_ledgerSno, $sub_account_name, $sub_account_bal0);
            
            //add new product as a subledger under Normal Interest Income
            $parent_ledgerSno = '500100';
            $sub_account_name = decrypt($productName);
            $sub_account_bal0 = '0';
            addSubLedger($conn, $parent_ledgerSno, $sub_account_name, $sub_account_bal0);
            
            //add new product as a subledger under Loan Fees Income
            $parent_ledgerSno = '501100';
            $sub_account_name = decrypt($productName);
            $sub_account_bal0 = '0';
            addSubLedger($conn, $parent_ledgerSno, $sub_account_name, $sub_account_bal0);
            
            header("Location: /loans");
        }
    }
    
    //edit loan product
    if(isset($_POST['editProduct'])){
        $productNamePrev00 = $_POST['editThisProduct'];
        $productNamePrev00 = explode(" - ", $productNamePrev00);
        $productName = encrypt($productNamePrev00[1]);
        $productNo = $productNamePrev00[0];

        $maxTerm = encrypt($_POST['max-termE']);
        $interest = encrypt($_POST['loan-interestE']);
        $loanFees = encrypt($_POST['loan-feesE']);
        $loanMax = encrypt($_POST['loan-maxE']);
        $repayFrequency = encrypt($_POST['repayFrequencyE']);
        
        $sqlEditProduct = $conn->query("UPDATE loan_products SET product_name='$productName', product_maxAmount='$loanMax', product_maxTerm='$maxTerm', product_interest='$interest', 
        product_fees='$loanFees', repaymentFrequency='$repayFrequency' WHERE product_no='$productNo' ") ;

        if($sqlEditProduct){
           header("Location: loans");
        }
    }
    
    //remove loan product
    if(isset($_POST['removeProduct'])){
        $productName = $_POST['removeThisProduct'];
        $productNo = explode(' - ' , $productName);
        $productName1 = $productNo[1];
        $productNo1 = $productNo[0];
        
        $deactivated = encrypt('inactive');
        
        $allowRemove= removeSubLedger($conn,$productName1);
        
        if($allowRemove){
            $sqlRemoveProducts = $conn->query("UPDATE loan_products SET product_status='$deactivated' WHERE product_no='$productNo1' ");
            
            if($sqlRemoveProducts){
                header("Location: loans");
            } else {
                header("Location: loans");
            }
        } else {
            header("Location: loans");
        }
    }
    
    //add loan classification
    if(isset($_POST['addClassification'])){
        $productName = $_POST['thisProduct'];
        $productNo = explode(' - ' , $productName);
        $productNo1 = $productNo[0];
        $productName1 = encrypt($productNo[1]);
        
        $classification = encrypt($_POST['classification']);
        $daysInArrears = encrypt($_POST['daysInArrears']);
        
        //Insert product
        $sqlAddProduct1 = $conn->query("INSERT INTO loan_classification (product_no, product_name, days_inArrears, classification) 
        VALUES ('$productNo1', '$productName1', '$daysInArrears', '$classification')"); 
        
        if($sqlAddProduct1){
           header("Location: loans");
        }
    }
    
    //Remove loan classification
    if(isset($_POST['removeProductClass'])){
        $productName = $_POST['removeThisProductClass'];
        $productNo = explode(' - ' , $productName);
        $sNo1 = $productNo[0];
        
        $delClass = $conn->query("DELETE FROM loan_classification WHERE s_no = '$sNo1'");
        
        if($delClass){
            header("Location: loans");
        } else {
            header("Location: loans");
        }
        
    }
    
    //make loan application form_tokenApply
    if(isset($_POST['submitLoan']) ){
        $session_token = $_POST['session_token_submitLoan'];
        
        $formSent = checkSessionToken($conn, $session_token, 'loan_applications');
        
        if($formSent){
            //do not send data again
        } else {
            
            $loanType1 = $_POST['loanTypeX'];
            if($loanType1 === "New Loan"){
                $applicant = $_POST['memberSelectLoan'];
                $productSelectLoan1 = $_POST['productSelectLoan'];
                $loanType1 = $loanType1;
            } else {
                $applicantTopup = $_POST['loanSelectX'];
                $applicantTopup1 = explode(" - ", $applicantTopup);
                $applicant = $applicantTopup1[2];
                $topupLoanNum = $applicantTopup1[0];
                $productSelectLoan1 = $_POST['topupLoanTypeH'];
                $loanType1 = $loanType1 . '-' . $topupLoanNum;
            }
            
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
            
            $applicant1 = encrypt($applicant);
            $productSelectLoan = encrypt($productSelectLoan1);
            $interestRates = encrypt($interestRates);
            $loanFees = encrypt($loanFees);
            $amount = encrypt($amount);
            $noOfInstallments = encrypt($noOfInstallments1);
            $loanInstallment = encrypt($loanInstallment);
            $status = encrypt('Not Approved');
            $date = encrypt($date);
            $takeHome = encrypt($takeHome);
            $grossLoanAmt = encrypt($grossLoanAmt);
            $loanType = encrypt($loanType1);
            $repaymentFrequency1 = encrypt($pymtFrequency);
            
            
            //get the customer/member no
            $sqlcustomerNum = $conn->query("SELECT * FROM customers WHERE customer_phone='$applicant1'");
            $sqlcustomerNumResults = $sqlcustomerNum->fetch_assoc();
            $customerNo = $sqlcustomerNumResults['customer_no'];
            $customerName = $sqlcustomerNumResults['customer_name'];
            $customerPhone = $sqlcustomerNumResults['customer_phone'];
            $location_name = $sqlcustomerNumResults['location_name'];
            $owner = $sqlcustomerNumResults['staff_phone'];
            
            $nextLoanNum = 0;
            
            //get next loan number
            $sqlGetLoanNo1 = $conn->query("SELECT MAX(s_no) AS max_loan_no FROM loan_applications ");
            
            if($sqlGetLoanNo1->num_rows > 0){
                $sqlGetLoanNoResult  = $sqlGetLoanNo1->fetch_assoc();
                $loanNum = intval($sqlGetLoanNoResult['max_loan_no']);
            } else {
                $loanNum1 = 0;
                $loanNum = intval($loanNum1);
            }
            
            $nextLoanNum = $loanNum + 1;
            
            $locality = 'customer_docs';
            
            $documentName='loanForm';
            $loanFormName = $documentName . $applicant . 'No' . $nextLoanNum;
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
            
            //check whether the loan has already been created
            $sqlCheckLoanNum = $conn->query("SELECT * FROM loan_applications WHERE loan_no=$nextLoanNum");
            if($sqlCheckLoanNum->num_rows > 0){
                //do nothing
            } else {
                //insert into table loans
                $sqlLoanApp = $conn->prepare("INSERT INTO loan_applications (loan_no, customer_no, customer_name, customer_phone, loan_product, loan_type, loan_amount, no_of_installments, loan_interest, loan_installment, principalBal, interestBal, loan_applicationDate, take_home, loan_status, location_name, gross_loan, loan_balance, loan_form, repaymentFrequency, session_token, staff_phone)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $sqlLoanApp->bind_param("ssssssssssssssssssssss", $nextLoanNum, $customerNo, $customerName, $customerPhone, $productSelectLoan, $loanType, $amount, $noOfInstallments, $interestRates, $loanInstallment, $principalBal, $interestBal, $date, $takeHome, $status, $location_name, $grossLoanAmt, $grossLoanAmt, $loanForm1, $repaymentFrequency1, $session_token, $owner);
                $sqlLoanApp->execute();
                
                if($sqlLoanApp){
                    //update loan number to the loan application fee paid in customer_registration table
                    $account = '254' . substr(decrypt($customerPhone), -9);
                    
                    $conn->query("UPDATE customer_registration SET loan_no = '$nextLoanNum' WHERE (account = '$account' AND loan_no IS NULL )");
                    
                    //save the notification
                    $notification = "You have a new loan application pending review.";
                    $role = 'Admin';
                    saveNotification($notification, $role);
                    
                    //update appraisal
                    $review = 'Application';
                    $actionBy = $username;
                    $description = 'Loan application captured.';
                    $loan_no = $nextLoanNum;
                    
                    addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
                    
                    header("Location: /loans");
                } else {
                    header("Location: /loans");
                }
            }
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
            $payMode = $_POST['payment-mode1'];
            $posting_date = $_POST['paidDate'];
            $posting_description = ("Repayment");
            $descriptionNo = $payMode;
            $transactionBy = $username;
            
            repayLoan($conn, $loanNo3, $repaymentAmt, $payMode, $posting_date, $posting_description, $descriptionNo, $transactionBy, $session_token);
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
    
    //get the count of how many loans are pending or reviewed
    $pending = encrypt('Not Approved');
    $reviewed = encrypt('Reviewed');
    $countPending = 0;
    $countReviewed = 0;
    
    $sqlCountPending = $conn->query("SELECT loan_status FROM loan_applications WHERE s_no > 0 $limit ");
    if($sqlCountPending->num_rows > 0){
        while($resultCountPending = $sqlCountPending->fetch_assoc()){
            $countP = $resultCountPending['loan_status'];
            if($countP === $pending){
                $countPending ++;
            } else if ($countP === $reviewed){
                $countReviewed ++;
            }
        }
    }
    
    //count loans due today
    $today = encrypt(date("Y-m-d"));
    $dueTodays = 0;
    $sqlSchedulesDueToday = $conn->query("SELECT COUNT(customer_phone) as count_due FROM loan_schedules WHERE due_date = '$today' ");
    if($sqlSchedulesDueToday->num_rows > 0){
        $countDueToday = $sqlSchedulesDueToday->fetch_assoc();
        $dueTodays = $countDueToday['count_due'];
    }
    
        
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Loans</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                //Load processing gif
                $('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                //process payment status check
              $('#form1').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                
                // Make an AJAX request to the PHP script
                $.ajax({
                  url: '',
                  type: 'POST',
                  data: { getInvoiceStatus: true, invoice_idT: $('#invoice_id').val() },
                  dataType: 'json',
                  success: function(response) {
                    // Update the status on the page
                    
                    if (response.state === "COMPLETE") {
                      $('#status').text(response.state);
                      // Print link or perform any other action upon completion
                      //$('#back1').show();
                        
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "FAILED") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "RETRY") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else {
                      // Display the loading GIF
                      //$('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                    }
                  },
                  error: function() {
                    alert('An error occurred while retrieving the invoice status.');
                    $('#status').text('Error while processing');
                    clearInterval(statusInterval);
                  }
                });
              });
            
              // Check the status every 5 seconds
              var statusInterval = setInterval(function() {
                $('#form1').submit();
              }, 5000);
            });

        </script>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark" >
                Loan Management
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#applyLoanModal">Apply Loan</button>
                            </li>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#recordRepaymentModal">Record Repayment</button>
                            </li>
                            <li <?php if(!$admin || !$access){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#debitModal">Add Debit</button>
                            </li>
                            <li <?php if($admin !== 2 || !$access){ echo 'hidden'; } ?>>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#addEditProductModal">Add/Edit/Remove Loan Product</button>
                            </li>
                            <li <?php if($admin !== 2 || !$access){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#addEditProductClassificationModal"> Add/Remove Loan Classification</button>
                            </li>
                            <li>
                                <button type="button" id="openingModal" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#stkPushModal">Initiate Loan Fees Payment</button>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <!-- Quick Stats or Metrics -->
                        <div <?php if($admin !== 2 ){ echo 'hidden'; } ?> class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Loan Register Information</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <span> Total Loans Advanced</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <?php
                                                 $cumm_loans = getBalances($conn, 'cumm_loans');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($cumm_loans, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Loan Repayments</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <?php
                                                 $cumm_repayments = getBalances($conn, 'cumm_repayments');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($cumm_repayments, 2); ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Loan Balance</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <?php
                                                 $loan_bal = getBalances($conn, 'loan_bal');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($loan_bal, 2); ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div <?php if($admin !== 2){ echo 'hidden'; } ?> class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Loan Register Information</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <span> Total Count Advanced</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <?php
                                                 $cumm_loans1 = getBalances($conn, 'count_loans');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($cumm_loans1, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Repayments</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <?php
                                                 $cumm_repayments1 = getBalances($conn, 'count_repayments');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($cumm_repayments1, 2); ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Outstanding</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <?php
                                                 $loan_bal1 = getBalances($conn, 'count_bal');
                                            ?> 
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($loan_bal1, 2); ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan Applications</b>
                                <span class="badge text-bg-info text-bg-light">New <?php echo $countPending;?></span>
                                <span class="badge text-bg-info text-bg-light">Reviewed <?php echo $countReviewed;?></span>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button <?php if(!$admin){ echo 'hidden'; } ?> type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('applications-table', 'applications')" >Export to Excel</button>
                            
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
                                <table id="applications-table" class="table table-hover border border-rounded">
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
                                            <th>Loan Interest</th>
                                            <th>Loan Installment</th>
                                            <th>Application Date</th>
                                            <th>Approved Date</th>
                                            <th>Gross Loan</th>
                                            <th>Take Home</th>
                                            <th>Status</th>
                                            <th>Loan Form</th>
                                            <th>Branch</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php

                                        $sqlLoansApp = "SELECT * FROM loan_applications WHERE s_no > 0 $limit ORDER BY s_no DESC";
                                
                                        $resultLoansApp = $conn->query($sqlLoansApp);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultLoansApp->num_rows > 0) {
                                            while ($rowLoansApp = $resultLoansApp->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoansApp['loan_no']}'>{$rowLoansApp['loan_no']}</a></td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowLoansApp['customer_no']}'>{$rowLoansApp['customer_no']}</a></td>
                                                    <td>" . decrypt($rowLoansApp["customer_name"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["customer_phone"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_product"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_type"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_amount"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["no_of_installments"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_interest"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_installment"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_applicationDate"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_approvalDate"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_balance"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["take_home"]) . "</td>
                                                    <td>" . decrypt($rowLoansApp["loan_status"]) . "</td>
                                                    <td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($rowLoansApp["loan_form"]) . "' download>Download</a></td>
                                                    <td>" . $rowLoansApp["location_name"] . "</td>";
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
                    
                    <div  class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Running Loans</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button <?php if(!$admin){ echo 'hidden'; } ?> type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('running-table', 'running_loans')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="payments-search2" onkeyup="searchTable2()" placeholder="Search by name or phone number"  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size2">Rows per page:</label>
                                <select id="page-size2">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="running-table" class="table table-hover border border-rounded">
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
                                            <th>Interest % (p.m.)</th>
                                            <th>Loan Installment</th>
                                            <th>Application Date</th>
                                            <th>Approved Date</th>
                                            <th>Gross Loan</th>
                                            <th>Total Paid</th>
                                            <th>Loan Balance</th>
                                            <th>First Repayment Date</th>
                                            <th>Last Pay Date</th>
                                            <th>Status</th>
                                            <th>Classification</th>
                                            <th>Branch</th>
                                            <th>Portfolio Owner</th>
                                            <th>Statement</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        $open = encrypt('Open');
                                        $sqlLoans = "SELECT * FROM loans WHERE loan_status='$open' $limit ORDER BY loan_no DESC";
                                
                                        $resultLoans = $conn->query($sqlLoans);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultLoans->num_rows > 0) {
                                            while ($rowLoans = $resultLoans->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans['loan_no']}'>{$rowLoans['loan_no']}</a></td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowLoans['customer_no']}'>{$rowLoans['customer_no']}</a></td>
                                                    <td>" . decrypt($rowLoans["customer_name"]) . "</td>
                                                    <td>" . decrypt($rowLoans["customer_phone"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_product"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_type"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_amount"]) . "</td>
                                                    <td>" . decrypt($rowLoans["no_of_installments"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_interest"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_installment"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_applicationDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_approvalDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["gross_loan"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_payments"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_balance"]) . "</td>
                                                    <td>" . decrypt($rowLoans["firstRepaymentDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["last_paymentDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_status"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_classification"]) . "</td>
                                                    <td>" . $rowLoans["location_name"] . "</td>
                                                    <td>" . decrypt($rowLoans["staff_phone"]) . "</td>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/statement?lno={$rowLoans['loan_no']}'>Statement</a></td>
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
                            <div class="pagination">
                                <button id="prev-page2">Previous Page</button>
                                <span id="page-info2"></span>
                                <button id="next-page2">Next Page</button>
                            </div>
                        </div>
                    </div>
                    
                    <br>
                    <div class="card bg-info shadow">
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
                                            <th>S/No.</th>
                                            <th>Loan No.</th>
                                            <th>Customer Number</th>
                                            <th>Customer Name</th>
                                            <th>Customer Phone</th>
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
                                        //$sqlPaid = "SELECT * FROM loan_transactions ORDER BY s_no DESC";
                                        $sqlPaid = "SELECT lt.*, c.customer_phone, c.customer_name, c.staff_phone FROM loan_transactions lt 
                                            LEFT JOIN customers c ON lt.customer_no = c.customer_no WHERE c.customer_no > 0 $limit
                                            ORDER BY lt.s_no DESC";
                                
                                        $resultPaid = $conn->query($sqlPaid);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultPaid->num_rows > 0) {
                                            while ($rowLoans2 = $resultPaid->fetch_assoc()) {
                                                
                                                echo "<tr>";
                                                echo " <td>" . $rowLoans2["s_no"] . "</td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans2['loan_no']}'>{$rowLoans2['loan_no']}</a></td>
                                                    <td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$rowLoans2['customer_no']}'>{$rowLoans2['customer_no']}</a></td>
                                                    <td>" . decrypt($rowLoans2["customer_name"]) . "</td>
                                                    <td>" . decrypt($rowLoans2["customer_phone"]) . "</td>
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
                    <div <?php if($admin != 2) { echo "hidden";} ?> class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan Repayment Schedules</b>
                                <span class="badge text-bg-info text-bg-light">Due <?php echo $dueTodays;?></span></a>

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
                                            <th>Installment</th>
                                            <th>Due Date</th>
                                            <th>Paid</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        
                                        $sqlSchedules = "SELECT * FROM loan_schedules ORDER BY s_no DESC";
                                
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
                    <div class="modal fade" id="applyLoanModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Apply Loan</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="form" method="POST" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="session_token_submitLoan" value="<?php echo date("YmdHis"); ?>">
                                        <div class=" mb-2">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="loanTypeX" class="form-label">Loan Type</label>
                                            <input class="form-control" list="datalistOptions811" id="loanTypeX" name="loanTypeX" placeholder="Loan Type" autocomplete="off" required>
                                            <datalist id="datalistOptions811">
                                                <option value="New Loan"></option>';
                                                <option value="Topup Loan"></option>';
                                            </datalist>
                                        </div>
                                        
                                        <div hidden="hidden" class=" mb-3 searchCustomerTopup">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="loanSelectX" class="form-label">Type to search Customer/Loans..</label>
                                            <input class="form-control" list="datalistOptions81X" id="loanSelectX" name="loanSelectX" placeholder="Type to search Customer/Loans.." autocomplete="off" >
                                            <datalist id="datalistOptions81X">
                                                <?php
                                                    $closed1X = encrypt('Closed');
                                                    $sqlLoans11X = "SELECT * FROM loans WHERE customer_no > 0 AND loan_status <> '$closed1X' $limit" ;
                                                    $resultLoans11X = $conn->query($sqlLoans11X);
                                                    
                                                    if ($resultLoans11X->num_rows > 0) {
                                                        while ($rowLoans11X = $resultLoans11X->fetch_assoc()) {
                                                            $loan_No1X = $rowLoans11X['loan_no'];
                                                            $customer_name1X = decrypt($rowLoans11X['customer_name']);
                                                            $customer_phone1X = decrypt($rowLoans11X['customer_phone']);
                                                            $loan_balance1X = decrypt($rowLoans11X['loan_balance']);
                                                            
                                                            $loanDetailsX = "$loan_No1X - $customer_name1X - $customer_phone1X - $loan_balance1X";
                                                            
                                                            echo '<option value="' . $loanDetailsX . '"></option>';
                                                        }
                                                    } else {
                                                        echo "No Customer/loan found";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <div hidden="hidden" class="searchCustomerNewLoan">
                                            <?php 
                                                //if(!$admin){
                                                    //Show nothing, assume account number is default username
                                                //} else {
                                            ?>
                                                <div  class=" mb-3"> <?php //if(!$admin) { echo 'disabled'; } ?>
                                                    
                                                    <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                    <label for="memberSelectLoan" class="form-label">Type to search Customer..</label>
                                                    <input class="form-control" list="datalistOptions1" id="memberSelectLoan" name="memberSelectLoan" placeholder="Type to search Customer.." autocomplete="off" >
                                                    <datalist id="datalistOptions1">
                                                        <?php
                                                            $activeStatus = encrypt('active');
                                                            $username111 = encrypt($username);
                                                            $sqlMembers = "SELECT * FROM customers WHERE customer_no > 0 AND status='$activeStatus' $limit";
                                                            $resultMembers = $conn->query($sqlMembers);
                                                            
                                                            if ($resultMembers->num_rows > 0) {
                                                                while ($rowMembers = $resultMembers->fetch_assoc()) {
                                                                    echo '<option value="' . decrypt($rowMembers['customer_phone']) . '"></option>';
                                                                }
                                                            } else {
                                                                echo "No Customer found";
                                                            }
                                                            
                                                        ?>
                                                    </datalist>
                                                </div>
                                            <?php
                                                //}
                                            ?>
                                            
                                        </div>
                                            
                                        <div hidden="hidden" class=" mb-3 productSelectLoan1">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
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
                                        <div hidden="hidden" class="form-floating mb-3 text-start topupLoanTypeC">
                                            <input disabled class="form-control" type="text" id="topupLoanType" name="topupLoanType" value="" required>
                                            <input type="hidden" name="topupLoanTypeH" id="topupLoanTypeH" value="">
                                            <label for="topupLoanType">Loan Product</label>
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
                                        <div hidden="hidden" class="form-floating mb-3 text-start pymtFrequency">
                                            <input class="form-control" type="text" id="pymtFrequency" name="pymtFrequency" placeholder="PaymentFrequency" value="" required> 
                                            <label for="pymtFrequency">Payment Frequency</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start loanPeriod">
                                            <input class="form-control" type="number" id="loan-term" name="loan-term" placeholder="Loan Period (months):"> 
                                            <label for="loan-term">Loan Period (months):</label>
                                        </div>
                                        <div hidden="hidden" class="form-floating mb-3 text-start no-of-installments">
                                            <input class="form-control" type="number" id="no-of-installments" name="no-of-installments" placeholder="No. of Installments" required> 
                                            <label for="no-of-installments">No. of Installments</label>
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
                                    <button disabled="disabled" type="button" class="btn btn-primary submission" onclick="btnClick('submitLoanId')">Submit Application</button>                                
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

                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="memberSelect1" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions9" id="memberSelect1"  name="memberSelect1" placeholder="Type to search Customer.." autocomplete="off" required >
                                            <datalist id="datalistOptions9">
                                                <?php
                                                    $closed1 = encrypt('Closed');
                                                    $sqlLoans11 = "SELECT * FROM loans WHERE customer_no > 0 AND loan_status <> '$closed1'" ;
                                                    $resultLoans11 = $conn->query($sqlLoans11);
                                                    
                                                    if ($resultLoans11->num_rows > 0) {
                                                        while ($rowLoans11 = $resultLoans11->fetch_assoc()) {
                                                            $loan_No1 = $rowLoans11['loan_no'];
                                                            $customer_name1 = decrypt($rowLoans11['customer_name']);
                                                            $customer_phone1 = decrypt($rowLoans11['customer_phone']);
                                                            $loan_balance1 = decrypt($rowLoans11['loan_balance']);
                                                            
                                                            $loanDetails = "$loan_No1 - $customer_name1 - $customer_phone1 - $loan_balance1";
                                                            
                                                            echo '<option value="' . $loanDetails . '"></option>';
                                                        }
                                                    } else {
                                                        echo "No Customer/loan found";
                                                    }
                                                ?>
                                            </datalist>
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
                                                <option id="closure" name="closure" >Closure</option>
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
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="memberSelect11" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions91" id="memberSelect11"  name="memberSelect11" placeholder="Type to search Customer.." autocomplete="off" required >
                                            <datalist id="datalistOptions91">
                                                <?php
                                                    $closed1 = encrypt('Closed');
                                                    $sqlLoans111 = "SELECT * FROM loans WHERE customer_no > 0 AND loan_status <> '$closed1'" ;
                                                    $resultLoans111 = $conn->query($sqlLoans111);
                                                    
                                                    if ($resultLoans111->num_rows > 0) {
                                                        while ($rowLoans111 = $resultLoans111->fetch_assoc()) {
                                                            $loan_No11 = $rowLoans111['loan_no'];
                                                            $customer_name11 = decrypt($rowLoans111['customer_name']);
                                                            $customer_phone11 = decrypt($rowLoans111['customer_phone']);
                                                            $loan_balance11 = decrypt($rowLoans111['loan_balance']);
                                                            
                                                            $loanDetails1 = "$loan_No11 - $customer_name11 - $customer_phone11 - $loan_balance11";
                                                            
                                                            echo '<option value="' . $loanDetails1 . '"></option>';
                                                        }
                                                    } else {
                                                        echo "No Customer/loan found";
                                                    }
                                                ?>
                                            </datalist>
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
                    
                    <div class="modal fade" id="addEditProductModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add/Edit/Remove Loan Product</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add New Product</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Name" type="text" id="product-name" name="product-name"  required autocomplete="off">
                                                        <label for="product-name"> Product Name</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Max Amount" type="number" id="loan-max" name="loan-max"  required autocomplete="off">
                                                        <label for="loan-max"> Product Max Amount (Kshs.)</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Max No. of Installments" type="text" id="max-term" name="max-term"  required autocomplete="off">
                                                        <label for="max-term">Max No. of Installments</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Interest (% p.m.)" type="text" id="loan-interest" name="loan-interest"  required autocomplete="off">
                                                        <label for="loan-interest">Interest (% p.m.)</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Fees" type="number" id="loan-fees" name="loan-fees"  required autocomplete="off">
                                                        <label for="loan-fees"> Product Fees (Kshs.)</label>
                                                    </div>
                                                    <div class=" mb-2">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="repayFrequency" class="form-label">Repayment Frequency</label>
                                                        <select class="form-select" name="repayFrequency" id="repayFrequency" required>
                                                            <option value="Daily">Daily</option>';
                                                            <option value="Weekly">Weekly</option>';
                                                            <option value="Monthly">Monthly</option>';
                                                        </select>
                                                    </div>
                                                    <input  class="btn btn-info" type="submit" value="Add Product" name="addProduct" id="addProduct" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Edit Product</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3 editThisProduct">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="editThisProduct" class="form-label">Type to search Product..</label>
                                                        <input class="form-control" list="datalistOptions3edit" id="editThisProduct"  name="editThisProduct" placeholder="Type to search Product.." autocomplete="off">
                                                        <datalist id="datalistOptions3edit">
                                                            <?php
                                                                $activeStatus1E = encrypt('active');
                                                                $sqlproductSelect1E = "SELECT * FROM loan_products WHERE product_status='$activeStatus1E'";
                                                                $resultproductSelect1E = $conn->query($sqlproductSelect1E);
                                                                
                                                                if ($resultproductSelect1E->num_rows > 0) {
                                                                    while ($rowproductSelect1E = $resultproductSelect1E->fetch_assoc()) {
                                                                        $productNumE = $rowproductSelect1E['product_no'];
                                                                        $productName23E = decrypt($rowproductSelect1E['product_name']);
                                                                        
                                                                        $prodDetailsE ="$productNumE - $productName23E";
                                                                        echo '<option value="' . $prodDetailsE . '"></option>';
                                                                    }
                                                                } else {
                                                                    echo "No loan product found";
                                                                }
                                                                
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <div hidden class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Name" type="text" id="editThisProductH" name="editThisProductH" value="" required autocomplete="off">
                                                        <label for="editThisProductH"> Product Name</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Max Amount" type="number" id="loan-maxE" name="loan-maxE"  required autocomplete="off">
                                                        <label for="loan-maxE"> Product Max Amount (Kshs.)</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Max No. of Installments" type="text" id="max-termE" name="max-termE"  required autocomplete="off">
                                                        <label for="max-termE">Max No. of Installments</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Interest (% p.m.)" type="text" id="loan-interestE" name="loan-interestE"  required autocomplete="off">
                                                        <label for="loan-interestE">Interest (% p.m.)</label>
                                                    </div>
                                                    <div class="form-floating mb-2">
                                                        <input  class="form-control" placeholder="Product Fees" type="number" id="loan-feesE" name="loan-feesE"  required autocomplete="off">
                                                        <label for="loan-feesE"> Product Fees (Kshs.)</label>
                                                    </div>
                                                    <div class=" mb-2">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="repayFrequencyE" class="form-label">Repayment Frequency</label>
                                                        <select class="form-select" name="repayFrequencyE" id="repayFrequencyE" required>
                                                            <option value="Daily">Daily</option>';
                                                            <option value="Weekly">Weekly</option>';
                                                            <option value="Monthly">Monthly</option>';
                                                        </select>
                                                    </div>
                                                    <input  class="btn btn-warning" type="submit" value="Edit Product" name="editProduct" id="editProduct" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Remove Product</h3>
                                            <div class="" >
                                                <form style="width: 80%;" method="POST" action="">
                                                    <span>NB: You can only remove a Product that does not have an outstanding loan balance.</span>
                                                    <br>
                                                    <br>
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="removeThisProduct" class="form-label">Type to search Product..</label>
                                                        <input class="form-control" list="datalistOptions3" id="removeThisProduct"  name="removeThisProduct" placeholder="Type to search Product.." autocomplete="off">
                                                        <datalist id="datalistOptions3">
                                                            <?php
                                                                $activeStatus1 = encrypt('active');
                                                                $sqlproductSelect1 = "SELECT * FROM loan_products WHERE product_status='$activeStatus1'";
                                                                $resultproductSelect1 = $conn->query($sqlproductSelect1);
                                                                
                                                                if ($resultproductSelect1->num_rows > 0) {
                                                                    while ($rowproductSelect1 = $resultproductSelect1->fetch_assoc()) {
                                                                        $productNum = $rowproductSelect1['product_no'];
                                                                        $productName23 = decrypt($rowproductSelect1['product_name']);
                                                                        
                                                                        $prodDetails ="$productNum - $productName23";
                                                                        echo '<option value="' . $prodDetails . '"></option>';
                                                                    }
                                                                } else {
                                                                    echo "No loan product found";
                                                                }
                                                                
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    
                                                    <input class=" btn btn-danger" type="submit" value="Remove Product" name="removeProduct" id="removeProduct">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="addEditProductClassificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add/Remove Loan Product Classification</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add New Product Classification</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    
                                                    <div class=" mb-3">
                                                        <label for="thisProduct" class="form-label">Type to search Product..</label>
                                                        <input class="form-control" list="datalistOptions4" id="thisProduct"  name="thisProduct" placeholder="Type to search Product.." autocomplete="off">
                                                        <datalist id="datalistOptions4">
                                                            <?php
                                                                $activeStatus12 = encrypt('active');
                                                                $sqlproductSelect12 = "SELECT * FROM loan_products WHERE product_status='$activeStatus12'";
                                                                $resultproductSelect12 = $conn->query($sqlproductSelect12);
                                                                
                                                                if ($resultproductSelect12->num_rows > 0) {
                                                                    while ($rowproductSelect12 = $resultproductSelect12->fetch_assoc()) {
                                                                        $productNum2 = $rowproductSelect12['product_no'];
                                                                        $productName232 = decrypt($rowproductSelect12['product_name']);
                                                                        
                                                                        $prodDetails2 ="$productNum2 - $productName232";
                                                                        echo '<option value="' . $prodDetails2 . '"></option>';
                                                                    }
                                                                } else {
                                                                    echo "No loan product found";
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="daysInArrears" class="form-label">Days In Arrears</label>
                                                        <select class="form-select" name="daysInArrears" id="daysInArrears" required>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="15">15</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="classification" class="form-label">Classification</label>
                                                        <select class="form-select" name="classification" id="classification" required>
                                                            <option value="Normal">Normal</option>
                                                            <option value="Watch">Watch</option>
                                                            <option value="Substandard">Substandard</option>
                                                            <option value="Doutful">Doutful</option>
                                                            <option value="Loss">Loss</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <input class="btn btn-info" type="submit" value="Add Classification" name="addClassification" id="addClassification" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Remove Product Classification</h3>
                                            <div class="" >
                                                <form style="width: 80%;" method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="removeThisProductClass" class="form-label">Type to search Product Classification..</label>
                                                        <input class="form-control" list="datalistOptions5" id="removeThisProductClass"  name="removeThisProductClass" placeholder="Type to search Product.." autocomplete="off">
                                                        <datalist id="datalistOptions5">
                                                            <?php
                                                                $sqlproductClass1 = "SELECT * FROM loan_classification";
                                                                $resultproductClass1 = $conn->query($sqlproductClass1);
                                                                
                                                                if ($resultproductClass1->num_rows > 0) {
                                                                    while ($rowproductClass1 = $resultproductClass1->fetch_assoc()) {
                                                                        $productNum1 = $rowproductClass1['s_no'];
                                                                        $productName231 = decrypt($rowproductClass1['product_name']);
                                                                        $daysInArrears1 = decrypt($rowproductClass1['days_inArrears']);
                                                                        $classification21 = decrypt($rowproductClass1['classification']);
                                                                        
                                                                        $prodDetailsClass ="$productNum1 - $productName231 - $daysInArrears1 - $classification21";
                                                                        echo '<option value="' . $prodDetailsClass . '"></option>';
                                                                    }
                                                                } else {
                                                                    echo "No classification found";
                                                                }
                                                                
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    
                                                    <input class="btn btn-danger" type="submit" value="Remove Classification" name="removeProductClass" id="removeProductClass" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="stkPushModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Initiate Mpesa Payment</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="form" method="POST" action="">
                                        <div >
                                            <div class="form-floating mb-3 text-start">
                                                <input class="form-control" type="number" id="phone_number" name="phone_number" placeholder="Phone Number 07... OR 01..." required>
                                                <label for="phone_number" for="phone_number">Phone Number</label>
                                            </div>
                                            <div class="form-floating mb-3 text-start ">
                                                <input class="form-control" type="number" id="amount" name="amount" placeholder="Amount (Kes.):"required><br><br>
                                                <label for="amount" for="amount">Amount (Kes.):</label>
                                            </div>
                                            <div class="position-relative">
                                                <div class="position-absolute start-50 translate-middle">
                                                    <input class="btn btn-success btn-bg " type="submit" id="stkPushed" name="stkPushed" value="REQUEST PAYMENT" >
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                
                                    <br>
                                    
                                    <?php
                                    if (isset($_POST['stkPushed'])) {
                                        if ($invoice_id === null) {
                                            echo "";
                                        } else {
                                            //echo "Payment for Invoice ID " . $invoice_id . " is Successfully Initiated";
                                            echo "<div id=pushedData >";
                                            echo "Payment of Kshs." . $amount . " to Phone " . $phone_number . " is Successfully Initiated. Invoice ID " . $invoice_id ;
                                            echo "</div>";
                                    ?>
                                            
                                            <form id="form1" action="" method="POST">
                                                <input type="hidden" id="invoice_id" name="invoice_id" value="<?php  if(isset($invoice_id)){ echo $invoice_id ; } ?>">
                                                <br>
                                                <input type="submit" id="getInvoiceStatus" value="Get Payment Status" hidden>
                                            </form>
                                            
                                            <div id="status"></div>
                                            <br>
                                            <div id="back1" style="display: none;">
                                                <div  id="back" > <a class="btn btn-sm btn-secondary" href="pay1.php?phone_number=<?php echo urlencode($phone_number); ?>&amount=<?php echo urlencode($amount); ?>&mode=<?php echo urlencode('Mpesa Online'); ?>">Record Payment Now?</a></div>
                                            </div>
                                            
                                            <script>
                                                document.getElementById('openingModal').click();
                                            </script>
                                            
                                            <?php    
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php include 'templates/toaster.php'; ?>
                    
                </div>  <!--End of container fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div> 
        
        <script>
            //Loan application handler
            document.addEventListener('DOMContentLoaded', function() {
                
                function openCustomerSelect(){
                    var loanType = document.getElementById('loanTypeX').value;
                    
                    if(loanType === 'New Loan' ){
                        document.querySelector('.searchCustomerNewLoan').removeAttribute('hidden');
                        document.querySelector('.searchCustomerTopup').setAttribute('hidden', 'hidden');
                    } else if (loanType === 'Topup Loan') {
                        document.querySelector('.searchCustomerTopup').removeAttribute('hidden');
                        document.querySelector('.searchCustomerNewLoan').setAttribute('hidden', 'hidden');
                    } else {
                        alert("Invalid Loan type");
                    }
                    
                }
                
                function checkRegistPayment(){
                    
                    var customerPhone = document.getElementById('customer-phone').value;
                    
                    const newData1 = {
                        data: customerPhone,
                            check: "getCustRegistPayment"
                    };
                    
                    // Send an AJAX request to a PHP script to update the data
                    return fetch('templates/checkMemberDetails.php', {
                        method: 'POST',
                        body: JSON.stringify(newData1),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success === true) {
                            var mpesaCode = data.message;
                            
                            document.getElementById('mpesa-code').value = mpesaCode;
                            
                        } else if (data.success === false){
                            document.getElementById('customer-phone').value = ""; 
                            
                            alert(data.message);
                        }
                    });
                }
                
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
                                //document.getElementById('mpesa-code').value = data.message;
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
                                document.getElementById('loanSelectX').value = '';
                            }
                        });

                    } 
                }
                
                function validateCustomer(){
                    var customer = document.getElementById('memberSelect1').value;
                    
                    const newData = {
                            data: customer,
                            check:"memberValid"
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
                            document.getElementById('memberSelect1').value = '';
                        } else {
                            //do nothing
                        }
                    });
                }
                
                function validateCustomer1(){
                    var customer = document.getElementById('memberSelect11').value;
                    
                    const newData = {
                            data: customer,
                            check:"memberValid"
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
                            document.getElementById('memberSelect11').value = '';
                        } else {
                            //do nothing
                        }
                    });
                }
                
                function getProductInfo(){
                    var prod1 = document.getElementById('productSelectLoan').value;
                    var prod2 = document.getElementById('topupLoanType').value;
                    
                    if(prod2 === ""){
                        prod = prod1;
                    } else {
                        prod = prod2;
                    }
                    
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
                            
                            document.getElementById('interest-rate').value = int;
                            document.getElementById('interest-rateH').value = int;
                            document.querySelector('.interest').removeAttribute('hidden');
                            
                            document.getElementById('loan-fee').value = prod_fees;
                            document.getElementById('loan-feeH').value = prod_fees;
                            document.querySelector('.loanFees').removeAttribute('hidden');
                            
                            document.getElementById('max-loan').value = prod_max;
                            document.getElementById('max-period').value = max_term;
                            document.getElementById('no-of-installments').value = max_term;
                            
                            document.getElementById('pymtFrequency').value = repayFrequency;
                            
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
                    var termEntered = document.getElementById('loan-term').value;
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
                    
                    var totalInterest = (parseInt(amountApplied) * (parseInt(intrst)/100) * (parseInt(noOfInstallments) / parseInt(divideBy)));
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
                    return fetch('templates/checkProductDetails.php', {
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
                                document.getElementById('approveLoan').value = 'Approve Loan';
                            }
                                
                            if(data.message === null || data.message === reviewerThis){
                                //alert("You can not review and approve same application.");
                                //document.getElementById('loanSelect').value = ''; 
                                document.querySelector('.repayDate').removeAttribute('hidden');

                            } else {
                                //do nothing
                                document.querySelector('.repayDate').removeAttribute('hidden');
                            }
                        } else {
                            throw new Error(data.message); // Use throw to reject the promise with an error
                            alert(data.message);
                        }
                    });
                }
                
                function getProductDetails(){
                    var selectedProduct = document.getElementById('editThisProduct').value;
                    //document.querySelector('.editThisProduct').removeAttribute('disabled', 'disabled');
                    document.getElementById('editThisProductH').value = selectedProduct;
                    
                    const newData = {
                            product: selectedProduct,
                            check:"productInfoEdit"
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
                            document.getElementById('editThisProduct').value = '';
                        } else {
                            var response_message = data.message;
                            var int = data.int_rate;
                            var prod_max = data.prod_max;
                            var max_term = data.max_term;
                            var prod_fees = data.prod_fees;
                            var repayFrequency = data.repayFrequency;
                            
                            document.getElementById('loan-maxE').value = prod_max;
                            document.getElementById('loan-interestE').value = int;
                            document.getElementById('max-termE').value = max_term;
                            document.getElementById('loan-feesE').value = prod_fees;
                            document.getElementById('repayFrequencyE').value = repayFrequency;
                            
                        }
                    });
                    
                }
                
                document.getElementById('editThisProduct').addEventListener('change', getProductDetails); 
                document.getElementById('loanTypeX').addEventListener('change', openCustomerSelect); 
                document.getElementById('loanSelectX').addEventListener('change', getCustomerInfo);
                document.getElementById('memberSelectLoan').addEventListener('change', getCustomerInfo);
                document.getElementById('productSelectLoan').addEventListener('change', getProductInfo);
                document.getElementById('amountApplied').addEventListener('change', checkAmount);
                document.getElementById('no-of-installments').addEventListener('change', checkLoanTerm);
                document.getElementById('loanForm').addEventListener('change', validateFormNow);
                document.getElementById('memberSelect1').addEventListener('change', validateCustomer); 
                document.getElementById('memberSelect11').addEventListener('change', validateCustomer1); 
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
                table = document.getElementById('applications-table');
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
            function searchTable2() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search2');
                filter = input.value.toUpperCase();
                table = document.getElementById('running-table');
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
                const table = document.getElementById('applications-table');
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

            //Pagination2
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('running-table');
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
                    document.getElementById('page-info2').textContent = `Page ${currentPage} of Page ${totalPages}`;
                }
                
                function updateButtons() {
                    document.getElementById('prev-page2').disabled = currentPage === 1;
                    document.getElementById('next-page2').disabled = currentPage === Math.ceil(rows.length / pageSize);
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
                    const selectedValue = document.getElementById('page-size2').value;

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
    
                document.getElementById('next-page2').addEventListener('click', nextPage);
                document.getElementById('prev-page2').addEventListener('click', prevPage);
                document.getElementById('page-size2').addEventListener('change', changePageSize);
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
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>