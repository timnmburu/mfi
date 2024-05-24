<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

    function addLoanAppraisal($conn, $loan_no, $action, $actionBy, $description){
        //update appraisal
        $action1 = encrypt($action);
        $actionBy1 = encrypt($actionBy);
        $description1 = encrypt($description);
        $date = encrypt(date("Y-m-d H:i:s"));
        
        $sqlUpdateUppraisal = $conn->prepare("INSERT INTO loan_appraisals(loan_no, action, action_by, description, date) 
        VALUES (?,?,?,?,?)");
        $sqlUpdateUppraisal->bind_param("sssss", $loan_no, $action1, $actionBy1, $description1, $date);
        if($sqlUpdateUppraisal->execute()){
            return true;
        } else {
            return false;
        }
        
    }
    
    function generateLoanScheduleAndInsert($conn, $loan_no, $customerN02, $customerPhone2, $loan_amount2, $term, $loan_interest2, $loan_installment2, $firstRepaymentDate2, $repaymentFrequency2) {
        $repaymentFrequency21 = decrypt($repaymentFrequency2);
        $firstRepaymentDate21 = decrypt($firstRepaymentDate2);
        
        if ($repaymentFrequency21 === 'Daily') {
            $adding = '1 Day';
            $divideBy = 28;
        } elseif ($repaymentFrequency21 === 'Weekly') {
            $adding = '1 Week';
            $divideBy = 4;
        } elseif ($repaymentFrequency21 === 'Monthly') {
            $adding = '1 Month';
            $divideBy = 1;
        }
        
        //calculate interest
        //$interest1 = ceil(intval(decrypt($loan_amount2)) * intval(decrypt($loan_interest2)) / 100); //for monthly interest
        $interest2 = ceil((intval(decrypt($loan_amount2)) * intval(decrypt($loan_interest2)) / 100) * (intval($term) / intval($divideBy))); //for weekly and daily interest
        
        $interest = ($repaymentFrequency21 === 'Monthly')? $interest2 / $term  : $interest2 / $term ;
        $principal = ceil(intval(decrypt($loan_amount2)) / $term);
        
        //check if schedules have been populated for the full term
        $sqlCheckSchedules = $conn->query("SELECT COUNT(loan_no) AS total_count FROM loan_schedules WHERE loan_no = '$loan_no'");
        
        if($sqlCheckSchedules->num_rows > 0) {
            $countRow = $sqlCheckSchedules->fetch_assoc();
            $count = intval($countRow['total_count']);
        } else {
            $count = 0;
        }
        
        if(($count < intval($term) + 1)){
            $otherTerms = intval($term) - 1;
            
            $principal1 = encrypt($principal);
            $interest1 = encrypt($interest);
            
            $loan_installment = intval($interest) + intval($principal);
            $loan_installment2 = encrypt($loan_installment);
            
            // Insert data into the database table using prepared statement
            $stmt = $conn->prepare("INSERT INTO loan_schedules (loan_no, customer_no, customer_phone, loan_amount, principal, interest, loan_installment, due_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $loan_no, $customerN02, $customerPhone2, $loan_amount2, $principal1, $interest1, $loan_installment2, $firstRepaymentDate2);
            $stmt->execute();
        
            for ($i = 1; $i <= $otherTerms; $i++) {
        
                $firstDate = date('Y-m-d', strtotime($firstRepaymentDate21));
                $nextPaymentDate1 = date('Y-m-d', strtotime($firstDate . " + $adding"));
                $nextPaymentDate12 = encrypt($nextPaymentDate1);
                
                // Insert data into the database table using prepared statement
                $stmt = $conn->prepare("INSERT INTO loan_schedules (loan_no, customer_no, customer_phone, loan_amount, principal, interest, loan_installment, due_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $loan_no, $customerN02, $customerPhone2, $loan_amount2, $principal1, $interest1, $loan_installment2, $nextPaymentDate12);
                $stmt->execute();
        
                // Update first repayment date for the next iteration
                $firstRepaymentDate21 = $nextPaymentDate1;
            }
        }
        
        return true;
    }

    function getBalances($conn, $item){
        //get sum of credits
        $sqlGetSumCredit = $conn->query("SELECT credit FROM loan_transactions");
        
        if($sqlGetSumCredit->num_rows > 0){
            $sumCredit = 0;
            $count_credit = 0;
            while($sqlCreditRows = $sqlGetSumCredit->fetch_assoc()){
                $credits = intval(decrypt($sqlCreditRows['credit']));
                $sumCredit += $credits;
            }

        } else {
            $sumCredit = 0;
        }
        
        //get count of credits
        $sqlGetSumCredit1 = $conn->query("SELECT COUNT(credit) AS count_credit FROM loan_transactions");
        
        if($sqlGetSumCredit1->num_rows > 0){
            $count_credit = 0;
            while($sqlCreditRows1 = $sqlGetSumCredit1->fetch_assoc()){
                $count_credit = intval($sqlCreditRows1['count_credit']);
            }

        } else {
            $count_credit = 0;
        }
        
        //get sum of debits
        $sqlGetSumDebit = $conn->query("SELECT gross_loan FROM loans");
        
        if($sqlGetSumDebit->num_rows > 0){
            $sumDebit = 0;
            while($sqlDebitRows = $sqlGetSumDebit->fetch_assoc()){
                $debits = intval(decrypt($sqlDebitRows['gross_loan']));
                $sumDebit += $debits;
            }
        } else {
            $sumDebit = 0;
        }
        
        //get COUNT of debits
        $sqlGetSumDebit1 = $conn->query("SELECT COUNT(loan_no) AS count_debit FROM loans");
        
        if($sqlGetSumDebit->num_rows > 0){
            $count_debit = 0;
            while($sqlDebitRows1 = $sqlGetSumDebit1->fetch_assoc()){
                $count_debit = intval($sqlDebitRows1['count_debit']);
            }
        } else {
            $count_debit = 0;
        }
        
        //loan book balance
        $open21 = encrypt("Open");
        $sqlGetSumDebit11 = $conn->query("SELECT loan_balance FROM loans WHERE loan_status = '$open21'");
        
        if($sqlGetSumDebit11->num_rows > 0){
            $balance = 0;
            while($sqlDebitRows11 = $sqlGetSumDebit11->fetch_assoc()){
                $debits11 = intval(decrypt($sqlDebitRows11['loan_balance']));
                $balance += $debits11;
            }
        } else {
            $balance = 0;
        }
        
        //get count of outstanding loans
        $open = encrypt("Open");
        $sqlGetSumOLB = $conn->query("SELECT COUNT(loan_status) AS count_OLB FROM loans WHERE loan_status='$open'");
        
        if($sqlGetSumOLB->num_rows > 0){
            $countOLB = 0;
            while($sqlOLBRows = $sqlGetSumOLB->fetch_assoc()){
                $count_OLB = intval($sqlOLBRows['count_OLB']);
                $countOLB += $count_OLB;
            }
        } else {
            $countOLB = 0;
        }
        
        //count all customers
        $sqlCountAllMembers = "SELECT COUNT(customer_no) AS customer_count FROM customers ";
        $resultCountAllMembers = $conn->query($sqlCountAllMembers);
        if($resultCountAllMembers->num_rows > 0){
            $rowCountAllMembers = $resultCountAllMembers->fetch_assoc();
            $countAllMembers = $rowCountAllMembers['customer_count'];
        } else {
            $countAllMembers = 0;
        }
        
        $sqlJoinDates = "SELECT customer_no, joinDate FROM customers";
        $resultJoinDates = $conn->query($sqlJoinDates);
        
        if ($resultJoinDates->num_rows > 0) {
            $countNewMembers = 0;
            while ($row = $resultJoinDates->fetch_assoc()) {
                $encryptedJoinDate = $row['joinDate'];
        
                // Decrypt the joinDate
                $decryptedJoinDate = decrypt($encryptedJoinDate);
        
                // Check if the decrypted date is within the current month
                $firstDayOfMonth = date('Y-m-01');
                $lastDayOfMonth = date('Y-m-t');
                
                if ($decryptedJoinDate >= $firstDayOfMonth && $decryptedJoinDate <= $lastDayOfMonth) {
                    $countNewMembers++;
                }
            }
        } else {
            $countNewMembers = 0;
        }
        
        $activeMember = encrypt('active');
        $sqlCountCurrentMembers = "SELECT COUNT(customer_no) AS active_customers FROM customers WHERE status = '$activeMember' ";
        $resultCountCurrentMembers = $conn->query($sqlCountCurrentMembers);
        if($resultCountCurrentMembers->num_rows > 0){
            $rowCountCurrentMembers = $resultCountCurrentMembers->fetch_assoc();
            $currentMembers = $rowCountCurrentMembers['active_customers'];
        } else {
            $currentMembers = 0;
        }
                                                  
        
        if($item === "cumm_loans"){
            $return = $sumDebit;
        } else if($item === "cumm_repayments"){
            $return = $sumCredit;
        } else if($item === "loan_bal"){
            $return = $balance;
        } else if($item === "count_loans"){
            $return = $count_debit;
        } else if($item === "count_repayments"){
            $return = $count_credit;
        } else if($item === "count_bal"){
            $return = $countOLB;
        } else if($item === "customer_no"){
            $return = $countAllMembers; 
        } else if($item === "new_customers"){
            $return = $countNewMembers; 
        } else if($item === "active_customers"){
            $return = $currentMembers; 
        }
        
        return $return;
        
    }
    
    function getBalancesIndividual($conn, $item, $userphone){
        $userphone1 = encrypt($userphone);
        
        // Initialize an empty array to store the loan numbers in the portfolio
        $loanList = [];
        
        // Fetch the loan numbers in the portfolio
        $sqlGetLoanList = $conn->query("SELECT * FROM loans WHERE staff_phone='$userphone1'");
        if ($sqlGetLoanList->num_rows > 0) {
            while ($loanListRow = $sqlGetLoanList->fetch_assoc()) {
                $loanList[] = $loanListRow['loan_no'];
            }
        
            // Initialize variables for sum of credits, count of credits, sum of debits, count of debits, balance, and count of outstanding loans
            $sumCredit = 0;
            $countCredit = 0;
            $sumDebit = 0;
            $countDebit = 0;
            $balance = 0;
            $countOLB = 0;
        
            // Calculate sum of credits
            $sqlGetSumCredit = $conn->query("SELECT SUM(credit) AS sum_credit FROM loan_transactions WHERE loan_no IN (" . implode(",", $loanList) . ")");
            $sumCreditRow = $sqlGetSumCredit->fetch_assoc();
            $sumCredit = intval($sumCreditRow['sum_credit']);
        
            // Calculate count of credits
            $sqlGetCountCredit = $conn->query("SELECT COUNT(credit) AS count_credit FROM loan_transactions WHERE loan_no IN (" . implode(",", $loanList) . ")");
            $countCreditRow = $sqlGetCountCredit->fetch_assoc();
            $countCredit = intval($countCreditRow['count_credit']);
        
            // Calculate sum of debits
            $sqlGetSumDebit = $conn->query("SELECT SUM(gross_loan) AS sum_debit FROM loans WHERE loan_no IN (" . implode(",", $loanList) . ")");
            $sumDebitRow = $sqlGetSumDebit->fetch_assoc();
            $sumDebit = intval($sumDebitRow['sum_debit']);
        
            // Calculate count of debits
            $sqlGetCountDebit = $conn->query("SELECT COUNT(loan_no) AS count_debit FROM loans WHERE loan_no IN (" . implode(",", $loanList) . ")");
            $countDebitRow = $sqlGetCountDebit->fetch_assoc();
            $countDebit = intval($countDebitRow['count_debit']);
        
            // Calculate loan book balance
            $sqlGetBalance = $conn->query("SELECT SUM(loan_balance) AS balance FROM loans WHERE loan_no IN (" . implode(",", $loanList) . ")");
            $balanceRow = $sqlGetBalance->fetch_assoc();
            $balance = intval($balanceRow['balance']);
        
            // Calculate count of outstanding loans
            $sqlGetCountOLB = $conn->query("SELECT COUNT(loan_status) AS count_OLB FROM loans WHERE loan_status='Open' AND loan_no IN (" . implode(",", $loanList) . ")");
            $countOLBRow = $sqlGetCountOLB->fetch_assoc();
            $countOLB = intval($countOLBRow['count_OLB']);
                    
            //count all customers
            $sqlCountAllMembers = "SELECT COUNT(customer_no) AS customer_count FROM customers WHERE staff_phone='$userphone1'";
            $resultCountAllMembers = $conn->query($sqlCountAllMembers);
            if($resultCountAllMembers->num_rows > 0){
                $rowCountAllMembers = $resultCountAllMembers->fetch_assoc();
                $countAllMembers = $rowCountAllMembers['customer_count'];
            } else {
                $countAllMembers = 0;
            }
            
            $sqlJoinDates = "SELECT customer_no, joinDate FROM customers WHERE staff_phone='$userphone1'";
            $resultJoinDates = $conn->query($sqlJoinDates);
            
            if ($resultJoinDates->num_rows > 0) {
                $countNewMembers = 0;
                while ($row = $resultJoinDates->fetch_assoc()) {
                    $encryptedJoinDate = $row['joinDate'];
            
                    // Decrypt the joinDate
                    $decryptedJoinDate = decrypt($encryptedJoinDate);
            
                    // Check if the decrypted date is within the current month
                    $firstDayOfMonth = date('Y-m-01');
                    $lastDayOfMonth = date('Y-m-t');
                    
                    if ($decryptedJoinDate >= $firstDayOfMonth && $decryptedJoinDate <= $lastDayOfMonth) {
                        $countNewMembers++;
                    }
                }
            } else {
                $countNewMembers = 0;
            }
            
            $activeMember = encrypt('active');
            $sqlCountCurrentMembers = "SELECT COUNT(customer_no) AS active_customers FROM customers WHERE status = '$activeMember' AND staff_phone='$userphone1'";
            $resultCountCurrentMembers = $conn->query($sqlCountCurrentMembers);
            if($resultCountCurrentMembers->num_rows > 0){
                $rowCountCurrentMembers = $resultCountCurrentMembers->fetch_assoc();
                $currentMembers = $rowCountCurrentMembers['active_customers'];
            } else {
                $currentMembers = 0;
            }
                                                      
            
            if($item === "cumm_loans"){
                $return = $sumDebit;
            } else if($item === "cumm_repayments"){
                $return = $sumCredit;
            } else if($item === "loan_bal"){
                $return = $balance;
            } else if($item === "count_loans"){
                $return = $count_debit;
            } else if($item === "count_repayments"){
                $return = $count_credit;
            } else if($item === "count_bal"){
                $return = $countOLB;
            } else if($item === "customer_no"){
                $return = $countAllMembers; 
            } else if($item === "new_customers"){
                $return = $countNewMembers; 
            } else if($item === "active_customers"){
                $return = $currentMembers; 
            }
            
        } else {
            if($item === "cumm_loans"){
                $return = '0';
            } else if($item === "cumm_repayments"){
                $return = '0';
            } else if($item === "loan_bal"){
                $return = '0';
            } else if($item === "count_loans"){
                $return = '0';
            } else if($item === "count_repayments"){
                $return = '0';
            } else if($item === "count_bal"){
                $return = '0';
            } else if($item === "customer_no"){
                $return = '0';
            } else if($item === "new_customers"){
                $return = '0'; 
            } else if($item === "active_customers"){
                $return = '0'; 
            }
        }
        
        return $return;
        
    }
    
    function accountClosure($conn, $loanNo){
        //get balances
        $sqlGetBal = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo' ");
        $sqlGetBalResult = $sqlGetBal->fetch_assoc();
        $grossLoan = intval(decrypt($sqlGetBalResult['gross_loan']));
        $totalPaid = intval(decrypt($sqlGetBalResult['loan_payments']));
        $installments = intval(decrypt($sqlGetBalResult['loan_installment']));
        
        $bal = $grossLoan - $totalPaid;
        
        //post the balance into loans table
        
        
        
    }
    
    
    function checkSessionToken($conn, $session_token, $table){
        $formSent = $conn->query("SELECT * from $table WHERE session_token = '$session_token'");
        if($formSent->num_rows > 0){
            return true;
        } else {
            return false;
        }
    }

    function getCustomerLoanBalance($conn, $loan_no){
        $loanBalance = $conn->query("SELECT * from loans WHERE loan_no = '$loan_no'");
        if($loanBalance->num_rows > 0){
            $loanBalRow = $loanBalance->fetch_assoc();
            $loanBal = intval(decrypt($loanBalRow['loan_balance']));
            return $loanBal;
        } else {
            return false;
        }
    }











?>