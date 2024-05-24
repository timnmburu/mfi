<?php
    $ok = 1;
    
    if($ok == 0){
        echo '<h1>Page under construction!</h1>';
        exit;
    }

    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/pay-process2.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/counter.php';
    require_once __DIR__.'/templates/checkMembersBalances.php';
    require_once __DIR__.'/templates/notifications.php';
    require_once __DIR__.'/templates/sendsms.php';
    require_once __DIR__.'/templates/upload_docs.php';
    require_once __DIR__.'/templates/loanActions.php';
    require_once __DIR__.'/templates/ledgerActions.php';
    require_once __DIR__.'/templates/logger.php';
    require_once __DIR__.'/api/jobs/run_schedules_update/run_schedules_update.php';


    use Dotenv\Dotenv;

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
    $admin = $_SESSION['admin'];
    
    if($admin != 2){
        header('Location: /');
    }
    $member_no = $_SESSION['member_no'];
    $userphone = $_SESSION['userphone'];
    if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
        $access = false;
        header("Location: /loans");
    } else {
        $access = true;
    }
    
    $loansAdancedLedger = '101100';
    $interestIncomeLedger = '500100';
    $retainedEarningsLedger = '105100';
    
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if(isset($_POST['createMainLedgerAccount'])){
        $account_destination = $_POST['account_destination'];
        $account_type0 = $_POST['account_type'];
        $account_type11 = explode(" - ", $account_type0);
        $account_type = $account_type11[0];
        $account_name = $_POST['account_name'];
        $account_bal0 = $_POST['account_bal'];
        $date = date("Y-m-d H:i:s");
        
        $account_bal = ($account_type == "Asset" || $account_type == "Income") ? $account_bal0 : -$account_bal0;
        
        $account_destination1 = encrypt($account_destination);
        $account_type1 = encrypt($account_type);
        $account_name1 = encrypt($account_name);
        $account_bal1 = encrypt($account_bal);
        $date1 = encrypt($date);
        $status = encrypt("active");
        
        //check the last ledger no.
        $sortingPref = ($account_destination == "Balance Sheet") ? '< 500' : '> 499';
        $sqlCheckLastLedgerNo = $conn->query("SELECT ledger_no FROM chart_of_accounts WHERE LEFT(ledger_no, 3) $sortingPref ORDER BY s_no DESC LIMIT 1");
        if($sqlCheckLastLedgerNo->num_rows > 0) {
            $lastLedger_no = $sqlCheckLastLedgerNo->fetch_assoc()['ledger_no'];
            $prevLedgerPref = intval(substr($lastLedger_no, 0, 3));
            $ledgerSuff = '100';
            
            $nextLedgerPref = $prevLedgerPref + 1;
            
            $nextLedgerNo = $nextLedgerPref . $ledgerSuff ;
        } else {
            if($account_destination == "Balance Sheet"){
                $nextLedgerNo = "100100";
            } else {
                $nextLedgerNo = "500100";
            }
        }
        
        $sqlCreateLedger = $conn->query("INSERT INTO chart_of_accounts (ledger_no, category, type, name, balance, status, action_date) 
            VALUES ('$nextLedgerNo', '$account_destination1', '$account_type1', '$account_name1', '$account_bal1', '$status', '$date1')");
        
        if($sqlCreateLedger){
            header("Location: /accounting");
        }
    }
    
    if(isset($_POST['removeMainLedgerAccount'])){
        $removeLedger = $_POST['removeLedger_account'];
        $removeLedger1 = explode(" - ", $removeLedger);
        $s_no = $removeLedger1[0];
        if(!is_numeric($s_no)){
            header("Location: /accounting");
        } else {
            
            $removal_reason = $_POST['removal_reason'];
            $date = date("Y-m-d H:i:s");
            
            $removeLedger1 = encrypt($removeLedger);
            $date1 = encrypt($date);
            $status = encrypt($removal_reason);
            
            $sqlCreateLedger = $conn->query("UPDATE chart_of_accounts SET status='$status', action_date='$date1' WHERE ledger_no = $s_no ");
            
            if($sqlCreateLedger){
                
                $conn->query("UPDATE chart_of_subaccounts SET status='$status', action_date='$date1' WHERE ledger_no = $s_no ");
                
                header("Location: /accounting");
            }
        }
    }
    
    if(isset($_POST['createSubLedgerAccount'])){
        $parent_ledger0 = $_POST['parent_ledger'];
        $parent_ledger1 = explode(" - ", $parent_ledger0);
        $parent_ledgerSno = $parent_ledger1[0];
        $parent_ledgerName = $parent_ledger1[1];
        $sub_account_name = $_POST['sub_account_name'];
        $sub_account_bal0 = $_POST['sub_account_bal'];
        $date = date("Y-m-d H:i:s");
        
        $parent_ledgerSnoPref = intval(substr($parent_ledgerSno, 0, 3));
        $parent_ledgerSnoSuff = intval(substr($parent_ledgerSno, -3));
        
        //get main ledger balance
        $sqlMianLedgerBal = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = '$parent_ledgerSno'");
        $sqlMianLedgerBalRow = $sqlMianLedgerBal->fetch_assoc();
        $mainLedgerBal = intval(decrypt($sqlMianLedgerBalRow['balance']));
        $type = decrypt($sqlMianLedgerBalRow['type']);
        
        $sub_account_bal = ($type == "Asset" || $type == "Income") ? $sub_account_bal0 : -$sub_account_bal0;
        
        $parent_ledgerName1 = encrypt($parent_ledgerName);
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
            
            header("Location: /accounting");
        }
    }
    
    if(isset($_POST['removeSubLedgerAccount'])){
        $removeLedger = $_POST['removeSubLedger_account'];
        $removeLedger1 = explode(" - ", $removeLedger);
        $s_no = $removeLedger1[0];
        if(!is_numeric($s_no)){
            header("Location: /accounting");
        } else {
            
            $removal_reason = $_POST['removal_reasonSub'];
            $date = date("Y-m-d H:i:s");
            
            $removeLedger1 = encrypt($removeLedger);
            $date1 = encrypt($date);
            $status = encrypt($removal_reason);
            
            $sqlCreateLedger = $conn->query("UPDATE chart_of_subaccounts SET status='$status', action_date='$date1' WHERE ledger_no = $s_no ");
            
            if($sqlCreateLedger){
                header("Location: /accounting");
            }
        }
    }
    
    if(isset($_POST['closeYear'])){
        //Send the balance of the Income statement to Retained Earnings ledger in Balance sheet
        $active12 = encrypt("active");
        $incomeStmtAccs1 = encrypt("Income Statement");
        
        $totalBalanceAcc1 = 0;
        $sqlGetCurrentAcc1 = $conn->query("SELECT * FROM chart_of_accounts WHERE status='$active12' AND category='$incomeStmtAccs1' ");
        if($sqlGetCurrentAcc1->num_rows > 0){
            while ($rowAcc11 = $sqlGetCurrentAcc1->fetch_assoc()) {
                $totalBalanceAcc1 += intval(decrypt($rowAcc11["balance"]));
            }
        }
        
        addSubLedgerBalanceCreditClosure($conn, $retainedEarningsLedger, $totalBalanceAcc1);
        
        // add column for the closing year
        $currentYear = 'FY' . date('Ymd') ;
        $conn->query("ALTER TABLE chart_of_accounts ADD $currentYear TEXT");
        //$conn->query("ALTER TABLE chart_of_subaccounts ADD $currentYear TEXT");
        
        // //copy all balances to closing_bal
        $activeLedger = encrypt("active");
        $conn->query("UPDATE chart_of_accounts SET $currentYear = balance WHERE status='$activeLedger' ");
        //$conn->query("UPDATE chart_of_subaccounts SET $currentYear = balance WHERE status='$activeLedger'");
        
        
        //Reset the balances for the Income Statement Items
        $parent_ledgerSnoPref = '> 499';
        $conn->query("UPDATE chart_of_accounts SET balance = NULL WHERE LEFT(ledger_no, 3) $parent_ledgerSnoPref ");
        //$conn->query("UPDATE chart_of_subaccounts SET balance = NULL WHERE LEFT(ledger_no, 3) $parent_ledgerSnoPref ");
        
        //$conn->query("UPDATE chart_of_accounts SET balance = FY2023 ");
        //$conn->query("UPDATE chart_of_subaccounts SET balance = NULL ");
        
        // //save the activity
        $message = "$username Closed Financial Year on " . date("Y-m-d H:i:s");
        $level = 'Superadmin';
        saveNotification($message, $level);
        
        $action = $message;
        logAction($action);
    }
    
    if(isset($_POST['topupLedger'])){
        $ledgerAccName = $_POST['topupLedger_account'];
        $ledgerAccName1 = explode(" - ", $ledgerAccName);
        $ledgerNo = $ledgerAccName1[0];
        $amount = $_POST['amountTopup'];
        
        addSubLedgerBalanceCredit($conn, $ledgerNo, $amount);
    }
    
    //update total advanced loans and interest income ledgers
    $openLoan = encrypt('Open');
    $principalAdvanced = 0;
    $principalPaid = 0;
    $interestPaidAll = 0;
    
    $sqlGetPrincipalAdvanced = $conn->query("SELECT * FROM loans WHERE loan_status = '$openLoan'");
    if($sqlGetPrincipalAdvanced->num_rows > 0){
        while($sqlGetPrincipalAdvancedRow = $sqlGetPrincipalAdvanced->fetch_assoc()){
            $principalAdvanced += intval(decrypt($sqlGetPrincipalAdvancedRow['take_home']));
            $filterLoanNo = $sqlGetPrincipalAdvancedRow['loan_no'];
            
            //get the principal amount paid and interest paid
            $sqlGetPaidAmounts = $conn->query("SELECT * FROM loan_schedules WHERE loan_no = $filterLoanNo ");
            while($sqlGetPaidAmountsRow = $sqlGetPaidAmounts->fetch_assoc()){
                $principalPaid += intval(decrypt($sqlGetPaidAmountsRow['principalPaid']));
            }
        }
    }
    
    //check whether we have a closed period, if so, create an array of the dates of the closure, get the highest date and pull report from that date going forward
    // Query to get column names from the chart_of_accounts table where the column name starts with 'FY'
    $sqlGetColumnFYBSCheck = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME LIKE 'FY%'");
    
    $colNameBS = []; // Initialize the array to store dates
    $closedPeriod = false; // Initialize $closedPeriod to false
    
    if ($sqlGetColumnFYBSCheck->num_rows > 0) {
        $closedPeriod = true;
        
        while ($sqlGetColumnFYColBS = $sqlGetColumnFYBSCheck->fetch_assoc()) {
            // Extract the last 10 characters (the date part) and store it in the array
            $dateStr = substr($sqlGetColumnFYColBS['COLUMN_NAME'], -8);
            $colNameBS[] = $dateStr;
        }
        
        // Convert the date strings to DateTime objects for comparison
        $dateObjects = array_map(function($dateStr) {
            return new DateTime($dateStr);
        }, $colNameBS);
        
        // Sort the DateTime objects
        usort($dateObjects, function($a, $b) {
            return $b <=> $a; // Sort in descending order
        });
        
        // The first element is the latest date
        $latestDate = $dateObjects[0]->format('Ymd');
    } else {
        $closedPeriod = false;
    }
    
    //get the principal amount paid and interest paid
    $sqlGetPaidAmounts = $conn->query("SELECT * FROM loan_schedules");
    while($sqlGetPaidAmountsRow = $sqlGetPaidAmounts->fetch_assoc()){
        //increment all interest paid 
        $interestPaidAll += intval(decrypt($sqlGetPaidAmountsRow['interestPaid']));
    }
    
    $prevInterestIncome = 0;
    
    if(!$closedPeriod){
        $prevInterestIncome = 0;
    } else {
        // The first element is the latest date
        $latestDate1 = 'FY' . $latestDate;
    
        //Get interest income for last period and subtract
        $sqlGetColumnFYBSCheckAll = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chart_of_accounts' AND COLUMN_NAME LIKE 'FY%'");

        while($sqlGetColumnFYBSCheckAllRow = $sqlGetColumnFYBSCheckAll->fetch_assoc()){
            $prevPeriodCheck = $sqlGetColumnFYBSCheckAllRow['COLUMN_NAME'];
            
            //$sqlGetLastInterestIncome = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no=$interestIncomeLedger");
            $sqlGetLastInterestIncome = $conn->query("SELECT $prevPeriodCheck FROM chart_of_accounts WHERE ledger_no =$interestIncomeLedger");
            $sqlGetLastInterestIncomeRow = $sqlGetLastInterestIncome->fetch_assoc();
            $prevInterestIncome += intval(decrypt($sqlGetLastInterestIncomeRow[$prevPeriodCheck]));
        }

    }
    
    $interestPaid = $interestPaidAll - $prevInterestIncome;
    
    $totalLoansAdvanced = $principalAdvanced - $principalPaid;
    $totalInterestIncome = $interestPaid;
    $totalLoansAdvanced1 = encrypt($totalLoansAdvanced);
    $totalInterestIncome1 = encrypt($totalInterestIncome);
    
    $conn->query("UPDATE chart_of_accounts SET balance = '$totalLoansAdvanced1' WHERE ledger_no = '$loansAdancedLedger' ");
    $conn->query("UPDATE chart_of_accounts SET balance = '$totalInterestIncome1' WHERE ledger_no = '$interestIncomeLedger' ");
    
    

    
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Accounting</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark" >
                Accounting Management
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div <?php if($admin !== 2 || !$access){ echo 'hidden'; } ?> class="dropdown mb-3">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#addMainLedgerAccountModal">Main Ledger Account</button>
                            </li>
                            <li>
                                <button hidden type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#addSubLedgerAccountModal">Sub Ledger Account</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#manageLedgerBalancesModal">Manage Ledger Balances</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#closeYearModal">Close F/Y</button>
                            </li>

                        </ul>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Quick Stats or Metrics -->
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Balance Sheet (Current Period)
                                
                                    <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('balance-sheet-current', 'balance-sheet-current')" >Export</button>
                                </h5>
                                <div class="card-body">
                                    <div class="table table-responsive">
                                        <table id="balance-sheet-current" class="table table-hover border border-rounded">
                                            <thead>
                                                <tr>
                                                    <th>Ledger No.</th>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>Subtotal</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider">
                                                <?php
                                                    $active11 = encrypt("active");
                                                    $balanceSheetAccs1 = encrypt("Balance Sheet");
                                                    $asset = encrypt("Asset");
                                                    $liability = encrypt("Liability");
                                                    $capital = encrypt("Capital");
                                                    
                                                    $sqlGetCurrentAcc = $conn->query("SELECT * FROM chart_of_accounts WHERE status='$active11' AND category='$balanceSheetAccs1' ORDER BY FIELD(type, '$asset', '$liability', '$capital'), s_no ASC ");
                                                    
                                                    $totalBalanceAcc = 0;
                                                    $totalBalanceAccSub = 0;
                                                    
                                                    if($sqlGetCurrentAcc->num_rows > 0){
                                                        while ($rowAcc1 = $sqlGetCurrentAcc->fetch_assoc()) {
                                                            
                                                            $s_noBalSht = $rowAcc1["ledger_no"];
                                                            $s_noBalShtPref = substr($s_noBalSht, 0, 3);
                                                            $balShtType = decrypt($rowAcc1["type"]);
                                                            
                                                            //check whether main ledger item has a sub ledger item
                                                            $sqlCheckForSubLedger = $conn->query("SELECT * FROM chart_of_subaccounts WHERE LEFT(ledger_no, 3) = '$s_noBalShtPref' AND status='$active11' ");
                                                            if($sqlCheckForSubLedger->num_rows > 0){
                                                                $hasSubLedger = true;
                                                                
                                                            } else {
                                                                $hasSubLedger = false;
                                                            }
                                                            
                                                            if($hasSubLedger){
                                                                echo "<tr>
                                                                    <td><b>" . $rowAcc1["ledger_no"] . "</b></td>
                                                                    <td><b>" . decrypt($rowAcc1["name"]) . "</b></td>
                                                                    <td><b>" . $balShtType . "</b></td>
                                                                    <td></td>
                                                                    <td><b>" . number_format(intval(decrypt($rowAcc1["balance"])), 2) . "</b></td> </b></tr>";
                                                                while($sqlCheckForSubLedgerRow=$sqlCheckForSubLedger->fetch_assoc()){
                                                                    echo "<tr>
                                                                        <td>" . $sqlCheckForSubLedgerRow["ledger_no"] . "</td>
                                                                        <td>" . decrypt($sqlCheckForSubLedgerRow["name"]) . "</td>
                                                                        <td></td>
                                                                        <td>" . number_format(intval(decrypt($sqlCheckForSubLedgerRow["balance"])), 2) . "</td></tr>";
                                                                }
                                                                    
                                                                $totalBalanceAcc += intval(decrypt($rowAcc1["balance"]));
                                                                
                                                            } else {
                                                                echo "<tr>
                                                                    <td><b>" . $rowAcc1["ledger_no"] . "</b></td>
                                                                    <td><b>" . decrypt($rowAcc1["name"]) . "</b></td>
                                                                    <td><b>" . $balShtType . "</b></td>
                                                                    <td></td>
                                                                    <td><b>" . number_format(intval(decrypt($rowAcc1["balance"])), 2) . "</b></td>  </tr>";
                                                                    
                                                                $totalBalanceAcc += intval(decrypt($rowAcc1["balance"]));
                                                                
                                                            }
                                                        }
                                                        
                                                        echo "<tr><td><b>Total</b></td> <td></td> <td></td> <td></td> <td><b>" . number_format($totalBalanceAcc, 2) . "</b></td> </tr>";
                                                        
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
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div >
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2"> Income Statement (Current Period)
                                    
                                        <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('income-statement-current', 'income-statement-current')" >Export</button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table id="income-statement-current" class="table table-hover border border-rounded">
                                            <thead>
                                                <tr>
                                                    <th>Ledger No.</th>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>Subtotal</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider">
                                                <?php
                                                    $active12 = encrypt("active");
                                                    $incomeStmtAccs1 = encrypt("Income Statement");
                                                    $revenue =  encrypt("Revenue");
                                                    $expense =  encrypt("Expense");
                                                    
                                                    $sqlGetCurrentAcc1 = $conn->query("SELECT * FROM chart_of_accounts WHERE status='$active12' AND category='$incomeStmtAccs1' ORDER BY FIELD(type, '$revenue', '$expense'), s_no ASC  ");
                                                    
                                                    $totalBalanceAcc1 = 0;
                                                    $totalBalanceAccSub1 = 0;
                                                    
                                                    if($sqlGetCurrentAcc1->num_rows > 0){
                                                        while ($rowAcc11 = $sqlGetCurrentAcc1->fetch_assoc()) {
                                                            
                                                            $s_noIncomeStmt = $rowAcc11["ledger_no"];
                                                            $s_noIncomeStmtPref = substr($s_noIncomeStmt, 0, 3);
                                                            $IncomeStmtType = decrypt($rowAcc11["type"]);
                                                            
                                                            //check whether main ledger item has a sub ledger item
                                                            $sqlCheckForSubLedger1 = $conn->query("SELECT * FROM chart_of_subaccounts WHERE LEFT(ledger_no, 3) = '$s_noIncomeStmtPref' AND status='$active12'");
                                                            if($sqlCheckForSubLedger1->num_rows > 0){
                                                                $hasSubLedger1 = true;
                                                                
                                                            } else {
                                                                $hasSubLedger1 = false;
                                                            }
                                                            
                                                            if($hasSubLedger1){
                                                                echo "<tr>
                                                                    <td><b>" . $rowAcc11["ledger_no"] . "</b></td>
                                                                    <td><b>" . decrypt($rowAcc11["name"]) . "</b></td> 
                                                                    <td><b>" . $IncomeStmtType . "</b></td>
                                                                    <td></td>
                                                                    <td><b>" . number_format(intval(decrypt($rowAcc11["balance"])), 2) . "</b></td>  </tr>";
                                                                while($sqlCheckForSubLedgerRow1=$sqlCheckForSubLedger1->fetch_assoc()){
                                                                    echo "<tr>
                                                                        <td>" . $sqlCheckForSubLedgerRow1["ledger_no"] . "</td>
                                                                        <td>" . decrypt($sqlCheckForSubLedgerRow1["name"]) . "</td>
                                                                        <td></td>
                                                                        <td>" . number_format(intval(decrypt($sqlCheckForSubLedgerRow1["balance"])), 2) . "</td></tr>";
                                                                }
                                                                    
                                                                $totalBalanceAcc1 += intval(decrypt($rowAcc11["balance"]));
                                                                    
                                                            } else {
                                                                echo "<tr> 
                                                                    <td><b>" . $rowAcc11["ledger_no"] . "</b></td>
                                                                    <td><b>" . decrypt($rowAcc11["name"]) . "</b></td>
                                                                    <td><b>" . $IncomeStmtType . "</b></td>
                                                                    <td></td>
                                                                    <td><b>" . number_format(intval(decrypt($rowAcc11["balance"])), 2) . "</td> </b> </tr>";
                                                                    
                                                                $totalBalanceAcc1 += intval(decrypt($rowAcc11["balance"]));
                                                            }
                                                        }
                                                        
                                                        echo "<tr><td><b>Total</b></td> <td></td> <td></td> <td></td> <td><b> " . number_format($totalBalanceAcc1, 2) . "</b></td> </tr>";
                                                        
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
                            </div>
                        
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Quick Stats or Metrics -->
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Balance Sheet (Past Period)
                                    <select id="filter-balance-sheet">
                                        <?php
                                            $sqlGetColumnFYBS = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chart_of_accounts'
                                                AND (COLUMN_NAME LIKE 'FY%')");
                                            if($sqlGetColumnFYBS->num_rows > 0){
                                                if(isset($_GET['prev'])){
                                                    echo '<option>' . $_GET["prev"] . '</option>';
                                                } else {
                                                    echo "<option>Select FY</option>";
                                                }
                                                while($sqlGetColumnFYColBS = $sqlGetColumnFYBS->fetch_assoc()){
                                                    $colNameBS = $sqlGetColumnFYColBS['COLUMN_NAME'];
                                                    echo "<option> $colNameBS </option>";
                                                }
                                            } else {
                                                echo "<option> No closed Period. </option>";
                                            }
                                        ?>
                                    </select>
                                    
                                    <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('balance-sheet-prev', 'balance-sheet-prev')" >Export</button>
                                </h5>
                                <div class="card-body">
                                    <div class="table table-responsive">
                                        <table id="balance-sheet-prev" class="table table-hover border border-rounded">
                                            <thead>
                                                <tr>
                                                    <th>Ledger No.</th>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>Subtotal</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider">
                                                <?php
                                                    if (isset($_GET['prev'])){
                                                        $filter = (isset($_GET['prev'])) ? $_GET['prev'] : 'balance';
                                                        $active11 = encrypt("active");
                                                        $balanceSheetAccs1 = encrypt("Balance Sheet");
                                                        
                                                        $sqlGetCurrentAcc = $conn->query("SELECT * FROM chart_of_accounts WHERE status='$active11' AND category='$balanceSheetAccs1' ");
                                                        
                                                        $totalBalanceAcc = 0;
                                                        $totalBalanceAccSub = 0;
                                                        
                                                        if($sqlGetCurrentAcc->num_rows > 0){
                                                            while ($rowAcc1 = $sqlGetCurrentAcc->fetch_assoc()) {
                                                                
                                                                $s_noBalSht = $rowAcc1["ledger_no"];
                                                                $s_noBalShtPref = substr($s_noBalSht, 0, 3);
                                                                $balShtType = decrypt($rowAcc1["type"]);
                                                                
                                                                //check whether main ledger item has a sub ledger item
                                                                $sqlCheckForSubLedger = $conn->query("SELECT * FROM chart_of_subaccounts WHERE LEFT(ledger_no, 3) = '$s_noBalShtPref' AND status='$active11'");
                                                                if($sqlCheckForSubLedger->num_rows > 0){
                                                                    $hasSubLedger = true;
                                                                    
                                                                } else {
                                                                    $hasSubLedger = false;
                                                                }
                                                                
                                                                if($hasSubLedger){
                                                                    echo "<tr>
                                                                        <td><b>" . $rowAcc1["ledger_no"] . "</b></td>
                                                                        <td><b>" . decrypt($rowAcc1["name"]) . "</b></td>
                                                                        <td><b>" . $balShtType . "</b></td>
                                                                        <td></td>
                                                                        <td><b>" . number_format(intval(decrypt($rowAcc1["$filter"])), 2) . "</b></td> </b></tr>";
                                                                    while($sqlCheckForSubLedgerRow=$sqlCheckForSubLedger->fetch_assoc()){
                                                                        echo "<tr>
                                                                            <td>" . $sqlCheckForSubLedgerRow["ledger_no"] . "</td>
                                                                            <td>" . decrypt($sqlCheckForSubLedgerRow["name"]) . "</td>
                                                                            <td></td>
                                                                            <td>" . number_format(intval(decrypt($sqlCheckForSubLedgerRow["$filter"])), 2) . "</td></tr>";
                                                                    }
                                                                        
                                                                    $totalBalanceAcc += intval(decrypt($rowAcc1["$filter"]));
                                                                    
                                                                } else {
                                                                    echo "<tr>
                                                                        <td><b>" . $rowAcc1["ledger_no"] . "</b></td>
                                                                        <td><b>" . decrypt($rowAcc1["name"]) . "</b></td>
                                                                        <td><b>" . $balShtType . "</b></td>
                                                                        <td></td>
                                                                        <td><b>" . number_format(intval(decrypt($rowAcc1["$filter"])), 2) . "</b></td>  </tr>";
                                                                        
                                                                    $totalBalanceAcc += intval(decrypt($rowAcc1["$filter"]));
                                                                    
                                                                }
                                                            }
                                                            
                                                            echo "<tr><td><b>Total</b></td> <td></td> <td></td> <td></td> <td><b>" . number_format($totalBalanceAcc, 2) . "</b></td> </tr>";
                                                            
                                                        } else {
                                                            echo "<tr><td colspan='9'>No results found.</td></tr>";
                                                        }
                                                    }
                                                    
                                                //$conn->close();
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div >
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2"> Income Statement (Past Period)
                                        <select id="filter-income-statement">
                                            <?php
                                                $sqlGetColumnFYIC = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chart_of_accounts'
                                                    AND (COLUMN_NAME LIKE 'FY%')");
                                                if($sqlGetColumnFYIC->num_rows > 0){
                                                    if(isset($_GET['prev'])){
                                                        echo '<option>' . $_GET["prev"] . '</option>';
                                                    } else {
                                                        echo "<option>Select FY</option>";
                                                    }
                                                    
                                                    while($sqlGetColumnFYColIC = $sqlGetColumnFYIC->fetch_assoc()){
                                                        $colNameIC = $sqlGetColumnFYColIC['COLUMN_NAME'];
                                                        echo "<option> $colNameIC </option>";
                                                    }
                                                } else {
                                                    echo "<option> No closed Period. </option>";
                                                }
                                            ?>
                                        </select>
                                        <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('income-statement-prev', 'income-statement-prev')" >Export</button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table id="income-statement-prev" class="table table-hover border border-rounded">
                                            <thead>
                                                <tr>
                                                    <th>Ledger No.</th>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>Subtotal</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="table-group-divider">
                                                <?php
                                                    if (isset($_GET['prev'])){
                                                        $filter = (isset($_GET['prev'])) ? $_GET['prev'] : 'balance';
                                                        $active12 = encrypt("active");
                                                        $incomeStmtAccs1 = encrypt("Income Statement");
                                                        
                                                        $sqlGetCurrentAcc1 = $conn->query("SELECT * FROM chart_of_accounts WHERE status='$active12' AND category='$incomeStmtAccs1'");
                                                        
                                                        $totalBalanceAcc1 = 0;
                                                        $totalBalanceAccSub1 = 0;
                                                        
                                                        if($sqlGetCurrentAcc1->num_rows > 0){
                                                            while ($rowAcc11 = $sqlGetCurrentAcc1->fetch_assoc()) {
                                                                
                                                                $s_noIncomeStmt = $rowAcc11["ledger_no"];
                                                                $s_noIncomeStmtPref = substr($s_noIncomeStmt, 0, 3);
                                                                $IncomeStmtType = decrypt($rowAcc11["type"]);
                                                                
                                                                //check whether main ledger item has a sub ledger item
                                                                $sqlCheckForSubLedger1 = $conn->query("SELECT * FROM chart_of_subaccounts WHERE LEFT(ledger_no, 3) = '$s_noIncomeStmtPref' AND status='$active12' ");
                                                                if($sqlCheckForSubLedger1->num_rows > 0){
                                                                    $hasSubLedger1 = true;
                                                                    
                                                                } else {
                                                                    $hasSubLedger1 = false;
                                                                }
                                                                
                                                                if($hasSubLedger1){
                                                                    echo "<tr>
                                                                        <td><b>" . $rowAcc11["ledger_no"] . "</b></td>
                                                                        <td><b>" . decrypt($rowAcc11["name"]) . "</b></td> 
                                                                        <td><b>" . $IncomeStmtType . "</b></td>
                                                                        <td></td>
                                                                        <td><b>" . number_format(intval(decrypt($rowAcc11["$filter"])), 2) . "</b></td>  </tr>";
                                                                    while($sqlCheckForSubLedgerRow1=$sqlCheckForSubLedger1->fetch_assoc()){
                                                                        echo "<tr>
                                                                            <td>" . $sqlCheckForSubLedgerRow1["ledger_no"] . "</td>
                                                                            <td>" . decrypt($sqlCheckForSubLedgerRow1["name"]) . "</td>
                                                                            <td></td>
                                                                            <td>" . number_format(intval(decrypt($sqlCheckForSubLedgerRow1["$filter"])), 2) . "</td></tr>";
                                                                    }
                                                                        
                                                                    $totalBalanceAcc1 += intval(decrypt($rowAcc11["$filter"]));
                                                                        
                                                                } else {
                                                                    echo "<tr> 
                                                                        <td><b>" . $rowAcc11["ledger_no"] . "</b></td>
                                                                        <td><b>" . decrypt($rowAcc11["name"]) . "</b></td>
                                                                        <td><b>" . $IncomeStmtType . "</b></td>
                                                                        <td></td>
                                                                        <td><b>" . number_format(intval(decrypt($rowAcc11["$filter"])), 2) . "</td> </b> </tr>";
                                                                        
                                                                    $totalBalanceAcc1 += intval(decrypt($rowAcc11["$filter"]));
                                                                }
                                                            }
                                                            
                                                            echo "<tr><td><b>Total</b></td> <td></td> <td></td> <td></td> <td><b> " . number_format($totalBalanceAcc1, 2) . "</b></td> </tr>";
                                                            
                                                        } else {
                                                            echo "<tr><td colspan='9'>No results found.</td></tr>";
                                                        }
                                                    }
                                                    
                                                //$conn->close();
                                                ?>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                    

                    <div class="modal fade" id="addMainLedgerAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Main Ledger Account</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add Main Ledger Account</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="account_destination" class="form-label">Select Account Destination</label>
                                                        <input class="form-control" list="datalistOptions" id="account_destination" name="account_destination" placeholder="Type to search Destination.." autocomplete="off" required>
                                                        <datalist id="datalistOptions">
                                                            <option value="Balance Sheet">
                                                            <option value="Income Statement">
                                                        </datalist>
                                                    </div>
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="account_type" class="form-label">Select Account Type</label>
                                                        <input class="form-control" list="datalistOptions1" id="account_type" name="account_type" placeholder="Type to search Type.." autocomplete="off" required>
                                                        <datalist id="datalistOptions1">
                                                            <option value="Asset - Balance Sheet">
                                                            <option value="Liability - Balance Sheet">
                                                            <option value="Capital - Balance Sheet">
                                                            <option value="Revenue - Income Statement">
                                                            <option value="Expense - Income Statement">
                                                        </datalist>
                                                    </div>
                                                    <div class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="text" id="account_name" name="account_name" placeholder="Account Name.." autocomplete="off" required>
                                                        <label  for="account_name">Account Name</label>
                                                    </div>
                                                    <div class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="text" id="account_bal" name="account_bal" placeholder="Account Balance.." autocomplete="off" required>
                                                        <label  for="account_bal">Account Balance</label>
                                                    </div>
                                                    
                                                    <input  class="btn btn-info" type="submit" value="Create New Main Ledger" name="createMainLedgerAccount" id="createMainLedgerAccount" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Remove Main Ledger Account</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <span>NB: You can only remove Ledger Accounts with zero balances. </span>
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="removeLedger_account" class="form-label">Select Ledger Account</label>
                                                        <input class="form-control" list="datalistOptions2" id="removeLedger_account" name="removeLedger_account" placeholder="Type to search Account.." autocomplete="off" required>
                                                        <datalist id="datalistOptions2">
                                                            <?php
                                                                $activeRemovable = encrypt('active');
                                                                $sqlRemoveableAcc = $conn->query("SELECT * FROM chart_of_accounts WHERE status = '$activeRemovable'");
                                                                
                                                                if($sqlRemoveableAcc->num_rows > 0){
                                                                    while ($sqlRemoveableAccRows = $sqlRemoveableAcc->fetch_assoc()){
                                                                        $removableBal = intval(decrypt($sqlRemoveableAccRows['balance']));
                                                                        
                                                                        if($removableBal == 0){
                                                                            $sno = $sqlRemoveableAccRows['ledger_no'];
                                                                            $name = decrypt($sqlRemoveableAccRows['name']);
                                                                            
                                                                            echo "<option value=\"$sno - $name\"> ";
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<option value=\"No account.\">";
                                                                }
                                                            
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input class="form-control" id="removal_reason" name="removal_reason" placeholder="Type removal reason.." autocomplete="off" required>
                                                    </div>

                                                    
                                                    <input  class="btn btn-danger" type="submit" value="Remove Main Ledger Account" name="removeMainLedgerAccount" id="removeMainLedgerAccount" >
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
                    
                    <div class="modal fade" id="addSubLedgerAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Sub Ledger Account</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add Sub Ledger Account</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="parent_ledger" class="form-label">Select Parent Ledger</label>
                                                        <input class="form-control" list="datalistOptions3" id="parent_ledger" name="parent_ledger" placeholder="Type to search Main Ledger.." autocomplete="off" required>
                                                        <datalist id="datalistOptions3">
                                                            <?php
                                                                $activeRemovable1 = encrypt('active');
                                                                $sqlRemoveableAcc1 = $conn->query("SELECT * FROM chart_of_accounts WHERE status = '$activeRemovable1'");
                                                                
                                                                if($sqlRemoveableAcc1->num_rows > 0){
                                                                    while ($sqlRemoveableAccRows1 = $sqlRemoveableAcc1->fetch_assoc()){
                                                                        $removableBal1 = intval(decrypt($sqlRemoveableAccRows1['balance']));
                                                                        $sno1 = $sqlRemoveableAccRows1['ledger_no'];
                                                                        $name1 = decrypt($sqlRemoveableAccRows1['name']);
                                                                        
                                                                        echo "<option value=\"$sno1 - $name1\"> ";
                                                                    }
                                                                } else {
                                                                    echo "<option value=\"No account.\">";
                                                                }
                                                            
                                                            ?>
                                                        </datalist>
                                                    </div>

                                                    <div class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="text" id="sub_account_name" name="sub_account_name" placeholder="Sub Account Name.." autocomplete="off" required>
                                                        <label  for="sub_account_name">Sub Account Name</label>
                                                    </div>
                                                    <div class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="text" id="sub_account_bal" name="sub_account_bal" placeholder="Account Balance.." autocomplete="off" required>
                                                        <label  for="sub_account_bal">Sub Account Balance</label>
                                                    </div>
                                                    
                                                    <input  class="btn btn-info" type="submit" value="Create Sub Ledger" name="createSubLedgerAccount" id="createSubLedgerAccount" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Remove Sub Ledger Account</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <span>NB: You can only remove Sub Ledger Accounts with zero balances. </span>
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="removeSubLedger_account" class="form-label">Select Sub Ledger Account</label>
                                                        <input class="form-control" list="datalistOptions4" id="removeSubLedger_account" name="removeSubLedger_account" placeholder="Type to search Account.." autocomplete="off" required>
                                                        <datalist id="datalistOptions4">
                                                            <?php
                                                                $activeRemovable2 = encrypt('active');
                                                                $sqlRemoveableAcc2 = $conn->query("SELECT * FROM chart_of_subaccounts WHERE status = '$activeRemovable2'");
                                                                
                                                                if($sqlRemoveableAcc2->num_rows > 0){
                                                                    while ($sqlRemoveableAccRows2 = $sqlRemoveableAcc2->fetch_assoc()){
                                                                        $removableBal2 = intval(decrypt($sqlRemoveableAccRows2['balance']));
                                                                        
                                                                        if($removableBal2 == 0){
                                                                            $sno2 = $sqlRemoveableAccRows2['ledger_no'];
                                                                            $name2 = decrypt($sqlRemoveableAccRows2['name']);
                                                                            
                                                                            echo "<option value=\"$sno2 - $name2\"> ";
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo "<option value=\"No account.\">";
                                                                }
                                                            
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <div class="mb-3">
                                                        <input class="form-control" id="removal_reasonSub" name="removal_reasonSub" placeholder="Type removal reason.." autocomplete="off" required>
                                                    </div>

                                                    
                                                    <input  class="btn btn-danger" type="submit" value="Remove Sub Ledger Account" name="removeSubLedgerAccount" id="removeSubLedgerAccount" >
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
                    
                    <div class="modal fade" id="closeYearModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Close Year</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Close Current Financial Year/Period</h3>
                                            <div class="" >
                                                
                                                <input hidden class="btn btn-warning" type="submit" value="Request Security Code" name="closeYearCode" id="closeYearCode" >
                                                <form  method="POST" action="">
                                                    <div hidden class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="text" id="close_otp" name="close_otp" placeholder="Enter Security Code" autocomplete="off" >
                                                        <label  for="close_otp">Enter Security Code</label>
                                                    </div>
                                                    
                                                    <input class="btn btn-info" type="submit" value="Close Year" name="closeYear" id="closeYear" >
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
                    
                    <div class="modal fade" id="manageLedgerBalancesModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Manage Ledger Balances</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add Ledger Balance</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="topupLedger_account" class="form-label">Select Ledger Account</label>
                                                        <input class="form-control" list="datalistOptions5" id="topupLedger_account" name="topupLedger_account" placeholder="Type to search Account.." autocomplete="off" required>
                                                        <datalist id="datalistOptions5">
                                                            <?php
                                                                $activeRemovable21 = encrypt('active');
                                                                $sqlRemoveableAcc21 = $conn->query("SELECT * FROM chart_of_accounts WHERE status = '$activeRemovable2'");
                                                                
                                                                if($sqlRemoveableAcc21->num_rows > 0){
                                                                    while ($sqlRemoveableAccRows21 = $sqlRemoveableAcc21->fetch_assoc()){
                                                                        $sno21 = $sqlRemoveableAccRows21['ledger_no'];
                                                                        $name21 = decrypt($sqlRemoveableAccRows21['name']);
                                                                        
                                                                        echo "<option value=\"$sno21 - $name21\"> ";
                                                                    }
                                                                } else {
                                                                    echo "<option value=\"No account.\">";
                                                                }
                                                            
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <br>
                                                    <br>
                                                    
                                                    <div class="form-floating mb-3 text-start">
                                                        <input class="form-control" type="number" id="amountTopup" name="amountTopup" placeholder="Amount" autocomplete="off" required>
                                                        <label  for="amountTopup">Amount (NB: to reduce, add negative sign)</label>
                                                    </div>
                                                    
                                                    <input class="btn btn-info" type="submit" value="Submit" name="topupLedger" id="topupLedger" >
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
                    
                </div>  <!--End of container fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div> 
        
        <script>
            //Add a script to search the table -->
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('entry-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('loan-details');
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
            function searchTable1() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('staff-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('performance-history');
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

            //Pagination2
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('loan-details');
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
            document.addEventListener('DOMContentLoadewd', function() {
                const table = document.getElementById('performance-history');
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
            
            //Portfolio transfer handler
            document.addEventListener('DOMContentLoaded', function() {
                
                function getCustomerOwner(){
                    var customerDetails = document.getElementById('customer_details').value;
                    
                    const newData = {
                            data: customerDetails,
                            check:"getCustomerOwner"
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
                            document.getElementById('customer_details').value = '';
                        } else {
                            var response_message = data.message;
                            
                            document.getElementById('currentOwner').value = response_message;
                            document.getElementById('currentOwnerH').value = response_message;
                            
                        }
                    });
                    
                }
                
                document.getElementById('customer_details').addEventListener('change', getCustomerOwner); 
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                function checkStfParam(){
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('stf')) {
                        const stfValue = urlParams.get('stf');
                        return stfValue;
                    } else {
                        return false;
                    }
                }
                
                function reloadPage(classification){
                    var stfParam = checkStfParam();
                    
                    if(stfParam){
                        var newFilter = '?stf=' + stfParam + '&cls=' + classification;
                        
                        window.location.href = newFilter;
                    } else {
                        var newFilter = '?cls=' + classification;
                        
                        window.location.href = newFilter;
                    }
                }
                
                document.getElementById('Normal').addEventListener('click', function() { reloadPage('Normal'); }); 

                document.getElementById('Watch').addEventListener('click', function(){ reloadPage('Watch'); }); 

                document.getElementById('Substandard').addEventListener('click', function(){ reloadPage('Substandard'); }); 

                document.getElementById('Doubtful').addEventListener('click', function(){ reloadPage('Doubtful'); }); 

                document.getElementById('Loss').addEventListener('click', function() { reloadPage('Loss'); });

            });
            
            document.addEventListener('DOMContentLoaded', function() {
                
                function filterprevBS(){
                    var filterPeriod = document.getElementById('filter-balance-sheet').value;
                    
                    window.location.href = window.location.pathname + '?prev=' + filterPeriod;
                };
                
                function filterprevIS(){
                    var filterPeriod = document.getElementById('filter-income-statement').value;
                    
                    window.location.href = window.location.pathname + '?prev=' + filterPeriod;
                };
                
                document.getElementById('filter-balance-sheet').addEventListener('change', filterprevBS);
                document.getElementById('filter-income-statement').addEventListener('change', filterprevIS);
            });    

        </script>
        
        <?php //include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>