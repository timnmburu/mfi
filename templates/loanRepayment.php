<?php

    function repayLoan($conn, $loanNo3, $repaymentAmt, $payMode1, $posting_date1, $posting_description1, $descriptionNo1, $transactionBy1, $session_token){
        $repaymentAmt1 = encrypt($repaymentAmt);
        $payMode = encrypt($payMode1);
        $posting_date = encrypt($posting_date1);
        $posting_description = encrypt($posting_description1);
        $descriptionNo = encrypt($descriptionNo1);
        $transactionBy = encrypt($transactionBy1);
        
        //Insert into loan_transactions table
        $sqlGetLastLoanDetails = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo3' ");
        $sqlGetLastLoanDetailsResult = $sqlGetLastLoanDetails->fetch_assoc();
        $customer_no = $sqlGetLastLoanDetailsResult['customer_no'];
        $loan_payments = decrypt($sqlGetLastLoanDetailsResult['loan_payments']);
        $productName31 = decrypt($sqlGetLastLoanDetailsResult['loan_product']);
        $loan_balance31 = decrypt($sqlGetLastLoanDetailsResult['loan_balance']);
        $grossLoan31 = decrypt($sqlGetLastLoanDetailsResult['gross_loan']);
        $thisInstallment = intval(decrypt($sqlGetLastLoanDetailsResult['loan_installment']));
        $thisInstallment1 = encrypt($thisInstallment);
        
        $runningBal = intval($loan_balance31) - intval($repaymentAmt);
        
        $runningBal1 = encrypt($runningBal);
        
        $sqlUpdateLoanTransaction = $conn->query("INSERT INTO loan_transactions (loan_no, customer_no, posting_date, posting_description, description_no, credit, payment_mode, transaction_by, running_balance, session_token) 
        VALUES ('$loanNo3', '$customer_no', '$posting_date', '$posting_description', '$descriptionNo', '$repaymentAmt1', '$payMode', '$transactionBy', '$runningBal1', $session_token) ");
        
        if($sqlUpdateLoanTransaction){
            //Update loans table
            $newLoanBalance3 = $runningBal;
            $newLoanBalance311 = encrypt($newLoanBalance3);
            
            $newCummRepaid1 = intval($loan_payments) + intval($repaymentAmt);
            $newCummRepaid11 = encrypt($newCummRepaid1);
                
            if(intval($loan_balance31) === intval($repaymentAmt) || intval($repaymentAmt) > intval($loan_balance31)){
                $loanStatus = encrypt('Closed');
                $loan_classification = encrypt("Normal");
                $days_inArrears = encrypt("0");
                $amount_inArrears = encrypt("0");
                
                //update arrears amount and days
                $conn->query("UPDATE loans SET loan_classification='$loan_classification', days_inArrears='$days_inArrears', amount_inArrears='$amount_inArrears'  WHERE loan_no='$loanNo3'");
            } else {
                $loanStatus = encrypt('Open');
            }
            
            $sqlUpdateLoansTable5 = $conn->query("UPDATE loans SET loan_balance='$newLoanBalance311', loan_payments='$newCummRepaid11', last_paymentDate='$posting_date', loan_status='$loanStatus'  WHERE loan_no='$loanNo3'");
            
            if($sqlUpdateLoansTable5){
                //update appraisal
                $review = $posting_description1;
                $actionBy = $_SESSION['username'];
                $description = 'Posted Kshs. ' . number_format($repaymentAmt, 2) . ' to customer account.';
                $loan_no = $loanNo3;
                
                addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
                
                //add loan amount to the bank ledger
                $sub_ledgerSno1 = '100101';
                addSubLedgerBalanceCredit($conn, $sub_ledgerSno1, $repaymentAmt);
                
            }
        } else {
            header("Location: /loan/open/?lno=$loan_no");
        }
        
        header("Location: /loan/open/?lno=$loan_no");
    }
    
    function numberOfRemainingPayments($conn, $loanNo3){
        //get number of remaining payments
        $sqlRemaining  = $conn->query("SELECT COUNT(paid) AS remaining FROM loan_schedules WHERE loan_no = '$loanNo3' AND paid IS NULL");
        $remainingRow = $sqlRemaining->fetch_assoc();
        $noOfRemainingPayments = intval($remainingRow['remaining']);
        
        return $noOfRemainingPayments;
    }
    
    function amountRemainingAfterSettlingInstallment($amountToPostNext, $thisInstallment){
        $aftermath = intval(($amountToPostNext % $thisInstallment));
        
        if($aftermath > 0){
            return $aftermath;
        } else {
            return false;
        }
    }
    
    function installmentSettledTimes($amountToPostNext, $thisInstallment ){
        if($amountToPostNext < $thisInstallment || $amountToPostNext == $thisInstallment){
            $times = 1;
        } else {
            $times = floor(($amountToPostNext / $thisInstallment));
        }
        
        return intval($times);
    }
    
    function settlesLastDue($repaymentAmt, $prevAmountRemaining){
        if($repaymentAmt === $prevAmountRemaining || $repaymentAmt > $prevAmountRemaining){
            $surplus = intval($repaymentAmt) - intval($prevAmountRemaining);
            return $surplus;
        } else if($repaymentAmt < $prevAmountRemaining){
            return false;
        }
    }
    
    function lastPaidAmount($conn, $snoLastPaid){
        $sqlGetLastPaid = $conn->query("SELECT * FROM loan_schedules WHERE s_no = '$snoLastPaid' ");
        if($sqlGetLastPaid->num_rows > 0){
            $lastPaidRow = $sqlGetLastPaid->fetch_assoc();
            $lastPaid = intval(decrypt($lastPaidRow['paid']));
            
            return $lastPaid;
        }
    }
    
    function principalToPay($conn, $loanNo3){
        $sqlGetLastPaidSno = $conn->query("SELECT * FROM loan_schedules WHERE loan_no = '$loanNo3' ORDER BY s_no ASC LIMIT 1");
        if($sqlGetLastPaidSno->num_rows > 0){
            $principal = $sqlGetLastPaidSno->fetch_assoc()['principal'];
            $principal1 = intval(decrypt($principal));
            
            return $principal1;
        }
    }
    
    function interestToPay($conn, $loanNo3){
        $sqlGetLastPaidSno = $conn->query("SELECT * FROM loan_schedules WHERE loan_no = '$loanNo3' ORDER BY s_no ASC LIMIT 1");
        if($sqlGetLastPaidSno->num_rows > 0){
            $interest = $sqlGetLastPaidSno->fetch_assoc()['interest'];
            $interest1 = intval(decrypt($interest));
            
            return $interest1;
        }
    }
    
    function s_noNextPay($conn, $loanNo3){
        $sqlGetLastPaidSno = $conn->query("SELECT * FROM loan_schedules WHERE loan_no = '$loanNo3' ORDER BY s_no DESC");
        
        if($sqlGetLastPaidSno->num_rows > 0){
            
            while($snoRow1 = $sqlGetLastPaidSno->fetch_assoc()){
                $sno = $snoRow1['s_no'];
                $snoRowInstallment = intval(decrypt($snoRow1['loan_installment']));
                $snoRowPaidAmount = intval(decrypt($snoRow1['paid']));
                
                if($snoRowPaidAmount != 0 && ($snoRowInstallment == $snoRowPaidAmount || $snoRowInstallment < $snoRowPaidAmount) ){
                    return $sno + 1;
                } else {
                    return $sno;
                }
            }
        } else {
            return false;
        }
    }
    
    function s_noLastPaid($conn, $loanNo3){
        $sqlGetLastPaidSno = $conn->query("SELECT * FROM loan_schedules WHERE loan_no = '$loanNo3' AND paid IS NOT NULL ORDER BY s_no DESC LIMIT 1");
        if($sqlGetLastPaidSno->num_rows > 0){
            $snoRow1 = $sqlGetLastPaidSno->fetch_assoc();
                $sno = $snoRow1['s_no'];
                return $sno;
        } else {
            return false;
        }
    }




?>