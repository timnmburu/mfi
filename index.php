<?php
    require_once __DIR__.'/vendor/autoload.php'; 
    require_once __DIR__.'/templates/emailing.php';
    require_once __DIR__.'/templates/crypt.php';
    require_once __DIR__.'/templates/checkMembersBalances.php';
    require_once __DIR__.'/templates/loanActions.php';


    use Dotenv\Dotenv;
    use GuzzleHttp\Client;
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    
    if (!isset($_SESSION['username'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; // Store the target page URL
        header('Location: login'); // Redirect to the login page
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
    
    if(!$admin){
        $location_name = $_SESSION['location_name'];
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
    
    if(isset($_POST['submitTicket'])){
        $issueType = $_POST['issueType'];
        $issue = $_POST['issue'];
        $username = $_SESSION['username'];
        $date = date('Y-m-d H:i:s');
        $status = 'New';
        $customerUrl = $_SERVER['SERVER_NAME'];
        
        //Set Next ticket number
        $sqlGetLastTicketNumber = $conn->query("SELECT MAX(s_no) as lastTicketId FROM support_tickets");
        
        if($sqlGetLastTicketNumber->num_rows > 0){
            $resultLastTicket = $sqlGetLastTicketNumber->fetch_assoc();
            $lastTicketId = $resultLastTicket['lastTicketId'];
            
            $newTicketId = $lastTicketId + 1 ;
        } else {
            $newTicketId = 1 ;
        }
        
        $sqlGetCustID = $conn->query("SELECT custID FROM users LIMIT 1");
        $resultCustID = $sqlGetCustID->fetch_assoc();
        $custID = $resultCustID['custID'];
        
        $newTicketId = $custID . $newTicketId;
        
        $issue1 = encrypt($issue);
        $issueType1 = encrypt($issueType);
        $username1 = encrypt($username); 
        $date1 = encrypt($date);
        $status1 = encrypt($status);
        $newTicketId1 = $newTicketId;
        
        $sqlSubmitTicket = "INSERT INTO support_tickets (issue, type, ticket_by, ticket_date, status, ticketID) 
        VALUES ('$issue1', '$issueType1', '$username1', '$date1', '$status1', '$newTicketId1')";
        $resultSubmitTicket = $conn->query($sqlSubmitTicket);
        
        $supportData = '';
        if($resultSubmitTicket){
            //Notify System Admin
            $supportData .= $customerUrl ;
            $supportData .= '^' . $issueType;
            $supportData .= '^' . $issue;
            $supportData .= '^' . $username;
            $supportData .= '^' . $date;
            $supportData .= '^' . $status;
            $supportData .= '^' . $newTicketId;
            
            $url = 'https://m.essentialapp.site/api/requests/get_support_tickets/?data=' . $supportData;
            
            $client = new Client();
            $response = $client->request('GET', $url);
            $response = $response->getBody();
            
             //Check if the request was successful
            if ($response === false) {
                // Handle the error, e.g., display an error message
                //print_r('Error sending ticket data to support team ' . $url);
                exit;
            } else {
                $subject = "New Ticket From '$customerUrl'";
                $body = $issue;
                $replyTo = $_ENV['THE_EMAIL'];
                $email = 'support@essentialapp.site';
                
                sendEmail($email, $subject, $body, $replyTo);
            }
        } else {
            echo "Error: " . $sqlSubmitTicket . "<br>" . $conn->error;
        }
        
    }
    
    if(isset($_POST['readNotification'])){
        $sno = $_POST['s_no'];
        $action = encrypt("Read");
        $conn->query("UPDATE `notifications` SET `$member_no` ='$action' WHERE `s_no`='$sno' ");
    }
    
    //get the count of how many loans are pending or reviewed
    $pending = encrypt('Not Approved');
    $reviewed = encrypt('Reviewed');
    $countPending = 0;
    $countReviewed = 0;
    
    $sqlCountPending = $conn->query("SELECT loan_status FROM loan_applications WHERE s_no > 0 $limit ");
    if($sqlCountPending->num_rows > 0){
        while($resultCountPending = $sqlCountPending->fetch_assoc()){
            $countP = $resultCountPending['loan_status'];
            if($countP === $pending){
                $countPending ++;
            } else if ($countP === $reviewed){
                $countReviewed ++;
            }
        }
    }
    
    //count loans due today
    // $today = encrypt(date("Y-m-d"));
    // $dueTodays = 0;
    // $sqlSchedulesDueToday = $conn->query("SELECT COUNT(customer_phone) as count_due FROM loan_schedules WHERE due_date = '$today' ");
    // if($sqlSchedulesDueToday->num_rows > 0){
    //     $countDueToday = $sqlSchedulesDueToday->fetch_assoc();
    //     $dueTodays = $countDueToday['count_due'];
    // }
    
    $today = encrypt(date("Y-m-d"));
    $dueTodays = 0;
    $loanBal = encrypt("0");
    
    $sqlSchedulesDueToday = $conn->query("SELECT COUNT(ls.customer_phone) as count_due FROM loan_schedules ls
        JOIN loans l ON ls.loan_no = l.loan_no
        WHERE ls.due_date = '$today' AND l.loan_balance <> '$loanBal'");
    if($sqlSchedulesDueToday->num_rows > 0){
        $countDueToday = $sqlSchedulesDueToday->fetch_assoc();
        $dueTodays = $countDueToday['count_due'];
    }

    
    
    //pull notifications for this user
    if ($admin) {
        //$levelWhich = 'Superadmin';
        //$levelWhich = encrypt($levelWhich);
        $actionWhich = 'new';
        $actionWhich = encrypt($actionWhich);
        $queryNotification = "SELECT * FROM `notifications` WHERE `$member_no` IS NULL ORDER BY `s_no` DESC";
    } else {
        $levelWhich = 'User';
        $levelWhich = encrypt($levelWhich);
        $actionWhich = 'new';
        $actionWhich = encrypt($actionWhich);
        
        $queryNotification = "SELECT * FROM `notifications` WHERE `$member_no` IS NULL AND `level` = '$levelWhich' ORDER BY `s_no` DESC";
    }
    
    $resultNotification = mysqli_query($conn, $queryNotification);
    
    $numOfNotifications = mysqli_num_rows($resultNotification);

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Home</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Excel Tech Essentials is your destination for all your technology needs.
        From website development, business process automation systems, creative designs and other technology related matters.
        We also handle big data analysis to generate informative data visualization that will help make your decisions more efficient and effective.
        Talk to us today to engage us in a journey that will benefit you and your vision through innovation. Let us help you excel.
        Automate you business today, such as being the best beauty salon management systems, hotel automation systems, service industry systems.">
        
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>


    </head>
    <body class="body">
        <div class="card " style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark mx-5">
                <span > Welcome to Essentialapp</span> 
            </h1>
            <div class="card-body">
                <div class="container-fluid  col-xs-12 col-sm-12 col-md-12 col-lg-12 ">
                    
                    <div class="dashboard-container">
                        <div class="row">
                            <!-- Quick Stats or Metrics -->
                            <div class="col-md-4" <?php if($admin != 2){ echo "hidden";} ?>>
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
                            
                            <div class="col-md-4" <?php if($admin != 2){ echo "hidden";} ?>>
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2">Customer Register Stats</h5>
                                    <div class="card-body">
                                        <!-- Display relevant statistics here -->
                                        <span> Total Number of Customers</span>
                                        <div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-success" style="width: 100%">
                                                <?php
                                                    $countAllMembers = getBalances($conn, 'customer_no');  
                                                ?>
                                                
                                                <div class="justify-center text-dark"> <span> <?php echo $countAllMembers; ?></span></div>
                                            </div>
                                        </div>
                                        <span> Total Number of Active Customers</span>
                                        <div class="progress" role="progressbar" aria-label="Info example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-info text-dark" style="width: 100%">
                                                <?php
                                                    $active_customers = getBalances($conn, 'active_customers');
                                                ?>

                                                <div class="justify-center text-dark"> <span> <?php echo $active_customers; ?></span></div>
                                            </div>
                                        </div>
                                        <span> Total Number of New Customers This Month</span>
                                        <div class="progress" role="progressbar" aria-label="Info example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar bg-info text-dark" style="width: 100%">
                                                <?php
                                                    $new_customers = getBalances($conn, 'new_customers');
                                                ?> 
                                                <div class="justify-center text-dark"> <span> <?php echo $new_customers; ?></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <!-- Quick Links or Shortcuts -->
                            <div class="<?php if(!$admin){ echo "col-md-6";} else { echo "col-md-4";} ?>">
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2">Quick Links</h5>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <!-- Include links to important sections of your application -->
                                            <li class="list-group-item"><a href="loans">Loan Management
                                                <span class="badge text-bg-info text-bg-info">New <?php echo $countPending;?></span>
                                                <span class="badge text-bg-info text-bg-info">Reviewed <?php echo $countReviewed;?></span></a>
                                            </li>
                                            <li class="list-group-item"><a href="due_today">Loans Due Today</a>
                                                <span class="badge text-bg-info text-bg-info">Due <?php echo $dueTodays;?></span></a>
                                            </li>
                                            <li class="list-group-item"><a href="customers">Customer Register</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Recent Activity -->
                            <div class="col-md-6">
                                <div class="card shadow mt-3 ">
                                    <h5 class="card-title text-dark  m-2">Recent Account Activity</h5>
                                    <div class="card-body">
                                        
                                        <div id="recentAccountActivity" class="carousel slide">
                                            <div class="carousel-inner">
                                                <?php
                                                if($admin){
                                                    $queryRecentActivity = "SELECT * FROM userlogs ORDER BY s_no DESC";
                                                } else{
                                                    $username1 = encrypt($username);
                                                    $queryRecentActivity = "SELECT * FROM userlogs WHERE username='$username1' ORDER BY s_no DESC";
                                                }
                                                
                                                $resultRecentActivity = mysqli_query($conn, $queryRecentActivity);
                                                
                                                $active = true; // To track the active slide
                                                
                                                if (mysqli_num_rows($resultRecentActivity) > 0) {
                                                    $limit = 5;
                                                    $counter = 0;
                                                    
                                                    while ($rowRecentActivity = mysqli_fetch_assoc($resultRecentActivity)) {
                                                        if ($counter % $limit == 0) {
                                                            // Start a new carousel item for every 5 activities
                                                            $activeClass1 = $active ? 'active' : '';
                                                            echo "<div class='carousel-item $activeClass1'>";
                                                            $active = false; // Set active to false for subsequent slides
                                                        }
                                                        
                                                        $usernameActor = decrypt($rowRecentActivity['username']);
                                                        $actorDid = decrypt($rowRecentActivity['user_activity']);
                                                        $when = decrypt($rowRecentActivity['date']);
                                                        
                                                        echo "<p class='text-capitalize'>{$usernameActor} {$actorDid} at {$when}</p>";
                                                        
                                                        $counter++;
                                                        
                                                        if ($counter % $limit == 0) {
                                                            // Close the current carousel item after every 5 activities
                                                            echo "</div>";
                                                        }
                                                    }
                                                    
                                                    // Close the last carousel item if not already closed
                                                    if ($counter % $limit != 0) {
                                                        echo "</div>";
                                                    }
                                                } else {
                                                    // If there are no recent activities, display a default item
                                                    echo "<div class='carousel-item active'>
                                                            <p>No recent activity.</p>
                                                        </div>";
                                                }
                                                ?>
                                            </div>
                                            <button class="carousel-control-prev " type="button" data-bs-target="#recentAccountActivity" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next " type="button" data-bs-target="#recentAccountActivity" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Notifications or Alerts -->
                            <div class="col-md-6">
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2">Notifications ( <?php echo $numOfNotifications; ?> )</h5>
                                    <div class="card-body">
                                        <div id="notificationCarousel" class="carousel slide">
                                            <div class="carousel-inner">
                                                <?php
                                                    
                                                    $active = true; // To track the active slide
                                                    
                                                    if (mysqli_num_rows($resultNotification) > 0) {
                                                        $limit = 5;
                                                        $counter = 0;
                                                        
                                                        while ($rowNotification = mysqli_fetch_assoc($resultNotification)) {
                                                            if ($counter % $limit == 0) {
                                                                // Start a new carousel item for every 5 notifications
                                                                $activeClass1 = $active ? 'active' : '';
                                                                echo "<div class='carousel-item $activeClass1 '>";
                                                                $active = false; // Set active to false for subsequent slides
                                                            }
                                                            
                                                            echo "<tr>
                                                            <td>" . decrypt($rowNotification["message"]) . "</td>
                                                            <td>" . decrypt($rowNotification["date"]) . "</td>";
                                                            echo "<td>";
                                                            if (decrypt($rowNotification["action"]) === "new") {
                                                                echo "<form method='post' class='text-center '>
                                                                        <input type='hidden' name='s_no' value='" . $rowNotification["s_no"] . "'>
                                                                        <button class='btn btn-info ' type='submit' name='readNotification' > Read </button>
                                                                    </form>";
                                                            } else {
                                                                echo "No new notifications.";
                                                            }
                                                            
                                                            echo "</td>
                                                                </tr>";
                                                            
                                                            $counter++;
                                                            
                                                            if ($counter % $limit == 0) {
                                                            // Close the current carousel item after every 5 notifications
                                                            echo "</div>";
                                                            }
                                                        }
                                                        
                                                        // Close the last carousel item if not already closed
                                                        if ($counter % $limit != 0) {
                                                            echo "</div>";
                                                        }
                                                    } else {
                                                    // If there are no new notifications, display a default item
                                                    echo "<div class='carousel-item active'>
                                                                <p>No new notifications.</p>
                                                            </div>";
                                                    }
                                                    
                                                ?>
                                            </div>
                                            
                                            <button class="carousel-control-prev" type="button" data-bs-target="#notificationCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#notificationCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Support Tickets -->
                            <div class="col-md-12">
                                <div class="card shadow mt-3 bg-info">
                                    <div class="card-title text-dark m-2 row">
                                        <h5 class="col-lg-9 col-md-8 col-sm-6">Support Tickets</h5>
                                        <button type="button" class="btn btn-sm btn-primary col-lg-3 col-md-4 col-sm-6" data-bs-toggle="modal" data-bs-target="#newTicketModal">Submit New Ticket</button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Add a search bar -->
                                        <input class="form-control d-inline" type="text" id="tickets-search" onkeyup="searchTable()" placeholder="Search tickets"  >
                                        
                                        <div class="page-size-dropdown d-inline">
                                            <label for="page-size">Rows per page:</label>
                                            <select id="page-size">
                                                <option value="3">3</option>
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="all">All</option>
                                            </select>
                                        </div>
                                        <div class="table table-responsive">
                                            <table id="support-tickets-table" class="table table-hover border border-rounded">
                                                <thead>
                                                    <tr>
                                                        <th>Ticket No.</th>
                                                        <th>Ticket Description</th>
                                                        <th>Ticket Type</th>
                                                        <th>Ticket By</th>
                                                        <th>Ticket Date</th>
                                                        <th>Action</th>
                                                        <th>Comments</th>
                                                        <th>Action By</th>
                                                        <th>Action Date</th>
                                                        <th>Status</th>                                                    
                                                    </tr>
                                                </thead>
                                                <tbody class="table-group-divider">
                                                    
                                                <?php
                                                    if($admin){
                                                        $queryTicket = "SELECT * FROM support_tickets ORDER BY s_no DESC ";
                                                    } else{
                                                        $username12 = encrypt($username);
                                                        $queryTicket = "SELECT * FROM support_tickets WHERE ticket_by = '$username12' ORDER BY s_no DESC ";
                                                    }
                                                    
                                                    $resultTicket = mysqli_query($conn, $queryTicket);
                                                    
                                                    if (mysqli_num_rows($resultTicket) > 0) {
                                                        while ($rowTicket = mysqli_fetch_assoc($resultTicket)) {
                                                            echo "<tr>
                                                                    <td>" . $rowTicket["s_no"] . "</td>
                                                                    <td>" . decrypt($rowTicket["issue"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["type"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["ticket_by"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["ticket_date"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["action"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["comments"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["action_by"]) . "</td>
                                                                    <td>" . decrypt($rowTicket["action_date"]) . "</td>";
                                                            if(decrypt($rowTicket["status"]) == "New"){
                                                                echo "<td class='btn bg-warning'>" . decrypt($rowTicket["status"]) . "</td>";
                                                            } elseif (decrypt($rowTicket["status"]) == "Closed"){
                                                                echo "<td class='btn bg-secondary'>" . decrypt($rowTicket["status"]) . "</td>";
                                                            } else {
                                                                echo "<td class='btn bg-primary'>" . decrypt($rowTicket["status"]) . "</td>";
                                                            }
                                                                echo "</tr>";
                                                            
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='7'>No results found.</td></tr>";
                                                    }
                                                        
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
                            </div>
                            

                        </div>
                        
                    </div>

                    
                    <div class="modal fade" id="viewNotificationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">View Ticket</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" method="POST">
                                        <input class="form-control" id="notificationAreaId" name="notificationAreaId" />
                                        <textarea class="form-control" id="notificationAreaMessage"></textarea>
                                        <input hidden type="submit" name="submitView" id="submitView" />
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('submitView')">Read Notification</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal fade" id="newTicketModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Submit Ticket</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                        <form  method="POST" action="">
                                            <div class="mb-3">
                                                <label class="form-label"> Issue Type</label>
                                                <select class="form-select" name="issueType">
                                                    <option value="system">System Error</option>
                                                    <option value="payment">Payment Error</option>
                                                    <option value="subscription">Account Subscription</option>
                                                    <option value="suggestions">Suggestions</option>
                                                    <option value="others">Others</option>
                                                </select>
                                            </div>
                                            <div class=" mt-3">
                                                <label class="form-label">Issue Description</label>
                                                <textarea class="form-control" rows="4" type="text" placeholder="Issue Description" name="issue" required ></textarea>
                                            </div>
                                            
                                            <input hidden type="submit" value="Add New Ticket" name="submitTicket" id="submitTicket" />
                                        </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" onclick="btnClick('submitTicket')">Submit Issue</button>
                                </div>
                            </div>
                        </div>
                    </div>
 
                    
                </div> <!-- end on container-fluid -->
            </div>
            
            <div class="card-footer text-center text-dark">
                All rights reserved.  <a href="https://essentialtech.site"><i class="bi bi-c-circle"></i> Excel Tech Essentials</a>
            </div>
        </div>

        <script>
            function btnClick(btnId) {
                document.getElementById(btnId).click();
            }
            
            function submitViewing(message){
                
                //localStorage.setItem("s_no", s_no);
                document.getElementById('notificationAreaMessage').value = message;
                document.getElementById('viewNotificationModalBtn').click();
            }
        </script>
        <script> 
            //Search
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('tickets-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('support-tickets-table');
                tr = table.getElementsByTagName('tr');
            
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName('td'); // Get all elements with class 'edit-field'
            
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
                const table = document.getElementById('support-tickets-table');
                const tbody = table.querySelector('tbody');
                const rows = tbody.querySelectorAll('tr');
                let currentPage = 1;
                let pageSize = 3;
                
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
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>