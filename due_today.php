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
    
    //get totals for all the due today amount for open loans
    $today = encrypt(date("Y-m-d"));
    $totalAmountDue = 0;
    $totalCountDue = 0;
    $openL = encrypt("Open");
    
    $getDueTotal = $conn->query("SELECT * FROM loan_schedules WHERE due_date = '$today' ");
    if($getDueTotal->num_rows > 0){
        while($totalAmountDueRow = $getDueTotal->fetch_assoc()){
            $loan_no = $totalAmountDueRow['loan_no'];
            
            //get the installment for each loan number
            $sqlGetInst = $conn->query("SELECT * FROM loans WHERE loan_no = '$loan_no' AND loan_status = '$openL'");
            while($sqlGetInstRow = $sqlGetInst->fetch_assoc()){
                $inst = intval(decrypt($sqlGetInstRow['loan_installment']));
                
                $totalAmountDue += $inst;
                $totalCountDue ++;
            }
        }
    } else {
        $totalAmountDue = 0;
        $totalCountDue = 0;
    }
    
    //get totals for all individual the due today amount for open loans
    $openLoanInd = encrypt("Open");
    $totalAmountDueInd = 0;
    $totalCountDueInd = 0;
    $userphoneE = encrypt($userphone);
    
    $getDueTotalInd = $conn->query("SELECT * FROM loan_schedules WHERE due_date = '$today' ");
    if($getDueTotalInd->num_rows > 0){
        while($totalAmountDueRowInd = $getDueTotalInd->fetch_assoc()){
            $loan_noInd = $totalAmountDueRowInd['loan_no'];
            
            //get the installment for each loan number
            $sqlGetInstInd = $conn->query("SELECT * FROM loans WHERE loan_no = '$loan_noInd' AND loan_status = '$openL' AND staff_phone = '$userphoneE' ");
            while($sqlGetInstRowInd = $sqlGetInstInd->fetch_assoc()){
                $instInd = intval(decrypt($sqlGetInstRowInd['loan_installment']));
                
                $totalAmountDueInd += $instInd;
                $totalCountDueInd ++;
            }
        }
    } else {
        $totalAmountDueInd = 0;
        $totalCountDueInd = 0;
    }  

        
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Due Today</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Loans Due Today
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    
                    <div class="row">
                        <!-- Quick Stats or Metrics -->
                        <div <?php if($admin === 2 ){ echo 'hidden'; } ?> class="col-md-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Individual Report</h5>
                                <div class="card-body">
                                    <!-- Display relevant statistics here -->
                                    <span> Total Amount Due Today</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalAmountDueInd, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Due Today</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalCountDueInd, 2); ?></span></div>
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
                                    <span> Total Amount Due Today</span>
                                    <div class="progress" role="progressbar" aria-label="Warning example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-warning text-dark" style="width: 100%;">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalAmountDue, 2) ; ?></span></div>
                                        </div>
                                    </div>
                                    <span> Total Count Due Today</span>
                                    <div class="progress" role="progressbar" aria-label="Danger example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-danger" style="width: 100%">
                                            <div class="justify-center text-dark"> <span> <?php echo number_format($totalCountDue, 2); ?></span></div>
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
                                <b>Loans Due Today</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('due-today', 'due-today')" >Export to Excel</button>
                            
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
                                <table id="due-today" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Loan No.</th>
                                            <th>Customer Name</th>
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
                                        $today = encrypt(date("Y-m-d"));
                                        //$sqlSchedules = "SELECT * FROM loan_schedules WHERE due_date = '$today' AND paid IS NULL ORDER BY s_no DESC";
                                        $sqlSchedules = "SELECT * FROM loan_schedules WHERE due_date = '$today' AND paid IS NULL ORDER BY s_no DESC";
                                
                                        $resultSchedules = $conn->query($sqlSchedules);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultSchedules->num_rows > 0) {
                                            while ($rowSchedules = $resultSchedules->fetch_assoc()) {
                                                $loanNumbr = $rowSchedules['loan_no'];
                                                
                                                $sqlGetCustNameHere = $conn->query(" SELECT * FROM loans WHERE loan_no = $loanNumbr");
                                                $sqlGetCustNameHereRow = $sqlGetCustNameHere->fetch_assoc();
                                                $customer_name = decrypt($sqlGetCustNameHereRow['customer_name']);
                                                $staff_phone = decrypt($sqlGetCustNameHereRow['staff_phone']);
                                                
                                                $hasBal = intval(getCustomerLoanBalance($conn, $loanNumbr));
                
                                                if($hasBal > 0){
                                                    echo "<tr>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowSchedules['loan_no']}'>{$rowSchedules['loan_no']}</a></td>
                                                        <td>" . $customer_name . "</td>
                                                        <td>" . decrypt($rowSchedules["customer_phone"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["loan_amount"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["principal"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["interest"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["loan_installment"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["due_date"]) . "</td>
                                                        <td>" . decrypt($rowSchedules["paid"]) . "</td>";
                                                    echo "</tr>";
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
                                <b>Loans Due Today and Paid</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('paid-today', 'running_loans')" >Export to Excel</button>
                            
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
                                <table id="paid-today" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Loan No.</th>
                                            <th>Customer Name</th>
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
                                        $today1 = encrypt(date("Y-m-d"));
                                        $sqlSchedules1 = "SELECT * FROM loan_schedules WHERE due_date = '$today1' AND paid IS NOT NULL ORDER BY s_no DESC";
                                
                                        $resultSchedules1 = $conn->query($sqlSchedules1);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultSchedules1->num_rows > 0) {
                                            while ($rowSchedules1 = $resultSchedules1->fetch_assoc()) {
                                                $loanNumbr2 = $rowSchedules1['loan_no'];
                                                
                                                $sqlGetCustNameHere2 = $conn->query(" SELECT customer_name FROM loans WHERE loan_no = $loanNumbr2");
                                                $sqlGetCustNameHereRow2 = $sqlGetCustNameHere2->fetch_assoc();
                                                $customer_name2 = decrypt($sqlGetCustNameHereRow2['customer_name']);
                                                
                                                $hasBal2 = intval(getCustomerLoanBalance($conn, $loanNumbr2));
                
                                                if($hasBal2 > 0){
                                                    echo "<tr>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowSchedules1['loan_no']}'>{$rowSchedules1['loan_no']}</a></td>
                                                        <td>" . $customer_name2 . "</td>
                                                        <td>" . decrypt($rowSchedules1["customer_phone"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["loan_amount"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["principal"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["interest"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["loan_installment"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["due_date"]) . "</td>
                                                        <td>" . decrypt($rowSchedules1["paid"]) . "</td>";
                                                    echo "</tr>";
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
                                <button id="prev-page2">Previous Page</button>
                                <span id="page-info2"></span>
                                <button id="next-page2">Next Page</button>
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
                table = document.getElementById('due-today');
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
                table = document.getElementById('paid-today');
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
                const table = document.getElementById('due-today');
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
                const table = document.getElementById('paid-today');
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