<?php
    require_once (__DIR__ . '/../vendor/autoload.php');
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use Dotenv\Dotenv;
    
    // Load the environment variables from .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    function sendEmail($email, $subject, $body, $replyTo='info@essentialapp.site') {
        
        $mail = new PHPMailer(true);
    
        // SMTP configuration for Titan email
        $mail->isSMTP();
        $mail->Host = $_ENV['TITAN_EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['TITAN_EMAIL_USERNAME'];
        $mail->Password = $_ENV['TITAN_EMAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['TITAN_SMTP_SECURE'];
        $mail->Port = $_ENV['TITAN_SMTP_PORT'];
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
        // Set the From and To addresses, subject, and message body
        $mail->setFrom($_ENV['THE_EMAIL'], 'Essentialapp');
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->addReplyTo($replyTo);
        $mail->isHTML(true);
    
        // Send the email
        if ($mail->send()) {
            //echo 'Email sent successfully!';
        } else {
            //echo 'Error sending email: ' . $mail->ErrorInfo;
        }
    }
    
    function sendEmailBackups($temp_file_path) {
        
        $mail = new PHPMailer(true);
    
        // SMTP configuration for Titan email
        $mail->isSMTP();
        $mail->Host = $_ENV['TITAN_EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['TITAN_EMAIL_USERNAME'];
        $mail->Password = $_ENV['TITAN_EMAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['TITAN_SMTP_SECURE'];
        $mail->Port = $_ENV['TITAN_SMTP_PORT'];
    
        // Set the From and To addresses, subject, and message body
        $mail->setFrom($_ENV['THE_EMAIL'], 'Essentialapp');
        $mail->addAddress($_ENV['THE_EMAIL']);
        $mail->addAttachment($temp_file_path, 'database_backup.sql');
        $mail->Subject = 'Database Backup - ' . date('Y-m-d H:i:s');
        $mail->Body = 'Please find the attached database backup file.';
    
        // Send the email
        if ($mail->send()) {
            echo 'Backup Emailed successfully!';
        } else {
            echo 'Error sending email: ' . $mail->ErrorInfo;
        }
    }
?>