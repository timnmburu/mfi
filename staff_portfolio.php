<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


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
    }
    
    $username = $_SESSION['username'];
    $admin = $_SESSION['admin'];
    $member_no = $_SESSION['member_no'];
    $userphone = $_SESSION['userphone'];
    if(!isset($_SESSION['access']) || $_SESSION['access'] === false){
        $access = false;
        header("Location: /loans");
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
    if(isset($_POST['filterPortfolio'])){
        $portfolioOwner = $_POST['portfolioOwner'];
        
        $portfolioOwnerX = explode(" - ", $portfolioOwner);
        
        $ownerP = $portfolioOwnerX[1];
        if($ownerP == 'Staff'){
            header("Location: /portfolio");
        } else {
            header("Location: /portfolio?stf=$ownerP");
        }
    }
    
    if(isset($_POST['transferCustomer'])){
        $currentOwnerS = $_POST['currentOwner'];
        $currentOwnerSx = explode(" - ", $currentOwnerS);
        $currentOwnerPhone = encrypt($currentOwnerSx[1]);
        
        $customerD = $_POST['customer_details'];
        $customerDx = explode(" - ", $customerD);
        $customerNos = $customerDx[0];
        
        $newOwnerS = $_POST['newOwner'];
        $newOwnerSx = explode(" - ", $newOwnerS);
        $newOwnerPhone = encrypt($newOwnerSx[1]);
        
        $sqlUpdatePortfolioOwner = $conn->query("UPDATE customers SET staff_phone='$newOwnerPhone' WHERE customer_no='$customerNos' ");
        
        if($sqlUpdatePortfolioOwner){
            $statusLoan = encrypt("Open");
            $sqlUpdatePortfolioOwnerLoans = $conn->query("UPDATE loans SET staff_phone='$newOwnerPhone' WHERE customer_no='$customerNos' AND loan_status='$statusLoan' ");
            
            if($sqlUpdatePortfolioOwnerLoans){
                header("Location: portfolio");
            }
        }
        
    }
    
    if(isset($_POST['transferPortfolio'])){
        $currentOwnerSp = $_POST['currentPortfolioOwner'];
        $currentOwnerSxp = explode(" - ", $currentOwnerSp);
        $currentOwnerPhonep = encrypt($currentOwnerSxp[1]);

        $customerD = $_POST['customer_details'];
        $customerDx = explode(" - ", $customerD);
        $customerNos = $customerDx[0];
        
        $newOwnerSp = $_POST['newPortfolioOwner'];
        $newOwnerSxp = explode(" - ", $newOwnerSp);
        $newOwnerPhonep = encrypt($newOwnerSxp[1]);
        
        $sqlUpdatePortfolioOwnerp = $conn->query("UPDATE customers SET staff_phone='$newOwnerPhonep' WHERE staff_phone ='$currentOwnerPhonep' ");
        
        if($sqlUpdatePortfolioOwnerp){
            $statusLoan = encrypt("Open");
            $sqlUpdatePortfolioOwnerLoans = $conn->query("UPDATE loans SET staff_phone='$newOwnerPhonep' WHERE staff_phone ='$currentOwnerPhonep' AND loan_status='$statusLoan' ");
            
            if($sqlUpdatePortfolioOwnerLoans){
                header("Location: portfolio");
            }
        }
    }
    
    // Define variables for the analytics figures
    $userphone1 = (isset($_GET['stf'])) ? encrypt($_GET['stf']) : encrypt($userphone);
    $forWho = (isset($_GET['stf'])) ? $_GET['stf'] : $userphone;
    
    $limit = ($admin != 2 || isset($_GET['stf']))? "AND staff_phone = '$userphone1' " : "" ;
    
    $normalCount = 0;
    $watchCount = 0;
    $substandardCount = 0;
    $doubtfulCount = 0;
    $lossCount = 0;
    
    $normalAbsolute = 0;
    $watchAbsolute = 0;
    $substandardAbsolute = 0;
    $doubtfulAbsolute = 0;
    $lossAbsolute = 0;
    
    $statuses = array(
        'Normal' => array('count' => 0, 'absolute' => 0),
        'Watch' => array('count' => 0, 'absolute' => 0),
        'Substandard' => array('count' => 0, 'absolute' => 0),
        'Doubtful' => array('count' => 0, 'absolute' => 0),
        'Loss' => array('count' => 0, 'absolute' => 0)
    );
    
    foreach ($statuses as $status => &$data) {
        $encryptedStatus = encrypt($status);
        $sqlAnalytics = "SELECT loan_classification, loan_balance FROM loans WHERE loan_classification = '$encryptedStatus' $limit ";
        $resultAnalytics = $conn->query($sqlAnalytics);
        if ($resultAnalytics->num_rows > 0) {
            while ($rowAnalytics = $resultAnalytics->fetch_assoc()) {
                $decryptedStatus = decrypt($rowAnalytics['loan_classification']);
                if ($decryptedStatus === $status) {
                    
                    $balance = intval(decrypt($rowAnalytics['loan_balance']));
                    if ($balance > 0) {
                        $data['count']++;
                        $data['absolute'] += $balance;
                    }
                }
            }
        }
    }
    
    $normalCount = $statuses['Normal']['count'];
    $watchCount = $statuses['Watch']['count'];
    $substandardCount = $statuses['Substandard']['count'];
    $doubtfulCount = $statuses['Doubtful']['count'];
    $lossCount = $statuses['Loss']['count'];
    
    $normalAbsolute = $statuses['Normal']['absolute'];
    $watchAbsolute = $statuses['Watch']['absolute'];
    $substandardAbsolute = $statuses['Substandard']['absolute'];
    $doubtfulAbsolute = $statuses['Doubtful']['absolute'];
    $lossAbsolute = $statuses['Loss']['absolute'];
    
    $totalCount = $normalCount + $watchCount + $substandardCount + $doubtfulCount + $lossCount;
    $totalAbsolute = $normalAbsolute + $watchAbsolute + $substandardAbsolute + $doubtfulAbsolute + $lossAbsolute;
    
    // Calculate percentages
    $normalPercentage = ($normalAbsolute == 0 ) ? 0 : ($normalAbsolute / $totalAbsolute) * 100;
    $watchPercentage = ($watchAbsolute == 0 ) ? 0 : ($watchAbsolute / $totalAbsolute) * 100;
    $substandardPercentage = ($substandardAbsolute == 0 ) ? 0 : ($substandardAbsolute / $totalAbsolute) * 100;
    $doubtfulPercentage = ($doubtfulAbsolute == 0 ) ? 0 : ($doubtfulAbsolute / $totalAbsolute) * 100;
    $lossPercentage = ($lossAbsolute == 0 ) ? 0 : ($lossAbsolute / $totalAbsolute) * 100;
    
    
    
    //get disbursement analytics
    $dateOneOfMonth = date('Y-m-01 00:00:00');
    $lastDateOfMonth = date('Y-m-t 23:59:59');
    
    //get targets count/volume
    $disbTargetCount = 0;
    $disbTargetVolume = 0;
    $disbTargetCountAll = 0;
    $disbTargetVolumeAll = 0;
    
    $activeStaff = encrypt('active');
    $sqlGetTargets = $conn->query("SELECT * FROM staff WHERE status = '$activeStaff' $limit ");
    
    if($sqlGetTargets->num_rows > 0){
        while($targetRows = $sqlGetTargets->fetch_assoc()){
            $joinedDate = decrypt($targetRows['joinDate']);
            //months lapsed since joining
            $staffAge = getMonthsPast($joinedDate);
            
            $disbTargetCount += intval(decrypt($targetRows['disb_target_count'])) ;
            $disbTargetVolume += intval(decrypt($targetRows['disb_target_volume'])) ;
            
            $disbTargetCountAll += intval(decrypt($targetRows['disb_target_count'])) * $staffAge;
            $disbTargetVolumeAll += intval(decrypt($targetRows['disb_target_volume'])) * $staffAge;
        }
    }
    
    function getMonthsPast($joinedDate){
        $todays = new DateTime();
        $date2 = new DateTime($joinedDate);
        
        $diff = $todays->diff($date2);
        
        $totalMonths = $diff->y * 12 + $diff->m + 1;
        
        return $totalMonths;
    }
    
    
    //get disbursement absolutes MTD
    $startTime = strtotime($dateOneOfMonth);
    $endTime = strtotime($lastDateOfMonth);
    $disbAbsoluteCount = 0;
    $disbAbsoluteVolume = 0;
    
    $sqlGetAbsolutes = $conn->query("SELECT loan_amount, loan_approvalDate FROM loans WHERE loan_approvalDate IS NOT NULL $limit ");
    
    if($sqlGetAbsolutes->num_rows > 0){
        while($absoluteRows = $sqlGetAbsolutes->fetch_assoc()){
            $approvalDate = strtotime(decrypt($absoluteRows['loan_approvalDate']));
            
            if($approvalDate > $startTime && $approvalDate < $endTime){
                $disbAbsoluteCount += 1;
                $disbAbsoluteVolume += intval(decrypt($absoluteRows['loan_amount']));
            }
        }
    }
    
    //get dibursement absolutes Totals
    $disbAbsoluteCountTotal = 0;
    $disbAbsoluteVolumeTotal = 0;
    
    $sqlGetAbsolutesTotal = $conn->query("SELECT loan_amount FROM loans WHERE loan_approvalDate IS NOT NULL $limit ");
    
    if($sqlGetAbsolutesTotal->num_rows > 0){
        while($absoluteRowsTotal = $sqlGetAbsolutesTotal->fetch_assoc()){
            $disbAbsoluteCountTotal += 1;
            $disbAbsoluteVolumeTotal += intval(decrypt($absoluteRowsTotal['loan_amount']));
        }
    }
    
    $actualMTDCount = $disbAbsoluteCount;
    $actualMTDAbsolute = $disbAbsoluteVolume;
    
    $targetMTDCount = $disbTargetCount;
    $targetMTDAbsolute = $disbTargetVolume;
    
    $actualTotalCount = $disbAbsoluteCountTotal;
    $targetTotalCount = $disbTargetCountAll;
    
    $actualTotalAbsolute = $disbAbsoluteVolumeTotal;
    $targetTotalAbsolute = $disbTargetVolumeAll;
    
    $actualMTDPercentage = ($actualMTDAbsolute == 0 || $targetMTDAbsolute == 0) ? 0 : $actualMTDAbsolute / $targetMTDAbsolute * 100;
    $actualTotalPercentage = ($actualTotalAbsolute === 0 || $targetTotalAbsolute == 0) ? 0 : $actualTotalAbsolute / $targetTotalAbsolute * 100;
    
    
?>
<!DOCTYPE html>
<html en-US>
    <head>
        <title>Portfolio</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		
        <?php include __DIR__ . "/templates/header-admins1.php"; ?>
        <?php include __DIR__ . "/templates/exportExcel/exportTableToExcel.php"; ?>
        
    </head> 
    
    <body class="body">
        <div class="card shadow" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark" >
                Portfolio Management
            </h1>
            <div class="card-body">
                <div class="container-fluid col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div <?php if($admin !== 2 || !$access){ echo 'hidden'; } ?> class="dropdown mb-3">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                        <ul class="dropdown-menu">
                            <li  >
                                <button type="button" class="dropdown-item border-bottom" data-bs-toggle="modal" data-bs-target="#portfolioManagementModal">Portfolio Transfer</button>
                            </li>
                        </ul>
                    </div>
                    
                    <div <?php if($admin !== 2){ echo "hidden"; }   ?> class="card shadow mb-3">
                        <form method="POST" action="">
                            <div class=" mb-2 m-3">
                                <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                <label for="portfolioOwner" class="form-label">Select Staff</label>
                                <input class="form-control" list="datalistOptions" id="portfolioOwner" name="portfolioOwner" placeholder="Type to search Staff.." autocomplete="off" required >
                                <datalist id="datalistOptions">
                                    <?php
                                        $statusA1 = encrypt("active");
                                        $sqlStaffUser4 = "SELECT * FROM staff WHERE status='$statusA1' AND staff_no > 0";
                                        $resultStaffUser4 = $conn->query($sqlStaffUser4);
                                        
                                        echo "<option value='All - Staff'>";
                                        
                                        // Generate dropdown options from staff phone
                                        while ($rowStaffUser4 = $resultStaffUser4->fetch_assoc()) {
                                            $userphone2user34 = decrypt($rowStaffUser4['staff_phone']);
                                            $username2user34 = decrypt($rowStaffUser4['staff_name']);
                                            
                                            $combi34 = "$username2user34 - $userphone2user34";
                                            
                                            echo "<option value=\"$combi34\">";
                                        }
                                    ?>
                                </datalist>
                            </div>
                            
                            <input class="btn btn-info m-3" type="submit" value="Filter" name="filterPortfolio" id="filterPortfolio" >
                        </form>
                    </div>
                    
                    <div class="row mb-3">
                        <!-- Quick Stats or Metrics -->
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="card shadow mt-3">
                                <h5 class="card-title text-dark m-2"> Loan Portfolio Summary
                                    <span <?php if(!isset($_GET['stf'])){ echo "hidden"; } ?> > <?php echo "For: " . $forWho; ?></span>
                                
                                    <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('portfolio-summary', 'portfolio-summary')" >Export</button>
                                </h5>
                                <div class="card-body">
                                    <div class="table table-responsive">
                                        <table id="portfolio-summary" class="table table-hover border border-rounded">
                                            <tr>
                                                <th>Classification</th>
                                                <th>Count</th>
                                                <th>Absolute</th>
                                                <th>%</th>
                                            </tr>
                                            <tr>
                                                <td><a id="Normal" class="btn btn-success btn-sm">Normal</a> </td>
                                                <td><?php echo $normalCount; ?></td>
                                                <td><?php echo number_format($normalAbsolute) ; ?></td>
                                                <td><?php echo number_format($normalPercentage, 2); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><a id="Watch" class="btn btn-info btn-sm">Watch</a> </td>
                                                <td><?php echo $watchCount; ?></td>
                                                <td><?php echo number_format($watchAbsolute); ?></td>
                                                <td><?php echo number_format($watchPercentage, 2); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><a id="Substandard" class="btn btn-secondary btn-sm">Substandard</a> </td>
                                                <td><?php echo $substandardCount; ?></td>
                                                <td><?php echo number_format($substandardAbsolute); ?></td>
                                                <td><?php echo number_format($substandardPercentage, 2); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><a id="Doubtful" class="btn btn-warning btn-sm">Doubtful</a> </td>
                                                <td><?php echo $doubtfulCount; ?></td>
                                                <td><?php echo number_format($doubtfulAbsolute); ?></td>
                                                <td><?php echo number_format($doubtfulPercentage, 2); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td><a id="Loss" class="btn btn-danger btn-sm">Loss</a> </td>
                                                <td><?php echo $lossCount; ?></td>
                                                <td><?php echo number_format($lossAbsolute); ?></td>
                                                <td><?php echo number_format($lossPercentage, 2); ?>%</td>
                                            </tr>
                                            <tr>
                                                <td>Total OLB</td>
                                                <td><?php echo $totalCount; ?></td>
                                                <td><?php echo number_format($totalAbsolute); ?></td>
                                                <td>100%</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div >
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2"> Loan Disbursement (MTD)
                                        <span <?php if(!isset($_GET['stf'])){ echo "hidden"; } ?> > <?php echo "For: " . $forWho; ?></span>
                                    
                                        <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('disbursement-summary', 'disbursement-summary')" >Export</button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table id="disbursement-summary" class="table table-hover border border-rounded">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Count</th>
                                                    <th>Absolute</th>
                                                    <th>%</th>
                                                </tr>
                                                <tr>
                                                    <td>Actual</td>
                                                    <td><?php echo $actualMTDCount; ?></td>
                                                    <td><?php echo number_format($actualMTDAbsolute) ; ?></td>
                                                    <td><?php echo number_format($actualMTDPercentage, 2); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>Target</td>
                                                    <td><?php echo $targetMTDCount; ?></td>
                                                    <td><?php echo number_format($targetMTDAbsolute); ?></td>
                                                    <td>100%</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <div >
                                <div class="card shadow mt-3">
                                    <h5 class="card-title text-dark m-2"> Loan Disbursement (Totals)
                                        <span <?php if(!isset($_GET['stf'])){ echo "hidden"; } ?> > <?php echo "For: " . $forWho; ?></span>
                                    
                                        <button  type="button" class="btn btn-secondary btn-sm position-absolute end-0 " style="width: 60px; margin-right: 10px;" onclick="exportTableToExcel('disbursement-summary', 'disbursement-summary')" >Export</button>
                                    </h5>
                                    <div class="card-body">
                                        <div class="table table-responsive">
                                            <table id="disbursement-summary" class="table table-hover border border-rounded">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Count</th>
                                                    <th>Absolute</th>
                                                    <th>%</th>
                                                </tr>
                                                <tr>
                                                    <td>Actual</td>
                                                    <td><?php echo $actualTotalCount; ?></td>
                                                    <td><?php echo number_format($actualTotalAbsolute) ; ?></td>
                                                    <td><?php echo number_format($actualTotalPercentage, 2); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td>Target</td>
                                                    <td><?php echo $targetTotalCount; ?></td>
                                                    <td><?php echo number_format($targetTotalAbsolute); ?></td>
                                                    <td>100%</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="card bg-info shadow"   >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Loan List</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button  type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('loan-details', 'loan-details')" >Export to Excel</button>
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="customer-search" onkeyup="searchTable()" placeholder="Search by name or phone number"  >
                            
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
                                            $openStatus = encrypt("Open");
                                            $theClass1 = (isset($_GET['cls'])) ? $_GET['cls'] : "";
                                            $theClass = encrypt($theClass1);
                                            $limitClass = (isset($_GET['cls'])) ? " AND loan_classification = '$theClass' " : "";
                                            
                                            $sqlGetLoan1 = $conn->query("SELECT * FROM loans WHERE loan_status='$openStatus' $limit $limitClass ");
                                                
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
                            <div class="pagination">
                                <button id="prev-page2">Previous Page</button>
                                <span id="page-info2"></span>
                                <button id="next-page2">Next Page</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    
                    <div class="card bg-info shadow"   >
                        <div class="card-header bg-secondary ">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-light" >
                                <b>Performance History</b>
                            </div>
                        </div>
                        <div class="card-body ">
                            
                            <!-- Add a button to export the table to Excel -->
                            <button  type="button" class="btn btn-secondary btn-sm"  onclick="exportTableToExcel('performance-history', 'performance-history')" >Export to Excel</button>
                            <!-- Add a search bar -->
                            <input class="form-control d-inline" type="text" id="staff-search" onkeyup="searchTable1()" placeholder="Search by name or phone number"  >
                            
                            <div class="page-size-dropdown d-inline">
                                <label for="page-size3">Rows per page:</label>
                                <select id="page-size3">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="table table-responsive">
                                <table id="performance-history" class="table table-hover border border-rounded">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Disb Count Target</th>
                                            <th>Disb Count Actual</th>
                                            <th>Disb Vol Target</th>
                                            <th>Disb Vol Actual</th>
                                            <th>Disb Achvmt %</th>
                                            <th>Normal Count</th>
                                            <th>Normal Absolute</th>
                                            <th>Normal %</th>
                                            <th>Watch Count</th>
                                            <th>Watch Absolute</th>
                                            <th>Watch %</th>
                                            <th>Substandard Count</th>
                                            <th>Substandard Absolute</th>
                                            <th>Substandard %</th>
                                            <th>Doubtful Count</th>
                                            <th>Doubtful Absolute</th>
                                            <th>Doubtful %</th>
                                            <th>Loss Count</th>
                                            <th>Loss Absolute</th>
                                            <th>Loss %</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php
                                            
                                            $sqlGetPerformance = $conn->query("SELECT * FROM portfolio_performance WHERE start_date IS NOT NULL $limit ORDER BY s_no DESC ");
                                            
                                            if ($sqlGetPerformance->num_rows > 0) {
                                                $batchSums = array();
                                                while ($rowPerformance = $sqlGetPerformance->fetch_assoc()) {
                                                    $batchNo = $rowPerformance["batch"];
                                            
                                                    // Initialize batch sums if not already set
                                                    if (!isset($batchSums[$batchNo])) {
                                                        $batchSums[$batchNo] = array(
                                                            "disb_target_count" => 0,
                                                            "disb_actual_count" => 0,
                                                            "disb_target_vol" => 0,
                                                            "disb_actual_vol" => 0,
                                                            "normal_count" => 0,
                                                            "normal_vol" => 0,
                                                            "watch_count" => 0,
                                                            "watch_vol" => 0,
                                                            "substandard_count" => 0,
                                                            "substandard_vol" => 0,
                                                            "doubtful_count" => 0,
                                                            "doubtful_vol" => 0,
                                                            "loss_count" => 0,
                                                            "loss_vol" => 0,
                                                        );
                                                    }
                                            
                                                    // Update batch sums
                                                    $batchSums[$batchNo]["end_date"] = (decrypt($rowPerformance["end_date"]));
                                                    $batchSums[$batchNo]["disb_target_count"] += intval(decrypt($rowPerformance["disb_target_count"]));
                                                    $batchSums[$batchNo]["disb_actual_count"] += intval(decrypt($rowPerformance["disb_actual_count"]));
                                                    $batchSums[$batchNo]["disb_target_vol"] += intval(decrypt($rowPerformance["disb_target_vol"]));
                                                    $batchSums[$batchNo]["disb_actual_vol"] += intval(decrypt($rowPerformance["disb_actual_vol"])); 
                                                    $batchSums[$batchNo]["normal_count"] += intval(decrypt($rowPerformance["normal_count"]));
                                                    $batchSums[$batchNo]["normal_vol"] += intval(decrypt($rowPerformance["normal_vol"]));
                                                    $batchSums[$batchNo]["watch_count"] += intval(decrypt($rowPerformance["watch_count"]));
                                                    $batchSums[$batchNo]["watch_vol"] += intval(decrypt($rowPerformance["watch_vol"]));
                                                    $batchSums[$batchNo]["substandard_count"] += intval(decrypt($rowPerformance["substandard_count"]));
                                                    $batchSums[$batchNo]["substandard_vol"] += intval(decrypt($rowPerformance["substandard_vol"]));
                                                    $batchSums[$batchNo]["doubtful_count"] += intval(decrypt($rowPerformance["doubtful_count"]));
                                                    $batchSums[$batchNo]["doubtful_vol"] += intval(decrypt($rowPerformance["doubtful_vol"]));
                                                    $batchSums[$batchNo]["loss_count"] += intval(decrypt($rowPerformance["loss_count"]));
                                                    $batchSums[$batchNo]["loss_vol"] += intval(decrypt($rowPerformance["loss_vol"]));
                                                }
                                                
                                                // Output batch sums
                                                foreach ($batchSums as $batchNo => $sums) {
                                                    $totalDisbTargetCount = $sums['disb_target_count'];
                                                    $totalDisbActualCount = $sums['disb_actual_count'];
                                                    $totalDisbTargetVol = $sums['disb_target_vol'];
                                                    $totalDisbActualVol = $sums['disb_actual_vol'];
                                                    $normalCount = $sums['normal_count'];
                                                    $watchCount = $sums['watch_count'];
                                                    $substandardCount = $sums['substandard_count'];
                                                    $doubtfulCount = $sums['doubtful_count'];
                                                    $lossCount =  $sums['loss_count'];
                                                    $normalAbsolute = $sums['normal_vol'];
                                                    $watchAbsolute = $sums['watch_vol'];
                                                    $substandardAbsolute = $sums['substandard_vol'];
                                                    $doubtfulAbsolute = $sums['doubtful_vol'];
                                                    $lossAbsolute =  $sums['loss_vol'];
                                                    // Calculate percentages
                                                    $totalDisbAchvmtPercent = ($totalDisbActualVol == 0 ) ? 0 : ($totalDisbActualVol / $totalDisbTargetVol) * 100;

                                                    $totalAbsolute = $normalAbsolute + $watchAbsolute + $substandardAbsolute + $doubtfulAbsolute + $lossAbsolute;
                                                    
                                                    $normalPercentage = ($normalAbsolute == 0 ) ? 0 : ($normalAbsolute / $totalAbsolute) * 100;
                                                    $watchPercentage = ($watchAbsolute == 0 ) ? 0 : ($watchAbsolute / $totalAbsolute) * 100;
                                                    $substandardPercentage = ($substandardAbsolute == 0 ) ? 0 : ($substandardAbsolute / $totalAbsolute) * 100;
                                                    $doubtfulPercentage = ($doubtfulAbsolute == 0 ) ? 0 : ($doubtfulAbsolute / $totalAbsolute) * 100;
                                                    $lossPercentage = ($lossAbsolute == 0 ) ? 0 : ($lossAbsolute / $totalAbsolute) * 100;
                                            
                                                    echo "<tr>";
                                                    echo "<td>". date('m-Y', strtotime($sums['end_date'])) . "</td>";
                                                    echo "<td>{$totalDisbTargetCount}</td>";
                                                    echo "<td>{$totalDisbActualCount}</td>";
                                                    echo "<td>" . number_format($totalDisbTargetVol) . "</td>";
                                                    echo "<td>" . number_format($totalDisbActualVol) . "</td>";
                                                    echo "<td>" . number_format($totalDisbAchvmtPercent, 2) . "</td>";
                                                    echo "<td>" . number_format($normalCount) . "</td>";
                                                    echo "<td>" . number_format($normalAbsolute) . "</td>";
                                                    echo "<td>" . number_format($normalPercentage, 2) . "</td>";
                                                    echo "<td>{$watchCount}</td>";
                                                    echo "<td>" . number_format($watchAbsolute) . "</td>";
                                                    echo "<td>" . number_format($watchPercentage, 2) . "</td>";
                                                    echo "<td>{$substandardCount}</td>";
                                                    echo "<td>" . number_format($substandardAbsolute) . "</td>";
                                                    echo "<td>" . number_format($substandardPercentage, 2) . "</td>";
                                                    echo "<td>{$doubtfulCount}</td>";
                                                    echo "<td>" . number_format($doubtfulAbsolute) . "</td>";
                                                    echo "<td>" . number_format($doubtfulPercentage, 2) . "</td>";
                                                    echo "<td>{$lossCount}</td>";
                                                    echo "<td>" . number_format($lossAbsolute) . "</td>";
                                                    echo "<td>" . number_format($lossPercentage, 2) . "</td>";
                                                    echo "</tr>";
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
                                <button id="prev-page3">Previous Page</button>
                                <span id="page-info3"></span>
                                <button id="next-page3">Next Page</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="portfolioManagementModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Portfolio Transfer</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Transfer Individual Customer</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="customer_details" class="form-label">Type to search Customer..</label>
                                                        <input class="form-control" list="datalistOptions11" id="customer_details" name="customer_details" placeholder="Type to search Customer.." autocomplete="off" required>
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
                                                                        $customerNo1 = $rowcustomer['customer_no'];
                                                                        $name1 = decrypt($rowcustomer['customer_name']);
                                                                        $phone = decrypt($rowcustomer['customer_phone']);
                                                                        $combNameEmail = "$customerNo1 - $name1 - $phone";
                                                                        
                                                                        echo "<option value=\"$combNameEmail\">";
                                                                    }
                                                                } else {
                                                                    echo "<option value='No Customer found'>";
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    
                                                    <div class="form-floating mb-3 text-start">
                                                        <input disabled class="form-control" type="text" id="currentOwner" name="currentOwner" placeholder="Current Customer Owner.." >
                                                        <input class="form-control" type="hidden" id="currentOwnerH" name="currentOwnerH" required>
                                                        <label  for="currentOwner">Current Customer Owner..</label>
                                                    </div>
                                                    
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="newOwner" class="form-label">New Customer Owner..</label>
                                                        <input class="form-control" list="datalistOptions301" id="newOwner" name="newOwner" placeholder="Type to search Staff.." autocomplete="off" required >
                                                        <datalist id="datalistOptions301">
                                                            <?php
                                                                $sqlStaffUser2 = "SELECT * FROM staff WHERE status='$statusA1' AND staff_no > 0";
                                                                $resultStaffUser2 = $conn->query($sqlStaffUser2);
                                                                
                                                                // Generate dropdown options from staff phone
                                                                while ($rowStaffUser2 = $resultStaffUser2->fetch_assoc()) {
                                                                    $userphone2user1 = decrypt($rowStaffUser2['staff_phone']);
                                                                    $username2user1 = decrypt($rowStaffUser2['staff_name']);
                                                                    
                                                                    $combi1 = "$username2user1 - $userphone2user1";
                                                                    
                                                                    echo "<option value=\"$combi1\">";
                                                                    
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <input  class="btn btn-info" type="submit" value="Transfer Customer" name="transferCustomer" id="transferCustomer" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body border-bottom border-dark">
                                        <div class="container-fluid">
                                            <h3 class="form-label"> Reassign Full Staff Portfolio</h3>
                                            <div class="" >
                                                <form  method="POST" action="">
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="currentPortfolioOwner" class="form-label">Current Portfolio Owner..</label>
                                                        <input class="form-control" list="datalistOptions401" id="currentPortfolioOwner" name="currentPortfolioOwner" placeholder="Type to search Staff.." autocomplete="off"  >
                                                        <datalist id="datalistOptions401">
                                                            <?php
                                                             
                                                                $sqlStaffUser3 = "SELECT * FROM staff WHERE status='$statusA1' AND staff_no > 0";
                                                                $resultStaffUser3 = $conn->query($sqlStaffUser3);
                                                                
                                                                // Generate dropdown options from staff phone
                                                                while ($rowStaffUser3 = $resultStaffUser3->fetch_assoc()) {
                                                                    $userphone2user23 = decrypt($rowStaffUser3['staff_phone']);
                                                                    $username2user3 = decrypt($rowStaffUser3['staff_name']);
                                                                    
                                                                    $combi3 = "$username2user3 - $userphone2user23";
                                                                    
                                                                    echo "<option value=\"$combi3\">";
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <div class=" mb-3">
                                                        <!-- <select class="form-select" id="bankSelect"  ></select> -->
                                                        <label for="newPortfolioOwner" class="form-label">New Portfolio Owner..</label>
                                                        <input class="form-control" list="datalistOptions402" id="newPortfolioOwner" name="newPortfolioOwner" placeholder="Type to search Staff.." autocomplete="off" required >
                                                        <datalist id="datalistOptions402">
                                                            <?php
                                                            
                                                                $sqlStaffUser4 = "SELECT * FROM staff WHERE status='$statusA1' AND staff_no > 0";
                                                                $resultStaffUser4 = $conn->query($sqlStaffUser4);
                                                                
                                                                // Generate dropdown options from staff phone
                                                                while ($rowStaffUser4 = $resultStaffUser4->fetch_assoc()) {
                                                                    $userphone2user34 = decrypt($rowStaffUser4['staff_phone']);
                                                                    $username2user34 = decrypt($rowStaffUser4['staff_name']);
                                                                    
                                                                    $combi34 = "$username2user34 - $userphone2user34";
                                                                    
                                                                    echo "<option value=\"$combi34\">";
                                                                    
                                                                }
                                                            ?>
                                                        </datalist>
                                                    </div>
                                                    <input  class="btn btn-warning" type="submit" value="Transfer Portfolio" name="transferPortfolio" id="transferPortfolio" >
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
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
            function searchTable() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('customer-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('loan-details');
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
            function searchTable1() {
                var input, filter, table, tr, td, i, j, txtValue;
                input = document.getElementById('staff-search');
                filter = input.value.toUpperCase();
                table = document.getElementById('performance-history');
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
                const table = document.getElementById('loan-details');
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
            
            //Pagination3
            document.addEventListener('DOMContentLoaded', function() {
                const table = document.getElementById('performance-history');
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
            
            //Portfolio transfer handler
            document.addEventListener('DOMContentLoaded', function() {
                
                function getCustomerOwner(){
                    var customerDetails = document.getElementById('customer_details').value;
                    
                    const newData = {
                            data: customerDetails,
                            check:"getCustomerOwner"
                        };
        
                    // Send an AJAX request to a PHP script to update the data
                    fetch('/templates/checkMemberDetails.php', {
                        method: 'POST',
                        body: JSON.stringify(newData),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success === false) {
                            var response_message = data.message;
                            alert(response_message);
                            document.getElementById('customer_details').value = '';
                        } else {
                            var response_message = data.message;
                            
                            document.getElementById('currentOwner').value = response_message;
                            document.getElementById('currentOwnerH').value = response_message;
                            
                        }
                    });
                    
                }
                
                document.getElementById('customer_details').addEventListener('change', getCustomerOwner); 
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                function checkStfParam(){
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('stf')) {
                        const stfValue = urlParams.get('stf');
                        return stfValue;
                    } else {
                        return false;
                    }
                }
                
                function reloadPage(classification){
                    var stfParam = checkStfParam();
                    
                    if(stfParam){
                        var newFilter = '?stf=' + stfParam + '&cls=' + classification;
                        
                        window.location.href = newFilter;
                    } else {
                        var newFilter = '?cls=' + classification;
                        
                        window.location.href = newFilter;
                    }
                }
                
                document.getElementById('Normal').addEventListener('click', function() { reloadPage('Normal'); }); 

                document.getElementById('Watch').addEventListener('click', function(){ reloadPage('Watch'); }); 

                document.getElementById('Substandard').addEventListener('click', function(){ reloadPage('Substandard'); }); 

                document.getElementById('Doubtful').addEventListener('click', function(){ reloadPage('Doubtful'); }); 

                document.getElementById('Loss').addEventListener('click', function() { reloadPage('Loss'); });

            });

        </script>
        
        <?php //include 'templates/sessionTimeoutL.php'; ?>
        
        <?php include 'templates/scrollUp.php'; ?>
    </body>
</html>