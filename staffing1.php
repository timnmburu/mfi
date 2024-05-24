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
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
        exit;
    } elseif (!isset($_SESSION['access']) || $_SESSION['access'] === false || $_SESSION['admin'] !== 2){
        header('Location: /');
    }
    
    $admin = $_SESSION['admin'];
    $username = $_SESSION['username'];

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
    
    
    //Enter staff details in database
    if(isset($_POST['submitNewStaff'])){
        $name = $_POST['staff-name'];
        $phone1 = $_POST['staff-phone'];
        $email = $_POST['staff-email'];
        $joinDate = $_POST['joinDate'];
        $location = $_POST['location1'];
        $rate = $_POST['staff-rate'];
        $disbTargetCount = $_POST['staff-disb-target-count'];
        $disbTargetAbsolute = $_POST['staff-disb-target-absolute'];
        
        $name = encrypt($name);
        $phone = encrypt($phone1);
        $email = encrypt($email);
        $joinDate = encrypt($joinDate);
        $rate = encrypt($rate);
        $disbTargetCount = encrypt($disbTargetCount);
        $disbTargetAbsolute = encrypt($disbTargetAbsolute);
        
        $locality = "staff_docs";
        
        $documentName1='id-front';
        $employFormName1 = $documentName1 . $phone1;
        $documentName2='id-back';
        $employFormName2 = $documentName2 . $phone1;
        $documentName3='passport-pic';
        $employFormName3 = $documentName3 . $phone1;
        $documentName4='contract';
        $employFormName4 = $documentName4 . $phone1;
        
        $idFront = uploadDocs($documentName1, $locality, $employFormName1);
        $idBack = uploadDocs($documentName2, $locality, $employFormName2);
        $passportPic = uploadDocs($documentName3, $locality, $employFormName3);
        $contract = uploadDocs($documentName4, $locality, $employFormName4);
        
        if ($idFront && $idBack && $passportPic && $contract) {
            $path_id_front = encrypt($idFront);
            $path_id_back = encrypt($idBack);
            $path_passport_pic = encrypt($passportPic);
            $path_contract = encrypt($contract);
        
            $roleSa = 'Staff';
            $roleSa = encrypt($roleSa);
            
            $statusAa = 'active';
            $statusAa = encrypt($statusAa);
            
            //Check whether Staff exists
            $sqlCheckExists = $conn->query("SELECT staff_phone FROM staff WHERE staff_phone='$phone'");
            
            if($sqlCheckExists->num_rows > 0){
                //Do nothing
            } else {
                $sqlNewStaff = "INSERT INTO staff (staff_name, staff_phone, staff_email, joinDate, rate, ID_front, ID_back, passport_pic, contract, location_name, role, status, disb_target_count, disb_target_volume) 
                VALUES ('$name', '$phone', '$email','$joinDate', '$rate', '$path_id_front', '$path_id_back', '$path_passport_pic', '$path_contract', '$location', '$roleSa','$statusAa', '$disbTargetCount', '$disbTargetAbsolute')";
                
                if ($conn->query($sqlNewStaff) === TRUE) {
                     //Log activity
                    $action = "Created " . decrypt($name) . " as a new staff";
                    logAction($action);
                    
                    header("Location: staff");
                } else {
                    //echo "Error: " . $sqlNewStaff . "<br>" . $conn->error;
                    //echo "Error: " . $sqlStore . "<br>" . $conn->error;
                    header("Location: staff");
                }
            }
        }
        
        //$conn->close();
    }
    
    //Enter staff details in database
    if(isset($_POST['submitEditStaff'])){
        $staffNum1 = $_POST['staff_details0'];
        $staffNum2 = explode(" - ", $staffNum1);
        $staffNum = $staffNum2[0];
        $name = $_POST['staff-name0'];
        $phone1 = $_POST['staff-phone0'];
        $email = $_POST['staff-email0'];
        $joinDate = $_POST['joinDate0'];
        $location = $_POST['location0'];
        $rate = $_POST['staff-rate0'];
        
        $name = encrypt($name);
        $phone = encrypt($phone1); 
        $email = encrypt($email);
        $joinDate = encrypt($joinDate);
        $rate = encrypt($rate);
        
        $locality = "staff_docs";
        
        $documentName1='id-front0';
        $loanFormName1 = $documentName1 . $phone1 . 're';
        $documentName2='id-back0';
        $loanFormName2 = $documentName2 . $phone1 . 're';
        $documentName3='passport-pic0';
        $loanFormName3 = $documentName3 . $phone1 . 're';
        $documentName4='contract0';
        $loanFormName4 = $documentName4 . $phone1 . 're';
        
        $idFront = uploadDocs($documentName1, $locality, $loanFormName1);
        $idBack = uploadDocs($documentName2, $locality, $loanFormName2);
        $passportPic = uploadDocs($documentName3, $locality, $loanFormName3);
        $contract = uploadDocs($documentName4, $locality, $loanFormName4);
        
        if ($idFront && $idBack && $passportPic && $contract) {
            $path_id_front0 = encrypt($idFront);
            $path_id_back0 = encrypt($idBack);
            $path_passport_pic0 = encrypt($passportPic);
            $path_contract0 = encrypt($contract);
            
            //get the documents path from the db table
            $sqlGetDocPath = $conn->query("SELECT * FROM staff WHERE staff_no='$staffNum'");
            $sqlGetDocPathRow = $sqlGetDocPath->fetch_assoc();
            
            $prevStaffPhone = $sqlGetDocPathRow['staff_phone'];
            $path_id_front1 = $sqlGetDocPathRow['ID_front'];
            $path_id_back1 = $sqlGetDocPathRow['ID_back'];
            $path_passport_pic1 = $sqlGetDocPathRow['passport_pic'];
            $path_contract1 = $sqlGetDocPathRow['contract'];
            
            $path_id_front = ($idFront === "empty") ? $path_id_front1 : $path_id_front0;
            $path_id_back = ($idBack === "empty") ? $path_id_back1 : $path_id_back0;
            $path_passport_pic = ($passportPic === "empty") ? $path_passport_pic1 : $path_passport_pic0;
            $path_contract = ($contract === "empty") ? $path_contract1 : $path_contract0;
            
            $sqlNewcustomer = $conn->prepare("UPDATE staff SET staff_name=?, staff_phone=?, staff_email=?, joinDate=?, rate=?, ID_front=?, ID_back=?, passport_pic=?, contract=?, location_name=? WHERE staff_no=?");
            $sqlNewcustomer->bind_param("sssssssssss", $name, $phone, $email, $joinDate, $rate, $path_id_front, $path_id_back, $path_passport_pic, $path_contract, $location, $staffNum);
            
            if ($sqlNewcustomer->execute() === TRUE) {
                //update users table with the username (phone)
                $conn->query("UPDATE users SET username = '$phone' WHERE staff_no='$staffNum'");
                
                //update customers and loans tables with new staff phone number
                $conn->query("UPDATE customers SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loans SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loan_applications SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loan_appraisals SET action_by = '$phone' WHERE action_by='$prevStaffPhone'");
                
                //Log activity
                $action = "Edited staff details for " . decrypt($name) ;
                logAction($action);
                
                header("Location: staff");
            }
        } else {
            echo "Error updating staff.";
        }
    }
    
    //Add new admin
    if(isset($_POST['submitUser'])){
        //$username = $_POST['username'];
        //$username = strtolower($username);
        $phone0 = $_POST['userSelect'];
        $phone1 = explode(" - ", $phone0);
        $phone = $phone1[0];
        
        $roleAdmin = $_POST['role_admin'];
        
        //$username1 = encrypt($username);
        $phone1 = encrypt($phone);
        
        $password = encrypt('MFI.123');
        $token = '';
        $lastReset = '';
        
        $sqlGetStaffPhone = $conn->query("SELECT * FROM staff WHERE staff_phone='$phone1'");
        $rows = $sqlGetStaffPhone->fetch_assoc();
        $staff_no = $rows['staff_no'];
        $email = decrypt($rows['staff_email']);
        $staff_name = decrypt($rows['staff_name']);
        $location_name = $rows['location_name'];

        $email1 = encrypt($email);
        
        $phone = '0' . standardizePhoneNumber($phone);
        $phone1 = encrypt($phone);
        
        //Get CustID of the user business
        $sqlGetCustID = $conn->query("SELECT custID FROM users WHERE staff_no = '0'");
        $rowsCustID = $sqlGetCustID->fetch_assoc();
        $custID = !empty($rowsCustID['custID']) ? $rowsCustID['custID']: null;
        //$custID = '2400';
        
        $transactionLimit = '0';
        $transactionLimit = encrypt($transactionLimit);
        
        $passToken = substr(str_shuffle("0123456789aaaaabbbbbcccccddddddeeeee"), 0, 15);
        
        $current_date_time = date('Y-m-d H:i:s');
        $lastReset = encrypt($current_date_time);
        $passTokenEncrypted = encrypt($passToken);
        
        $role2 = encrypt($roleAdmin);
        
        //Check whether user exists
        $sqlCheckExists1 = $conn->query("SELECT username FROM users WHERE username='$phone1'");
        
        if($sqlCheckExists1->num_rows > 0){
            //Do nothing
        } else {
            $sqlNewAdmin = "INSERT INTO users (staff_no,username, password, email, phone, token, lastResetDate, custID, location_name, transaction_limit, role) 
            VALUES ('$staff_no', '$phone1','$password', '$email1', '$phone1', '$passTokenEncrypted','$lastReset', '$custID', '$location_name', '$transactionLimit', '$role2')";
            
            if ($conn->query($sqlNewAdmin) === TRUE ) {
                
                $conn->query("UPDATE staff SET role='$role2' WHERE staff_email='$email1'");
                
                //Send email of Password Token for resetting
                $subject = 'New User Credentials';
                
                $url = $_SERVER['SERVER_NAME'];
                $url .= '/new_password?pstk=';
                $url .= base64_encode($passTokenEncrypted);
                
                $body = 'Hi '. $email . ' 
                <br> <br> 
                Your user credentials have been created successfully' . '
                <br> Username: <b>'. $phone .' </b>. 
                <br> Password: <b>' . $passToken . '</b> 
                <br> <br> Please copy and use it to set your password within 1 hour.' . '<br>
                Alternatively, you can <a href="' . $url . '" style="display: inline-block; padding: 3px 5px; background-color: #4CAF50; color: white; text-align: center; text-decoration: none; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 3px;">Click Here</a> to reset.
                <br>
                <br> Thank you. <br> 
                <br> If you experience any challenge, please notify Admin immediately!';
                
                $replyTo = "support@essentialapp.site";
            
                sendEmail($email, $subject, $body, $replyTo);
                
                //Notify via SMS
                $message11 = "Dear ". $staff_name . ", your login details have been created successfully. Check your email " . $email . " for more information. Thank you.";
                $recipient11 = '0' . substr($phone, -9);
                
                sendSMS($recipient11, $message11);
                
                //Log activity
                $action = "Created " . $username . " as a new " . decrypt($role2);
                logAction($action);
                
                //Add staff no to notifications table
                $conn->query("ALTER TABLE `notifications` ADD COLUMN `$staff_no` TEXT NULL DEFAULT NULL");
                
                header("Location: staff");
                
            } else {
                //echo "Error: " . $sqlNewAdmin . "<br>" . $conn->error;
                header("Location: staff");
            }
        }
    }
    
    //Update admin roles either Super Admin or Admin
    if(isset($_POST['updateUser'])){
        $adminName0 = $_POST['adminUsername'];
        $adminName1 = explode(" - ", $adminName0);
        $adminName = $adminName1[0];
        
        $admin = $_POST['role_admin1'];
        
        $admin1 = encrypt($admin);
        $adminName1 = encrypt($adminName);
        $conn->query("UPDATE users SET role='$admin1' WHERE username='$adminName1'");
        
        //get user staff_no
        $sqlGetNo = $conn->query("SELECT staff_no FROM users WHERE username='$adminName1' ");
        $sqlGetNoResult = $sqlGetNo->fetch_assoc();
        $staff_no = $sqlGetNoResult['staff_no'];
        
        $conn->query("UPDATE staff SET role='$admin1' WHERE staff_no='$staff_no'");
        
        //Log activity
        $action = "Update the role of " . $adminName . " to ". $admin;
        logAction($action); 
        
        header("Location: staff");
        //echo "Successfully updated " . "<b>" . $username . "</b>" . " as " . "<b>" . $role . "</b>";
    }
    
    //Remove admin
    if(isset($_POST['removeUser'])){
        $userN1 = $_POST['StaffSelect1'];
        $userN0 = explode(" - ", $userN1);
        $userN = $userN0[0];
        
        $userN1 = encrypt($userN);
        
        $resultEmail = $conn->query("SELECT * FROM users WHERE username = '$userN1'");
        $rowEmail = $resultEmail->fetch_assoc();
        $staffNo = $rowEmail['staff_no'];
        
        $changedRole = encrypt("Staff");
        $updateAdminRole = ("UPDATE staff SET role='$changedRole' WHERE staff_no = '$staffNo'");
        
        if ($conn->query($updateAdminRole)) {
            
            $conn->query("DELETE FROM users WHERE username = '$userN1'");
            $conn->query("ALTER TABLE `notifications` DROP COLUMN `$staffNo`");
            
            //Log activity
            $action = "Removed " . $userN . " as a User.";
            logAction($action); 
            
            //echo "Successfully removed " . "<b>" . $userN . "</b>" . " as an " . "<b>" . "Admin" . "</b>";
            header("Location: staff");
            //exit();
        } else {
            //echo "Error: " . $updateAdminRole . "<br>" . $conn->error;
            header("Location: staff");
        }
    }
    
    //Exit staff
    if (isset($_POST['exitStaff'])) {
        $selectedStaff = $_POST['staff-details']; 
        $staffData = explode(' - ', $selectedStaff);
        $staff_name = $staffData[1]; 
        $staff_no = $staffData[0]; 
        $comment = $_POST['exit-comment'];
        $date = date('Y-m-d H:i:s');
        
        $staff_no = $staff_no;
        $comment1 = encrypt($comment);
        $date1 = encrypt($date);
        $status = encrypt('exited');
        $status1 = encrypt($status);
    
        $sqlExitStaff = "UPDATE staff SET status='$status1', exit_comment='$comment1', exited_date='$date1' WHERE staff_no = '$staff_no'";
    
        if ($conn->query($sqlExitStaff)) {
            //delete from Users (remove rights)
            $conn->query("DELETE FROM users WHERE staff_no='$staff_no'");
            
            //Log activity
            $action = "Removed " . $staff_name . " as a Staff.";
            logAction($action);
            //exit();
            header("Location: staff");
        } else {
            echo "Error: " . $sqlExitStaff . "<br>" . $conn->error;
        }
    }
    
    
    //Password reset
    if(isset($_POST['resetPassword'])){
        $userNa0 = $_POST['staffSelect'];
        $userNa1 = explode(" - ", $userNa0);
        $userNa = $userNa1[0];
        $userNa1 = encrypt($userNa);
        
        $sqlEmail =$conn->query("SELECT * FROM users WHERE username = '$userNa1'");
        $sqlEmailResults = $sqlEmail->fetch_assoc();
        
        $emailGot = decrypt($sqlEmailResults['email']);
        
        passReset($emailGot);
        
        //Log activity
        $action = "Reset password for " . $userNa ;
        logAction($action);
        
        header("Location: staff");
    }
    
    
    if(isset($_POST['reactivateStaff'])){
        $userPhoneRe0 = $_POST['staffSelectRe'];
        $userPhoneRe1 = explode(" - ", $userPhoneRe0);
        $userPhoneRe = $userPhoneRe1[0];

        $userPhoneRe1 = encrypt($userPhoneRe);
        
        $reactivated = encrypt('active');
        
        $StaffN = encrypt('Staff');
        
        $sqlEmail =$conn->query("UPDATE staff SET status='$reactivated', role='$StaffN' WHERE staff_phone='$userPhoneRe1'");
        
        //Log activity
        $action = "Reactivated staff: " . $userPhoneRe ;
        logAction($action);
        
        header("Location: staff");
    }
    
    
