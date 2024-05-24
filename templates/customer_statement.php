<?php
    require_once __DIR__.'/../vendor/autoload.php';
    require_once __DIR__.'/../templates/crypt.php';

    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_GET['lno']) || $_GET['lno'] === "" || $_GET['lno'] < 1) {
        header('Location: /loans'); // Redirect to the login page
        exit;
    } else {
        $loanNo = $_GET['lno'];
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
    
    //pull customer no for use
    $sqlGetCustNo = $conn->query("SELECT * FROM loans WHERE loan_no='$loanNo'");
    $getCustNoRow = $sqlGetCustNo->fetch_assoc();
    $custNo = $getCustNoRow['customer_no'];
    
    //get customer info
    $sqlGetCustInfo = $conn->query("SELECT * FROM customers WHERE customer_no='$custNo'");
    $custNoRow = $sqlGetCustInfo->fetch_assoc();
    
    $loanStatusThis = decrypt($getCustNoRow['loan_status']);
    $loanTypeXX = decrypt($getCustNoRow['loan_type']);
    
    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement</title>
    <style>
        /* Your custom CSS styles go here */
        body {
            font-family: Garamond; /*Arial, sans-serif*/
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .card-title {
            margin: 0;
            font-size: 16px;
        }
        .card-body1 {
            padding: 20px;
        }
        .card-footer {
            background-color: #f8f9fa;
            padding: 10px 20px;
            /*border-top: 1px solid #dee2e6;*/
            margin-bottom: 5px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        
        .table-container {
            max-height: auto; /* Set a maximum height for the container */
            overflow-y: auto; /* Enable vertical scrolling */
        }
        
        /* Hide the button when printing */
        @media print {
            .no-print {
                display: none;
            }
        }
        
        .no-print{
            background-color: grey;
        }
    </style>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>


</head>
<body>
    <div class="container">
        <div class="card">
            <button class="no-print no-pdf" onclick="printPage()">Print Statement</button>
            <button class="no-print no-pdf" onclick="saveAsPDF()">Download</button>
            
            <div class="card-header">
                <h1 class="card-title1">Truesales Capital Ltd</h1>
                <div style="display: flex; justify-content: space-between;">
                    <h3 class="card-title1">Customer Statement</h3>
                    <h5>Date: <?php echo date("Y-m-d H:i:s"); ?></h5>
                </div>
            </div>
            <div class="card-body">
                <div class="card">
                    <h5 class="card-title">Customer Information</h5>
                    <div class="card-body1">
                        <div style="display: flex; justify-content: space-between;">
                            <div>Customer Name: <?php echo decrypt($custNoRow['customer_name']); ?></div>
                            <div>Customer Phone: <?php echo decrypt($custNoRow['customer_phone']); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>Customer Email: <?php echo decrypt($custNoRow['customer_email']); ?></div>
                            <div>Branch: <?php echo $custNoRow['location_name']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h5 class="card-title">Loan Information</h5>
                    <div class="card-body1">
                        <!-- Display relevant statistics here -->
                        <div style="display: flex; justify-content: space-between;">
                            <div>Loan No: <?php echo $getCustNoRow['loan_no']; ?></div>
                            <div>Application Date: <?php echo decrypt($getCustNoRow['loan_applicationDate']); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>Loan Product: <?php echo decrypt($getCustNoRow['loan_product']); ?></div>
                            <div>Loan Amount: <?php echo number_format(decrypt($getCustNoRow['loan_amount']), 0, '.', ','); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>Loan Interest: <?php echo decrypt($getCustNoRow['loan_interest']); ?>% p.m.</div>
                            <div>Gross Loan: <?php echo number_format(decrypt($getCustNoRow['gross_loan']), 0, '.', ','); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>No. of Installments: <?php echo decrypt($getCustNoRow['no_of_installments']); ?></div>
                            <div>Loan Installments: <?php echo number_format(decrypt($getCustNoRow['loan_installment']), 0, '.', ','); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>First Repayment Date: <?php echo decrypt($getCustNoRow['firstRepaymentDate']); ?></div>
                            <div>Last Paid Date: <?php echo decrypt($getCustNoRow['last_paymentDate']); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>Loan Balance: <?php echo number_format(decrypt($getCustNoRow['loan_balance']), 0, '.', ','); ?></div>
                            <div>Arrears Amount: <?php echo number_format(decrypt($getCustNoRow['amount_inArrears']), 0, '.', ','); ?></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>Loan Category: <?php echo decrypt($getCustNoRow['loan_classification']); ?></div>
                            <div>Days in Arrears: <?php echo number_format(decrypt($getCustNoRow['days_inArrears']), 0, '.', ','); ?></div>
                        </div>
                    </div>

                    <h5 class="card-title">Loan Transactions</h5>
                    <div class="card-body table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Posting Date</th>
                                    <th>Description</th>
                                    <th>Description No.</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                    <th>Payment Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlPaid = "SELECT * FROM loan_transactions WHERE loan_no='$loanNo' ORDER BY s_no ASC";
                                $resultPaid = $conn->query($sqlPaid);
                                if ($resultPaid->num_rows > 0) {
                                    while ($rowLoans2 = $resultPaid->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . decrypt($rowLoans2["posting_date"]) . "</td>
                                            <td>" . decrypt($rowLoans2["posting_description"]) . "</td>
                                            <td>" . decrypt($rowLoans2["description_no"]) . "</td>
                                            <td>" . number_format(decrypt($rowLoans2["debit"]), 0, '.', ',') . "</td> 
                                            <td>" . number_format(decrypt($rowLoans2["credit"]), 0, '.', ',') . "</td>
                                            <td>" . number_format(decrypt($rowLoans2["running_balance"]), 0, '.', ',') . "</td>
                                            <td>" . decrypt($rowLoans2["payment_mode"]) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No results found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    
                </div>
            
                <div class="card-footer1" style="position:relative; bottom:0; font-size:14px; margin-top:20px;">
                    Any disputes should be forwarded to management within 7 days for resolution. 
                </div>
            </div>
        </div>
    </div>
    <script>
        function printPage() {
            window.print();
        }
    </script>
    <script>
    
        function saveAsPDF() {
            //hide the buttons
            document.querySelectorAll('.no-print').forEach(function(element) {
                element.setAttribute('hidden', 'hidden');
            });
            
            // Options for html2pdf
            const options = {
                filename: '<?php echo decrypt($custNoRow['customer_phone']) . '-' . $loanNo . '-' . date('YmdHis'); ?>',
                html2canvas: { scale: 2 },
                jsPDF: { 
                    unit: 'in', 
                    format: 'letter', 
                    orientation: 'portrait' 
                },
                repeatTableHeader: true
            };

            // Capture the body content and save as PDF
            html2pdf().from(document.body).set(options).save().then(function(){
                document.querySelectorAll('.no-print').forEach(function(element) {
                    element.removeAttribute('hidden');
                });
            });
        }
        

    </script>
</body>
</html>
