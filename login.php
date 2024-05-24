<?php 
    require_once 'vendor/autoload.php'; // Include the Dotenv library
    require_once 'templates/notifications.php';
    require_once 'templates/getGeoLocation.php';
    require_once 'templates/cryptOtp.php';
    require_once 'templates/standardize_phone.php';
    require_once 'templates/logger.php';
    require_once 'templates/crypt.php';
    
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Database connection
    $servername = $_ENV['DB_HOST'];
    $usernameD = $_ENV['DB_USERNAME'];
    $passwordD = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = New mysqli($servername, $usernameD, $passwordD, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $username = strtolower($username);
        $username1 = encrypt($username);
        $password = $_POST['password'];
        $password1 = encrypt($password);
        
        try{
            //Get Geo Location
            //notify(getGeoLocation($_ENV['IPinfo_API_KEY']));
                
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s",$username1);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if($result->num_rows === 1){
                $storedPassword = $row['password'];
                $storedToken = $row['token'];
            } else {
                $storedPassword = '';
                $storedToken = '';
            }
            
            //$correctPassword = decryptLogin($storedPassword);
            
            if ($password1 === $storedPassword) {
                //Get Geo Location
                $data=getGeoLocation($_ENV['IPinfo_API_KEY']);
                $url = $_SERVER['REQUEST_URI'];
                checkLocation($data, $url);
                
                //Get account details    
                try{
                    include "api/requests/get_account_details/get_account_details.php";
                } catch (Exception $e){
                    session_start();
                    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
                    header("Location: errorpage");
                    exit();
                }
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start(); 
                }
                
                session_unset();
                
                $checkAccess = $conn->query("SELECT subAmount, lastPayAmount, subDate FROM account");
                
                if($checkAccess->num_rows < 1){
                    //if there are no entries
                    $_SESSION['access'] = false;
                } else {
                    $totalBilled = 0;
                    $totalPaid = 0;
                    
                    while($rows = $checkAccess->fetch_assoc()){
                        $billed = $rows['subAmount'];
                        $paid = $rows['lastPayAmount'];
                        $billed1 = intval(decrypt($billed));
                        $paid1 = intval(decrypt($paid));
                        $totalBilled += $billed1;
                        $totalPaid += $paid1;
                        
                    }
                    
                    if($totalBilled > $totalPaid){
                        $dayOne = date('Y-m-01');
                        $dayFive = date('Y-m-d', strtotime($dayOne . ' + 4 days'));
                        
                        $message = "Reminder to make your monthly subscription fee by " . $dayFive;
                        $level = 'Superadmin';
                        saveNotification($message, $level);
                        
                        $today = date('Y-m-d');
                        
                        if(strtotime($today) > strtotime($dayFive)){
                            $_SESSION['access'] = false;
                            $_SESSION['amount_due'] = $totalBilled - $totalPaid;
                        } else {
                            $_SESSION['access'] = true;
                            $_SESSION['amount_due'] = $totalBilled - $totalPaid;
                        }
                    } else {
                        $_SESSION['access'] = true;
                        $_SESSION['amount_due'] = 0;
                    }
                }
                
                $_SESSION['username'] = $username;
                
                $checkUserRole = "SELECT * FROM users WHERE username='$username1'";
                $resultRole = $conn->query($checkUserRole);
                $rowRole = $resultRole->fetch_assoc();
                $role = $rowRole['role'];
                $role1 = decrypt($role);
                $phone = $rowRole['phone'];
                $phone = decrypt($phone);
                $staffNo = $rowRole['staff_no'];
                $location_name = $rowRole['location_name'];
                //$memberNo = (!isset($staffNo) || $staffNo === null || $staffNo === 0)? 0: $staffNo;
                
                $_SESSION['userphone'] = $phone;
                $_SESSION['member_no'] = $staffNo; 
                $_SESSION['location_name'] = $location_name;
                
                if($role1 === 'Superadmin'){
                    $admin = 2;
                } elseif ($role1 === 'Admin'){
                    $admin = 1;
                } else {
                    $admin = false;
                } 
                
                $_SESSION['admin'] = $admin;

                // Retrieve the stored redirect URL from the session
                $redirectUrl = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/';
                unset($_SESSION['redirect_url']); // Clear the stored URL
                header("Location: $redirectUrl"); // Redirect to the target page
                $date = date('Y-m-d H:i:s');
                $action = "Login";
                logAction($action);
                
                notify("Login successful by user: " . $username . " in " . $_SERVER['HTTP_HOST']);
                exit;
            } else if($password1 == $storedToken){
                //$newPassUrl = $_SERVER['SERVER_NAME'];
                $newPassUrl .= 'new_password?pstk=';
                $newPassUrl .= base64_encode($storedToken);
                
                header("Location: /$newPassUrl");
            } else { // password verification failed
                $error_message = "Wrong Username or Password.";
                notify("Wrong Username or Password entered by a user: " . $username);
            }
            
            $conn->close();
        } catch (Exception $e){
            session_start();
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            header("Location: errorpage");
            exit();
        }
        
        
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>LOGIN</title>
        
        <!-- Bootstrap Css -->
        <link href="bootstrap1.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- App favicon -->
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
        
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
                                        <a href="" class="logo logo-admin">
                                            <img src="logos/Logo.jpg" height="80" alt="logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">MFI Login</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <?php if (isset($error_message)) { ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php } ?>
                                <form id="login" class="form-horizontal mt-3" action="" method="post">
                                    <!--
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            Your Phone Number:<br>
                                            <input id="phone" name="phone" class="form-control" type="number" value="" placeholder="Your Phone" autofocus/> <br>
                                            <button name="sendCode" value="sendCode" type="submit">Request New Code</button>
                                        </div>
                                    </div>
                                    style="color: green; "
                                    -->
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <!--Username: demo <br> Password: demo.123<br> -->
                                            <input id="username" name="username" class="form-control" type="text" required="" value="" placeholder="Username" autofocus/>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 row">
                                        <div class="col-12">
                                            <input id="password" name="password" class="form-control" type="password" value="" placeholder="Password" required autocomplete="current-password"/>
                                        </div>
                                        <!--
                                        <div class="col-12">
                                            <br>
                                            <input id="phone" name="phone" class="form-control" type="number" placeholder="Phone Number" required/>
                                        </div>
                                        
                                        <div class="col-12">
                                            <br>
                                            Enter Login Code: (No code? <a  href="signup"> Get Login Code</a>)<br>
                                            <input id="code" name="code" class="form-control" type="number" required="" value="" placeholder="Login code" autofocus/>
                                        </div><br>
                                    </div>
                                    <!--
                                    <div class="g-recaptcha" data-sitekey="6Lf0x3gnAAAAAD-kvGHVFZgpvZsoBmp5D2NGJXHY">
                                    </div>
                                    -->
                                    <div class="form-group mb-3 text-center row mt-3 pt-1">
                                        <div class="col-12">
                                            <!--
                                            <button class="btn btn-sm btn-success w-100 waves-effect waves-light" value="Login" type="submit">Login</button>
                                            -->
                                            <button class="g-recaptcha btn btn-sm btn-success w-100 waves-effect waves-light" data-sitekey="6LfWwHEoAAAAAKvnaNAcsByowYBUinPL5yo6jtkl" data-callback='onSubmit' data-action='submit'>Login</button>
                                            <br>
                                            <br>
                                            <a href="passwordReset"> Forgot Password?</a>
                                            <span>        </span>
                                            
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
             document.getElementById("login").submit();
           }
         </script>
        
        
    </body>
</html>