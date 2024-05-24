<?php
   require_once(__DIR__ . '/../vendor/autoload.php');

    use Dotenv\Dotenv;
     
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    function encrypt1($data){
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']);   
            
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        //$iv = $_ENV['IV_KEY'];
        
        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
        
        $encrypted = base64_encode($iv.$second_encrypted.$first_encrypted);    
        
        return $encrypted;
    }
    
    function decrypt1($data){
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']); 
        
        $mix = base64_decode($data);
                
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
                    
        $iv = substr($mix, 0, $iv_length);
        
        $second_encrypted = substr($mix, $iv_length, 64);
        $first_encrypted = substr($mix, $iv_length + 64);
                    
        $data = openssl_decrypt($first_encrypted, $method, $first_key,  OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
            
        if (hash_equals($second_encrypted, $second_encrypted_new)){
            $decrypted = $data;
        } else {
            $decrypted = false;   
        }
        
        return $decrypted;
    }
        
    function encrypt($data){
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']);   
        
        $method = "aes-256-cbc";    
        $iv_length = openssl_cipher_iv_length($method);
    
        // Use the fixed IV from the environment variable
        $fixed_iv = hex2bin($_ENV['FIXED_IV']);
    
        $first_encrypted = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $fixed_iv);
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
        
        $encrypted = base64_encode($fixed_iv.$second_encrypted.$first_encrypted);    
        
        return $encrypted;
    }
    
    function decrypt($data){
        $first_key = base64_decode($_ENV['OTP_KEY1']);
        $second_key = base64_decode($_ENV['OTP_KEY2']); 
    
        // Use the fixed IV from the environment variable
        $fixed_iv = hex2bin($_ENV['FIXED_IV']);
        
        if(empty($data)){
            $decrypted = false;
        } else {
            $mix = base64_decode($data);
                    
            $method = "aes-256-cbc";    
            $iv_length = openssl_cipher_iv_length($method);
                        
            $second_encrypted = substr($mix, $iv_length, 64);
            $first_encrypted = substr($mix, $iv_length + 64);
                        
            $data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $fixed_iv);
            $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
                
            if (hash_equals($second_encrypted, $second_encrypted_new)){
                $decrypted = $data;
            } else {
                $decrypted = false;   
            }
        }
        
        return $decrypted;
    }

    function decryptOtp12($phone){
        // Database connection
        $db_servername = $_ENV['DB_HOST'];
        $db_username = $_ENV['DB_USERNAME'];
        $db_password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_NAME'];
        
        $conn = new mysqli($db_servername, $db_username, $db_password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        //$now = date('H', strtotime('+3 hours'));
        $phone1 = encrypt($phone);
        
        $sqlGetHashedOtp = $conn->prepare("SELECT * FROM otpQ WHERE phone=? ORDER BY s_no DESC LIMIT 1");
        $sqlGetHashedOtp->bind_param("s", $phone1);
        $sqlGetHashedOtp->execute();
        $results = $sqlGetHashedOtp->get_result();
        
        if($results-> num_rows > 0){
            $rows = $results->fetch_assoc();
            
            $storedHashOtp = decrypt($rows['otpHash']);
            
            $dateInitiated = decrypt($rows['dateInitiated']);
            
            $initial_timestamp = strtotime($dateInitiated);
            $current_timestamp = date('Y-m-d H:i:s');
            $current_timestamp = strtotime($current_timestamp);
    
            // Calculate the difference in seconds
            $difference = $current_timestamp - $initial_timestamp;
            
            // Convert the difference to secs
            $timePast = floor($difference);
            
            if ($timePast > 60) {
                return json_encode(["error" => "Timeout: $timePast"]);
            } else {
                $decrypted = decrypt($storedHashOtp);
                
                //return $decrypted;
                if($decrypted !== false){
                    return json_encode(["success" => "$decrypted"]);
                } else {
                    return json_encode(["error" => "Failed to process1."]);
                }
            }
        } else {
            return json_encode(["error" => "Failed to process2."]);
        }
    }
    
    
    
    
    
    
    
    
?>