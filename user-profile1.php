<?php
    require_once __DIR__.'/vendor/autoload.php'; 
    require_once __DIR__.'/api/requests/get_account_details/get_account_details.php';

    use Dotenv\Dotenv;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    }
    
    $admin = $_SESSION['admin'];
    
    if($_SESSION['access'] === true){
        $access = true;
    } else {
        $access = false;
    }
    
    $username = $_SESSION['username'];
        
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database credentials
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
    
    try{
        $usernameValue = $_SESSION['username'];
        $usernameVal = encrypt($usernameValue);
        
        // Prepare and execute the SQL query to get Email
        $stmt = $conn->prepare("SELECT email, lastResetDate FROM users WHERE username = ?");
        $stmt->bind_param("s", $usernameVal);
        $stmt->execute();
        
        // Get the result set
        $resultSet = $stmt->get_result();
        
        // Fetch the result
        if ($resultSet->num_rows > 0) {
            $result = $resultSet->fetch_assoc();
            $emailU = $result['email'];
            $lastResetDate = $result['lastResetDate'];
            $emailUsr = decrypt($emailU);
            $lastResetDateUsr = decrypt($lastResetDate);
            //$phoneU = $result['phone'];
            // Use the $emailU and $lastResetDate variables as needed
        } else {
            // Email not found or invalid username
            echo "Invalid username.";
        }
        
        // Close the statement and result set
        $stmt->close();
        $resultSet->close();
        
        if(is_numeric($username)){
            // Prepare and execute the SQL query to get phone number and full name
            $stmt2 = $conn->prepare("SELECT staff_phone, staff_name FROM staff WHERE staff_email = ?");
            $stmt2->bind_param("s", $emailU);
            $stmt2->execute();
            
            // Get the result set
            $resultSet2 = $stmt2->get_result();
            
            // Fetch the result
            if ($resultSet2->num_rows > 0) {
                $result2 = $resultSet2->fetch_assoc();
                $phoneU = $result2['staff_phone'];
                $nameU = $result2['staff_name'];
                $phoneUsr = decrypt($phoneU);
                $nameUsr = decrypt($nameU);
                // Use the $emailU and $lastResetDate variables as needed
            } else {
                // Email not found or invalid username
                echo "Invalid email.";
            }
            
            $stmt2->close();
            $resultSet2->close();
            
        } else {
            //for the Superadmin account
            $username1 = encrypt($username);
            $sqlSuperAdmin = $conn->query("SELECT * FROM users WHERE username='$username1'");
            $sqlSuperAdminRes = $sqlSuperAdmin->fetch_assoc();
            $phoneSuper = $sqlSuperAdminRes['phone'];
            
            //get the superadmin account name from account
            $sqlSuperAdmin2 = $conn->query("SELECT * FROM account WHERE custPhone='$phoneSuper'");
            $sqlSuperAdminRes2 = $sqlSuperAdmin2->fetch_assoc();
            $nameSuper = $sqlSuperAdminRes2['custName'];
            
            $nameUsr2 = decrypt($nameSuper);
            $phoneUsr2 = decrypt($phoneSuper);
        }
        
    } catch (Exception $e){
        //session_start();
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: error");
        exit();
    }
    
    // Process inventory information
    if (isset($_POST['addBusiness'])) {
        $name = $_POST['location-name'];
        $description = $_POST['description'];
        
        $sqlItem = "INSERT INTO location (location_name, description) VALUES ('$name', '$description')";
        
        if ($conn->query($sqlItem) === TRUE) {
            // Items successfully added to table
            header("Location: profile");
        } else {
            echo "Error inserting Business: " . $conn->error;
            exit;
        }
    }
    
    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Edit Profile</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <style>
            .profile_element {
                border:2px solid #ddd;
                align-items: center;
            }
            .profile_fields {
                border:2px solid grey; 
                width:300px; 
                height:30px;
                margin-right:10px;
                font-size:24px;
            }
        </style>
        
        <?php include "templates/header-admins1.php" ?>
        
    </head>
    <body class="body">
        <div class="card" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                My Profile
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card bg-light shadow">
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Edit Profile Details</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            <form action="" method="POST" class="profile-element">    
                                <div class="form-floating mb-3 ">
                                    <input disabled class="text-capitalize form-control" name="userName" id="userName" value="<?php echo $usernameValue; ?>"  >
                                    <label for="userName"> Username: </label>
                                </div>
                                <div class=" form-floating mb-3 ">
                                    <input disabled name="fullName" id="fullName" value="<?php if(is_numeric($usernameValue)){ echo $nameUsr;} else {echo $nameUsr2; } ?>" class="profile_fields form-control" >
                                    <label for="fullName"> Full Name: </label>
                                </div>
                                
                                <div class="form-floating mb-3 ">
                                    <input disabled name="phone_no"  id="phone_no" value="<?php if(is_numeric($usernameValue)){ echo $phoneUsr;} else {echo $phoneUsr2; } ?>" class="profile_fields form-control">
                                    <label for="phone_no"> Phone No:</label>
                                </div>
                                
                                <div class="form-floating mb-3 ">
                                    <input disabled name="emailAdd" id="emailAdd" value="<?php echo $emailUsr; ?>" class="profile_fields form-control">
                                    <label for="emailAdd"> Email:</label>
                                </div>
                                
                                <div class="form-floating mb-3 ">
                                    <input name="passWord" disabled value="<?php echo "xxxxxxx"; ?>" class="profile_fields form-control" >
                                    <label for="passWord"> Password:</label>
                                    
                                    <a href="passwordReset"> Change? </a> 
                                    
                                    Last Password Reset:
                                    <?php echo $lastResetDateUsr; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <br>
                    <div class="card bg-info shadow" <?php if($admin !== 2) { echo 'hidden'; } ?>>
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Subscription Payments</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div >
                                <?php
                                    $sql11 = "SELECT * FROM account ORDER BY s_no DESC";
                                    $result = $conn->query($sql11);
                                    
                                    if ($result->num_rows > 0) {
                                        $custIDrow = $result->fetch_assoc();
                                        $customerID = $custIDrow["custID"];
                                        $amountDue = $_SESSION['amount_due'];
                                        
                                    }
                                    
                                    $home = $_SERVER['SERVER_NAME'];
                                    
                                    function encode_param($param) {
                                        return base64_encode($param);
                                    }
                                    
                                    $data = "customer=$customerID&amountDue=$amountDue&redir=$home";
                                    $encoded_param = encode_param($data);
                                    $url = 'https://m.essentialapp.site/mpesa?p=' . urlencode($encoded_param);
                                    
                                ?>
                                <h3>Account Details (Account Access: <?php if($access){ echo " Full)"; } else { echo " Limited <a class='btn btn-sm btn-warning' href='$url'>Pay Now?</a> )";} ?> </h3>
                                
                                <div class="table table-responsive">
                                    <table id="account" class="table table-hover border border-rounded">
                                        <thead>
                                            <tr>
                                                <th>Customer Number</th>
                                                <th>Subscription Date</th>
                                                <th>Subscription Amount</th>
                                                <th>Last Payment Amount</th>
                                                <th>Last Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider"> 
                                            
                                        <?php
                                        // Loop through the table data and generate HTML code for each row
                                        $sqlAccountTable = "SELECT * FROM account ORDER BY s_no DESC";
                                        $resultAcc = $conn->query($sqlAccountTable);
                                        
                                        if ($resultAcc->num_rows > 0) {
                                            while ($row = $resultAcc->fetch_assoc()) {
                                                echo "
                                                <tr>
                                                <td>" . $row["custID"] . "</td>
                                                <td>" . decrypt($row["subDate"]) . "</td>
                                                <td>" . decrypt($row["subAmount"]) . "</td>
                                                <td>" . decrypt($row["lastPayAmount"]) . "</td>
                                                <td>" . decrypt($row["lastPayDate"]) . "</td></tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7'>No results found.</td></tr>";
                                        }
                                            
                                        //$conn->close();
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card bg-info shadow" <?php if($admin !== 2) { echo 'hidden'; } ?> >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Businesses</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            <div>
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal">Add Business</button>
                                
                                <div class="table table-responsive">
                                    <table id="location-table" class="table table-hover border border-rounded">
                                        <thead>
                                            <tr>
                                                <?php if($admin){ ?>
                                                    <th>Action</th>
                                                <?php } ?>
                                                <th>No.</th>
                                                <th>Location Name</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-group-divider">
                                            <?php
                                                
                                                $sqlPaid = "SELECT * FROM location ORDER BY s_no ASC";
                                        
                                                $result = $conn->query($sqlPaid);
                                        
                                                // Loop through the table data and generate HTML code for each row
                                                if ($result->num_rows > 0) {
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<tr class='saved-row'>";
                                                        if($admin){ 
                                                            echo "<td><button class='edit-btn btn btn-info btn-sm' class='button' >Edit</button> <button class='save-btn btn btn-success btn-sm' style='display:none;'>Save</button></td>";
                                                        }
                                                        echo "<td>" . $row["s_no"] . "</td>";
                                                        echo "<td><input type='text' class='edit-field' disabled value='" . $row["location_name"] . "'></td>";
                                                        echo "<td><input type='text' class='edit-field' disabled value='" . $row["description"] . "'></td>
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
                    </div>
                    
                    <!--Application Modals -->
                    <div class="modal fade" id="addLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New Business</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="New Location name" type="text" name="location-name"  id="location-name" >
                                            <label for="location-name">New Business name</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" rows="3" placeholder="Description" type="text" name="description"  id="description" ></textarea>
                                        </div>
                                        <button hidden class="btn btn-success btn-sm" id="addBusiness" name="addBusiness" >Add</button>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button"  class="btn btn-primary" onclick="btnClick('addBusiness')">Add Business</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
            <?php include 'templates/toaster.php';   ?>
        </div>
        <script>
            //Script to edit values on the table 
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
                        location_name: editFields[0].value,
                        description: editFields[1].value,
                        table:"location"
                    };
    
                    // Send an AJAX request to a PHP script to update the data
                    fetch('templates/editTables.php', {
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
            
            function btnClick(btnId){
                document.getElementById(btnId).click();
            }
        </script>
   
        <?php include 'templates/sessionTimeoutL.php'; ?>    
    </body>
</html>