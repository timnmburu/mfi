<?php 
    require_once __DIR__ .'/../vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__ .'/notifications.php';
    require_once __DIR__ .'/getGeoLocation.php';
    //require_once __DIR__ .'/cryptOtp.php';
    require_once __DIR__ .'/sendsms.php';
    require_once __DIR__ .'/standardize_phone.php';
    require_once __DIR__ .'/emailing.php';
    require_once __DIR__ .'/crypt.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
    
    function setOtp(){    
        // Database connection
        $servername = $_ENV['DB_HOST'];
        $usernameD = $_ENV['DB_USERNAME'];
        $passwordD = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = mysqli_connect($servername, $usernameD, $passwordD, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $newOtp = rand(100000, 999999);
        
        $hashedOtp1 = encrypt($newOtp);
        $hashedOtp = base64_encode($hashedOtp1);
        
        $date = date('Y-m-d H:i:s');
        $date = encrypt($date);
        
        //Get user phone number and email
        $username = $_SESSION['username'];
        $username1 = encrypt($username);
        
        $result = $conn->prepare("SELECT * FROM users WHERE username=?");
        $result->bind_param("s", $username1);
        $result->execute();
        $result1 = $result->get_result();
        if($result1->num_rows > 0){
            $row = $result1->fetch_assoc();
            $phone = $row['phone'];
            $email = $row['email'];
        } else {
            $phone = encrypt('0');
            $email = encrypt('0');
        }
        
        //Add new otp to table
        $conn->query("INSERT INTO otpQ (phone, otpHash, dateInitiated) VALUES ('$phone', '$hashedOtp', '$date')");
        
        $phone1 = decrypt($phone);
        //Send SMS OTP
        $recipient = '254'. standardizePhoneNumber($phone1);
        $message = 'OTP: '. $newOtp;
        
        sendSMS($recipient, $message);
        
        //Send Email OTP
        $subject = 'OTP';
        $body = 'OTP: '. $newOtp;
        $replyTo='info@essentialapp.site';
        
        $email1 = decrypt($email);
        
        sendEmail($email1, $subject, $body, $replyTo);
        
        header("Location: ../verify");
        
        exit;
    }
    
    setOtp();
?>