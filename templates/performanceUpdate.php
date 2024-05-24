<?php
    require_once __DIR__ . '/../vendor/autoload.php'; 
    require_once __DIR__ . '/../templates/crypt.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    function updateCashIn($cashIn, $location_name ){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $date = date('Y-m-d H:i:s');
        $dateTime = new DateTime($date);
        $month = $dateTime->format('F'); // Full month name (e.g., January)
        $year = $dateTime->format('Y'); // 4-digit year (e.g., 2024)
        
        $location_name1 = encrypt($location_name);
        $month1 = encrypt($month);
        $year1 = encrypt($year);
        
        //get the count so far for cashIn
        $sqlCountCashIn = $conn->prepare("SELECT MAX(cashInCount) as lastCount FROM performanceHistory WHERE location_name=? AND month=? AND year=?");
        $sqlCountCashIn->bind_param("sss", $location_name1, $month1, $year1);
        $sqlCountCashIn->execute();
        $resultRows = $sqlCountCashIn->get_result();
        
        $lastCount = 0;
        if($resultRows->num_rows > 0){
            $rowCount = $resultRows->fetch_assoc();
            $lastCount = decrypt($rowCount['lastCount']);
        }
        
        $cashInCount = intval($lastCount) + 1;
        
        $cashIn1 = encrypt($cashIn);
        $cashInCount1 = encrypt($cashInCount);
        $date1 = encrypt($date);
        $month1 = encrypt($month);
        $year1 = encrypt($year);
        $location_name1 = encrypt($location_name);
        
        $sqlAddCashIn = "INSERT INTO performanceHistory (cashIn, cashInCount, date, month, year, location_name ) VALUES ('$cashIn1', '$cashInCount1', $date1', '$month1', '$year1', '$location_name1')";
        
    }

?>
