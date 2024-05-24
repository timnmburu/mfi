<?php
    require_once __DIR__ . '/../vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__ . '/../templates/emailing.php';
    require_once __DIR__ . '/../templates/cryptOtp.php';
    require_once __DIR__ . '/../templates/sendsms.php';
    require_once __DIR__ . '/../templates/crypt.php';
    
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    function passReset($email){
        
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = mysqli_connect($db_servername, $db_username, $db_password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $email1 = encrypt($email);
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email1);
        $stmt->execute();
        $results = $stmt->get_result();
    
        if ($results->num_rows === 1) {
            $rows = $results->fetch_assoc();
            $phone = decrypt($rows['phone']);
            // email found, generate password token
            $passToken = substr(str_shuffle("0123456789aaaaabbbbbcccccddddddeeeee"), 0, 15);
            $current_date_time = encrypt(date('Y-m-d H:i:s'));
            $encryptedToken = encrypt($passToken);
            
            $addPassToken = "UPDATE users SET token='$encryptedToken', password='', lastResetDate='$current_date_time' WHERE email='$email1'";
            $conn->query($addPassToken);

            // Fetch the username from the result
            $username = $rows['username'];
            $username1 = decrypt($username);
        
            // Call the sendEmail() function to notify the user about the password change
            $subject = 'Password Change Notification';
            
            $url = $_SERVER['SERVER_NAME'];
            $url .= '/new_password?pstk=';
            $url .= base64_encode($encryptedToken);
            
            
            $body = 'Hi <b>' . $username1 . '</b>,
            <br>
            Your new password is <b>' . $passToken . '</b>
            <br>
            Please copy and use it to reset your password within 1 hour.
            <br>
            Alternatively, you can <a href="' . $url . '" style="display: inline-block; padding: 3px 5px; background-color: #4CAF50; color: white; text-align: center; text-decoration: none; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 3px;">Click Here</a> to reset.
            <br>
            Thank you.
            <br><br>
            If you did not request for a new password, please notify the Admin immediately by replying to this email.';
            
            
            $replyTo = "info@essentialapp.site";
            
            $return = sendEmail($email, $subject, $body, $replyTo);
            
            $message = "Password reset request successful. Please check your email for instructions. " . $_SERVER['SERVER_NAME'] ;
            
            $return = sendSMS($phone, $message);
            
        } else {
          // query failed, show an error message to the user
          $return = "Invalid details";
        }
        
        return $return;
    }








?>