?>

<!DOCTYPE html>
<html en-US>
    <head>
        <title>Staff Management</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <?php include "templates/header-admins1.php" ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        <?php include 'templates/toaster.php'; ?>
        
    </head>
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                Staff Management
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div <?php if($admin !== 2){ echo 'hidden'; } ?> class="dropdown">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#newStaffModal">Add New Staff</button>
                            </li>
                            <li <?php if(!$admin){ echo "hidden";}?>>
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#editStaffModal">Edit Staff Details</button>
                            </li>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#exitStaffModal">Exit Staff</button>
                            </li>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#newAdminModal">Add/Edit User</button>
                            </li>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#removeAdminModal">Remove User</button>
                            </li>
                            <li >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#passResetModal">Password Reset</button>
                            </li>
                            <li <?php if(!$admin){ echo 'hidden'; } ?> >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#reactivateStaffModal">Reactivate Staff</button>
                            </li>

                        </ul>
                    </div>
                    
                    <div class="card bg-info shadow" <?php if(!$admin){ echo 'hidden'; } ?> >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Staff Details Management</b>
                            </div>
                        </div>
                        
                        <div class="card-body ">
                            <!-- Add a button to export the table to Excel -->
                            <button type="button" class="btn btn-secondary btn-sm d-inline"  onclick="exportTableToExcel('staff', 'Staff')" >Export to Excel</button>
                            
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="payments-search" onkeyup="searchTable()" placeholder="Search by name or phone.."  >
                            
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
                                <table id="staff" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <?php if($admin){ ?>
                                                <th>Action</th>
                                            <?php } ?>
                                            <th>Staff No.</th>
                                            <th>Staff Name</th>
                                            <th>Staff Phone</th>
                                            <th>Staff Email</th>
                                            <th>Joined Date</th>
                                            <th>Disb Target Count</th>
                                            <th>Disb Target Volume</th>
                                            <th>Branch</th>
                                            <th>Role</th>
                                            <th>ID Front</th>
                                            <th>ID Back</th>
                                            <th>Passport Pic</th>
                                            <th>Contract</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php    
                                        
                                            $activeS = "active";
                                            $activeS = encrypt($activeS);
                                            
                                            $sqlStaffTable = "SELECT * FROM staff WHERE status='$activeS' ORDER BY staff_no DESC";
                                            $result = $conn->query($sqlStaffTable);
                                    
                                            // Loop through the table data and generate HTML code for each row
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                echo "<tr class='saved-row'>";
                                                    if($admin){ 
                                                        echo "<td><button class='edit-btn btn btn-primary btn-sm' class='button' >Edit</button> <button class='save-btn btn btn-success btn-sm' style='display:none;'>Save</button></td>";
                                                    }
                                                    echo "<td>" . $row["staff_no"] . "</td>";
                                                    echo "<td><input type='text' class='edit-field' disabled value='" . decrypt($row["staff_name"]) . "'></td>";
                                                    echo "<td><input type='text' class='edit-field' disabled value='" . decrypt($row["staff_phone"]) . "'></td>";
                                                    echo "<td><input type='text' class='edit-field' disabled value='" . decrypt($row["staff_email"]) . "'></td>";
                                                    echo "<td>" . decrypt($row["joinDate"]) . "</td>";
                                                    echo "<td><input type='text' class='edit-field' disabled value='" . decrypt($row["disb_target_count"]) . "'></td>";
                                                    echo "<td><input type='text' class='edit-field' disabled value='" . decrypt($row["disb_target_volume"]) . "'></td>";
                                                    echo "<td>" . $row["location_name"] . "</td>";
                                                    echo "<td>" . decrypt($row["role"]) . "</td>";
                                                    echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["ID_front"]) . "' download>Download</a></td>";
                                                    echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["ID_back"]) . "' download>Download</a></td>";
                                                    echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["passport_pic"]) . "' download>Download</a></td>";
                                                    echo "<td>" . "<a class='btn btn-primary btn-sm' href='" . decrypt($row["contract"]) . "' download>Download</a></td>";
                                                    echo "</tr>"; // download link
                                                }
                                            } else {
                                                echo "<tr><td colspan='4'>No results found.</td></tr>";
                                            }
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
                    <div class="modal fade" id="newStaffModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New Staff</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="" enctype="multipart/form-data">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Staff name" type="text" name="staff-name"  id="staff-name" required  >
                                            <label for="staff-name"> Staff Name: </label>
                                        </div> 
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Staff Phone Number" type="text" name="staff-phone" id="staff-phone"  required>
                                            <label for="staff-phone"> Staff Phone:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Staff Email" type="email" name="staff-email"  id="staff-email"  required>
                                            <label for="staff-email"> Staff Email:</label>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location1" class="form-label">Branch</label>
                                            <select class="form-select" name="location1" id="location1" required>
                                                <?php
                                                    // Retrieve staff email addresses from the staff table
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
                                            <input class="form-control" placeholder="Staff Remuneration Rate" type="number" name="staff-rate"  id="staff-rate"  required>
                                            <label for="staff-rate"> Staff Remuneration Rate:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Staff Disbursement Target Count" type="number" name="staff-disb-target-count"  id="staff-disb-target-count"  required>
                                            <label for="staff-disb-target-count"> Staff Disbursement Target Count:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Staff Disbursement Target Volume" type="number" name="staff-disb-target-absolute"  id="staff-disb-target-absolute"  required>
                                            <label for="staff-disb-target-absolute"> Staff Disbursement Target Volume:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" placeholder="Joined Date" type="date" name="joinDate" id="joinDate" required>
                                            <label for="joinDate"> Joined Date:</label>
                                        </div>
                                        <div class="mb-3">
                                            <h3 class="form-label"> Staff Docs: <small>(Images Only)</small> </h3>
                                            
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
                                                <label for="passport-pic">  Passport Photo:</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" type="file" name="contract" id="contract" required accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                <label for="contract"> Employment Contract:</label>
                                            </div>
                                        </div>
                                        
                                        <input hidden class="button" type="submit" value="Add New Staff" name="submitNewStaff" id="addStaffBtn"style="width: 100px; height: 30px; padding: 4px;" >
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClickCheck('addStaffBtn')">Add Staff</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="newAdminModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Add New User</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                        <h3 class="form-label"> Create User</h3>
                                        <div class="newAdmin" >
                                            <form style="width: 80%;" method="POST" action="">
                                                <div class="mb-3">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="userSelect" class="form-label">Type to search Staff..</label>
                                                        <input class="form-control" list="datalistOptions30" id="userSelect" name="userSelect" placeholder="Type to search Staff.." autocomplete="off" required >
                                                        <datalist id="datalistOptions30">
                                                            <?php
                                                                $roleS1 = 'Staff';
                                                                $roleS10 = encrypt($roleS1);
                                                                
                                                                $statusA1 = 'active';
                                                                $statusA10 = encrypt($statusA1);
                                                                
                                                                $sqlStaffUser = "SELECT * FROM staff WHERE role='$roleS10' AND status='$statusA10'";
                                                                
                                                                $resultStaffUser = $conn->query($sqlStaffUser);
                                                                
                                                                // Generate dropdown options from staff email addresses
                                                                while ($rowStaffUser = $resultStaffUser->fetch_assoc()) {
                                                                    $userphone2user = decrypt($rowStaffUser['staff_phone']);
                                                                    $userphone2userName = decrypt($rowStaffUser['staff_name']);
                                                                    echo "<option value=\"$userphone2user - $userphone2userName\"></option>";
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role_admin" class="form-label">User Role?</label>
                                                    <select class="form-select" name="role_admin" id="role_admin">
                                                        <option value="User" >User</option>
                                                        <option value="Admin" >Admin</option>
                                                        <option value="Superadmin" >Superadmin</option>
                                                    </select>
                                                </div>
                                                <input class="btn btn-primary btn-sm" type="submit" value="Add New User" name="submitUser" >
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                        <h3 class="form-label"> Update User Role</h3>
                                        <div class="newSuperAdmin" >
                                            <form style="width: 80%;" method="POST" action="">
                                                <div class=" mb-3">
                                                    <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                    <label for="adminUsername1" class="form-label">Type to search User..</label>
                                                    <input class="form-control" list="datalistOptions1" id="adminUsername1" name="adminUsername" placeholder="Type to search Staff.." autocomplete="off">
                                                    <datalist id="datalistOptions1">
                                                        <?php
                                                            $username111 = encrypt($username);
                                                            //$sqlStaffs0 = "SELECT * FROM users WHERE username <> '$username111' AND staff_no > 0";
                                                            $sqlStaffs0 = "SELECT u.username, s.staff_name FROM users u JOIN staff s ON u.username = s.staff_phone WHERE username <> '$username111' AND u.staff_no > 0 ";
                                                            
                                                            $resultStaffs0 = $conn->query($sqlStaffs0);
                                                            
                                                            if ($resultStaffs0->num_rows > 0) {
                                                                while ($rowStaffs0 = $resultStaffs0->fetch_assoc()) {
                                                                    $username2 = $rowStaffs0['username'];
                                                                    $username21 = decrypt($username2);
                                                                    $username2Name = decrypt($rowStaffs0['staff_name']);
                                                                    echo "<option value=\"$username21 - $username2Name\"></option>";                                                        
                                                                    
                                                                }
                                                            } else {
                                                                echo "No Staff found";
                                                            }
                                                            
                                                        ?>
                                                    </datalist>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="role_admin1" class="form-label">Admin?</label>
                                                    <select class="form-select" name="role_admin1" id="role_admin1">
                                                        <option value="User" >User</option>
                                                        <option value="Admin" >Admin</option>
                                                        <option value="Superadmin" >Superadmin</option>
                                                    </select>
                                                </div>
                                                <input class="btn btn-primary btn-sm" type="submit" value="Update Role" name="updateUser" >
                                            </form>
                                        </div>
                                        </div>
                                    </div>
                                    <br>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="passResetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Password Reset</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="staffSelect" class="form-label">Type to search User..</label>
                                            <input class="form-control" list="datalistOptions21" id="staffSelect" name="staffSelect" placeholder="Type to search Staff.." autocomplete="off">
                                            <datalist id="datalistOptions21">
                                                <?php
                                                    
                                                    //$sqlStaffs02 = "SELECT * FROM users WHERE staff_no > 0 $limit";
                                                    $sqlStaffs02 = "SELECT u.username, s.staff_name FROM users u JOIN staff s ON u.username = s.staff_phone WHERE u.staff_no > 0 ";
                                                    $resultStaffs02 = $conn->query($sqlStaffs02);
                                                    
                                                    if ($resultStaffs02->num_rows > 0) {
                                                        while ($rowStaffs02 = $resultStaffs02->fetch_assoc()) {
                                                            $username20 = $rowStaffs02['username'];
                                                            $name20 = $rowStaffs02['staff_name'];
                                                            $username212 = decrypt($username20);
                                                            $name212 = decrypt($name20);
                                                            echo "<option value=\"$username212 - $name212\"></option>";                                                        
                                                        }
                                                    } else {
                                                        echo "No Staff found";
                                                    }
                                                    
                                                ?>
                                            </datalist>
                                        </div>
                                        <input hidden class="btn" type="submit" value="Reset Password" name="resetPassword" id="resetPassword" style="width: 120px; height: 30px; padding: 4px;">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('resetPassword')">Reset Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="reactivateStaffModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Reactivate Staff</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="staffSelectRe" class="form-label">Type to search Staff..</label>
                                            <input class="form-control" list="datalistOptions31" id="staffSelectRe" name="staffSelectRe" placeholder="Type to search Staff.." autocomplete="off" required >
                                            <datalist id="datalistOptions31">
                                                <?php
                                                    $activeStatus1 = encrypt('active');
                                                    $sqlStaff21 = "SELECT * FROM staff WHERE status <> '$activeStatus1'";
                                                    $resultStaff21 = $conn->query($sqlStaff21);
                                                    
                                                    // Generate dropdown options from staff email addresses
                                                    while ($rowStaff21 = $resultStaff21->fetch_assoc()) {
                                                        $userphone21 = decrypt($rowStaff21['staff_phone']);
                                                        $userphone21name = decrypt($rowStaff21['staff_name']);
                                                        echo "<option value=\"$userphone21 - $userphone21name\">";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <input hidden class="btn" type="submit" value="Reactivate Staff" name="reactivateStaff" id="reactivateStaff" style="width: 120px; height: 30px; padding: 4px;">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('reactivateStaff')">Reactivate Staff</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="editStaffModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Staff</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="" enctype="multipart/form-data">
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="staff_details0" class="form-label">Type to search Staff..</label>
                                            <input class="form-control" list="datalistOptions11" id="staff_details0" name="staff_details0" placeholder="Type to search Staff.." autocomplete="off">
                                            <datalist id="datalistOptions11">
                                                <?php
                                                    // Retrieve staff email addresses from the staff table
                                                    $statusA1 = 'active';
                                                    $statusA1 = encrypt($statusA1);
                                                    
                                                    $sqlStaff1 = "SELECT * FROM staff WHERE status='$statusA1' AND staff_no > 0";
                                                    $resultStaff = $conn->query($sqlStaff1);
                                                    if($resultStaff -> num_rows > 0){
                                                        // Generate dropdown options from staff email addresses
                                                        while ($rowStaff = $resultStaff->fetch_assoc()) {
                                                            $staffNo1 = $rowStaff['staff_no'];
                                                            $name1 = decrypt($rowStaff['staff_name']);
                                                            $email = decrypt($rowStaff['staff_email']);
                                                            $combNameEmail = "$staffNo1 - $name1 - $email";
                                                            
                                                            echo "<option value=\"$combNameEmail\">";
                                                        }
                                                    } else {
                                                        echo "<option value='No Staff found'>";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <div hidden="hidden" id="staff-details-part">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="Staff name" type="text" name="staff-name0" id="staff-name0"  required>
                                                <label for="staff-name"> Staff Name: </label>
                                            </div> 
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="Staff Phone Number" type="text" name="staff-phone0"  id="staff-phone0" required>
                                                <label for="staff-phone"> Staff Phone:</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="Staff Email" type="email" name="staff-email0" id="staff-email0"  required>
                                                <label for="staff-email"> Staff Email:</label>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Branch</label>
                                                <select class="form-select" name="location0" id="location0" required>
                                                    <?php
                                                        // Retrieve staff email addresses from the staff table
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
                                                <input class="form-control" placeholder="Staff Remuneration" type="number" name="staff-rate0" id="staff-rate0" required>
                                                <label for="staff-rate"> Staff Remuneration Rate:</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" placeholder="Joined Date" type="date" name="joinDate0" id="joinDate0" required>
                                                <label for="joinDate"> Joined Date:</label>
                                            </div>
                                        
                                            <div class="mb-3" id="staff-docs">
                                                <h3 class="form-label"> Staff Docs: <small>(Images Only)</small> </h3>
                                                
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="id-front0" id="id-front0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" > 
                                                    <label for="id-front"> Upload ID (Front):</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="id-back0" id="id-back0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                    <label for="id-back"> Upload ID (Back):</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="passport-pic0" id="passport-pic0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"  > 
                                                    <label for="passport-pic"></label> Passport Photo:</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input class="form-control" type="file" name="contract0" id="contract0"  accept="image/jpeg, image/png, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document" >
                                                    <label for="contract"> Employment Contract:</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input hidden class="button" type="submit" value="Edit Staff" name="submitEditStaff" id="editStaffBtn"style="width: 100px; height: 30px; padding: 4px;" >
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('editStaffBtn')">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="exitStaffModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Exit Staff</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="staff-details" class="form-label">Type to search Staff..</label>
                                            <input class="form-control" list="datalistOptions4" id="staff-details" name="staff-details" placeholder="Type to search Staff.." autocomplete="off">
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
                                                        
                                                        $combinedValue = "$staff_no21 - $staff_name21 - $userphone21";
                                                        echo "<option value=\"$combinedValue\"></option>";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" rows="3" type="text" placeholder="Exit Comment" name="exit-comment" id="exit-comment" ></textarea>
                                            <label for="exit-comment">Exit comment</label>
                                        </div>        
                                        <input hidden class="button" type="submit" value="Remove Staff" name="exitStaff" id="exitStaff" style="width: 120px; height: 30px; padding: 4px;">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('exitStaff')">Exit Staff</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="removeAdminModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Remove Admin</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <form style="width: 80%;" method="POST" action="">
                                        
                                        <div class=" mb-3">
                                            <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                            <label for="StaffSelect1" class="form-label">Type to search Staff..</label>
                                            <input class="form-control" list="datalistOptions51" id="StaffSelect1" name="StaffSelect1" placeholder="Type to search Staff.." autocomplete="off">
                                            <datalist id="datalistOptions51">
                                                <?php
                                                    $username1 = encrypt($username);
                                                    $sqlAdminRemove = "SELECT * FROM users WHERE username <> '$username1' AND staff_no > 0";
                                                    //SELECT u.username, s.staff_name FROM users u JOIN staff s ON u.username = s.staff_phone WHERE u.username <> '$username1' AND u.staff_no > 0 
                                                    $sqlAdminRemove = "SELECT u.username, s.staff_name FROM users u JOIN staff s ON u.username = s.staff_phone WHERE u.username <> '$username1' AND u.staff_no > 0";
                                                    $resultAdminRemove = $conn->query($sqlAdminRemove);
                                                    
                                                    if($resultAdminRemove->num_rows > 0){
                                                        // Generate dropdown options from staff email addresses
                                                        while ($rowAdminRemove = $resultAdminRemove->fetch_assoc()) {
                                                            $usernameAdminRemove = decrypt($rowAdminRemove['username']);
                                                            $usernameAdminNameRemove = decrypt($rowAdminRemove['staff_name']);
                                                            
                                                            echo "<option value=\"$usernameAdminRemove - $usernameAdminNameRemove\"></option>";
                                                        }
                                                    } else {
                                                        echo "No user found";
                                                    }
                                                ?>
                                            </datalist>
                                        </div>
                                        
                                        <input hidden class="button" type="submit" value="Remove Admin" name="removeUser" id="removeUser" style="width: 120px; height: 30px; padding: 4px;">
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('removeUser')">Remove Admin</button>
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
        <!--Script to edit values on the table -->
        <script>
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
                        staff_name: editFields[0].value,
                        staff_phone: editFields[1].value,
                        staff_email: editFields[2].value,
                        disb_target_count: editFields[3].value,
                        disb_target_volume: editFields[4].value,
                        table:"staff"
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
        </script>
        
            <!-- Add a script to open Add New Staff popup -->
        <script>
            function btnClick(btnId) {
                document.getElementById(btnId).click();
            }
            
            function btnClickCheck(btnId){
                var StaffPhone = document.getElementById('staff-phone').value;
                
                const newData = {
                        phone: StaffPhone,
                        check:"memberExists"
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
                        
                        if(message === 'active'){
                            alert("Staff already exists. Status " + message);
                        } else {
                            alert("Staff already exists. Status " + message + ". Reactivate staff.");
                        }
                    }
                });
            }
            
            //Populate staff edit details for editing 
            document.addEventListener('DOMContentLoaded', function() {
                
                function populateStaffDetails(){
                    
                    var staffIdNo = document.getElementById('staff_details0').value;
                    
                    if(staffIdNo === '0'){
                        //do nothing
                    } else {
                        const newData1 = {
                            data: staffIdNo,
                                check: "getMemberDetails"
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
                                var email = data.email;
                                var rate = data.rate;
                                var joined = data.joined;
                                var location = data.location1;
                                
                                document.getElementById('staff-name0').value = name;
                                document.getElementById('staff-phone0').value = phone;
                                document.getElementById('staff-email0').value = email;
                                document.getElementById('staff-rate0').value = rate;
                                document.getElementById('joinDate0').value = joined;
                                document.getElementById('location0').value = location; 
                                
                                document.getElementById('staff-details-part').removeAttribute("hidden");
                            }
                        });
                    }
                }
                
                document.getElementById('staff_details0').addEventListener('change', populateStaffDetails);
            });
        </script>
        <script>
            //Add a script to search the table -->
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('staff');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].querySelectorAll('.edit-field'); // Get all elements
            
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
            
            function searchTable1() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('payments-search1');
                filter = input.value.toUpperCase();
                table = document.getElementById('Staff');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName('td');  // Get all elements
            
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
                const table = document.getElementById('staff');
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
                
                function promptCheck(){
                    var role = document.getElementById('role_admin').value;
                    var who = document.getElementById('staff_email1').value;
                    
                    if(role == 'Superadmin'){
                        if(confirm("Are you sure you want to assign Superadmin rights to " + who + "?")){
                            //do nothing
                        } else {
                            document.getElementById('role_admin').value = 'User';
                        }
                    }
                    
                }
                
                function promptCheck1(){
                    var role = document.getElementById('role_admin1').value;
                    var who = document.getElementById('adminUsername1').value;
                    
                    if(role == 'Superadmin'){
                        if(confirm("Are you sure you want to assign Superadmin rights to " + who + "?")){
                            //do nothing
                        } else {
                            document.getElementById('role_admin1').value = 'User';
                        }
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
                document.getElementById('role_admin').addEventListener('change', promptCheck);
                document.getElementById('role_admin1').addEventListener('change', promptCheck1);
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
                return input.files[0].size <= 500000; //500KB = 500000
            }
        </script>

        <?php //include 'templates/sessionTimeoutL.php'; ?>

        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>                                   