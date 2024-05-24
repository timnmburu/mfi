<?php
    require_once 'vendor/autoload.php';
    require_once __DIR__ . '/templates/standardize_phone.php';
    
    use IntaSend\IntaSendPHP\Collection;
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); 
    }
    $admin = $_SESSION['admin'];
    $username = $_SESSION['username'];
    
    function initCollection() {
        $credentials = [
            'token' => $_ENV['INTASEND_TOKEN'],
            'publishable_key' => $_ENV['INTASEND_PUBLISHABLE_KEY'],
        ];
        
        $collection = new Collection();
        $collection->init($credentials);
        
        return $collection;
    }
    
    function getInvoiceStatus($invoice_id) {
        // Database credentials
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        $status = "SELECT * FROM mpesa_collections WHERE invoice_id='$invoice_id'";
        $resultStatus = $conn->query($status);
        $resultStatus = $resultStatus->fetch_assoc();
        $responseS = $resultStatus['state'];
        $responseR = $resultStatus['failed_reason'];
        $response = [
                'state'=>$responseS,
                'failed_reason' =>$responseR,
            ];
            
        return $response;
    }
    
    function performPaymentRequest($amount, $formatted_phone_number, $api_ref) {
        $collection = initCollection();
        $response = $collection->mpesa_stk_push($amount, $formatted_phone_number, $api_ref);
        return $response;
    }
    
    if (isset($_POST['getInvoiceStatus'])) {
        $invoice_id = $_POST['invoice_idT']; // Retrieve the invoice ID from the form input
        
        // Get the payment status
        $response = getInvoiceStatus($invoice_id);
        
        // Send the JSON-encoded response back to the client
        echo json_encode($response);
        exit;
    }
    
    if (isset($_POST['stkPushed'])) {
        // Retrieve the form data
        $amount = $_POST['amount'];
        $phone_number = $_POST['phone_number'];
        
        // Extract the last 9 digits from the phone number
        $standardizedInput = standardizePhoneNumber($phone_number);
        
        // Add the prefix "254" to the phone number
        $formatted_phone_number = '254' . $standardizedInput;
        
        $api_ref = "MFI-SMS"; // You can generate a unique reference for each transaction
        
        // Perform the payment request
        $response = performPaymentRequest($amount, $formatted_phone_number, $api_ref);
        
        // Get the invoice ID from the response
        $invoice = $response->invoice;
        $invoice_id = $invoice->invoice_id;
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>M-Pesa Payment</title>
        <link rel="ICON" href="logos/Emblem.ico" type="image/ico" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                //Load processing gif
                $('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                //process payment status check
              $('#form1').submit(function(event) {
                event.preventDefault(); // Prevent form submission
                
                // Make an AJAX request to the PHP script
                $.ajax({
                  url: '',
                  type: 'POST',
                  data: { getInvoiceStatus: true, invoice_idT: $('#invoice_id').val() },
                  dataType: 'json',
                  success: function(response) {
                    // Update the status on the page
                    
                    if (response.state === "COMPLETE") {
                      $('#status').text(response.state);
                      // Print link or perform any other action upon completion
                      //$('#back1').show();
                        
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "FAILED") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else if (response.state === "RETRY") {
                      $('#status').text(response.state + ': ' + response.failed_reason);
            
                      // Stop checking the status
                      clearInterval(statusInterval);
                    } else {
                      // Display the loading GIF
                      //$('#status').html('<img src="fileStore/processing.gif" alt="Processing..." style="display: flex; zoom: 70% ;">');
                    }
                  },
                  error: function() {
                    alert('An error occurred while retrieving the invoice status.');
                    $('#status').text('Error while processing');
                    clearInterval(statusInterval);
                  }
                });
              });
            
              // Check the status every 5 seconds
              var statusInterval = setInterval(function() {
                $('#form1').submit();
              }, 5000);
            });

        </script>
        <style>
            #status, #back, #pushedData {
                display: flex;
                align-items: center;
                justify-content: center;
                zoom: 70% ;
            }
        </style>
        
        <?php include "templates/header-admins1.php"; ?>
    </head>
    <body class="body">
        <div class="card shadow text-center" style="margin-top:125px;">
            <h1 class="card-title col-xs-12 col-sm-12 col-md-12 col-lg-12 text-dark">
                <b>M-Pesa Payment<b>
                <?php if(isset($_GET['p']) && $_GET['p'] == 'sms'){ echo "<h5>Purchase SMS Units</h5>";}     ?>
            </h1>
            <div class="card-body d-flex justify-content-center ">
                <div class="container-fluid responsive">
                    
                        <form id="form" method="POST" action="">
                            <div >
                                <div class="mb-3 text-start">
                                    <label class="form-label  " for="phone_number">Phone Number</label>
                                    <input class="form-control shadow" type="number" id="phone_number" name="phone_number" placeholder="07... OR 01..." required>
                                </div>
                                <div class="mb-2 text-start ">
                                    <label class="form-label " for="amount">Amount (Kes.):</label>
                                    <input class="form-control shadow" type="number" id="amount" name="amount" required><br><br>
                                </div>
                                <div <?php if(!isset($_GET['p']) || $_GET['p'] != 'sms'){ echo "hidden";} ?> class="mb-3 text-start ">
                                    <label class="form-label " for="amount">SMS Units:</label>
                                    <input disabled class="form-control shadow" type="number" id="units" name="units" ><br><br>
                                </div>
                                <input class="btn btn-success btn-bg shadow" type="submit" id="stkPushed" name="stkPushed" value="REQUEST PAYMENT" >
                            </div>
                        </form>
                    
                        <br>
                        
                        <?php
                        if (isset($_POST['stkPushed'])) {
                            if ($invoice_id === null) {
                                echo "";
                            } else {
                                //echo "Payment for Invoice ID " . $invoice_id . " is Successfully Initiated";
                                echo "<div id=pushedData >";
                                echo "Payment of Kshs." . $amount . " to Phone " . $phone_number . " is Successfully Initiated. Invoice ID " . $invoice_id ;
                                echo "</div>";
                        ?>
                                
                                <form id="form1" action="" method="POST">
                                    <input type="hidden" id="invoice_id" name="invoice_id" value="<?php  if(isset($invoice_id)){ echo $invoice_id ; } ?>">
                                    <br>
                                    <input type="submit" id="getInvoiceStatus" value="Get Payment Status" hidden>
                                </form>
                                
                                <div id="status"></div>
                                <br>
                                
                                <div id="back1" style="display: none;">
                                    <div  id="back" > <a href="pay?phone_number=<?php echo urlencode($phone_number); ?>&amount=<?php echo urlencode($amount); ?>&mode=<?php echo urlencode('Mpesa Online'); ?>">Record Payment Now?</a></div>
                                </div>
                                
                                <?php    
                            }
                        }
                        ?>
                </div>
            </div>
            <div class="card-footer border-light">
                <?php
                    include 'templates/intasend_payment_badge.php';
                ?>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function calcUnits(){
                    var amountToPay = document.getElementById('amount').value;
                    var charge = 30;
                    var netAmount = parseInt(amountToPay) * 0.97;
                    var unitsToGet = (parseInt(netAmount) - parseInt(charge)) * 2 ;
                    
                    document.getElementById('units').value = unitsToGet;
                }

                document.getElementById('amount').addEventListener('change', calcUnits);
            });
        </script>
    </body>
</html>
