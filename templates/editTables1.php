<?php
    require_once __DIR__ .'/../vendor/autoload.php';
    require_once __DIR__ .'/../templates/crypt.php';
    require_once __DIR__ .'/../templates/sendsms.php';
    require_once __DIR__ .'/../templates/logger.php';

    use Dotenv\Dotenv;
    use GuzzleHttp\Client;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__. '/../');
    $dotenv->load();

    // Database connection
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    // Replace this with your own logic to retrieve payment details from the database
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Check if the request method is POST to edit the expenses table
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get the JSON data from the request body
        $requestData = file_get_contents("php://input");
        $expenseData = json_decode($requestData);
        
        $table = $expenseData->table;
        
        // Update table with decoded json data
        if ($expenseData !== null && $table === 'expenses') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $price = $expenseData->price;
            $quantity = $expenseData->quantity;
            $date = $expenseData->date;
            
            $name = encrypt($name);
            $price = encrypt($price);
            $quantity = encrypt($quantity);
            $date = encrypt($date);
            
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET name=?, price=?, quantity=?, date=? WHERE id=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("sssss", $name, $price, $quantity, $date, $id);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false);
            }
    
            // Close the database connection
            $stmt->close();
            //$conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        } elseif ($expenseData !== null && $table === 'payments') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $phone = $expenseData->phone;
            $services = $expenseData->services;
            $amount = $expenseData->amount;
            $staff_name = $expenseData->staff_name;
            $staff_phone = $expenseData->staff_phone;
            $date = $expenseData->date;
            
            $name1 = encrypt($name);
            $phone1 = encrypt($phone);
            $services1 = encrypt($services);
            $amount1 = encrypt($amount);
            $staff_name1 = encrypt($staff_name);
            $staff_phone1 = encrypt($staff_phone);
            $date1 = encrypt($date);
            
            $sqlUpdate = "UPDATE $table SET name=?, phone=?, services=?, amount=?, staff_name=?, staff_phone=?, date=? WHERE s_no=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssssssss", $name1, $phone1, $services1, $amount1, $staff_name1, $staff_phone1, $date1, $id);
            
            if($stmt->execute() === true){
                //refresh customer loyalty points
                refreshLoyaltyPoints($conn);
                
                $response = array("success" => true);
            } else {
                $response = array("success" => false);
            }
            
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        }elseif ($expenseData !== null && $table === 'inventory') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->name;
            $price = $expenseData->price;
            $quantity = $expenseData->quantity;
            $date = $expenseData->date;
            $itemCategory = $expenseData->itemCategory;
            $supplier = $expenseData->supplier;
            
            $name = encrypt($name);
            $price = encrypt($price);
            $quantity = encrypt($quantity);
            $date = encrypt($date);
            $itemCategory = encrypt($itemCategory);
            $supplier = encrypt($supplier);
            
    
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET name=?, price=?, quantity=?, date=?, itemCategory=?, supplier=? WHERE id=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("sssssss", $name, $price, $quantity, $date, $itemCategory, $supplier, $id);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false);
            }
    
            // Close the database connection
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
        }elseif ($expenseData !== null && $table === 'staff') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->staff_name;
            $phone = $expenseData->staff_phone;
            $email = $expenseData->staff_email;
            $disb_target_count = $expenseData->disb_target_count;
            $disb_target_volume = $expenseData->disb_target_volume;
            $name = encrypt($name);
            $phone = encrypt($phone);
            $email = encrypt($email);
            $disb_target_count = encrypt($disb_target_count);
            $disb_target_volume = encrypt($disb_target_volume);
            
            //get prevPhone before updating
            $sqlPrevPhone = $conn->query("SELECT * FROM staff WHERE staff_no = $id");
            $sqlPrevPhoneRow = $sqlPrevPhone->fetch_assoc();
            $prevStaffPhone = $sqlPrevPhoneRow['staff_phone'];
            
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET staff_name=?, staff_phone=?, staff_email=?, disb_target_count=? ,disb_target_volume=? WHERE staff_no=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("sssssi", $name, $phone, $email, $disb_target_count, $disb_target_volume, $id);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                $sqlUpdateUser = "UPDATE users SET email=?, username=?, phone=? WHERE staff_no=?";
                $stmt1 = $conn->prepare($sqlUpdateUser);
                $stmt1->bind_param("sssi", $email, $phone, $phone, $id);
                $stmt1->execute();
                $stmt1->close();
                
                //update customers and loans tables with new staff phone number
                $conn->query("UPDATE customers SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loans SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loan_applications SET staff_phone = '$phone' WHERE staff_phone='$prevStaffPhone'");
                $conn->query("UPDATE loan_appraisals SET action_by = '$phone' WHERE action_by='$prevStaffPhone'");
                
                //Log activity
                $action = "Edited staff details for " . decrypt($name) ;
                logAction($action);

                
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false);
            }
    
            // Close the database connection
            $stmt->close();
            //$conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
            
        }elseif ($expenseData !== null && $table === 'customers') {
            // Extract the data from the JSON object
            $id = $expenseData->id;
            $name = $expenseData->customer_name;
            $phone = $expenseData->customer_phone;
            $email = $expenseData->customer_email;
            $name = encrypt($name);
            $phone = encrypt($phone);
            $email = encrypt($email);
            $rate = encrypt($rate);
            
            // Use prepared statements to prevent SQL injection
            $sqlUpdate = "UPDATE $table SET customer_name=?, customer_phone=?, customer_email=?, rate=? WHERE customer_no=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssssi", $name, $phone, $email, $rate, $id);
    
            if ($stmt->execute() === true) {
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false);
            }
    
            // Close the database connection
            $stmt->close();
            //$conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
            
        } elseif ($expenseData !== null && $table === 'location') {
            // Extract the data from the JSON object
            $s_no = $expenseData->id;
            $location_name = $expenseData->location_name;
            $description = $expenseData->description;
            
            //get the old location_name
            $sqlGetOldName = "SELECT location_name FROM $table WHERE s_no=$s_no";
            $sqlGetOldName1 = $conn->query($sqlGetOldName);
            $sqlGetOldNameResult = $sqlGetOldName1->fetch_assoc();
            $oldLocationName = $sqlGetOldNameResult['location_name'];
            
            // Use prepared statements to prevent SQL injection while updating new location_name
            $sqlUpdate = "UPDATE $table SET location_name=?, description=? WHERE s_no=?";
            $stmt = $conn->prepare($sqlUpdate);
            $stmt->bind_param("ssi", $location_name, $description, $s_no);
    
            if ($stmt->execute() === true) {
                // Database update was successful
                
                //$locationData = $oldLocationName . '>' . $location_name;
                
                //$sever_name = $_SERVER['SERVER_NAME'];
                //$url = $sever_name . '/test.php?data=' . $locationData;
                
                //$client = new Client();
                //$response = $client->request('GET', $url);
                //$response = $response->getBody();
                
                //$jsonResponse = 'response.json';
                
                //file_put_contents($jsonResponse, $response);
                
                $response = array("success" => true);
            } else {
                // Database update failed
                $response = array("success" => false);
            }
            
            // Close the database connection
            $stmt->close();
            $conn->close();
    
            // Send a JSON response to the client
            header("Content-Type: application/json");
            echo json_encode($response);
            
        } else {
            // JSON data could not be decoded
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "Invalid JSON data"));
        }
    }
    
    function refreshLoyaltyPoints($conn){
        
        $getPhoneList = $conn->query("SELECT custPhone FROM customers");
        
        if ($getPhoneList->num_rows > 0){
            while ($getPhoneListRow = $getPhoneList->fetch_assoc()){
                $phoneNos = $getPhoneListRow['custPhone'];
                
                //Get total payments made by phone number
                $totalPaymentsMade = $conn->query("SELECT amount FROM payments WHERE phone='$phoneNos'");
                if($totalPaymentsMade->num_rows > 0 ){
                    $totalPayments = 0;
                    while ($totalPaymentsMadeRow = $totalPaymentsMade->fetch_assoc()){
                        $totalPayment = $totalPaymentsMadeRow['amount'];
                        $amount = decrypt($totalPayment);
                        $totalPayments += $amount;
                        
                        $totalPoints = $totalPayments * 0.01;
                        
                        //Get the redeemed amount from the customers table
                        $redeemedAmount = $conn->query("SELECT redeemed FROM customers WHERE custPhone='$phoneNos'");
                        if($redeemedAmount->num_rows > 0){
                            $redeemedAmountRow = $redeemedAmount->fetch_assoc();
                            $redeemedAmount = $redeemedAmountRow['redeemed'];
                            $redeemedAmount = decrypt($redeemedAmount);
                        } else {
                            $redeemedAmount = 0;
                        }
                        
                        //Calculate the pointsBal
                        $pointsBal = $totalPoints - $redeemedAmount;
                        
                        //Update the customers table
                        $totalPoints = encrypt($totalPoints);
                        $redeemedAmount = encrypt($redeemedAmount);
                        $pointsBal = encrypt($pointsBal);
                        
                        $updateCustomer = $conn->query("UPDATE customers SET points='$totalPoints', pointsBal='$pointsBal' WHERE custPhone='$phoneNos'");
                        
                        if($updateCustomer){
                            //all went well
                            //exit;
                        } else {
                            //echo "Error updating tables: " . $conn->error();
                            //exit;
                        }
                        
                    }
                } else {
                    //exit;
                }
            
            }
        } else {
            //exit;
        }
    }
    
    function updateLocation($new_value, $old_value){
        $column_name = "location_name";
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Fetch list of tables
        $tables_query = "SHOW TABLES";
        $tables_result = $conn->query($tables_query);
        
        if ($tables_result) {
            while ($row = $tables_result->fetch_row()) {
                $table_name = $row[0];
        
                // Check if the column exists in the table
                $column_check_query = "SHOW COLUMNS FROM $table_name LIKE '$column_name'";
                $column_check_result = $conn->query($column_check_query);
        
                if ($column_check_result->num_rows > 0) {
                    // Column exists, update the table
                    $update_query = "UPDATE $table_name SET $column_name = '$new_value' WHERE $column_name = '$old_value'";
                    
                    if ($conn->query($update_query) === TRUE) {
                        echo "<script> console.log('Table $table_name updated successfully'); </script><br>";
                    } else {
                        echo "<script> console.error('Error updating table $table_name: " . $conn->error . "'); </script><br>";
                    }
                }   else {
                    // Column does not exist in the table
                    echo "<script> console.warn('Column $column_name does not exist in table $table_name'); </script><br>";
                }
            }
        
            $tables_result->free();
        } else {
            echo "<script> console.error('Error fetching tables: " . $conn->error . "'); </script>";
        }
        
        // Close connection
        //$conn->close();
        
        return $response;
    }
?>