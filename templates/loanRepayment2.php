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
                //update loan schedules with the payment made
                $amount_paid = intval($repaymentAmt);
                
                //post payment to schedules
                //get the s_no for the last paid amount
                $snoLastPaid = s_noLastPaid($conn, $loanNo3); //true means there was a last payment
                
                //get s_no for the next payment
                $snoNextPay = s_noNextPay($conn, $loanNo3);
                
                //get principal for the installment
                $principalToPay = principalToPay($conn, $loanNo3);
                $principalToPay1 = encrypt($principalToPay);
                
                //get interest to pay for the installment
                $interestToPay = interestToPay($conn, $loanNo3);
                $interestToPay1 = encrypt($interestToPay);
                
                $surplus = 0; //where there is an amount remaining after posting any payment or installment due
                $settles = false;
                
                if($snoLastPaid){
                    //get the last paid amount
                    $lastPaidAmount = lastPaidAmount($conn, $snoLastPaid);
                
                    //check if remaining amount for last installment is settled by amount paid
                    if($lastPaidAmount == $thisInstallment){
                        //do nothing
                    } else if(($lastPaidAmount < $thisInstallment)){
                        
                        $prevAmountRemaining = $thisInstallment - $lastPaidAmount;
                        
                        $settles = settlesLastDue($amount_paid, $prevAmountRemaining);
                        
                        if($settles){ 
                            $surplus += $settles; //get the surplus
                            
                            //post amount remaining in last installment due
                            $conn->query("UPDATE loan_schedules SET paid='$thisInstallment1', principalPaid='$principalToPay1', interestPaid='$interestToPay1' WHERE s_no='$snoLastPaid'");
                        } else if($settles == false){
                            
                            $amountToPostNow = intval($lastPaidAmount) + intval($amount_paid);
                            $amountToPostNow1 = encrypt($amountToPostNow);
                            
                            $sqlGetLastPaid = $conn->query("SELECT * FROM loan_schedules WHERE s_no = '$snoLastPaid'");
                            $lastPaidRow = $sqlGetLastPaid->fetch_assoc();
                            $principalPaid = intval(decrypt($lastPaidRow['principalPaid']));
                            $interestPaid = intval(decrypt($lastPaidRow['interestPaid']));
                            
                            if($interestPaid == $interestToPay || $interestPaid > $interestToPay){
                                $amountToPrincipal = $amount_paid + $principalPaid;
                                $amountToInterest = $interestPaid;
                            } else {
                                if($amount_paid > $interestToPay){
                                    $amountToPrincipal = $amount_paid - $interestToPay + $principalPaid;
                                    $amountToInterest = $interestToPay;
                                } else {
                                    $amountToPrincipal = 0 + $principalPaid;
                                    $amountToInterest = $amount_paid + $interestPaid;
                                }
                            }
                            
                            $amountToPrincipal1 = encrypt($amountToPrincipal);
                            $amountToInterest1 = encrypt($amountToInterest);
                            
                            $conn->query("UPDATE loan_schedules SET paid='$amountToPostNow1', principalPaid='$amountToPrincipal1', interestPaid='$amountToInterest1' WHERE s_no='$snoLastPaid'");
                        }
                    }
                } else {
                    //its the first payment
                }
                
                if($snoNextPay){
                    $amountToPostNext = ($settles)? $surplus : $amount_paid;
                    $amountToPostNext1 = encrypt($amountToPostNext);
                    
                    //check how many times does the amount settle the installment
                    $timesSettling = installmentSettledTimes($amountToPostNext, $thisInstallment);
                    
                    //check whether there is any amount remaining after settling the installment(s)
                    $aftermathAmount = amountRemainingAfterSettlingInstallment($amountToPostNext, $thisInstallment);
                    
                    //check how many installments remain
                    $noOfRemainingPayments = numberOfRemainingPayments($conn, $loanNo3);
                    
                    if($timesSettling === 1){
                        if($amountToPostNext == $thisInstallment){
                            //post amount in next installment due
                            $conn->query("UPDATE loan_schedules SET paid='$thisInstallment1', principalPaid='$principalToPay1', interestPaid='$interestToPay1' WHERE s_no='$snoNextPay'");
                        } else if($amountToPostNext > $thisInstallment) {
                            //post amount in next installment due
                            $conn->query("UPDATE loan_schedules SET paid='$thisInstallment1', principalPaid='$principalToPay1', interestPaid='$interestToPay1' WHERE s_no='$snoNextPay'");
                            
                            if($aftermathAmount){
                                $aftermathAmount1 = encrypt($aftermathAmount);
                                
                                if($aftermathAmount > $interestToPay){
                                    $amountToPrincipal = $aftermathAmount - $interestToPay;
                                    $amountToInterest = $interestToPay;
                                } else {
                                    $amountToPrincipal = 0;
                                    $amountToInterest = $aftermathAmount;
                                }
                                
                                $amountToPrincipal1 = encrypt($amountToPrincipal);
                                $amountToInterest1 = encrypt($amountToInterest);
                                
                                $conn->query("UPDATE loan_schedules SET paid='$aftermathAmount1', principalPaid='$amountToPrincipal1', interestPaid='$amountToInterest1' WHERE loan_no = '$loanNo3' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                            }
                        } else {
                            if($amountToPostNext > $interestToPay){
                                $amountToPrincipal = $amountToPostNext - $interestToPay;
                                $amountToInterest = $interestToPay;
                            } else {
                                $amountToPrincipal = 0;
                                $amountToInterest = $amountToPostNext;
                            }
                            
                            $amountToPrincipal1 = encrypt($amountToPrincipal);
                            $amountToInterest1 = encrypt($amountToInterest);
                            
                            $conn->query("UPDATE loan_schedules SET paid='$amountToPostNext1', principalPaid='$amountToPrincipal1', interestPaid='$amountToInterest1' WHERE s_no='$snoNextPay'");
                        }
                    } else {
                        
                        $times = intval($timesSettling);
                        
                        //$times = ($settles) ? ($times1 - 1) : $times1;
                        
                        for ($i = 1; $i <= $times; $i++) {
                            $amountToPost1 = encrypt($amountToPostNext);
                            $conn->query("UPDATE loan_schedules SET paid = '$thisInstallment1', principalPaid='$principalToPay1', interestPaid='$interestToPay1' WHERE loan_no = '$loanNo3' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                              
                        }
                        
                        if($aftermathAmount){
                            $aftermathAmount1 = encrypt($aftermathAmount);
                            
                            if($aftermathAmount > $interestToPay){
                                $amountToPrincipal = $aftermathAmount - $interestToPay;
                                $amountToInterest = $interestToPay;
                            } else {
                                $amountToPrincipal = 0;
                                $amountToInterest = $aftermathAmount;
                            }
                            
                            $amountToPrincipal1 = encrypt($amountToPrincipal);
                            $amountToInterest1 = encrypt($amountToInterest);
                            
                            $conn->query("UPDATE loan_schedules SET paid='$aftermathAmount1', principalPaid='$amountToPrincipal1', interestPaid='$amountToInterest1' WHERE loan_no = '$loanNo3' AND paid IS NULL ORDER BY s_no ASC LIMIT 1");
                        }
                    }
                    
                } else {
                    //its the last payment
                }
                
                //finally, if the loan is paid in full
                if($amount_paid == intval($loan_balance31) || $amount_paid > intval($loan_balance31)){
                    $conn->query("UPDATE loan_schedules SET paid='$thisInstallment1', principalPaid='$principalToPay1', interestPaid='$interestToPay1' WHERE loan_no='$loanNo3' ORDER BY s_no DESC");
                }
                
                //update appraisal
                $review = $posting_description1;
                $actionBy = $_SESSION['username'];
                $description = 'Posted Kshs. ' . number_format($repaymentAmt, 2) . ' to customer account.';
                $loan_no = $loanNo3;
                
                addLoanAppraisal($conn, $loan_no, $review, $actionBy, $description);
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