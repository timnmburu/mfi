<?php
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/notifications.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/ledgerActions.php';


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
        header('Location: /');
    }
    
    $username = $_SESSION['username'];
    $admin = $_SESSION['admin'];
    $member_no = $_SESSION['member_no'];
    $userphone = $_SESSION['userphone'];
    $location_name = $_SESSION['location_name'];
    $expenseLedger = '502100';
    $bankLedger ='100100';

    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Create connection
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    
    //Add expense
    if (isset($_POST['submitExpenses'])) {
        $name = $_POST['item-name'];
        $price = $_POST['item-price'];
        $quantity = $_POST['item-quantity'];
        $date = $_POST['date'];
        $paymentMode = $_POST['payment-mode'];
        $itemCategory0 = $_POST['item-category'];
        $itemCategory00 = explode(" - ", $itemCategory0);
        $itemCategory = $itemCategory00[1];
        
        $name1 = encrypt($name);
        $price1 = encrypt($price);
        $quantity1 = encrypt($quantity);
        $date1 = encrypt($date);
        
        if(!$admin){
            $location_name = $location_name;
        } else {
            $location_name = $_POST['location_name'];
        }
        
        $sqlItem = "INSERT INTO expenses (name, price, quantity, date, paidFrom, location_name) VALUES ('$name1', '$price1', '$quantity1', '$date1', '$paymentMode', '$location_name')";
        
        
        if ($conn->query($sqlItem) === TRUE) {
            // Items successfully added to table
            //add balance to subledger
            $sub_ledgerSno = $itemCategory00[0];
            $subledger_amount0 = $price;
            addSubLedgerBalanceCredit($conn, $expenseLedger, $subledger_amount0);
            
            $subledger_amount0 = $price;
            addSubLedgerBalanceDebit($conn, $bankLedger, $subledger_amount0);
            
            
            header("Location: expenses");
        } else {
            //echo "Error inserting Expense: " . $conn->error;
            header("Location: expenses");
        }
    }
    
    
    if(isset($_POST['addCategory'])){
        $name = $_POST['category-name'];
        $location_name = (!$admin)? $location_name : $_POST['location_name2'];
        
        $name1 = encrypt($name);
        
        // Use prepared statements to prevent SQL injection
        $sqlUpdate = "INSERT INTO expenses_category (name, location_name) VALUES (?,?)";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param("ss", $name1, $location_name);

        if ($stmt->execute() === true) {
            // Database update was successful
            $stmt->close();
            
            //add sub ledger balance to Plan and Equipment Ledger
            $parent_ledgerSno = '502100';
            $sub_account_name = $name;
            $sub_account_bal0 = 0;
            
            addSubLedger($conn, $parent_ledgerSno, $sub_account_name, $sub_account_bal0);
            
            //reload page
            header("Location: /expenses");
        } else {
            // Database update failed
            $stmt->close();
            header("Location: /expenses");
        }
        // Close the database connection
        //$stmt->close();
    }
    
    if(isset($_POST['removeCategory'])){
        $name = $_POST['removing-name'];
        
        $name1 = encrypt($name);
        
        // Use prepared statements to prevent SQL injection
        $sqlUpdateRemove = "DELETE FROM expenses_category WHERE name=?";
        $stmtRemove = $conn->prepare($sqlUpdateRemove);
        $stmtRemove->bind_param("s", $name1);

        if ($stmtRemove->execute() === true) {
            // Database update was successful
            $stmtRemove->close();
            
            // //get the subledgerno
            // $active1 = encrypt("active");
            // $sqlGetSubLedgrNo = $conn->query("SELECT * FROM chart_of_subaccounts WHERE name = '$name1' AND status = '$active1' ");
            // $sqlGetSubLedgrNoRow = $sqlGetSubLedgrNo->fetch_assoc();
            // $subLedgerNo = $sqlGetSubLedgrNoRow['ledger_no'];
            // $subLedgerBal = intval(decrypt($sqlGetSubLedgrNoRow['balance']));
            
            // //deactivate subledger account
            // $inactive = encrypt("Deactivated");
            // $date1 = encrypt(date('Y-m-d H:i:s'));
            // $conn->query("UPDATE chart_of_subaccounts SET status='$inactive', action_date='$date1' WHERE ledger_no = $subLedgerNo ");
            
            // //remove subledger balance from main ledger
            // $mainLedgerNo = $expenseLedger ;
            // //get main ledger balance
            // $sqlGetMainLedgrNo = $conn->query("SELECT * FROM chart_of_accounts WHERE ledger_no = $mainLedgerNo ");
            // $sqlGetMainLedgrNoRow = $sqlGetMainLedgrNo->fetch_assoc();
            // $mainLedgerBal = intval(decrypt($sqlGetMainLedgrNoRow['balance']));
            
            // //update the main ledger bal
            // $newMainLedgerBal = $mainLedgerBal - $subLedgerBal;
            // $newMainLedgerBal1 = encrypt($newMainLedgerBal);
            // $conn->query("UPDATE chart_of_accounts SET balance = '$newMainLedgerBal1' WHERE ledger_no = '$mainLedgerNo'");
            
            header("Location: /expenses");
        } else {
            // Database update failed
            $stmtRemove->close();
            header("Location: /expenses");
        }
        
    }
    
    
    
