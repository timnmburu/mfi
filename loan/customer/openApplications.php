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
    
    if (!isset($_GET['cno']) || $_GET['cno'] === "" || $_GET['cno'] < 1) {
        header('Location: /customers'); // Redirect to the login page
        exit;
    } else {
        $custNo = $_GET['cno'];
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
    
    //get customer info
    $sqlGetCustInfo = $conn->query("SELECT * FROM customers WHERE customer_no='$custNo'");
    $custNoRow = $sqlGetCustInfo->fetch_assoc();
    
    //get customer metrics
    $sqlGetLoan = $conn->query("SELECT * FROM loans WHERE customer_no='$custNo' ORDER BY loan_no DESC");
    if($sqlGetLoan->num_rows > 0){
        $noOfLoans = $sqlGetLoan->num_rows;
        $volumeDisbursed = 0;
        $loan_balance = 0;
        $loan_payments = 0;
        $worstDaysInArrears = 0;
        $classification = 0 ; 
        
        while($sqlGetLoanRow = $sqlGetLoan->fetch_assoc()){
            $volumeDisbursed += intval(decrypt($sqlGetLoanRow['loan_amount']));
            $loan_payments += intval(decrypt($sqlGetLoanRow['loan_payments'])); 
            $loan_balance += intval(decrypt($sqlGetLoanRow['loan_balance']));
            
            $worstDaysInArrearsNew = decrypt($sqlGetLoanRow['worst_daysInArrears']);
            
            if($worstDaysInArrearsNew > $worstDaysInArrears){
                $worstDaysInArrears = $worstDaysInArrearsNew;
            }
            
            $worstClassificationNew = decrypt($sqlGetLoanRow['worst_classification']);
            
            if($worstClassificationNew === "Normal"){
                $classificationNew = 0;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            } else if ($worstClassificationNew === "Watch"){
                $classificationNew = 1;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            } else if ($worstClassificationNew === "Substandard"){
                $classificationNew = 2;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            } else if ($worstClassificationNew === "Doubtful"){
                $classificationNew = 3;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            } else if ($worstClassificationNew === "Loss"){
                $classificationNew = 4;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            } else {
                $classificationNew = 0;
                if($classificationNew > $classification){
                    $classification = $classificationNew;
                }
            }
            
        }
        
        if($classification == 0){
            $worstClassification = "Normal";
        } else if ($classification == 1){
            $worstClassification = "Watch";
        } else if ($classification == 2){
            $worstClassification = "Substandard";
        } else if ($classification == 3){
            $worstClassification = "Doubtful";
        } else if ($classification == 4){
            $worstClassification = "Loss";
        }
    } else {
        $noOfLoans = 0;
        $volumeDisbursed = 0;
        $loan_balance = 0;
        $loan_payments = 0;
        $worstDaysInArrears = NULL;
        $worstClassification  = NULL ;
    }
        
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Customer View</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/../../templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/../../templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <div class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark d-flex flex-row">
                <h1 class="mr-3">Customer Full View </h1>
                <a class="btn btn-sm btn-info align-self-center " onclick="window.history.back()">Go Back</a>
            </div>
            <div class="card-body">
                <div class="container-fluid col-12">
                    
                    <div class="col-md-12">
                        <div class="card shadow mt-3">
                            
                            <div class="card-body">
                                <!-- Display relevant statistics here -->
                                <h5 class="card-title text-dark m-2"> Customer Information</h5>
                                <div class="card-body shadow mx-3">
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
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> ID Front: <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['ID_front']); ?> . ">View</a></span>
                                        
                                        <span class="d-block mb-3 col-md-6"> ID Back: <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['ID_back']); ?> . ">View</a></span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6"> Passport Photo:  <a class="btn btn-sm btn-info" href=" <?php echo decrypt($custNoRow['passport_pic']); ?> . ">View</a></span>
        
                                        <span class="d-block mb-2 col-md-6 "> Portfolio Owner: <?php echo decrypt($custNoRow['staff_phone']);   ?></span>
                                    </div>
                                </div>
                                <br>
                                <h5 class="card-title text-dark m-2"> Loan Information</h5>
                                <div class="card-body shadow mx-3">
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6 fw-bold"> Count of Loans:  <?php echo $noOfLoans; ?> </span>
                                        
                                        <span class="d-block mb-3 col-md-6 fw-bold"> Total Disbursed:  <?php echo number_format($volumeDisbursed, 2); ?> </span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6 fw-bold"> Total Payments:  <?php echo number_format($loan_payments, 2); ?> </span>
        
                                        <span class="d-block mb-2 col-md-6 fw-bold"> Total Loan Balance: <?php echo number_format($loan_balance, 2); ?></span>
                                    </div>
                                    <div class="row">
                                        <span class="d-block mb-3 col-md-6 fw-bold"> Worst Days In Arrears:  <?php echo $worstDaysInArrears; ?> </span>
        
                                        <span class="d-block mb-2 col-md-6 fw-bold"> Worst Classification: <?php echo $worstClassification; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                        
                    <!-- //////////////////////////////////Populate the list of loans below//////////////////////////////////////////////// -->
                    <div class="card bg-info shadow" >
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
                                            $sqlGetLoan1 = $conn->query("SELECT * FROM loans WHERE customer_no='$custNo' ORDER BY loan_no DESC");
    
                                            if($sqlGetLoan1->num_rows > 0){
                                                while ($rowLoans1 = $sqlGetLoan1->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td><a class='btn btn-sm btn-info' href='/loan/open/?lno={$rowLoans1['loan_no']}'>{$rowLoans1['loan_no']}</a></td>
                                                        <td>" . $rowLoans1["customer_no"] . "</td>
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