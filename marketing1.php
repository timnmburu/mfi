<?php
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/standardize_phone.php';
    require_once __DIR__.'/templates/sendsms.php';
    require_once __DIR__.'/templates/logger.php';
    require_once __DIR__.'/templates/crypt.php';
    
    use Dotenv\Dotenv;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
     
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        session_unset();
        header('Location: login'); // Redirect to the login page
        exit;   
    }  elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] === false){
        header('Location: /');
    } else {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; 
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
    
    // Initialize SMS MODE to false by default
    $smsMode = '1';
    
    // Check if the Edit button is clicked
    if (isset($_GET['mode'])) {
        // User is authorized to export tables
        $smsMode = $_GET['mode'];;
    } else {
        $smsMode = '1';
    }
    
    // Check if the session username is an admin
    $admin = $_SESSION['admin'];
    
    //Process the SMS to send
    if (isset($_POST['submitSMS'])) {

        $messageTo = $_POST['message'];
        
        $phone = $_POST['phoneNo'];
        
        $standardizedInput1 = standardizePhoneNumber($phone) ;
        
        $recipient1 = '0'. $standardizedInput1;
        
        $phone1 = encrypt($recipient1);
        $sqlGetLocation = $conn->query("SELECT location_name FROM staff WHERE staff_phone='$phone1'");
        if($sqlGetLocation->num_rows > 0){
            $sqlGetLocation1 = $sqlGetLocation->fetch_assoc();
            $location_name = ", Branch " . $sqlGetLocation1['location_name'];
        } else {
            $location_name = '';
        }
        
        $standardizedInput = standardizePhoneNumber($phone) ;
        
        $recipient = '254'. $standardizedInput;
        
        $message = $messageTo . "\n Truesales Credit Ltd $location_name";
        
        $return = sendSMS($recipient, $message);
        
        if($return){
            $action = "Blasted SMS";
            logAction($action);
            
            header("Location: marketing");
        }

        
    } elseif (isset($_POST['submitSMSBulk'])) {

        $messageTo = $_POST['message'];
        
        if (isset($_POST['customerBulk'])) {
            $selectedCustomer = $_POST['customerBulk'];
            $selectedCustomerParts = explode(" - ", $selectedCustomer);
            
            $phone = $selectedCustomerParts[1];
            
            $phone1 = encrypt($phone);
            $sqlGetLocation = $conn->query("SELECT location_name FROM staff WHERE staff_phone='$phone1'");
            $sqlGetLocation1 = $sqlGetLocation->fetch_assoc();
            $location_name = $location_name = ", Branch " . $sqlGetLocation1['location_name'];
            
            $standardizedInput = standardizePhoneNumber($phone) ;
        
            $recipient = '254'. $standardizedInput;
        
            $message = $messageTo . "\n Truesales Credit Ltd $location_name";
        
            $return = sendSMS($recipient, $message);
            
            if($return){
                $action = "Blasted SMS";
                logAction($action);
                
                header("Location: marketing");
            }
        }
        
    } elseif (isset($_POST['submitSMSAll'])) {
        $messageTo = $_POST['message'];
        
        $location_name = ($_POST['location_name'] == 'All')? '': $_POST['location_name'];
        
        $active = encrypt("active");
        // SQL query to fetch customer from the customers table
        $sqlCustomerList1 = "SELECT customer_phone FROM customers WHERE status = '$active' AND location_name='$location_name'";
        $sqlCustomerList2 = "SELECT customer_phone FROM customers WHERE status = '$active' ";
        
        $sqlCustomerList = ($location_name === 'All')? $sqlCustomerList2: $sqlCustomerList1;
        
        // Execute the query
        $result = $conn->query($sqlCustomerList);
        
        // Initialize an empty string to store phone numbers
        $allPhoneNumbers = '';
        $countAllPhoneNos = 1;
        
        // Check if there are any rows returned
        if ($result->num_rows > 0) {
            // Loop through the rows and concatenate formatted phone numbers to the string
            while ($row = $result->fetch_assoc()) {
                // Get the phone number from the current row
                $phoneNumber1 = decrypt($row['customer_phone']);

                $phoneNumber = '254' . substr($phoneNumber1, 1);
                
                $recipient = $phoneNumber;
                
                $message = $messageTo . "\n Truesales Credit Ltd $location_name";
                
               sendSMS($recipient, $message);
            }

        } else {
            $allPhoneNumbers = 'No phone numbers found.';
        }
        
        $action = "Blasted SMS";
        logAction($action);
        
        header("Location: marketing");
    }
        
    //Message balance
    $msgBal = smsBalance($conn);
?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Marketing</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        
        <style>
            .singleMode {
               border:1px solid black; 
               display:flex; 
               width: 100%; 
            }
            
            .bulkMode {
               border:1px solid black; 
               display:flex; 
               width: 100%; 
            }
            
            .allMode {
               border:1px solid black; 
               display:flex; 
               width: 100%; 
            }
            
            .tab-nav {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
            }
            
            .tab-nav li {
                cursor: pointer;
                padding: 10px 20px;
                background-color: #f1f1f1;
                border: 1px solid #ccc;
            }
            
            .tab-nav li.active {
                background-color: #ddd;
            }
            
            /* CSS for the tab content */
            .inputPaymentInfo {
                display: flex;
                flex-direction: column;
            }
            
            .tab-pane {
                display: none;
                padding: 20px;
                border: 1px solid #ccc;
            }
            
            .tab-pane.active {
                display: block;
            }

            @media only screen and (max-width: 768px) {
                
            }
        </style>
        
        <?php include "templates/header-admins1.php" ?> 
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Marketing
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="/mpesa?p=sms" class="dropdown-item border-bottom" >Buy SMS Units</a>
                            </li>
                        </ul>
                    </div>
                    <div class="container-fluid">
                        You can send marketing messages to customers. Just enter the details are required below and blast the SMS.
                        <br>
                        <br>
                        Select Phone Number Source:
                        <ul class="tab-nav row">
                            <li class="col" onclick="changeTab(0)">Enter Phone</li>
                            <li class="col" onclick="changeTab(1)">Select From Customers</li>
                            <li class="col" onclick="changeTab(2)">Send To All Customers</li>
                        </ul>
                    </div>
                    
                    <div class="tab-pane shadow <?php if($smsMode === '1' || !isset($smsMode)){ echo 'active'; } ?>">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                            <form id="submitSMS"  method="POST" action="">
                                <input id="message-bal" value="Message Balance = <?php echo $msgBal; ?>" disabled>
                                <br>
                                <br>
                                <div class="form-floating mb-3">
                                    <input class="form-control" placeholder="Enter phone number 07...or 01..." type="number" name="phoneNo"  required> <br>
                                    <label for="phoneNo">Enter phone number 07...or 01...</label>
                                </div>
                                <div class=" mb-3">
                                    <label for="message">Type your message here...</label>
                                    <textarea class="form-control" rows="3" name="message" id="message1" placeholder = "Type your message here..." required oninput="checkCharacterCount('message1')" ></textarea>
                                </div>
                                <input  id="characterCount1" name="" value=""  disabled>
                                <br><br>
                                <input class="btn btn-success " type="submit" value="Send SMS" name="submitSMS"  />
                            </form>
                        </div>
                    </div>
                    
                    <div class="tab-pane shadow <?php if($smsMode === '2'){ echo 'active'; } ?>">
                        <div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                            <form id="submitSMS"  method="POST" action="">
                                <input id="message-bal" value="Message Balance = <?php echo $msgBal; ?>" disabled>
                                <br>
                                <br>
                                <div class=" mb-3">
                                    <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                    <label for="customerBulk" class="form-label">Type to search Customer..</label>
                                    <input class="form-control" list="datalistOptions11" id="customerBulk" name="customerBulk" placeholder="Type to search Customer.." autocomplete="off">
                                    <datalist id="datalistOptions11">
                                        <?php
                                            // Retrieve customer email addresses from the customer table
                                            $statusA1 = 'active';
                                            $statusA1 = encrypt($statusA1);
                                            
                                            $sqlcustomer1 = "SELECT * FROM customers WHERE status='$statusA1'";
                                            $resultcustomer = $conn->query($sqlcustomer1);
                                            if($resultcustomer -> num_rows > 0){
                                                // Generate dropdown options from customer email addresses
                                                while ($rowcustomer = $resultcustomer->fetch_assoc()) {
                                                    $name1 = decrypt($rowcustomer['customer_name']);
                                                    $email = decrypt($rowcustomer['customer_phone']);
                                                    $combNameEmail = "$name1 - $email";
                                                    
                                                    echo "<option value=\"$combNameEmail\">";
                                                }
                                            } else {
                                                echo "<option value='No Customer found'>";
                                            }
                                        ?>
                                    </datalist>
                                </div>
                                <br> 
                                
                                <div class="mb-3">
                                    <label for="message">Type your message here...</label>
                                    <textarea class="form-control" rows="3" name="message" id="message2" placeholder = "Type your message here..."  required oninput="checkCharacterCount('message2')" ></textarea>
                                </div>
                                <input id="characterCount2" name="" value="" disabled>
                                <br>
                                <br>
                                <input class="btn btn-success" type="submit" value="Send SMS" name="submitSMSBulk"  />
                            </form>
                        </div>
                    </div>
                    
                    <div class="tab-pane shadow <?php if($smsMode === '3'){ echo 'active'; } ?>">
                        <div  class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
                            <form id="submitSMS" method="POST" action="">
                                <input id="message-bal" value="Message Balance = <?php echo $msgBal; ?>" disabled>
                                <br>
                                <br>
                                <!--Select Business -->
                                <?php
                                    if(!$admin){
                                        //show nothing
                                    } else {
                                ?>
                                
                                <div class="mb-3">
                                    <label class="form-label"> Select Business </label>
                                    
                                    <?php
                                        // SQL query to fetch staff from the staff table
                                        $sqlStaffLocation = "SELECT * FROM location";
                                        
                                        // Execute the query
                                        $resultStaffLocation = $conn->query($sqlStaffLocation);
                                        
                                        // Check if there are any rows returned
                                        if ($resultStaffLocation->num_rows > 0) {
                                            // Start building the dropdown list
                                            echo '<select class="form-select form-select-sm" name="location_name" id="location_name" >';
                                            // Loop through the rows and add options to the dropdown list
                                            while ($rowStaffLocation = $resultStaffLocation->fetch_assoc()) {
                                                echo '<option value=" '. $rowStaffLocation['location_name'] .' ">' . $rowStaffLocation['location_name'] . '</option>';
                                            }
                                            echo '<option value="All">All</option>';
                                            // Close the dropdown list
                                            echo '</select>';
                                        } else {
                                            echo 'No location found.';
                                        }
                                    ?>
                                </div>
                                
                                <?php
                                    }
                                ?>
                                <textarea hidden name="allPhoneNos" value=""><?php //echo $allPhoneNumbers. ",254720099212"; ?></textarea>
                                
                                <input hidden id="countPhone" value="Customer Count = <?php //echo $countAllPhoneNos ?>" disabled>
                                <div class=" mb-3">
                                    <label for="message">Type your message here...</label>
                                    <textarea class="form-control" rows="3" name="message" id="message3" placeholder = "Type your message here..." value="" required oninput="checkCharacterCount('message3')" ></textarea>
                                </div>

                                <br>
                                <input  id="characterCount3" name="" value="" disabled>
                                <br>
                                <br>
                                <input class="btn btn-success" type="submit" value="Send SMS" name="submitSMSAll"  />
                            </form>
                        </div>
                    </div>
                    <br> 
                    
                    <div <?php if ($admin != 2){ echo "hidden";}   ?> class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Sent SMS</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button  type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('sentSMS', 'sentSMS')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="sms-search" onkeyup="searchTable()" placeholder="Search by phone number or message"  >
                            
                            
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
                                <table id="sentSMS" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>S/No.</th>
                                            <th>Customer No.</th>
                                            <th>Message</th>
                                            <th>Date Sent</th>
                                            <th>Status</th>
                                            <th>Date Delivered</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                            $sqlGetSMS = $conn->query("SELECT * FROM smsQ ORDER BY s_no DESC ");
                                                
                                            if($sqlGetSMS->num_rows > 0){
                                                while ($rowSMS = $sqlGetSMS->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $rowSMS["s_no"] . "</td>
                                                        <td>" . $rowSMS["recipient"] . "</td>
                                                        <td>" . $rowSMS["message"] . "</td>
                                                        <td>" . $rowSMS["dateInitiated"] . "</td>
                                                        <td>" . $rowSMS["delivery"] . "</td>
                                                        <td>" . $rowSMS["dateDelivered"] . "</td>
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
                                <button id="prev-page1">Previous Page</button>
                                <span id="page-info1"></span>
                                <button id="next-page1">Next Page</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                </div> <!-- End of container-fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div>
        
        <script>
        
            //change tabs from New booking to Booking confirmation
            function changeTab(tabIndex) {
                const tabs = document.querySelectorAll('.tab-pane');
                const tabNavItems = document.querySelectorAll('.tab-nav li');
                
                tabs.forEach((tab, index) => {
                    if (index === tabIndex) {
                        tab.classList.add('active');
                        tabNavItems[index].classList.add('active');
                    } else {
                        tab.classList.remove('active');
                        tabNavItems[index].classList.remove('active');
                    }
                }); 
            }
            
            
            function addModeQueryParam1() {
                // Prevent form submission
                event.preventDefault();
                
                // Show singleMode and hide bulkMode
                document.querySelector('.singleMode').removeAttribute('hidden');
                document.querySelector('.bulkMode').setAttribute('hidden', 'hidden');
                document.querySelector('.allMode').setAttribute('hidden', 'hidden');
                
                // Get the current URL
                var url = 'marketing';
                
                // Check if the query string already exists
                if (url.indexOf('?') === -1) {
                    // Add the query string with 'mode=single'
                    url += '?mode=1';
                } else {
                    // Add the query string with '&mode=single'
                    //url += '';
                }
                
                // Reload the page with the new URL
                window.location.href = url;
            }
            
            function addModeQueryParam2() {
                // Prevent form submission
                event.preventDefault();
                
                // Show bulkMode and hide singleMode
                document.querySelector('.bulkMode').removeAttribute('hidden');
                document.querySelector('.singleMode').setAttribute('hidden', 'hidden');
                document.querySelector('.allMode').setAttribute('hidden', 'hidden');
                
                // Get the current URL
                var url = 'marketing';
                
                // Check if the query string already exists
                if (url.indexOf('?') === -1) {
                    // Add the query string with 'mode=bulk'
                    url += '?mode=2';
                } else {
                    // Add the query string with '&mode=bulk'
                    //url += '&mode=2';
                }
                
                // Reload the page with the new URL
                window.location.href = url;
            }  
            
            function addModeQueryParam3() {
                // Prevent form submission
                event.preventDefault();
                
                // Show bulkMode and hide singleMode
                document.querySelector('.allMode').removeAttribute('hidden');
                document.querySelector('.singleMode').setAttribute('hidden', 'hidden');
                document.querySelector('.bulkMode').setAttribute('hidden', 'hidden');
                
                // Get the current URL
                var url = 'marketing';
                
                // Check if the query string already exists
                if (url.indexOf('?') === -1) {
                    // Add the query string with 'mode=bulk'
                    url += '?mode=3';
                } else {
                    // Add the query string with '&mode=bulk'
                    //url += '&mode=2';
                }
                
                // Reload the page with the new URL
                window.location.href = url;
            } 
            
            //Character count in the text area
            function checkCharacterCount(textareaNo) {
                let textarea = document.getElementById(textareaNo);
                const maxLength = 113;
                let message = textarea.value;
                
                //if (message.length > maxLength) {
                // message = message.slice(0, maxLength); // Truncate the message
                ///textarea.value = message; // Update the textarea with truncated message
                //}
            
                const remainingChars = maxLength - message.length;
                
                if(textareaNo == 'message1'){
                    var charBox = 'characterCount1';
                } else if (textareaNo == 'message2'){
                    var charBox = 'characterCount2';
                } else if (textareaNo == 'message3'){
                    var charBox = 'characterCount3';
                }
                document.getElementById(charBox).value = remainingChars + ' chars left';
            }
            
            //Pagination1
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('sentSMS');
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
            
            //Add a script to search the table -->
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('sms-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('sentSMS');
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
            
        </script>
        
        <?php include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>