<?php
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/pay-process2.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/counter.php';
    require_once __DIR__.'/templates/checkMembersBalances.php';
    require_once __DIR__.'/templates/notifications.php';
    require_once __DIR__.'/templates/sendsms.php';
    require_once __DIR__.'/templates/loanActions.php';

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
    }  elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false){
        header('Location: /loans'); // Redirect to the login page
        exit;
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
    
    $userphone1 = encrypt($userphone);
    $limit = (!$admin)? "AND staff_phone = '$userphone1' " : "" ;
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    

    if(isset($_POST['submitWriteoff'])){
        $memberSelect = $_POST['memberSelect11'];
        $loan = explode(" - ", $memberSelect);
        $loan_no = $loan[0];
        $reason = $_POST['reason'];
        
        //close account in loans table and writeoff
        $sqlGetLoanDetailsTopup = $conn->query("SELECT * FROM loans WHERE loan_no='$loan_no' ");
        $sqlTopupResults = $sqlGetLoanDetailsTopup->fetch_assoc();
        $gross_loan = $sqlTopupResults['gross_loan'];
        $loan_balance = $sqlTopupResults['loan_balance'];
        $customerN02 = $sqlTopupResults['customer_no'];
        $loan_status = encrypt('Closed');
        $zero = encrypt("0");
        $writeoff = encrypt('Writeoff');
        
        $conn->query("UPDATE loans SET loan_payments = '$gross_loan', loan_status='$loan_status', loan_balance = '$zero', loan_writeoff='$writeoff' WHERE loan_no='$loan_no' ");
        
        //add transaction in transaction table
        $description_no = encrypt($loan_no);
        $posting_description = encrypt('Writeoff');
        $transaction_by = encrypt($username);
        
        $posting_date = encrypt(date('Y-m-d H:i:s'));
        
        $sqlInsertTransactions = $conn->query("INSERT INTO loan_transactions(loan_no, customer_no, posting_date, posting_description, description_no, credit, transaction_by) 
            VALUES ('$loan_no','$customerN02','$posting_date','$posting_description','$description_no','$loan_balance','$transaction_by')");
        
        
        //add transaction in loan schedules
        $sqlGetInstallment = $conn->query("SELECT * FROM loan_schedules WHERE loan_no='$loan_no'");
        $installmentRow = $sqlGetInstallment->fetch_assoc();
        $installmentDue = $installmentRow['loan_installment'];
        
        $conn->query("UPDATE loan_schedules SET paid = '$installmentDue' WHERE loan_no = '$loan_no' ");
        
        //update appraisal
        $review = 'Writeoff';
        $actionBy = $username;

        addLoanAppraisal($conn, $loan_no, $review, $actionBy, $reason);
        
    }
    
    //get totals for all the arrears amount for open loans
    $openLoan = encrypt("Open");
    $totalAmountInArrears = 0;
    $totalCountInArrears = 0;
    $totalLoanBal = 0;
    
    $getArrearsTotal = $conn->query("SELECT * FROM loans WHERE loan_status = '$openLoan' ");
    if($getArrearsTotal->num_rows > 0){
        while($totalAmountInArrearsRow = $getArrearsTotal->fetch_assoc()){
            $totalAmountInArrears += intval(decrypt($totalAmountInArrearsRow['amount_inArrears']));
            $totalLoanBal += intval(decrypt($totalAmountInArrearsRow['loan_balance']));
            if(intval(decrypt($totalAmountInArrearsRow['amount_inArrears'])) > 0){
                $totalCountInArrears ++;
            }
        }
    } else {
        $totalAmountInArrears = 0;
        $totalCountInArrears = 0;
        $totalLoanBal = 0;
    }
    
    $totalPercInArrears = ($totalLoanBal > 0) ? $totalAmountInArrears / $totalLoanBal * 100 : 0;
    
    
    //get totals for all individual the arrears amount for open loans
    $openLoanInd = encrypt("Open");
    $totalAmountInArrearsInd = 0;
    $totalCountInArrearsInd = 0;
    $totalLoanBalInd = 0;
    
    $getArrearsTotalInd = $conn->query("SELECT * FROM loans WHERE loan_status = '$openLoanInd' AND staff_phone = '$userphone1' ");
    if($getArrearsTotalInd->num_rows > 0){
        while($totalAmountInArrearsRowInd = $getArrearsTotalInd->fetch_assoc()){
            $totalAmountInArrearsInd += intval(decrypt($totalAmountInArrearsRowInd['amount_inArrears']));
            $totalLoanBalInd += intval(decrypt($totalAmountInArrearsRowInd['loan_balance']));
            if(intval(decrypt($totalAmountInArrearsRowInd['amount_inArrears'])) > 0){
                $totalCountInArrearsInd ++;
            }
        }
    } else {
        $totalAmountInArrearsInd = 0;
        $totalCountInArrearsInd = 0;
        $totalLoanBalInd = 0;
    }
    
    $totalPercInArrearsInd = ($totalLoanBalInd > 0) ? $totalAmountInArrearsInd / $totalLoanBalInd * 100: 0;

        
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Past Due</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Loans Past Due
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div <?php if($admin !== 2 || !$access){ echo 'hidden'; } ?> class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#loanWriteoffModal">Loan Writeoff</button>
                            </li>
                        </ul>
                    </div>
                    <br>
                    
                    <div class="row">
                        <!-- Quick Stats or Metrics -->
                        <div <?php if($admin === 2 ){ echo 'hidden'; } ?> class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Individual Report</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <span> Total Loans Past Due</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalAmountInArrearsInd, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Past Due</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalCountInArrearsInd, 2); ?></span></div>
                                        </div>
                                    </div>
                                    <span> Percentage of Amount In Arrears</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalPercInArrearsInd, 2) . "%"; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div <?php if(!$admin){ echo 'hidden'; } ?> class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Company Report</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <span> Total Loans Past Due</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalAmountInArrears, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Past Due</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalCountInArrears, 2); ?></span></div>
                                        </div>
                                    </div>
                                    <span> Percentage of Amount In Arrears</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalPercInArrears, 2) . "%"; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-light" >
                                <b>Loans Past Due </b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button  type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('past-due', 'past-due')" >Export to Excel</button>
                            
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
                                <table id="past-due" class="table table-hover border border-rounded">
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
                                            $normal = encrypt('Normal');
                                            $loan_status = encrypt('Open');
                                            $sqlGetLoan = $conn->query("SELECT * FROM loans WHERE loan_status = '$loan_status' $limit ");
                                                
                                            if($sqlGetLoan->num_rows > 0){
                                                while ($rowLoans = $sqlGetLoan->fetch_assoc()) {
                                                    $amount_inArrears = intval(decrypt($rowLoans["amount_inArrears"]));
                                                    
                                                    if($amount_inArrears > 0){
                                                        echo "<tr>";
                                                        echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans['loan_no']}'>{$rowLoans['loan_no']}</a></td>
                                                            <td>" . $rowLoans["customer_no"] . "</td>
                                                            <td>" . decrypt($rowLoans["customer_name"]) . "</td>
                                                            <td>" . decrypt($rowLoans["customer_phone"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_product"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_type"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_amount"]) . "</td>
                                                            <td>" . decrypt($rowLoans["no_of_installments"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_installment"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_applicationDate"]) . "</td>
                                                            <td>" . decrypt($rowLoans["gross_loan"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_payments"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_balance"]) . "</td>
                                                            <td>" . decrypt($rowLoans["last_paymentDate"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_status"]) . "</td>
                                                            <td>" . decrypt($rowLoans["loan_classification"]) . "</td>
                                                            <td>" . decrypt($rowLoans["days_inArrears"]) . "</td>
                                                            <td>" . decrypt($rowLoans["amount_inArrears"]) . "</td>
                                                            <td>" . decrypt($rowLoans["worst_classification"]) . "</td>
                                                            <td>" . decrypt($rowLoans["worst_daysInArrears"]) . "</td>
                                                            <td>" . $rowLoans["location_name"] . "</td>";
                                                        echo "<td><a class='btn btn-sm btn-info' href='/statement?lno={$rowLoans['loan_no']}'>Statement</a></td>
                                                            </tr>";
                                                    }
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
                    
                    <div class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loans Witten Off</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('written-off', 'written-off')" >Export to Excel</button>
                            
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
                                <table id="written-off" class="table table-hover border border-rounded">
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
                                            $writtenOff = encrypt('Writeoff');

                                            $sqlGetLoan1 = $conn->query("SELECT * FROM loans WHERE loan_writeoff='$writtenOff'");
                                            
                                            if ($sqlGetLoan1->num_rows > 0){
                                                while ($rowLoans1 = $sqlGetLoan1->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans1['loan_no']}'>{$rowLoans1['loan_no']}</a></td>
                                                        <td>" . $rowLoans1["customer_no"] . "</td>
                                                        <td>" . decrypt($rowLoans1["customer_name"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["customer_phone"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_product"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_type"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["loan_amount"]) . "</td>
                                                        <td>" . decrypt($rowLoans1["no_of_installments"]) . "</td>
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
                                                    echo "<td>
                                                                <form method='POST' action='templates/generateDocs.php' target='_blank'>
                                                                    <input type='hidden' name='payment_id' value='" . $rowLoans1["loan_no"] . "'>
                                                                    <input type='submit' class='btn btn-info btn-sm' value='Generate Statement' >
                                                                </form>
                                                            </td>
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
                    
                    
                    
                    <!--Application Modals-->
                    <div class="modal fade" id="loanWriteoffModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Loan Writeoff</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form id="" method="POST" action="">
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="memberSelect11" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions91" id="memberSelect11"  name="memberSelect11" placeholder="Type to search Customer.." autocomplete="off">
                                            <datalist id="datalistOptions91">
                                                <?php
                                                    $closed1 = encrypt('Closed');
                                                    $sqlLoans111 = "SELECT * FROM loans WHERE loan_status <> '$closed1'" ;
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
                                            <textarea  class="form-control" rows="3" placeholder="Reason for Writeoff" type="text" id="reason" name="reason"  required></textarea>
                                            <label for="reason"> Reason for Writeoff</label>
                                        </div>
                                        <br>
                                        <input hidden class="btn btn-primary btn-sm" id="submitWriteoff" name="submitWriteoff" type="submit" value="Record Deposit" />
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger" onclick="btnClick('submitWriteoff')">Submit Writeoff</button>
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
            //Btn click function
            function btnClick(btnId){
                document.getElementById(btnId).click();
            }
            
            //Add a script to search the table -->
            function searchTable1() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search1');
                filter = input.value.toUpperCase();
                table = document.getElementById('past-due');
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
                table = document.getElementById('written-off');
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
                const table = document.getElementById('past-due');
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
                const table = document.getElementById('written-off');
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
            
        </script>
        
        <?php //include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>