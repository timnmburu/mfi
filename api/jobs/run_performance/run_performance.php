<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once(__DIR__ . '/../../../templates/sendsms.php');
    require_once(__DIR__ . '/../../../templates/crypt.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
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
    
    function runPerformance($conn){
        $dateOneOfMonth = date('Y-m-01');
        $lastDateOfMonth = date('Y-m-t');
        
        // Get the staff no
        $activeStaff = encrypt('active');
        $sqlActiveStaff = $conn->query("SELECT * FROM staff WHERE status = '$activeStaff' ");
        if($sqlActiveStaff->num_rows > 0){
            while($staffRows = $sqlActiveStaff->fetch_assoc()){
                $staff_phone = $staffRows['staff_phone'];
                $staff_phone1 = decrypt($staff_phone);
                
                $dateOneOfMonth1 = encrypt($dateOneOfMonth);
                $lastDateOfMonth1 = encrypt($lastDateOfMonth);
                
                $batchNo = date('YmdHis');
                
                //Insert the dates of the month in question
                $conn->query("INSERT INTO portfolio_performance (staff_phone, batch, start_date, end_date) VALUES ('$staff_phone', '$batchNo', '$dateOneOfMonth1', '$lastDateOfMonth1')");
                
                //Get the s_no of the said dates
                $sqlGetLastDate=$conn->query("SELECT s_no FROM portfolio_performance ORDER BY s_no DESC LIMIT 1");
                $sqlGetLastDateRow = $sqlGetLastDate->fetch_assoc();
                $sno = $sqlGetLastDateRow['s_no'];
                
                insertDisbursements($conn, $sno, $staff_phone1);
                
                insertClassification($conn, $sno, $staff_phone1);
            }
        }
                
    }
    
    function insertClassification($conn, $sno, $staff_phone1){ 
        $staff_phone = encrypt($staff_phone1);
        
        $limit = " AND staff_phone='$staff_phone' ";
        
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
                        $data['count']++;
                        $balance = intval(decrypt($rowAnalytics['loan_balance']));
                        if ($balance > 0) {
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
        
        //UPDATE the portfolio performance le with the values for the staff
        $normalCount = encrypt($normalCount);
        $watchCount = encrypt($watchCount);
        $substandardCount = encrypt($substandardCount);
        $doubtfulCount = encrypt($doubtfulCount);
        $lossCount = encrypt($lossCount);
        
        $normalAbsolute = encrypt($normalAbsolute);
        $watchAbsolute = encrypt(($watchAbsolute));
        $substandardAbsolute = encrypt(($substandardAbsolute));
        $doubtfulAbsolute = encrypt(($doubtfulAbsolute));
        $lossAbsolute = encrypt(($lossAbsolute));
        
        $normalPercentage = encrypt(number_format($normalPercentage, 2));
        $watchPercentage = encrypt(number_format($watchPercentage, 2));
        $substandardPercentage = encrypt(number_format($substandardPercentage, 2));
        $doubtfulPercentage = encrypt(number_format($doubtfulPercentage, 2));
        $lossPercentage = encrypt(number_format($lossPercentage, 2));
        
        //update the table
        $stmUpdate = $conn->prepare("UPDATE portfolio_performance SET normal_count=?, normal_vol=?, normal_percent=?, watch_count=?, watch_vol=?, watch_percent=?, substandard_count=?, 
            substandard_vol=?, substandard_percent=?, doubtful_count=?, doubtful_vol=?, doubtful_percent=?, loss_count=?, loss_vol=?, loss_percent=? WHERE s_no=?");
        $stmUpdate->bind_param("sssssssssssssssi", $normalCount, $normalAbsolute, $normalPercentage, $watchCount, $watchAbsolute, $watchPercentage, $substandardCount, 
            $substandardAbsolute, $substandardPercentage, $doubtfulCount, $doubtfulAbsolute, $doubtfulPercentage, $lossCount, $lossAbsolute, $lossPercentage, $sno);
        $stmUpdate->execute();
    }
        
    function insertDisbursements($conn, $sno, $staff_phone1){  
        $staff_phone = encrypt($staff_phone1);
        
        $limit = " AND staff_phone='$staff_phone' ";
        
        //get disbursement analytics
        $dateOneOfMonth = date('Y-m-01 00:00:00');
        $lastDateOfMonth = date('Y-m-t 23:59:59');
        
        //get targets count/volume
        $disbTargetCount = 0;
        $disbTargetVolume = 0;
        
        $activeStaff = encrypt('active');
        $sqlGetTargets = $conn->query("SELECT * FROM staff WHERE status = '$activeStaff' $limit ");
        
        if($sqlGetTargets->num_rows > 0){
            while($targetRows = $sqlGetTargets->fetch_assoc()){
                $disbTargetCount += intval(decrypt($targetRows['disb_target_count']));
                $disbTargetVolume += intval(decrypt($targetRows['disb_target_volume']));
                $location_name = $targetRows['location_name'];
            }
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
        
        $actualMTDCount1 = $disbAbsoluteCount;
        $actualMTDAbsolute1 = $disbAbsoluteVolume;
        
        $targetMTDCount = $disbTargetCount;
        $targetMTDAbsolute = $disbTargetVolume;
        
        $actualMTDPercentage = ($actualMTDAbsolute1 == 0 || $targetMTDAbsolute == 0) ? 0 : $actualMTDAbsolute1 / $targetMTDAbsolute * 100;
        
        //encrypt to send to db
        $actualMTDCount = encrypt($actualMTDCount1);
        $actualMTDAbsolute = encrypt(($actualMTDAbsolute1));
        
        $targetMTDCount = encrypt($targetMTDCount);
        $targetMTDAbsolute = encrypt(($targetMTDAbsolute));
        
        $actualMTDPercentage = encrypt(number_format($actualMTDPercentage, 2));
        
        //update the table
        $stmUpdate = $conn->prepare("UPDATE portfolio_performance SET disb_target_count=?, disb_actual_count=?, disb_target_vol=?, disb_actual_vol=?, disb_achvmt_percent=?, location_name=? WHERE s_no=?");
        $stmUpdate->bind_param("ssssssi", $targetMTDCount, $actualMTDCount, $targetMTDAbsolute, $actualMTDAbsolute, $actualMTDPercentage, $location_name, $sno);
        $stmUpdate->execute();
    }
    
    runPerformance($conn);
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
?>