?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Expenses</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <?php include "templates/header-admins1.php" ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
    </head> 
    
    <body class="body">
        <div class="card" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Expenses Management
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    
                    <div class="card bg-light shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                Add New Expenses
                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="formExpenses">
                                <form id="submitExpenses1" method="POST" action="">
                                    
                                    <div class="mb-3">
                                        <label class="form-label"> Select Expense category</label>
                                        <?php
                                                $limit = (!$admin)? "WHERE location_name='$location_name' "  : "";
                                                $sqlCategoryList = "SELECT * FROM expenses_category $limit";
                                        
                                            // Execute the query
                                            $result = $conn->query($sqlCategoryList);
                                        
                                            // Check if there are any rows returned
                                            if ($result->num_rows > 0) {
                                                echo '';
                                                
                                                echo '<select class="form-select form-select" name="item-category" >
                                                 <option>Item Category</option>';
                                                 
                                                while ($row = $result->fetch_assoc()) {
                                                    $name = $row['name'];
                                                    $name1 = decrypt($name);
                                                    $location = ($admin) ? ' - ' .$row['location_name'] : '';
                                                    
                                                    // //get the subledgerno
                                                    // $active = encrypt("active");
                                                    // $sqlGetSubLedgrNo = $conn->query("SELECT ledger_no FROM chart_of_subaccounts WHERE name = '$name' AND status = '$active' ");
                                                    // $sqlGetSubLedgrNoRow = $sqlGetSubLedgrNo->fetch_assoc();
                                                    // $subLedgerNo = $sqlGetSubLedgrNoRow['ledger_no'];
                                                    
                                                    echo '<option value="' . $expenseLedger . ' - ' . $name1 .'">' . $expenseLedger . ' - ' . $name1 . $location . '</option>';
                                                    
                                                }
                                                
                                                echo '</select>' ;
                                                
                                            } else {
                                                echo 'No Category found.';
                                            }
                                        ?>
                                        Not on list? <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add</button>
    
                                    </div>
                                    
                                    <div class="form-floating mb-3">
                                        <input class="form-control" placeholder="Item Description" type="text" name="item-name" required autocomplete="off" >
                                        <label for="item-name"> Expense Description</label>
                                    </div>
                                    
                                    <div class="form-floating mb-3">
                                        <input class="form-control" placeholder="Paid Amount" type="number" name="item-price" required autocomplete="off" >
                                        <label for="item-price"> Expense Amount</label>
                                    </div>
                                    
                                    <div class="form-floating mb-3">
                                        <input class="form-control" placeholder="Item Quantity" type="number" name="item-quantity" required autocomplete="off"  >
                                        <label for="item-quantity"> Expense Quantity</label>
                                    </div>
                                    
                                    <div class="form-floating mb-3">
                                        <input  class="form-control" placeholder="Date of Payment" type="date" name="date" required>
                                        <label for="date"> Date </label>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label"> Select Payment Mode:</label>
                                        <select class="form-select" name="payment-mode"  required>
                                            <option name="Bank" >Bank</option> 
                                            <option name="Mpesa" >Mpesa</option>
                                        </select> 
                                    </div>
                                    <?php
                                        if(!$admin){
                                            //show nothing
                                        } else {
                                    ?>
                                    
                                    <div class="mb-2">
                                        <label class="form-label"> Select Business </label>
                                        
                                        <?php
                                            // SQL query to fetch staff from the staff table
                                            $sqlStaffLocation = "SELECT * FROM location";
                                            
                                            // Execute the query
                                            $resultStaffLocation = $conn->query($sqlStaffLocation);
                                            
                                            // Check if there are any rows returned
                                            if ($resultStaffLocation->num_rows > 0) {
                                                // Start building the dropdown list
                                                echo '<select class="form-select form-select" name="location_name" id="location_name" >';
                                                // Loop through the rows and add options to the dropdown list
                                                while ($rowStaffLocation = $resultStaffLocation->fetch_assoc()) {
                                                    echo '<option value="'. $rowStaffLocation['location_name'] .'">' . $rowStaffLocation['location_name'] . '</option>';
                                                }
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
                                    <input class="btn btn-info btn-sm" type="submit" value="Submit Expense" name="submitExpenses" />
                                </form>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card bg-info shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                Recorded Expenses
                            </div>
                        </div>
                        <div class="card-body ">
                            <!-- Add a button to export the table to Excel -->
                            <button type="button" class="btn btn-secondary btn-sm" <?php if (!$admin) { echo 'hidden'; } ?> onclick="exportTableToExcel('expenses-table', 'expenses')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control" type="text" id="expenses-search" onkeyup="searchTable()" placeholder="Search by item name" >
                            
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
                                <table id="expenses-table" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Item Description</th>
                                            <th>Paid Amount</th>
                                            <th>Item Quantity</th>
                                            <th>Date of Payment</th>
                                            <?php if($admin){ ?>
                                                <th>Business</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                    
                                            // Fetch data from the database
                                            $limit = (!$admin) ?  " WHERE location_name = '$location_name' " :"";
                                            $sqlExpenses = "SELECT * FROM expenses $limit ORDER BY id DESC";
                                            $result = $conn->query($sqlExpenses);
                            
                                            // Display data in the table
                                            if ($result->num_rows > 0) {
                                                while($row = $result->fetch_assoc()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $row["id"] . "</td>";
                                                    echo "<td>" . decrypt($row["name"]) . "</td>";
                                                    echo "<td>" . decrypt($row["price"]) . "</td>";
                                                    echo "<td>" . decrypt($row["quantity"]) . "</td>";
                                                    echo "<td>" . decrypt($row["date"]) . "</td>";
                                                    if($admin){ 
                                                        echo "<td>" . $row["location_name"] . "</td>";
                                                    }
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No results found.</td></tr>";
                                            }
                            
                                            // Close the database connection
                                            //$conn->close();
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
                    
                    <div class="modal fade" id="addRecurrentExpModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add Recurrent Expenditure</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <p>NB: Adding a recurrent expenditure adds on to your monthly target for base revenue.</p>
                                    <div class="mb-3" >
                                        <form  method="POST" action="">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="text" placeholder="Expense Name" name="name" id="name" required >
                                                <label for="name"> Expense Name</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="text" placeholder="Expense Amount" name="amount"  id="amount" required >
                                                <label for="amount">Expense Amount</label>
                                            </div>
                                            
                                            <?php
                                                if(!$admin){
                                                    //show nothing
                                                } else {
                                            ?>
                                            
                                            <div class="mt-2">
                                                <label class="form-label"> Select Business: </label>
                                                
                                                <?php
                                                    // SQL query to fetch staff from the staff table
                                                    $sqlExpenseLocation = "SELECT * FROM location";
                                                    
                                                    // Execute the query
                                                    $resultExpenseLocation = $conn->query($sqlExpenseLocation);
                                                    
                                                    // Check if there are any rows returned
                                                    if ($resultExpenseLocation->num_rows > 0) {
                                                        // Start building the dropdown list
                                                        echo '<select class="form-select form-select" name="location_name" id="location_name" >';
                                                        // Loop through the rows and add options to the dropdown list
                                                        while ($rowExpenseLocation = $resultExpenseLocation->fetch_assoc()) {
                                                            echo '<option value=" '. $rowExpenseLocation['location_name'] .' ">' . $rowExpenseLocation['location_name'] . '</option>';
                                                        }
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
                                            <input hidden type="submit" value="Add New Expense" name="submitRecurrentExpense" id="submitRecurrentExpense" />
                                        </form>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <form id="download" action="" method="POST">
                                            <div class="mb-3">
                                                <label class="form-label"> Download All Recurrent Expenses</label>
                                                <input class="btn btn-info btn-sm" type="submit" value="Download" name="download" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" name="stopOffer" id="stopOffer2" class="btn btn-primary" onclick="btnClick('submitRecurrentExpense')">Add Expense</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="removeRecurrentExpModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Remove Recurrent Expenditure</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        <div class="mb-3">
                                            <label class="form-label">Expense Name</label>
                                            <?php
                                                $sqlExpenses = "SELECT * FROM recurrentExp";
                                                $resultExpenses = $conn->query($sqlExpenses);
                                                
                                                // Check if there are any rows returned
                                                if ($resultExpenses->num_rows > 0) {
                                                    // Start building the dropdown list
                                                    echo '<select class="form-select" name="expenseName" id="expenseName" required>';
                                                    // Loop through the rows and add options to the dropdown list
                                                    //echo '<option value="Select Expense" >Select Expense<option/>';
                                                    while ($rowExpenses = $resultExpenses->fetch_assoc()) {
                                                        $name = decrypt($rowExpenses['name']);
                                                        $amount = decrypt($rowExpenses['amount']);
                                                        $location_name = $rowExpenses['location_name'];
                                                        echo "<option value=\"$name\">$name - $location_name - $amount </option>";
                                                    }
                                                    // Close the dropdown list
                                                    echo '</select>';
                                                    
                                                    $_SESSION['disableBtn'] = false;
                                                } else {
                                                    echo 'No expenses found.';
                                                    $_SESSION['disableBtn'] = true;
                                                }
                                            ?>
                                        </div>
                                        
                                        <input hidden type="submit" value="Remove Expense" name="removeExpense" id="removeExpenseBtn" style="width: 120px; height: 30px; padding: 4px;">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <?php 
                                        if($_SESSION['disableBtn'] === true){
                                    ?>
                                    <button disabled type="button" name="stopOffer" id="stopOffer2" class="btn btn-primary" onclick="btnClick('removeExpenseBtn')">Remove Expense</button>
                                    <?php
                                        unset($_SESSION['disableBtn']);
                                        } else {
                                    ?>
                                    <button type="button" name="stopOffer" id="stopOffer2" class="btn btn-primary" onclick="btnClick('removeExpenseBtn')">Remove Expense</button>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="addCategoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel"></h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Add Expenses Category</h3>
                                            <form method="POST" action="">
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" placeholder="Category name" type="text" name="category-name"  id="category-name" required autocomplete="off" >
                                                    <label for="category-name">Category name</label>
                                                </div>
                                                <?php
                                                    if(!$admin){
                                                        //Show nothing
                                                    } else {
                                                ?>
                                                
                                                <div class="mb-2">
                                                    <label class="form-label"> Select Business </label>
                                                    
                                                    <?php
                                                        // SQL query to fetch staff from the staff table
                                                        $sqlStaffLocation = "SELECT * FROM location";
                                                        
                                                        // Execute the query
                                                        $resultStaffLocation = $conn->query($sqlStaffLocation);
                                                        
                                                        // Check if there are any rows returned
                                                        if ($resultStaffLocation->num_rows > 0) {
                                                            // Start building the dropdown list
                                                            echo '<select class="form-select form-select" name="location_name2" id="location_name2" >';
                                                            // Loop through the rows and add options to the dropdown list
                                                            while ($rowStaffLocation = $resultStaffLocation->fetch_assoc()) {
                                                                echo '<option value="'. $rowStaffLocation['location_name'] .'">' . $rowStaffLocation['location_name'] . '</option>';
                                                            }
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
                                                <button  class="btn btn-primary btn-sm" id="addCategory" name="addCategory">Add</button>
                                            </form>
                                        </div>
                                    </div>
                                        
                                    <div  <?php if(!$admin){ echo 'hidden';} ?> class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Remove Expenses Category</h3>
                                            <form  method="POST" action="">
                                                <?php
                                                    $limit = (!$admin)? " WHERE location_name='$location_name'" : "";
                                                    $sqlCategoryList2 = "SELECT * FROM expenses_category $limit";
                                                
                                                    // Execute the query
                                                    $result2 = $conn->query($sqlCategoryList2);
                                                
                                                    // Check if there are any rows returned
                                                    if ($result2->num_rows > 0) {
                                                        echo '';
                                                        
                                                        echo '<select class="form-select form-select mb-2" name="removing-name">
                                                         <option>Item Category</option>';
                                                         
                                                        while ($row2 = $result2->fetch_assoc()) {
                                                            $name = decrypt($row2['name']);
                                                            $location = ($admin) ? ' - ' .$row2['location_name'] : '';
                                                            
                                                            echo '<option value="' . $name .'">' . $name . $location . '</option>';
                                                            
                                                        }
                                                        
                                                        echo '</select>' ;
                                                        
                                                    } else {
                                                        echo 'No Category found.';
                                                    }
                                                ?>
                                                <button  class="btn btn-primary btn-sm" id="removeCategory" name="removeCategory" >Remove</button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php include 'templates/toaster.php'; ?>

                </div> <!-- End of container-fluid -->
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div>
        
        <script>
            //Add a script to search the table -->
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('expenses-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('expenses-table');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].querySelectorAll('.edit-field'); // Get all elements with class 'edit-field'
            
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

            //To add/remove recurrent expenses
            function btnClick(btnId) {
                document.getElementById(btnId).click();
            }

        <!--Script to edit values on the table -->
            document.querySelectorAll('.edit-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = button.closest('tr');
                    const editFields = row.querySelectorAll('.edit-field');
                    editFields.forEach(function(field) {
                        field.removeAttribute('disabled');
                    });
                    row.classList.remove('saved-row');
                    row.classList.add('editing-row');
                    button.style.display = 'none';
                    row.querySelector('.save-btn').style.display = 'inline';
                });
            });
    
            document.querySelectorAll('.save-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const row = button.closest('tr');
                    const editFields = row.querySelectorAll('.edit-field');
                    const newData = {
                        id: row.querySelector('td:nth-child(2)').innerText,
                        name: editFields[0].value,
                        price: editFields[1].value,
                        quantity: editFields[2].value,
                        date: editFields[3].value,
                        table:"expenses"
                    };
    
                    // Send an AJAX request to a PHP script to update the data
                    fetch('templates/editTables1.php', {
                        method: 'POST',
                        body: JSON.stringify(newData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the UI or provide feedback to the user
                            editFields.forEach(function(field) {
                                field.setAttribute('disabled', 'disabled');
                            });
                            row.classList.remove('editing-row');
                            row.classList.add('saved-row');
                            button.style.display = 'none';
                            row.querySelector('.edit-btn').style.display = 'inline';
                            
                            localStorage.setItem('toasterMessage', 'Successfully saved changes.');
                            document.getElementById('liveToastBtn').click();
                        } else {
                            // Handle errors or display an error message to the user
                            localStorage.setItem('toasterMessage', 'Error saving changes.');
                            document.getElementById('liveToastBtn').click();
                        }
                    });
                });
            });

            //Pagination
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('expenses-table');
                const tbody = table.querySelector('tbody');
                const rows = tbody.querySelectorAll('.saved-row');
                let currentPage = 1;
                let pageSize = 10;
                
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
            
        <?php include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>