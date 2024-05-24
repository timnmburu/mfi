<?php

    function addSubLedger($conn, $parent_ledgerSno, $sub_account_name, $sub_account_bal0){
        /*
        $date = date("Y-m-d H:i:s");
        
        $parent_ledgerSnoPref = intval(substr($parent_ledgerSno, 0, 3));
        $parent_ledgerSnoSuff = intval(substr($parent_ledgerSno, -3));
        
        //get main ledger balance
        $sqlMianLedgerBal = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = '$parent_ledgerSno'");
        $sqlMianLedgerBalRow = $sqlMianLedgerBal->fetch_assoc();
        $mainLedgerBal = intval(decrypt($sqlMianLedgerBalRow['balance']));
        $type = decrypt($sqlMianLedgerBalRow['type']);
        
        $sub_account_bal = ($type == "Liability" || $type == "Expense") ? -$sub_account_bal0 : $sub_account_bal0;
        
        $sub_account_name1 = encrypt($sub_account_name);
        $sub_account_bal1 = encrypt($sub_account_bal);
        $date1 = encrypt($date);
        $status = encrypt("active");
        
        //new sub ledger no
        $checkSubLedgerNo = $conn->query("SELECT ledger_no FROM chart_of_subaccounts WHERE LEFT(ledger_no, 3) = '$parent_ledgerSnoPref' ORDER BY s_no DESC LIMIT 1 ");
        if($checkSubLedgerNo->num_rows > 0){
            $lastLedgerNo = $checkSubLedgerNo -> fetch_Assoc()['ledger_no'];
            $lastLedgerNoSuff = intval(substr($lastLedgerNo, -3));
            
            $newLedgerNoSuff = $lastLedgerNoSuff + 1;
            
            $newLedgerNo = $parent_ledgerSnoPref . $newLedgerNoSuff;

        } else {

            $newLedgerNoSuff = $parent_ledgerSnoSuff + 1;
            
            $newLedgerNo = $parent_ledgerSnoPref . $newLedgerNoSuff;
            // $newLedgerNo = $newLedgerNoSuff;

        }
        
        $sqlCreateLedger = $conn->query("INSERT INTO chart_of_subaccounts (ledger_no, name, balance, status, action_date) 
            VALUES ('$newLedgerNo', '$sub_account_name1', '$sub_account_bal1', '$status', '$date1')");
        
        if($sqlCreateLedger){
            //add the balance to the main ledger
            //update the main ledger bal
            $newMainLedgerBal = $mainLedgerBal + $sub_account_bal;
            $newMainLedgerBal1 = encrypt($newMainLedgerBal);
            $conn->query("UPDATE chart_of_accounts SET balance = '$newMainLedgerBal1' WHERE ledger_no = '$parent_ledgerSno'");
            
        }
        */
    }
    
    function removeSubLedger($conn,$sub_account_name){
        /*
        $date = date("Y-m-d H:i:s");
        $date1 = encrypt($date);
        $removeLedger1 = encrypt($sub_account_name);
        $status = encrypt('Deactivate');
        $currentState = encrypt('active');
        $balancesheet = encrypt("Balance Sheet");
        
        //check whether ledger has 0 balance to allow
        $sqlLedgerBal = $conn->query("SELECT balance FROM chart_of_subaccounts WHERE name = '$removeLedger1' AND status = '$currentState' AND category = '$balancesheet' ");
        $ledgerBalRow = $sqlLedgerBal->fetch_assoc();
        $ledgerBal = intval(decrypt($ledgerBalRow['balance']));
        
        if($ledgerBal < 0 || $ledgerBal == 0){
            //set ledger to inactive state
            $conn->query("UPDATE chart_of_subaccounts SET status='$status', action_date='$date1' WHERE name = '$removeLedger1' AND status = '$currentState' ");
            
            return true;
        } else {
            return false;
        }
        */
    }

    function addSubLedgerBalanceCredit($conn, $parent_ledgerSno, $subledger_amount0){
        
        // $parent_ledgerSno = substr($sub_ledgerSno, 0, 3) . '100';
        
        $date = date("Y-m-d H:i:s");
        
        // //get sub ledger balance
        // $sqlSubLedgerBal = $conn->query("SELECT * FROM chart_of_subaccounts WHERE ledger_no = '$sub_ledgerSno'");
        // $sqlSubLedgerBalRow = $sqlSubLedgerBal->fetch_assoc();
        // $subLedgerBal = intval(decrypt($sqlSubLedgerBalRow['balance']));
        
        //get main ledger balance
        $sqlMianLedgerBal = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = '$parent_ledgerSno'");
        $sqlMianLedgerBalRow = $sqlMianLedgerBal->fetch_assoc();
        $mainLedgerBal = intval(decrypt($sqlMianLedgerBalRow['balance']));
        $type = decrypt($sqlMianLedgerBalRow['type']);
        
        $subledger_amount = ($type == "Asset" || $type == "Revenue") ? $subledger_amount0 : -$subledger_amount0;
        
        // //update subledger balance
        // $newSubLedgerBal = $subLedgerBal + $subledger_amount;
        // $newSubLedgerBal1 = encrypt($newSubLedgerBal);
        // //$conn->query("UPDATE chart_of_subaccounts SET balance = '$newSubLedgerBal1' WHERE ledger_no = '$sub_ledgerSno'");
        
        //update the main ledger bal
        $newMainLedgerBal = $mainLedgerBal + $subledger_amount;
        $newMainLedgerBal1 = encrypt($newMainLedgerBal);
        $conn->query("UPDATE chart_of_accounts SET balance = '$newMainLedgerBal1' WHERE ledger_no = '$parent_ledgerSno'");

    }

    function addSubLedgerBalanceDebit($conn, $parent_ledgerSno, $subledger_amount0){
        
        // $parent_ledgerSno = substr($sub_ledgerSno, 0, 3) . '100';
        
        $date = date("Y-m-d H:i:s");
        
        // //get sub ledger balance
        // $sqlSubLedgerBal = $conn->query("SELECT * FROM chart_of_subaccounts WHERE ledger_no = '$sub_ledgerSno'");
        // $sqlSubLedgerBalRow = $sqlSubLedgerBal->fetch_assoc();
        // $subLedgerBal = intval(decrypt($sqlSubLedgerBalRow['balance']));
        
        //get main ledger balance
        $sqlMianLedgerBal = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = '$parent_ledgerSno'");
        $sqlMianLedgerBalRow = $sqlMianLedgerBal->fetch_assoc();
        $mainLedgerBal = intval(decrypt($sqlMianLedgerBalRow['balance']));
        $type = decrypt($sqlMianLedgerBalRow['type']);
        
        $subledger_amount = ($type == "Asset" || $type == "Revenue") ? $subledger_amount0 : -$subledger_amount0;
        
        // //update subledger balance
        // $newSubLedgerBal = $subLedgerBal - $subledger_amount;
        // $newSubLedgerBal1 = encrypt($newSubLedgerBal);
        // //$conn->query("UPDATE chart_of_subaccounts SET balance = '$newSubLedgerBal1' WHERE ledger_no = '$sub_ledgerSno'");
        
        //update the main ledger bal
        $newMainLedgerBal = $mainLedgerBal - $subledger_amount;
        $newMainLedgerBal1 = encrypt($newMainLedgerBal);
        $conn->query("UPDATE chart_of_accounts SET balance = '$newMainLedgerBal1' WHERE ledger_no = '$parent_ledgerSno'");

    }


    function addSubLedgerBalanceCreditClosure($conn, $parent_ledgerSno, $subledger_amount0){
        
        // $parent_ledgerSno = substr($sub_ledgerSno, 0, 3) . '100';
        
        $date = date("Y-m-d H:i:s");
        
        // //get sub ledger balance
        // $sqlSubLedgerBal = $conn->query("SELECT * FROM chart_of_subaccounts WHERE ledger_no = '$sub_ledgerSno'");
        // $sqlSubLedgerBalRow = $sqlSubLedgerBal->fetch_assoc();
        // $subLedgerBal = intval(decrypt($sqlSubLedgerBalRow['balance']));
        
        //get main ledger balance
        $sqlMianLedgerBal = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = '$parent_ledgerSno'");
        $sqlMianLedgerBalRow = $sqlMianLedgerBal->fetch_assoc();
        $mainLedgerBal = intval(decrypt($sqlMianLedgerBalRow['balance']));
        $type = decrypt($sqlMianLedgerBalRow['type']);
        
        $subledger_amount = ($type == "Asset" || $type == "Revenue") ? $subledger_amount0 : $subledger_amount0;
        
        // //update subledger balance
        // $newSubLedgerBal = $subLedgerBal + $subledger_amount;
        // $newSubLedgerBal1 = encrypt($newSubLedgerBal);
        // //$conn->query("UPDATE chart_of_subaccounts SET balance = '$newSubLedgerBal1' WHERE ledger_no = '$sub_ledgerSno'");
        
        //update the main ledger bal
        $newMainLedgerBal = $mainLedgerBal + $subledger_amount0;
        $newMainLedgerBal1 = encrypt($newMainLedgerBal);
        $conn->query("UPDATE chart_of_accounts SET balance = '$newMainLedgerBal1' WHERE ledger_no = '$parent_ledgerSno'");

    }


















?>