<?php
require 'vendor/autoload.php'; // ตรวจสอบเส้นทางนี้ว่าถูกต้องหรือไม่
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// รวม autoload ของ Composer
require 'vendor/autoload.php';

// ตรวจสอบการส่ง OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'failure', 'message' => 'Invalid email format.']);
        exit();
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Save OTP and email to database
    if (save_otp_to_db($email, $otp)) {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.example.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'raulmummum@gmail.com';               // SMTP username
            $mail->Password   = 'ipkv kfsm mecf zrrb';                        // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('raulmummum@gmail.com', 'test');
            $mail->addAddress($email);                                  // Add a recipient

            // Content
            $mail->isHTML(false);                                      // Set email format to plain text
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is: $otp";

            $mail->send();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'failure', 'message' => 'Failed to send OTP email.']);
        }
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Failed to save OTP to database.']);
    }
}

function save_otp_to_db($email, $otp) {
    // Connect to your database
    $conn = new mysqli('localhost', 'username', 'password', 'database');

    // Check connection
    if ($conn->connect_error) {
        return false;
    }

    // Prepare and bind
    $stmt = $conn->prepare("REPLACE INTO otps (email, otp, created_at) VALUES (?, ?, NOW())");
    if (!$stmt) {
        $conn->close();
        return false;
    }
    $stmt->bind_param('si', $email, $otp);

    // Execute
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}
?>

