<?php
    require_once __DIR__.'/../vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/../templates/standardize_phone.php';
    require_once __DIR__.'/../templates/sendsms.php';
    require_once __DIR__.'/../templates/crypt.php';
    require_once __DIR__.'/../templates/performanceUpdate.php';
    require_once __DIR__.'/../templates/counter.php';
    
    use Dotenv\Dotenv;
    
    function updateCustomer($conn, $custNo, $amount ){
        // Customer exists, update their points
        $sqlCheckCustomer = $conn->prepare("SELECT * FROM customers WHERE custID=?");
        $sqlCheckCustomer->bind_param("s", $custNo);
        $sqlCheckCustomer->execute();
        $resultCust = $sqlCheckCustomer->get_result();
        
        $resultRow1 = $resultCust->fetch_assoc();
        $currentPoints = decrypt($resultRow1['points']);
        $currentRedeemed = decrypt($resultRow1['redeemed']);
        $location_name = $resultRow1['location_name'];
        
        $points1 = $amount * 0.01;
        $newPoints = $points1 + intval($currentPoints) ;
        $newPointsBal = $newPoints - intval($currentRedeemed);
        
        $newPoints = encrypt($newPoints);
        $newPointsBal = encrypt($newPointsBal);
        
        $sqlPoints = "UPDATE customers SET points = '$newPoints', pointsBal = '$newPointsBal' WHERE custID = '$custNo' ";
        $conn ->query($sqlPoints);
        
        $column = 'repeat_customer_count';
        addCount($column, $location_name);
        
        return $newPointsBal;
    }
    
    function insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount){
        // Customer doesn't exist, create new customer and set their points
        $points1 = encrypt('0');
        $pointsBal1 = encrypt('0');
        $redeemed1 = encrypt('0');
        $name1 = encrypt($name);
        $location_name1 = $location_name;
        
        $sqlNewCustomer = ("INSERT INTO customers (custName, custPhone, points, redeemed, pointsBal, location_name, date) VALUES ('$name1', '$phone1', '$points1', '$redeemed1', '$pointsBal1', '$location_name1', '$date')");
        
        $points1 = encrypt($amount * 0.01);
        $sqlPoints = "UPDATE customers SET points = '$points1', pointsBal = '$points1' WHERE custPhone = '$phone1'";
        
        if ($conn->query($sqlNewCustomer) !== TRUE) {
            echo "Error creating new customer: " . $conn->error;
            exit();
        } else {
            $conn ->query($sqlPoints);
            
        }
        
        $column = 'new_customer_count';
        addCount($column, $location_name);
        
        return $points1;
    }
    
    function processPayment($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $paymentMode, $date, $notify, $location_name){
        $date = date('Y-m-d H:i:s');
        $date = encrypt($date);
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $standardizedInput = standardizePhoneNumber($phoneNumber);
        
        $phone = '0'. $standardizedInput;
        $phone1 = encrypt($phone);
        
        // Check if customer already exists in customers table
        $sqlCheckCustomer = $conn->prepare("SELECT custID, custPhone FROM customers WHERE custPhone='$phone1'");
        $sqlCheckCustomer->execute();
        $resultCust = $sqlCheckCustomer->get_result();
        
        if($resultCust->num_rows > 0){
            $custNo = $resultCust->fetch_assoc()['custID'];
            
            $pointsBal = updateCustomer($conn, $custNo, $amount);
        } else {
            $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount);
        }
        /*
        // Array to store decrypted phone numbers
        $decryptedPhones = array();
        if($resultCust->num_rows > 0){
            while ($rowDecrypted = $resultCust->fetch_assoc()) {
                // Decrypt each encrypted phone number using the decrypt() function
                $decryptedPhone = decrypt($rowDecrypted['custPhone']);
            
                // Add the decrypted phone number to the array
                $decryptedPhones[] = $decryptedPhone;
                $decryptedPhonesWithCustNo[$decryptedPhone] = $rowDecrypted['custID'];
            }
            
            if (array_key_exists($phone, $decryptedPhonesWithCustNo)) {
    
                $custNo = $decryptedPhonesWithCustNo[$phone];
                
                $pointsBal = updateCustomer($conn, $custNo, $amount);
    
            } else {
                $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount);
            }
        } else {
            $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount);
        }
        */
        
        $services1 = encrypt($services);
        $amount1 = encrypt($amount);
        $staff_name1 = encrypt($staff_name);
        $staff_phone1 = encrypt($staff_phone);
        $paymentMode1 = encrypt($paymentMode);
        $name1 = encrypt($name);
        
        if($resultCust->num_rows > 0){
            $custID = $custNo;
        } else {
            $resultMaxCustID = $conn->query("SELECT MAX(custID) as maxCustID FROM customers");
            $custID = $resultMaxCustID->fetch_assoc()['maxCustID'];
        }
        
        $sqlPay = $conn->prepare("INSERT INTO payments (custID, name, phone, services, amount, staff_name, staff_phone, date, payment_mode, location_name ) 
        VALUES (?,?,?,?,?,?,?,?,?,? )");
        $sqlPay->bind_param("ssssssssss", $custID, $name1, $phone1, $services1, $amount1, $staff_name1, $staff_phone1, $date, $paymentMode1, $location_name);
        
        if (decrypt($paymentMode) === 'KCB Paybill') {
            //Query wallet balance
            $sqlBalKcb = "SELECT kcb FROM wallet";
            
            $resultKcb = $conn->query($sqlBalKcb);
            
            if ($resultKcb->num_rows > 0) {
                while ($row = $resultKcb->fetch_assoc()) {
                    $accBalKcb= decrypt($row["kcb"]);
                }
            }
            
            $newKcbWalletBal = intval($accBalKcb) + intval($amount);
            
            $sqlUpdateWallet = "UPDATE wallet SET kcb='$newKcbWalletBal'";
            $conn->query($sqlUpdateWallet);
        } else {
            //do nothing
        }
        
        
        if ($sqlPay->execute() === TRUE) {
            // Payment and points successfully inserted/update
            //set correct phone format
            $recipient = '+254' . substr($phone, -9);
            
            if($staff_phone === '0781099212'){
            //Construct SMS to customer
            $message = 'Dear ' . $name . ', payment for your booking is received: Kshs.' . $amount . '. Loyalty Points now at ' . decrypt($pointsBal) . '. Thank you for choosing us. www.essentialtech.site';
            } else {
            //Construct SMS to customer
            $message = 'Dear ' . $name . ', thank you for doing business with us. Payment received: Kshs.' . $amount . '. Loyalty Points now at ' . decrypt($pointsBal) . '. See you again. www.essentialtech.site';
            }
            
            //send SMS
            if($notify > 0){
                sendSMS($recipient, $message);
            }
            
            //updateCashIn($amount, $location_name);
            
            
            header('Location: pay');
    

            exit();
        } else {
            echo "Error inserting payment or updating points: " . $conn->error;
            header('Location: pay');
        }
        
        $conn->close();

    } 
    
    
    
    //Where 1 customer is served by 2 staff, consolidate the amount paid for all services offered and capture staff
    function processPayment2($name, $phoneNumber, $services, $amount, $staff_name, $staff_phone, $services2, $amount2, $staff_name2, $staff_phone2, $paymentMode, $date, $notify, $location_name){
            
        $date = date('Y-m-d H:i:s');
        $date = encrypt($date);
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $standardizedInput = standardizePhoneNumber($phoneNumber);
        
        $phone = '0'. $standardizedInput;
        $phone1 = encrypt($phone);
        
        // Check if customer already exists in customers table
        $sqlCheckCustomer = $conn->prepare("SELECT custID, custPhone FROM customers WHERE custPhone='$phone1'");
        $sqlCheckCustomer->execute();
        $resultCust = $sqlCheckCustomer->get_result();
        
        if($resultCust->num_rows > 0){
            $custNo = $resultCust->fetch_assoc()['custID'];
            
            $amount4 = $amount + $amount2;
            $pointsBal = updateCustomer($conn, $custNo, $amount4);
        } else {
            $amount4 = $amount + $amount2;
            $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount4);
        }
        
        /*
        // Array to store decrypted phone numbers
        $decryptedPhones = array();
        
        if($resultCust->num_rows > 0){
            while ($rowDecrypted = $resultCust->fetch_assoc()) {
                // Decrypt each encrypted phone number using the decrypt() function
                $decryptedPhone = decrypt($rowDecrypted['custPhone']);
            
                // Add the decrypted phone number to the array
                $decryptedPhones[] = $decryptedPhone;
                $decryptedPhonesWithCustNo[$decryptedPhone] = $rowDecrypted['custID'];
            }
            
            if (array_key_exists($phone, $decryptedPhonesWithCustNo)) {
    
                $custNo = $decryptedPhonesWithCustNo[$phone];
                $amount4 = $amount + $amount2;
                $pointsBal = updateCustomer($conn, $custNo, $amount4);
            
            } else {
                $amount4 = $amount + $amount2;
                $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount4);
            }
        } else {
            $amount4 = $amount + $amount2;
            $pointsBal = insertNewCustomer($conn, $name, $location_name, $phone1, $date, $amount4);
        }
        */
        
        
        $name1 = encrypt($name);
        $services1 = encrypt($services);
        $services21 = encrypt($services2);
        $amount1 = encrypt($amount);
        $staff_name1 = encrypt($staff_name);
        $staff_phone1 = encrypt($staff_phone);
        $amount21 = encrypt($amount2);
        $staff_name21 = encrypt($staff_name2);
        $staff_phone21 = encrypt($staff_phone2);
        $paymentMode1 = encrypt($paymentMode);
        
        if($resultCust->num_rows > 0){
            $custID = $custNo;
        } else {
            $resultMaxCustID = $conn->query("SELECT MAX(custID) as maxCustID FROM customers");
            $custID = $resultMaxCustID->fetch_assoc()['maxCustID'];
        }
        
        $sqlPay = ("INSERT INTO payments (custID, name, phone, services, amount, staff_name, staff_phone, date, payment_mode, location_name ) 
        VALUES ('$custID', '$name1', '$phone1', '$services1', '$amount1', '$staff_name1', '$staff_phone1', '$date', '$paymentMode1', '$location_name')");
        
        $sqlPay2 =("INSERT INTO payments (custID, name, phone, services, amount, staff_name, staff_phone, date, payment_mode, location_name ) 
        VALUES ('$custID', '$name1', '$phone1', '$services21', '$amount21', '$staff_name21', '$staff_phone21', '$date', '$paymentMode1', '$location_name')");
        
        if (decrypt($paymentMode1) === 'KCB Paybill') {
            //Query wallet balance
            $sqlBalKcb = "SELECT kcb FROM wallet";
            
            $resultKcb = $conn->query($sqlBalKcb);
            
            if ($resultKcb->num_rows > 0) {
                while ($row = $resultKcb->fetch_assoc()) {
                    $accBalKcb= decrypt($row["kcb"]);
                }
            }
            
            $newKcbWalletBal = intval($accBalKcb) + intval($amount) + intval($amount2);
            
            $sqlUpdateWallet = "UPDATE wallet SET kcb='$newKcbWalletBal'";
            $conn->query($sqlUpdateWallet);
        } else {
            //do nothing
        }
        
        
        if ($conn->query($sqlPay) === TRUE && $conn->query($sqlPay2) === TRUE) {
            // Payment and points successfully inserted/updated
            //set correct phone format
            $recipient = '+254' . substr($phone, -9);
            $amount3 = $amount + $amount2;
            
            //Construct SMS to customer
            $message = 'Dear ' . $name . ', thank you for doing business with us. Payment received: Kshs.' . $amount3 . '. Loyalty Points now at ' . decrypt($pointsBal) . '. See you again. www.essentialtech.site';
            
            $_SESSION['redirect_url'] = 'pay'; //Save the session to return back after processing
            
            //send SMS
            if($notify > 0){
                sendSMS($recipient, $message);
            }
                
            header('Location: pay');
            

            //exit();
        } else {
            echo "Error inserting payment or updating points: " . $conn->error;
            header('Location: pay');
        }
        
        $conn->close();
    }
    
?>
