<?php
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/emailing.php';
    //require_once __DIR__.'/templates/cryptOtp.php';
    require_once __DIR__.'/templates/standardize_phone.php';
    require_once __DIR__.'/templates/passReset.php';
    require_once __DIR__.'/templates/logger.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/upload_docs.php';
    
    use Dotenv\Dotenv;
    use IntaSend\IntaSendPHP\Collection;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: /login'); // Redirect to the login page
        exit;
    }
    

    $username = $_SESSION['username'];
    $username1 = encrypt($username);
    
    $admin = $_SESSION['admin'];
    $limit = (!$admin) ? " AND staff_phone = '$username1'" : "";
    
    $member_no = $_SESSION['member_no'];
    if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
        $access = false;
    } else {
        $access = true;
    }
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    //Filtering params for the table
    $filterColumn = 'status';
    $filterValue = encrypt('active');
    
    //STK PUSH
    function initCollection() {
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $collection = new Collection();
        $collection->init($credentials);
        
        return $collection;
    }
    
    function getInvoiceStatus($invoice_id) {
        // Database credentials
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        //sleep(2);
        
        $status = "SELECT * FROM mpesa_collections WHERE invoice_id='$invoice_id'";
        $resultStatus = $conn->query($status);
        $resultStatus = $resultStatus->fetch_assoc();
        $responseS = $resultStatus['state'];
        $responseR = $resultStatus['failed_reason'];
        $response = [
                'state'=>$responseS,
                'failed_reason' =>$responseR,
            ];
            
        return $response;
    }
    
    function performPaymentRequest($amount, $formatted_phone_number, $api_ref) {
        $collection = initCollection();
        $response = $collection->mpesa_stk_push($amount, $formatted_phone_number, $api_ref);
        return $response;
    }
    
    if (isset($_POST['getInvoiceStatus'])) {
        $invoice_id = $_POST['invoice_idT']; // Retrieve the invoice ID from the form input
        
        // Get the payment status
        $response = getInvoiceStatus($invoice_id);
        
        // Send the JSON-encoded response back to the client
        echo json_encode($response);
        exit;
    }
    
    if (isset($_POST['stkPushed'])) {
        // Retrieve the form data
        $amount = $_POST['amount'];
        $phone_number = $_POST['phone_number'];
        
        // Extract the last 9 digits from the phone number
        $standardizedInput = standardizePhoneNumber($phone_number);
        
        // Add the prefix "254" to the phone number
        $formatted_phone_number = '254' . $standardizedInput;
        
        $api_ref = "MFI-CUSTOMERS"; // You can generate a unique reference for each transaction
        
        // Perform the payment request
        $response = performPaymentRequest($amount, $formatted_phone_number, $api_ref);
        
        // Get the invoice ID from the response
        $invoice = $response->invoice;
        $invoice_id = $invoice->invoice_id;
    }
    
    
    //Enter customer details in database
    if(isset($_POST['submitNewCustomer'])){
        $name1 = $_POST['customer-name'];
        $phone1 = $_POST['customer-phone'];
        $customer_idno1 = $_POST['customer-idno']; 
        $email1 = $_POST['customer-email'];
        $joinDate1 = $_POST['joinDate'];
        $location = $_POST['location1'];
        $customerOwner1 = $_POST['staff-phone'];
        $customerOwner1x= explode(" - ", $customerOwner1);
        $customerOwner = $customerOwner1x[1];
        
        $name = encrypt($name1);
        $phone = encrypt($phone1);
        $customer_idno = encrypt($customer_idno1);
        $email = encrypt($email1);
        $joinDate = encrypt($joinDate1);
        //$mpesaCode = encrypt($mpesaCode1);
        $customerOwner1 = encrypt($customerOwner);
        
        $locality = "customer_docs";
        
        $documentName1='id-front';
        $loanFormName1 = $documentName1 . $phone1;
        $documentName2='id-back';
        $loanFormName2 = $documentName2 . $phone1;
        $documentName3='passport-pic';
        $loanFormName3 = $documentName3 . $phone1;
        $documentName4='contract';
        $loanFormName4 = $documentName4 . $phone1;
        
        $idFront = uploadDocs($documentName1, $locality, $loanFormName1);
        $idBack = uploadDocs($documentName2, $locality, $loanFormName2);
        $passportPic = uploadDocs($documentName3, $locality, $loanFormName3);
        $contract = uploadDocs($documentName4, $locality, $loanFormName4);
        
        if ($idFront && $idBack && $passportPic && $contract) {
            $path_id_front = encrypt($idFront);
            $path_id_back = encrypt($idBack);
            $path_passport_pic = encrypt($passportPic);
            $path_contract = encrypt($contract);
            
            $statusAa = 'active';
            $statusAa = encrypt($statusAa);
            
            $sqlNewcustomer = $conn->prepare("INSERT INTO customers (customer_name, customer_phone, customer_idno, customer_email, joinDate, ID_front, ID_back, passport_pic, contract, location_name, status, mpesa_registration_code, staff_phone) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $sqlNewcustomer->bind_param("sssssssssssss", $name, $phone, $customer_idno, $email, $joinDate, $path_id_front, $path_id_back, $path_passport_pic, $path_contract, $location, $statusAa, $mpesaCode, $customerOwner1);
             
            if ($sqlNewcustomer->execute() === TRUE) {
                
                header("Location: customers");
            } else {
                //echo "Error: " . $sqlNewcustomer . "<br>" . $conn->error;
                //echo "Error: " . $sqlStore . "<br>" . $conn->error;
            }
        } else {
            echo "Error creating customer.";
        }

    }
    
    //Enter customer details in database 
    if(isset($_POST['submitEditCustomer'])){
        $customerNum = $_POST['customer_details0'];
        $no = explode(" - ", $customerNum);
        $customer_no = $no[0];
        $name1 = $_POST['customer-name0'];
        $phone1 = $_POST['customer-phone0'];
        $customer_idno1 = $_POST['customer-idno0'];
        $email1 = $_POST['customer-email0'];
        $joinDate1 = $_POST['joinDate0'];
        $location = $_POST['location0'];
        $status = $_POST['customer-status0'];
        
        $name = encrypt($name1);
        $phone = encrypt($phone1);
        $customer_idno = encrypt($customer_idno1);
        $email = encrypt($email1);
        $joinDate = encrypt($joinDate1);
        
        $locality = "customer_docs";
        
        $documentName1='id-front0';
        $loanFormName1 = $documentName1 . $phone1 . 'No' . $customer_no;
        $documentName2='id-back0';
        $loanFormName2 = $documentName2 . $phone1 . 'No' . $customer_no;
        $documentName3='passport-pic0';
        $loanFormName3 = $documentName3 . $phone1 . 'No' . $customer_no;
        $documentName4='contract0';
        $loanFormName4 = $documentName4 . $phone1 . 'No' . $customer_no;
        
        $idFront = uploadDocs($documentName1, $locality, $loanFormName1);
        $idBack = uploadDocs($documentName2, $locality, $loanFormName2);
        $passportPic = uploadDocs($documentName3, $locality, $loanFormName3);
        $contract = uploadDocs($documentName4, $locality, $loanFormName4);
        
        if ($idFront && $idBack && $passportPic && $contract) {
            $path_id_front0 = encrypt($idFront);
            $path_id_back0 = encrypt($idBack);
            $path_passport_pic0 = encrypt($passportPic);
            $path_contract0 = encrypt($contract);
            
            $statusAa = $status;
            $statusAa = encrypt($statusAa);
            
            //get the documents path from the db table
            $sqlGetDocPath = $conn->query("SELECT * FROM customers WHERE customer_no='$customer_no'");
            $sqlGetDocPathRow = $sqlGetDocPath->fetch_assoc();
            
            $path_id_front1 = $sqlGetDocPathRow['ID_front'];
            $path_id_back1 = $sqlGetDocPathRow['ID_back'];
            $path_passport_pic1 = $sqlGetDocPathRow['passport_pic'];
            $path_contract1 = $sqlGetDocPathRow['contract'];
            
            $path_id_front = ($idFront === "empty") ? $path_id_front1 : $path_id_front0;
            $path_id_back = ($idBack === "empty") ? $path_id_back1 : $path_id_back0;
            $path_passport_pic = ($passportPic === "empty") ? $path_passport_pic1 : $path_passport_pic0;
            $path_contract = ($contract === "empty") ? $path_contract1 : $path_contract0;
            
            $sqlNewcustomer = $conn->prepare("UPDATE customers SET customer_name=?, customer_phone=?, customer_idno=?, customer_email=?, joinDate=?, ID_front=?, ID_back=?, passport_pic=?, contract=?, location_name=?, status=? WHERE customer_no=?");
            $sqlNewcustomer->bind_param("ssssssssssss", $name, $phone, $customer_idno, $email, $joinDate, $path_id_front, $path_id_back, $path_passport_pic, $path_contract, $location, $statusAa, $customer_no);
            
            if ($sqlNewcustomer->execute() === TRUE) {
                $conn->query("UPDATE loan_applications SET customer_phone = '$phone' WHERE customer_no='$customer_no'");
                $conn->query("UPDATE loans SET customer_phone = '$phone' WHERE customer_no='$customer_no'");
                $conn->query("UPDATE loan_schedules SET customer_phone = '$phone' WHERE customer_no='$customer_no'");
                
                header("Location: customers");
            } else {
                //echo "Error: " . $sqlNewcustomer . "<br>" . $conn->error;
                //echo "Error: " . $sqlStore . "<br>" . $conn->error;
            }
        } else {
            echo "Error updating customer.";
        }
    }
    
    if(isset($_POST['filtering'])){
        $filterColumn = $_POST['filterBy'];
        $filterValue = ($filterColumn === 'location_name') ? $_POST['filterWith'] : encrypt($_POST['filterWith']);
        
    }
    
    if(isset($_POST['uploadCustomers'])){
        //Strip excel file, get name, phone, location and insert into members
        // Prepare the SQL statement for inserting records
        $insertStatement = $conn->prepare("INSERT INTO customers (customer_name, customer_phone, customer_idno, customer_email, joinDate, ID_front, ID_back, passport_pic, contract, location_name, status) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        
        // Prepare the SQL statement for checking if phone exists
        $checkPhoneStatement = $conn->prepare("SELECT * FROM customers WHERE customer_phone = ?");
        
        // Read the Excel file
        $excelData = array_map('str_getcsv', file($_FILES['customerList']['tmp_name']));
        
        // Flag variable to track if it's the first row
        $isFirstRow = true;
        
        // Iterate through each row of the Excel file
        foreach ($excelData as $row) {
            // Skip the first row (header)
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }
        
            $name = encrypt($row[0]);
            $phone = encrypt($row[1]);
            $customer_idno = encrypt($row[2]);
            $email = encrypt($row[3]);
            $joinDate = encrypt($row[4]);
            $path_id_front = encrypt($row[5]);
            $path_id_back = encrypt($row[6]);
            $path_passport_pic = encrypt($row[7]);
            $path_contract = encrypt($row[8]);
            $location = $row[10];
            $statusAa = encrypt($row[9]);

            // Check if the phone number already exists in the database
            $checkPhoneStatement->bind_param("s", $phone);
            $checkPhoneStatement->execute();
            $result = $checkPhoneStatement->get_result(); // Get the result set
        
            if ($result->num_rows === 0) { // If phone number doesn't exist, insert the record
                $insertStatement->bind_param("sssssssssss", $name, $phone, $customer_idno, $email, $joinDate, $path_id_front, $path_id_back, $path_passport_pic, $path_contract, $location, $statusAa);
                $insertStatement->execute();
            } else {
                // echo "Skipped record for $name with phone $phone as it already exists\n";
            }
        }
        
        // Redirect after all inserts are done
        header("Location: customers");
    }
        
    
    
    
?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Customer Register</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php include "templates/header-admins1.php" ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                //Load processing gif
                $('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                //process payment status check
              $('#form1').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                
                // Make an AJAX request to the PHP script
                $.ajax({
                  url: '',
                  type: 'POST',
                  data: { getInvoiceStatus: true, invoice_idT: $('#invoice_id').val() },
                  dataType: 'json',
                  success: function(response) {
                    // Update the status on the page
                    
                    if (response.state === "COMPLETE") {
                      $('#status').text(response.state);
                      // Print link or perform any other action upon completion
                      //$('#back1').show();
                        
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "FAILED") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "RETRY") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else {
                      // Display the loading GIF
                      //$('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                    }
                  },
                  error: function() {
                    alert('An error occurred while retrieving the invoice status.');
                    $('#status').text('Error while processing');
                    clearInterval(statusInterval);
                  }
                });
              });
            
              // Check the status every 5 seconds
              var statusInterval = setInterval(function() {
                $('#form1').submit();
              }, 5000);
            });

        </script>
    </head>
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Customer Register
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li > 
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#newCustomerModal">Add New Customer</button>
                            </li>
                            <li <?php if(!$admin || !$access){ echo "hidden";}?>>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#editCustomerModal">Edit Customer Details</button>
                            </li>
                            <li <?php if($admin !== 2 || !$access){ echo "hidden";}?>>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#uploadCustomersModal">Upload Customers</button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card bg-info shadow" >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Customer Register</b>
                            </div>
                        </div>
                        
                        <div class="card-body ">
                            <div class="row mb-3">
                                <!-- Add a button to export the table to Excel -->
                                <button <?php if(!$admin || !$access){ echo "disabled";}?> type="button" class="btn btn-secondary btn-sm d-inline mr-3 col-auto"  onclick="exportTableToExcel('customers', 'Customers')" >Export to Excel</button>
                            </div>
                            
                            <div <?php if($admin != 2){ echo "hidden";} ?> class="accordion mb-3 " id="accordionExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                    <button class="accordion-button " type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Filter
                                    </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse " data-bs-parent="#accordionExample">
                                      <div class="accordion-body">
                                                <form class="col" action="" method="POST">
                                                    <div class="input-group">
                                                        <div class="row">
                                                            <div class="m-2  col-md-2">Filter By</div>
                                                            <select id="filterBy" name="filterBy" class=" form-select col-md-2 m-2 border-secondary filterBy">
                                                                <option value="status">Select</option>
                                                                <option value="status">Status</option>
                                                                <option value="location_name">Location</option>
                                                                <option value="staff_phone">Staff</option>
                                                            </select>

                                                            <div class=" m-2 col-md-6">
                                                                <label for="filterWith" class="form-label">Type to search Filter..</label>
                                                                <input class="form-control" list="datalistOptions11f" id="filterWith" name="filterWith" placeholder="Type to search Filter.." autocomplete="off">
                                                                <datalist id="datalistOptions11f">
                                                                    <!--populate depending on the filterBy selected-->
                                                                </datalist>
                                                            </div>
                                                            <input class="m-3 col-md-2 btn btn-sm btn-info" type="submit" id="filtering" name="filtering" value="Filter" >
                                                        </div>
                                                    </div>
                                                </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="customers-search" onkeyup="searchTable()" placeholder="Search by name or phone.."  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size">Rows per page:</label>
                                <select id="page-size">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="customers" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Customer No.</th>
                                            <th>Customer Name</th>
                                            <th>Customer Phone</th>
                                            <th>National ID No.</th>
                                            <th>Customer Email</th>
                                            <th>Joined Date</th>
                                            <th>Status</th>
                                            <th>Branch</th>
                                            <th>Customer Owner</th>
                                            <th>ID Front</th>
                                            <th>ID Back</th>
                                            <th>Passport Pic</th>
                                            <th>Contract</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php    
                                            
                                            function populateTable($conn, $filterColumn, $filterValue, $limit){
                                                $activeS = "active";
                                                $activeS = encrypt($activeS);
                                                
                                                $sqlcustomerTable = "SELECT * FROM customers WHERE $filterColumn='$filterValue' $limit ORDER BY customer_no DESC";
                                                $result = $conn->query($sqlcustomerTable);
                                        
                                                // Loop through the table data and generate HTML code for each row
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                    
                                                    echo "<tr>";
                                                        echo "<td><a class='btn btn-sm btn-info' href='/loan/customer/?cno={$row['customer_no']}'>{$row['customer_no']}</a></td>";
                                                        echo "<td>" . decrypt($row["customer_name"]) . "</td>";
                                                        echo "<td>" . decrypt($row["customer_phone"]) . "</td>";
                                                        echo "<td>" . decrypt($row["customer_idno"]) . "</td>";
                                                        echo "<td>" . decrypt($row["customer_email"]) . "</td>";
                                                        echo "<td>" . decrypt($row["joinDate"]) . "</td>";
                                                        echo "<td>" . decrypt($row["status"]) . "</td>";
                                                        echo "<td>" . $row["location_name"] . "</td>";
                                                        echo "<td>" . decrypt($row["staff_phone"]) . "</td>";
                                                        echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["ID_front"]) . "' download>Download</a></td>";
                                                        echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["ID_back"]) . "' download>Download</a></td>";
                                                        echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["passport_pic"]) . "' download>Download</a></td>";
                                                        echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["contract"]) . "' download>Download</a></td>";
                                                        echo "</tr>"; // download link
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='4'>No results found.</td></tr>";
                                                }
                                            }
                                            
                                            populateTable($conn, $filterColumn, $filterValue, $limit);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                <button id="prev-page">Previous Page</button>
                                <span id="page-info"></span>
                                <button id="next-page">Next Page</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    
                    <!--Application modals -->
                    <div class="modal fade" id="newCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New Customer</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Customer name" type="text" name="customer-name"  id="customer-name" autocomplete="off" required>
                                            <label for="customer-name"> Customer Name: </label>
                                        </div> 
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Customer Phone Number" type="number" name="customer-phone" id="customer-phone" autocomplete="off" required>
                                            <label for="customer-phone"> Customer Phone:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder=" National ID No.:" type="number" name="customer-idno" id="customer-idno" autocomplete="off" required>
                                            <label for="customer-idno"> National ID No.:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Customer Email" type="email" name="customer-email"  id="customer-email" autocomplete="off" required>
                                            <label for="customer-email"> Customer Email:</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location1" class="form-label">Branch</label>
                                            <select class="form-select" name="location1" id="location1" required autocomplete="off" >
                                                <?php
                                                    // Retrieve customer email addresses from the customer table
                                                    $sqlLocation = "SELECT * FROM location";
                                                    $resultLocation = $conn->query($sqlLocation);
                                                    
                                                    if($resultLocation->num_rows > 0){
                                                        while ($rowLocation = $resultLocation->fetch_assoc()) {
                                                            $location = $rowLocation['location_name'];
                                                            echo "<option value=\"$location\">$location</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Joined Date" type="date" name="joinDate" id="joinDate" required  autocomplete="off" >
                                            <label for="joinDate"> Joined Date:</label>
                                        </div>

                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="staff-phone" class="form-label">Customer owner..</label>
                                            <input class="form-control" list="datalistOptions4" id="staff-phone" name="staff-phone" placeholder="Type to search Staff.." autocomplete="off" required>
                                            <datalist id="datalistOptions4">
                                                <?php
                                                    $activeStatus = encrypt('active');
                                                    $sqlStaff2 = "SELECT * FROM staff WHERE status = '$activeStatus' AND staff_no > 0";
                                                    $resultStaff2 = $conn->query($sqlStaff2);
                                                    
                                                    // Generate dropdown options from staff email addresses
                                                    while ($rowStaff2 = $resultStaff2->fetch_assoc()) {
                                                        $userphone21 = decrypt($rowStaff2['staff_phone']);
                                                        $staff_no21 = $rowStaff2['staff_no'];
                                                        $staff_name21 = decrypt($rowStaff2['staff_name']);
                                                        
                                                        $combinedValue = "$staff_name21 - $userphone21";
                                                        echo "<option value=\"$combinedValue\"></option>";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        <div class="mb-3">
                                            <h3 class="form-label"> Customer Docs: <small>(Images, PDF and Docs Only)</small> </h3>
                                            
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="file" name="id-front" id="id-front" id="image" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                                <label for="id-front"> Upload ID (Front):</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="file" name="id-back" id="id-back" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                <label for="id-back"> Upload ID (Back):</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="file" name="passport-pic" id="passport-pic" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                                <label for="passport-pic"> Customer Passport Photo:</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="file" name="contract" id="contract" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                <label for="contract"> Customer Data Form:</label>
                                            </div>
                                        </div>
                                        
                                        <input hidden class="button" type="submit" value="Add New Customer" name="submitNewCustomer" id="addCustomerBtn"style="width: 100px; height: 30px; padding: 4px;" >
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnCheckCustomerExists('addCustomerBtn')">Add Customer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="editCustomerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Customer</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="" enctype="multipart/form-data">
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="customer_details0" class="form-label">Type to search Customer..</label>
                                            <input class="form-control" list="datalistOptions11" id="customer_details0" name="customer_details0" placeholder="Type to search Customer.." autocomplete="off">
                                            <datalist id="datalistOptions11">
                                                <?php
                                                    // Retrieve customer email addresses from the customer table
                                                    $statusA1 = 'active';
                                                    $statusA1 = encrypt($statusA1);
                                                    
                                                    // $sqlcustomer1 = "SELECT * FROM customers WHERE status='$statusA1'";
                                                    $sqlcustomer1 = "SELECT * FROM customers";
                                                    $resultcustomer = $conn->query($sqlcustomer1);
                                                    if($resultcustomer -> num_rows > 0){
                                                        // Generate dropdown options from customer email addresses
                                                        while ($rowcustomer = $resultcustomer->fetch_assoc()) {
                                                            $customerNo1 = $rowcustomer['customer_no'];
                                                            $name1 = decrypt($rowcustomer['customer_name']);
                                                            $email = decrypt($rowcustomer['customer_phone']);
                                                            $combNameEmail = "$customerNo1 - $name1 - $email";
                                                            
                                                            echo "<option value=\"$combNameEmail\">";
                                                        }
                                                    } else {
                                                        echo "<option value='No Customer found'>";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <div hidden="hidden" id="customer-details-part">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="customer name" type="text" name="customer-name0" id="customer-name0"  required>
                                                <label for="customer-name"> Customer Name: </label>
                                            </div> 
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="customer Phone Number" type="number" name="customer-phone0"  id="customer-phone0" required>
                                                <label for="customer-phone"> Customer Phone:</label>
                                            </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder=" National ID No.:" type="number" name="customer-idno0" id="customer-idno0"  required>
                                            <label for="customer-idno0"> National ID No.:</label>
                                        </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="customer Email" type="email" name="customer-email0" id="customer-email0"  required>
                                                <label for="customer-email"> Customer Email:</label>
                                            </div>

                                            <div class=" mb-3">
                                                <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                <label for="customer_details0" class="form-label">Type to search Customer Status..</label>
                                                <input class="form-control" list="datalistOptions110" id="customer-status0" name="customer-status0" placeholder="Type to search Customer Status.." autocomplete="off">
                                                <datalist id="datalistOptions110">
                                                    <option value="active">
                                                    <option value="blacklisted">
                                                </datalist>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Branch</label>
                                                <select class="form-select" name="location0" id="location0" required>
                                                    <?php
                                                        // Retrieve customer email addresses from the customer table
                                                        $sqlLocation = "SELECT * FROM location";
                                                        $resultLocation = $conn->query($sqlLocation);
                                                        
                                                        if($resultLocation->num_rows > 0){
                                                            while ($rowLocation = $resultLocation->fetch_assoc()) {
                                                                $location = $rowLocation['location_name'];
                                                                echo "<option value=\"$location\">$location</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="Joined Date" type="date" name="joinDate0" id="joinDate0" required>
                                                <label for="joinDate"> Joined Date:</label>
                                            </div>
                                        
                                            <div class="mb-3" id="customer-docs">
                                                <h3 class="form-label"> Customer Docs: <small>(Images, PDF and Docs Only)</small> </h3>
                                                
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="id-front0" id="id-front0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                                    <label for="id-front"> Upload ID (Front):</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="id-back0" id="id-back0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                    <label for="id-back"> Upload ID (Back):</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="passport-pic0" id="passport-pic0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                                    <label for="passport-pic"> Customer Passport Photo:</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="contract0" id="contract0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                    <label for="contract"> Customer Data Form:</label>
                                                </div>
                                            </div>
                                        </div>

                                        <input hidden class="button" type="submit" value="Edit Customer" name="submitEditCustomer" id="editCustomerBtn"style="width: 100px; height: 30px; padding: 4px;" >
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="editClickCheck('editCustomerBtn')">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="uploadCustomersModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Upload Customer List</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form  method="POST" action="" enctype="multipart/form-data">
                                            <div class="card shadow border-bottom border-black" id="upload"> 
                                                <div class="card-header">
                                                    <h3> Upload Customer list (Excel file)</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <div class="form-floating mb-3">
                                                            <input class="form-control" type="file" name="customerList" id="customerList" > 
                                                            <label for="customerList">Upload excel (csv) file:</label>
                                                        </div>
                                                        <span> Download template <a href="fileStore/customerslist.csv"> Here </a> NB: (Save as CSV, do not change format.)</span>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        <input hidden class="button" type="submit" value="Edit Customer" name="uploadCustomers" id="uploadCustomers"style="width: 100px; height: 30px; padding: 4px;" >
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('uploadCustomers')">Upload</button>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>  <!--End of containerfluid -->
            </div>
            <div class="card-footer text-center text-dark ">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div>
        
        <?php
             $conn->close();
        ?> 
            <!-- Add a script to open Add New customer popup -->
        <script>
            function btnClick(btnId) {
                document.getElementById(btnId).click();
            }
            
            //check if member has balance before exit
            function btnClickCheck1(btnId){
                var memberPhone = document.getElementById('member-details').value;
                
                const newData = {
                        phone: memberPhone,
                        check:"memberHasBalance"
                    };
    
                // Send an AJAX request to a PHP script to update the data
                fetch('templates/checkMemberExists.php', {
                    method: 'POST',
                    body: JSON.stringify(newData),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        btnClick(btnId);
                    } else {
                        var message = data.message;
                        
                        alert("Member still has a balance in " + message + ". Clear the balances and retry.");
                    }
                });
            }
            
            //check if customer exists to prevent double entry
            function btnCheckCustomerExists(btnId){
                var memberPhone = document.getElementById('customer-phone').value;
                
                const newData = {
                        phone: memberPhone,
                        check:"customerExists"
                    };
    
                // Send an AJAX request to a PHP script to update the data
                fetch('templates/checkMemberExists.php', {
                    method: 'POST',
                    body: JSON.stringify(newData),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        var idFrontInput = document.getElementById("id-front");
                        var idBackInput = document.getElementById("id-back");
                        var passportPicInput = document.getElementById("passport-pic");
                        var contractInput = document.getElementById("contract");
                        
                        //var filesOkay = validateForm(idFrontInput, idBackInput, passportPicInput, contractInput);
                        
                        //if(filesOkay === true){
                            btnClick(btnId);
                        //}
                        
                    } else {
                        var message = data.message;
                        
                        if(message === 'active'){
                            alert("Customer already exists. Status " + message);
                        } else {
                            alert("Customer already exists. Status " + message + ". Reactivate member.");
                        }
                    }
                });
            }
            
            //check files being uploaded on edit function
            function editClickCheck(btnId){
                var idFrontInput = document.getElementById("id-front0");
                var idBackInput = document.getElementById("id-back0");
                var passportPicInput = document.getElementById("passport-pic0");
                var contractInput = document.getElementById("contract0");
                
                //var filesOkay = validateForm(idFrontInput, idBackInput, passportPicInput, contractInput);
                
                //if(filesOkay === true){
                    btnClick(btnId);
                //}
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                function populateFilters(){
                    
                    var filterByVal = document.getElementById('filterBy').value;
                    
                    fetch('templates/checkMemberDetails.php', {
                        method: 'POST',
                        body: JSON.stringify({ check: "getFilters", data: filterByVal }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Replace the existing datalist options with the updated ones
                        document.getElementById('datalistOptions11f').innerHTML = data;
                    });
                    
                }
                
                document.getElementById('filterBy').addEventListener('change', populateFilters);
            });

            
            //Validate file being upload on selection
            document.addEventListener('DOMContentLoaded', function() {
                
                function validateFormNow1(){
                    var fileInput = document.getElementById('id-front0');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('id-front0').value = '';
                    }
                }
                
                function validateFormNow2(){
                    var fileInput = document.getElementById('id-back0');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('id-back0').value = '';
                    }
                }
                
                function validateFormNow3(){
                    var fileInput = document.getElementById('passport-pic0');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('passport-pic0').value = '';
                    }
                }
                
                function validateFormNow4(){
                    var fileInput = document.getElementById('contract0');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('contract0').value = '';
                    }
                }
                
                function validateFormNow11(){
                    var fileInput = document.getElementById('id-front');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('id-front').value = '';
                    }
                }
                
                function validateFormNow22(){
                    var fileInput = document.getElementById('id-back');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('id-back').value = '';
                    }
                }
                
                function validateFormNow33(){
                    var fileInput = document.getElementById('passport-pic');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('passport-pic').value = '';
                    }
                }
                
                function validateFormNow44(){
                    var fileInput = document.getElementById('contract');
                    var checked = validateForm(fileInput);
                    
                    if (checked === false){
                        document.getElementById('contract').value = '';
                    }
                }
                
                document.getElementById('id-front0').addEventListener('change', validateFormNow1);
                document.getElementById('id-back0').addEventListener('change', validateFormNow2);
                document.getElementById('passport-pic0').addEventListener('change', validateFormNow3);
                document.getElementById('contract0').addEventListener('change', validateFormNow4);
                document.getElementById('id-front').addEventListener('change', validateFormNow11);
                document.getElementById('id-back').addEventListener('change', validateFormNow22);
                document.getElementById('passport-pic').addEventListener('change', validateFormNow33);
                document.getElementById('contract').addEventListener('change', validateFormNow44);
            });
            
            //check the files being uploaded are okay
            function validateForm(fileInput) {
                // Check file types
                if (!validateFileType(fileInput)) {
                    alert("Please select only JPG, JPEG, PNG, GIF, PDF, DOC, or DOCX files.");
                    return false;
                }
            
                // Check file sizes
                if (!validateFileSize(fileInput)) {
                    alert("File size exceeds the limit of 500KB.");
                    return false;
                }
            
                return true;
            }
            
            function validateFileType(input) {
                var validExtensions = ["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx"];
                var fileExtension = input.value.split('.').pop().toLowerCase();
                return validExtensions.includes(fileExtension);
            }
            
            function validateFileSize(input) {
                //return input.files[0].size <= 500000; //500KB = 500000
                return true;
            }
            
            //Populate customer edit details 
            document.addEventListener('DOMContentLoaded', function() {
                
                function populatecustomerDetails(){
                    
                    var customerIdNo = document.getElementById('customer_details0').value;
                    
                    if(customerIdNo === '0'){
                        //do nothing
                    } else {
                        const newData1 = {
                            data: customerIdNo,
                                check: "getCustomerDetails"
                        };
                        
                        // Send an AJAX request to a PHP script to update the data
                        return fetch('templates/checkMemberDetails.php', {
                            method: 'POST',
                            body: JSON.stringify(newData1),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success === true) {
                                var name = data.name;
                                var phone = data.phone;
                                var idno = data.idno;
                                var email = data.email;
                                var joined = data.joined;
                                var location = data.location1;
                                var statusNow = data.statusNow;
                                
                                document.getElementById('customer-name0').value = name;
                                document.getElementById('customer-phone0').value = phone;
                                document.getElementById('customer-idno0').value = idno;
                                document.getElementById('customer-email0').value = email;
                                document.getElementById('joinDate0').value = joined;
                                document.getElementById('location0').value = location; 
                                document.getElementById('customer-status0').value = statusNow;
                                
                                document.getElementById('customer-details-part').removeAttribute("hidden");
                            }
                        });
                    }
                }
                
                document.getElementById('customer_details0').addEventListener('change', populatecustomerDetails);
            });
            
        </script>
        <script>
            //Add a script to search the table --> 
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('customers-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('customers');
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
            
            //Pagination
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('customers');
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
                    document.getElementById('page-info').textContent = `Page ${currentPage} of Page ${totalPages}`;
                }
                
                function updateButtons() {
                    document.getElementById('prev-page').disabled = currentPage === 1;
                    document.getElementById('next-page').disabled = currentPage === Math.ceil(rows.length / pageSize);
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
                    const selectedValue = document.getElementById('page-size').value;

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
    
                document.getElementById('next-page').addEventListener('click', nextPage);
                document.getElementById('prev-page').addEventListener('click', prevPage);
                document.getElementById('page-size').addEventListener('change', changePageSize);
            });
            
        </script>

        <?php //include 'templates/sessionTimeoutL.php'; ?>

        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>                                   