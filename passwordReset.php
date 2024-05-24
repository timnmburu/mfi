<?php 
    require_once __DIR__.'/vendor/autoload.php'; // Include the Dotenv library
    require_once __DIR__.'/templates/emailing.php';
    //require_once __DIR__.'/templates/cryptOtp.php';
    require_once __DIR__.'/templates/sendsms.php';
    require_once __DIR__.'/templates/crypt.php';
    
    use Dotenv\Dotenv;

    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $return = passReset($conn, $email);
        
        if($return){
            header('Location: new_password');
        } else {
            header('Location: new_password');
        }
    }

    if(isset($_POST['email'])){
        $email = $_POST['email'];
        $return = passReset($conn, $email);
    } elseif(isset($_GET['email'])) {
        $email = $_GET['email'];
        $return = passReset($conn, $email);
    } else {
        $email = null;
        //Do nothing
    }
    
    function passReset($conn, $email){
        $email1 = encrypt($email);
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email1);
        $stmt->execute();
        $results = $stmt->get_result();
    
        if ($results->num_rows === 1) {
            $rows = $results->fetch_assoc();
            $phone = $rows['phone'];
            $phone1 = decrypt($phone);
            // email found, generate password token
            $passToken = substr(str_shuffle("0123456789aaaaabbbbbcccccddddddeeeee"), 0, 15);
            $current_date_time = date('Y-m-d H:i:s');
            $current_date_time = encrypt($current_date_time);
            $encryptedToken = encrypt($passToken);
            
            $addPassToken = "UPDATE users SET token='$encryptedToken', password='', lastResetDate='$current_date_time' WHERE email='$email1'";
            $conn->query($addPassToken);

            // Fetch the username from the result
            $username1 = $rows['username'];
            $username = decrypt($username1);
        
            // Call the sendEmail() function to notify the user about the password change
            $subject = 'Password Change Notification';
            
            $url = $_SERVER['SERVER_NAME'];
            $url .= '/new_password?pstk=';
            $url .= base64_encode($encryptedToken);
            
            $body = 'Hi <b>' . $username . '</b>,
            <br>
            Your new password is <b>' . $passToken . '</b>
            <br>
            Please copy and use it to reset your password within 1 hour.
            <br>
            Alternatively, you can <a href="' . $url . '" style="display: inline-block; padding: 3px 5px; background-color: #4CAF50; color: white; text-align: center; text-decoration: none; font-size: 12px; margin: 1px 1px; cursor: pointer; border-radius: 3px;">Click Here</a> to reset.
            <br>
            Thank you.
            <br><br>
            If you did not request for a new password, please notify Support immediately by replying to this email.';
            
            
            $replyTo = "support@essentialapp.site";
            
            $return = sendEmail($email, $subject, $body, $replyTo);
            
            $message = "Password reset request successful. Please check your email for instructions. " . $_SERVER['SERVER_NAME'] ;
            
            //$return = sendSMS($phone1, $message);
            
        } else {
          // query failed, show an error message to the user
          $return = "Invalid details";
        }
        
        return $return;
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>Password Reset</title>
        
        <!-- Bootstrap Css -->
        <link href="bootstrap1.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- App favicon -->
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    </head>
    <body class="auth-body-bg">
    <div class="bg-overlay"></div>        
    <div class="wrapper-page">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-body">
                    <main class="py-0">
                        <div class="card card-success"> 
                            <div class="card-header p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <a href="/" class="logo logo-admin">
                                        <img src="logos/Logo.jpg" height="80" alt="logo" class="auth-logo">
                                    </a>
                                    <h4 class="mt-3 mb-1 fw-semibold font-18">Password Reset</h4>
                                </div>
                            </div>
                        </div>
                        <div class="p-3">
                            <?php if (isset($error_message)) { ?>
                            <p style="color: red;"><?php echo $error_message; ?></p>
                            <?php } ?>
                            <form id="reset" class="form-horizontal mt-3" action="" method="post">
                                <!-- <div class="g-recaptcha" data-sitekey="6LfWwHEoAAAAAKvnaNAcsByowYBUinPL5yo6jtkl"></div> -->
                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input id="email" name="email" class="form-control" type="email" required  placeholder="Email" autofocus/>
                                    </div>
                                </div>
                                <div class="form-group mb-3 text-center row mt-3 pt-1">
                    				<div class="col-12">
                    					<!-- <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfWwHEoAAAAAKvnaNAcsByowYBUinPL5yo6jtkl" value="reset" type="submit">Reset</button> -->
                                        <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfWwHEoAAAAAKvnaNAcsByowYBUinPL5yo6jtkl" data-callback='onSubmit' data-action='submit'>Reset</button>
                                        <br>
                    				</div>
                                </div>
                            </form>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
    <script>
        function onSubmit(token) {
            document.getElementById("reset").submit();
        }
    </script>
    </body>
</html>