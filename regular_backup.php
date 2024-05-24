<?php

    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/emailing.php';

    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database credentials
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    // Create connection
    $mysqli = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    // Check connection
    if ($mysqli->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

    // Retrieve the list of tables in the database
    $tables = array();
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
    
    // Generate the SQL content for each table
    $sql_data = '';
    foreach ($tables as $table) {
        $result = $mysqli->query("SELECT * FROM $table");
        $sql_data .= "DROP TABLE IF EXISTS $table;\n";
        $row2 = $mysqli->query("SHOW CREATE TABLE $table")->fetch_row();
        $sql_data .= $row2[1] . ";\n";
    
        while ($row = $result->fetch_assoc()) {
            $row = array_map('addslashes', $row);
            $sql_data .= "INSERT INTO $table VALUES ('" . implode("','", $row) . "');\n";
        }
    }
    
    // Close the database connection
    $mysqli->close();
    
    $date = date('dmY');
    
    $temp_file_path = 'fileStore/regular_backups/regular_backups_' . date('Ymd_His', strtotime('+3 hours')) . '.sql';
    file_put_contents($temp_file_path, $sql_data);
    
    
    
    sendEmailBackups($temp_file_path);
    
    // Set headers to force file download
    //header("Content-Type: application/sql");
    //header("Content-Disposition: attachment; filename=$date database_backup.sql ");
    
    // Output the SQL data to the browser
    //echo $sql_data;
?>
