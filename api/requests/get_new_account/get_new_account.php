<?php
    require_once(__DIR__ . '/../../../vendor/autoload.php');
    require_once (__DIR__ . '/../../../templates/emailing.php');
    require_once (__DIR__ . '/../../../templates/sendsms.php');
    require_once (__DIR__ . '/../../../templates/crypt.php');

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
    
    $domain = $_GET['domain'];
    $userName = $_GET['username'];
    $phone = $_GET['phone'];
    $email = $_GET['email'];
    $custID = $_GET['custID'];
    $staff_no = '0';
    $role = 'Superadmin';
    
    $userName1 = encrypt($userName);
    $phone1 = encrypt($phone);
    $email1 = encrypt($email);
    //$staff_no = encrypt($staff_no);
    $role = encrypt($role);
    
    $stmt = $conn->prepare("INSERT INTO users (staff_no, username, phone, email, role, custID) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $staff_no, $userName1, $phone1, $email1, $role, $custID);
    
    if ($stmt->execute()) {
        //header("Location: bookings");
        //Email feedback to LFH Email
        //$email = $_ENV['THE_EMAIL'];
        $subject = 'New Account Creation';
        $replyTo = $emailFrom;
        
        $passResetPage = $domain . '/passwordReset';
        
        $body = 'Hi '. $email . ' 
        <br> <br> 
        Thank you for chosing us. Your account has been created successfully. You new credentials are as follows;' . '
        <br> Username: <b>'. $userName .' </b>. 
        <br> Email: <b>' . $email . '</b> . 
        <br> Domain: <b>' . $domain . '</b>
        <br> <br> Please go to ' . $passResetPage .' and use the email to reset your password.' . '<br>
        Alternatively, you can <a href="' . $passResetPage . '" style="display: inline-block; padding: 3px 5px; background-color: #4CAF50; color: white; text-align: center; text-decoration: none; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 3px;">Click Here</a> to reset.
        <br>
        <br> Thank you. <br> 
        <br> If you experience any challenge, please notify Support immediately by repling to this email!';
        
        $replyTo = "support@essentialapp.site";
    
        sendEmail($email, $subject, $body, $replyTo);
        
        //Communicate to customer
        $recipient = '+254' . substr($phone, -9);
                
        // Construct the SMS message
        $message = 'Dear Customer, thank you for choosing us. Your account has been created successfully. Please check your email for further instructions. Excel Tech Essentials www.essentialtech.site.';
        
        //$_SESSION['redirect_url'] = 'book'; 
        
        sendSMS($recipient, $message);
        
        exit();
    } else {
        //echo "Error: " . $sqlBooking . "<br>" . mysqli_error($conn);
    }
    exit();
    
?>