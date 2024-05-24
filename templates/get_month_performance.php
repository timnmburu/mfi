<?php
    require_once __DIR__ . '/../vendor/autoload.php'; 
    require_once __DIR__ . '/../templates/crypt.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];

    // Connect to the database and retrieve data from the table
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $selectedMonth = null;
    
    if(isset($_GET['month'])){
        // Get the selected month from the AJAX request
        $selectedMonth = $_GET['month'];
        
        $monthYear = explode(' ' , $selectedMonth);
        $monthSelected = $monthYear[0];
        $yearSelected = $monthYear[1];
        
        //Get Monthly Target
        $sqlMonthlyTarget = "SELECT monthlyTarget FROM target";
        $resultMonthlyTarget = $conn->query($sqlMonthlyTarget);
        $monthlyTarget = 0;
        if ($resultMonthlyTarget->num_rows > 0) {
            while($rows = $resultMonthlyTarget->fetch_assoc()){
                $monthlyTargt = decrypt($rows['monthlyTarget']);
                
                $monthlyTarget += intval($monthlyTargt);
            }
        }
        
        // Validate the selected month to prevent SQL injection (you can use more robust validation as needed)
        if (!preg_match('/^[a-zA-Z]+$/', $selectedMonth)) {
            echo "Invalid month selected.";
            exit;
        }
        
        // Query to get the monthly performance for the selected month
        if($selectedMonth === 'all'){
            $sqlSelectedMonthPayments = "SELECT 
                                            DATE_FORMAT(date, '%M') AS month,
                                            SUM(amount) AS total_amount,
                                            COUNT(*) AS payment_count
                                        FROM payments 
                                        WHERE DATE_FORMAT(date, '%M') <> ''
                                        GROUP BY MONTH(date)";
            $sqlSelectedMonthPayments = "SELECT amount AS total_amount,
                                            COUNT(*) AS payment_count
                                        FROM payments 
                                        WHERE DATE_FORMAT(date, '%M') <> ''
                                        GROUP BY MONTH(date)";                            
                                        
        } else {
            $sqlSelectedMonthPayments = "SELECT 
                                            DATE_FORMAT(date, '%M') AS month,
                                            SUM(amount) AS total_amount,
                                            COUNT(*) AS payment_count
                                        FROM payments 
                                        WHERE DATE_FORMAT(date, '%M') = '$selectedMonth'
                                        GROUP BY MONTH(date)";
        }
        
        $resultSelectedMonthPayments = mysqli_query($conn, $sqlSelectedMonthPayments);
        
        // Build the HTML table for the selected month's performance
        $tableBody = '';
        if ($resultSelectedMonthPayments) {
            while ($row = mysqli_fetch_assoc($resultSelectedMonthPayments)) {
                $tableBody .= "<tr>";
                $tableBody .= "<td>" . $row["month"] . "</td>";
                $tableBody .= "<td>" . $row["total_amount"] . "</td>";
                $tableBody .= "<td>" . $row["payment_count"] . "</td>";
                $tableBody .= "<td>" . $monthlyTarget . "</td>";
                $tableBody .= "<td>" . ($row["total_amount"] - $monthlyTarget) . "</td>";
                $tableBody .= "<td>" . ($row["total_amount"] / $monthlyTarget * 100) . "%</td>";
                $tableBody .= "</tr>";
            }
        } else {
            $tableBody .= "<tr><td colspan='6'>No results found.</td></tr>";
        }
        
        // Return the table body content only
        echo $tableBody;
        
    } elseif(isset($_GET['business'])){
        // Get the selected month from the AJAX request
        $selectedBusiness = $_GET['business'];
        
        // Query to get the monthly performance for the selected month
        if($selectedBusiness === 'all'){
            $sqlPerforming = "SELECT * FROM performance";
        } else {
            $sqlPerforming = "SELECT * FROM performance WHERE location_name=? ";
        }
        
        $stmt = $conn->prepare($sqlPerforming);
    
        if ($selectedBusiness !== 'all') {
            $stmt->bind_param("s", $selectedBusiness);
        }
    
        $stmt->execute();
        $resultTable = $stmt->get_result();
    
        //$resultTable = $conn->query($sqlPerforming);
        
        // Build the HTML table for the selected month's performance
        $tableBody = '';
        if ($resultTable->num_rows > 0) {
            while ($row = $resultTable->fetch_assoc() ) {
                $tableBody .= "<tr>";
                $tableBody .= "<td>" . $row["location_name"] . "</td>";
                $tableBody .= "<td>" . $row["cashIn"] . "</td>";
                $tableBody .= "<td>" . $row["cashOut"] . "</td>";
                $tableBody .= "<td>" . $row["income"] . "</td>";
                $tableBody .= "<td>" . $row["percent"] . "</td>";
                $tableBody .= "<td>" . $row["date"] . "</td>";
                $tableBody .= "</tr>";
            }
        } else {
            $tableBody .= "<tr><td colspan='6'>No results found.</td></tr>";
        }
        
        // Return the table body content only
        echo $tableBody;
    }
    
    
    

?>
