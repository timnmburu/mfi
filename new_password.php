<?php
    require_once __DIR__.'/vendor/autoload.php';
    //require_once __DIR__.'/templates/cryptOtp.php';
    require_once __DIR__.'/templates/standardize_phone.php';
    require_once __DIR__.'/templates/crypt.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $email = encrypt($email);
        if(isset($_GET['pstk'])){
            $passToken = $_GET['pstk'];
            $passToken = base64_decode($passToken);
        } else {
            $passToken = $_POST['passToken'];
            $passToken = encrypt($passToken);
        }
        
        $newPassword = $_POST['newPassword'];
        $repeatPassword = $_POST['repeatPassword'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $results = $stmt->get_result();
    
        if ($results->num_rows === 1) {
            if($newPassword === $repeatPassword){
                // Generate the hash of the password
                $row = $results->fetch_assoc();
                
                $token = $row['token'];
                
                //Check whether 1 hr is past
                $dateInitiated = $row['lastResetDate'];
                $dateInitiated = decrypt($dateInitiated);
                
                $initial_timestamp = strtotime($dateInitiated);
                $current_timestamp = date('Y-m-d H:i:s');
                $current_timestamp2 = strtotime($current_timestamp);
            
                // Calculate the difference in seconds
                $difference = ($current_timestamp2 - $initial_timestamp) / 3600;
                
                //Check validity (1hr)
                if($difference > 1 ){
                    $error_message = "Timeout. Please request for new password reset and complete within 1 hour";
                    $error_message .= "<br><br>";
                    $error_message .= "<a href='passwordReset'>Reset Afresh</a>";
                    //sleep(3);
                } else {
                    
                    if ($passToken === $token) {
                            $encryptedPassword = encrypt($newPassword);
                            $changePass = "UPDATE users SET password='$encryptedPassword', token='' WHERE email='$email'";
                            $conn->query($changePass);
                            header('Location: login');
                       
                    } else {
                        // login failed, show an error message to the user
                        $error_message = "Invalid Emailed Password";
                    }
                }
            } else {
                $error_message = "Passwords do not match";
            }
        } else {
          // query failed, show an error message to the user
          $error_message = "Invalid details";
        }
    }
    
    if(isset($_GET['pstk']) && !empty($_GET['pstk'])){
        $pstk = $_GET['pstk'];
        $pstk = base64_decode($pstk);
        
        $params = true;
        
        $sqlGetEmail1 = $conn->prepare("SELECT * FROM users WHERE token=?");
        $sqlGetEmail1->bind_param("s",$pstk);
        $sqlGetEmail1->execute();
        $resultsEmail = $sqlGetEmail1->get_result();
        if($resultsEmail){
            if($resultsEmail->num_rows > 0){
                $resultsEmail1 = $resultsEmail->fetch_assoc();
                $email1 = $resultsEmail1['email'];
                $email1 = decrypt($email1);
                
                $decryptedPstk = decrypt($pstk);
            } else {
                $email1 = '';
                $decryptedPstk = '';
                $error_message = "Invalid reset details.";
            }
        } else {
            $error_message = $conn->error;
        }
    } else {
        $pstk ='';
        $params = false;
        $email1 = '';
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
    <div class="bg-overlay">
    </div>        
    <div class="wrapper-page">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-body">
                    <main class="py-0">
                        <div class="card card-success"> 
                            <div class="card-header p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <a href="" class="logo logo-admin">
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
                        </div>
                        <form id="reset" class="form-horizontal mt-3" action="" method="post">
                            <!-- <div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY"></div> -->
                            
                            <?php 
                                if($params){
                            ?>
                                <div hidden class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input id="email" name="email" class="form-control" type="email" required value="<?php echo $email1; ?>" placeholder="Email.." autofocus/>
                                    </div>
                                </div>
                                <div hidden class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input id="passToken" name="passToken" class="form-control" type="password" required="" value="<?php echo $pstk; ?>" placeholder="Emailed Password" autofocus/>
                                    </div>
                                </div>
                            <?php
                                } else {
                            ?>
                                <div  class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input id="email" name="email" class="form-control" type="email" required value="" placeholder="Email.." autofocus/>
                                    </div>
                                </div>
                                <div class="form-group mb-3 row">
                                    <div class="col-12">
                                        <input id="passToken" name="passToken" class="form-control" type="password" required="" value="" placeholder="Emailed Password" autofocus/>
                                    </div>
                                </div>
                            
                            <?php
                                }
                            ?>
                            <div class="form-group mb-3 row">
                                <div class="col-12">
                                    <input id="newPassword" name="newPassword" class="form-control" type="password" required="" value="" placeholder="New Password" autofocus/>
                                </div>
                            </div>
                            <div class="form-group mb-3 row">
                                <div class="col-12">
                                    <input id="repeatPassword" name="repeatPassword" class="form-control" type="password" required="" value="" placeholder="Repeat Password" autofocus/>
                                </div>
                            </div>	
                            <div class="form-group mb-3 text-center row mt-3 pt-1">
                                <div class="col-12">
                                    <!-- <button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="reset" type="submit">Reset Password</button> -->
                                    <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfWwHEoAAAAAKvnaNAcsByowYBUinPL5yo6jtkl" data-callback='onSubmit' data-action='submit'>Reset Password</button>
                                </div>
                            </div>
                        </form>
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