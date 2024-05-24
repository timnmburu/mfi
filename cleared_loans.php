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
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Cleared Loans</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark" >
                Cleared Loans
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
                    
                    <br>
                    
                    <div  class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Cleared Loans</b>
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
                                            <th>Last Pay Date</th>
                                            <th>Status</th>
                                            <th>Branch</th>
                                            <th>Statement</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                        $open = encrypt('Open');
                                        $sqlLoans = "SELECT * FROM loans WHERE loan_status<>'$open' ORDER BY loan_no DESC";
                                
                                        $resultLoans = $conn->query($sqlLoans);
                                
                                        // Loop through the table data and generate HTML code for each row
                                        if ($resultLoans->num_rows > 0) {
                                            while ($rowLoans = $resultLoans->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans['loan_no']}'>{$rowLoans['loan_no']}</a></td>
                                                    <td>" . $rowLoans["customer_no"] . "</td>
                                                    <td>" . decrypt($rowLoans["customer_name"]) . "</td>
                                                    <td>" . decrypt($rowLoans["customer_phone"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_product"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_type"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_amount"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_term"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_interest"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_installment"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_applicationDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_approvalDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["gross_loan"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_payments"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_balance"]) . "</td>
                                                    <td>" . decrypt($rowLoans["last_paymentDate"]) . "</td>
                                                    <td>" . decrypt($rowLoans["loan_status"]) . "</td>
                                                    <td>" . $rowLoans["location_name"] . "</td>";
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

                </div>  <!--End of container fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div> 
        
        <script>
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

        </script>
        
        <?php //include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>