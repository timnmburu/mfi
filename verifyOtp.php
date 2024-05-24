<?php 

    require_once(__DIR__ . '/vendor/autoload.php');
    require_once __DIR__.'/templates/notifications.php';
    //require_once __DIR__.'/templates/cryptOtp.php';
    require_once __DIR__.'/templates/crypt.php';
    
    use Dotenv\Dotenv;
 
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    $db_servername = $_ENV['DB_HOST'];
    $db_username = $_ENV['DB_USERNAME'];
    $db_password = $_ENV['DB_PASSWORD'];
    $dbname = $_ENV['DB_NAME'];
    
    $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if (isset($_POST['submit'])) {
        $userOtp = $_POST['userOtp'];
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); 
        }
        
        $phone = $_SESSION['userphone'];
        
        //$decryptedOtp = decryptOtp($phone);
        
        $phone1 = encrypt($phone);
        $userOtp1 = encrypt($userOtp);
        $userOtp1 = base64_encode($userOtp1);
        
        $sqlGetHashedOtp = $conn->prepare("SELECT * FROM otpQ WHERE otpHash=?");
        $sqlGetHashedOtp->bind_param("s", $userOtp1);
        $sqlGetHashedOtp->execute();
        $results = $sqlGetHashedOtp->get_result();
        
        if($results-> num_rows > 0){
            $rows = $results->fetch_assoc();
            
            $dateInitiated = decrypt($rows['dateInitiated']);
            
            $initial_timestamp = strtotime($dateInitiated);
            $current_timestamp = date('Y-m-d H:i:s');
            $current_timestamp = strtotime($current_timestamp);
    
            // Calculate the difference in seconds
            $difference = $current_timestamp - $initial_timestamp;
            
            // Convert the difference to secs
            $timePast = floor($difference);
            
            if ($timePast > 60) {
                $return = json_encode(["error" => "Timeout: $timePast"]);
            } else {
                $return =  json_encode(["success" => true]);
            }
        } else {
            $return =  json_encode(["error" => "Failed to process2."]);
        }
        
        $return = json_decode($return, true);
        
        if(isset($return['error'])) {
            $error_message = $return['error'];
            //$error_message = "Timeout, request new OTP!";
            notify($error_message);
            $showButton = true;
        } elseif (isset($return['success'])) {
                
                echo "
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('processing').hidden = false;
                            document.getElementById('wrapper-page').hidden = true;
                        });
                        
                        fetch(localStorage.getItem('targetUrl'))
                            .then(response => response.ok ? response.text()
                                .then(data => {
                                    localStorage.removeItem('targetUrl');
                                    window.location.href = localStorage.getItem('sourceUrl');
                                    localStorage.removeItem('sourceUrl');
                                }) 
                            : console.error('Network response was not ok'))
                                .catch(error => 
                                        console.error('Error:', error)
                                        );
                    </script>";
                
        } else { // password verification failed
            $error_message = "Wrong OTP code.";
            notify($error_message);
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>OTP</title>
        
        <!-- Bootstrap Css -->
        <link href="bootstrap1.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- App favicon -->
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/crypto-js.min.js"></script>
        <style>
            .processing {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
        </style>
    </head>
    <body class="auth-body-bg">
        <div class="processing" id="processing" hidden="true" >
            <!--Running man for Processing Requests -->
            <img src="fileStore/running.gif" alt="running-man">
        </div>
        <div class="bg-overlay"></div>        
        <div class="wrapper-page" id="wrapper-page" >
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
                                        <h4 class="mt-3 mb-1 fw-semibold font-18">Enter OTP Code</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <?php if (isset($error_message)) { ?>
                                    <p style="color: red;"><?php echo $error_message; ?></p>
                                <?php } ?>
                                <form id="verifyOTP" class="form-horizontal mt-3" action="" method="post">
                                    <div class="form-group mb-3 row">
                                        
                                        <div id="timer" <?php if(isset($showButton) && $showButton === true) { echo 'hidden'; } ?> >60 secs remaining</div>

                                        <div class="col-12">
                                            <input id="userOtp" name="userOtp" class="form-control" type="number"  placeholder="Enter OTP received.." required />
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 text-center row mt-3 pt-1">
                                        <div class="col-12">
                                            <button id ="verify" class="btn btn-sm btn-success w-100 waves-effect waves-light" name='submit'>Verify Code</button>
                                            <br>
                                            <br>
                                            <button class="btn btn-sm btn-info" id="newOTP" <?php if(isset($showButton) && $showButton === true) { echo ''; } else { echo "hidden"; } ?> onclick="resetOTP()" >Request New OTP</button>
                                               
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
           //OTP Timer
            let seconds = 60;
            let timerInterval;
    
            function startTimer() {
                timerInterval = setInterval(updateTimer, 1000);
            }
            
            function updateTimer() {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('verify').disabled = true;
                    document.getElementById('newOTP').hidden = false;
                }
                document.getElementById('timer').innerText = seconds + " secs remaining";
            }
            
            startTimer();
            
            function resetOTP() {
                
                clearInterval(timerInterval);
                
                // Send an AJAX request to the server to reset OTP
                let xhr = new XMLHttpRequest();
                xhr.open('GET', 'templates/setOtp.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                        seconds = 60;
                        document.getElementById('timer').hidden = false;
                        document.getElementById('timer').innerText = seconds + " secs remaining";
                        document.getElementById('newOTP').hidden = true;
                        document.getElementById('verify').disabled = false;
                        
                        startTimer();
                    }
                }
                xhr.send();
            }
        </script>
    </body>
</html>