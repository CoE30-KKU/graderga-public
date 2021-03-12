<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../../../vendor/PHPMailer/PHPMailer.php'; // Only file you REALLY need
require_once '../../../vendor/PHPMailer/Exception.php'; // If you want to debug
require_once '../../../vendor/PHPMailer/SMTP.php';

// Form details
$email_to = $_GET['email'];

$fullname = "<<EMAIL NAME>>"; // required
$email_from = "<<EMAIL ADDRESS HERE>>"; // required
$subject = "สวัสดี! " . $_GET['name']; // required
$message = "คุณได้ทำการส่งคำร้องขอรีเซ็ตรหัสผ่านเพื่อเข้าใช้งานเว็บไซต์"; // required

$email_message = file_get_contents('resetpassword.html');
$email_message = str_replace("{{name}}", $_GET['name'], $email_message);
$email_message = str_replace("{{key}}", $_GET['key'], $email_message);
$email_message = str_replace("{{email}}", $_GET['email'], $email_message);

// No need to set headers here

// Replace the mail() function with PHPMailer

$mail = new PHPMailer(true);                              // Passing `true` enables exceptions

try {
    //Server settings
    $mail->CharSet = "UTF-8";
    $mail->Encoding = 'base64';
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->isHTML(true);
    $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $fullname = "<<EMAIL NAME>>"; // required
    $email_from = "<<EMAIL ADDRESS HERE>>"; // required
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom($email_from, 'Computer_MVSK');
    $mail->addAddress($email_to, $fullname);     // Add the recipient

    //Content
    $mail->isHTML(true);                         // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $email_message;

    $mail->send();

    $_SESSION['swal_success'] = "รีเซ็ตรหัสผ่านสำเร็จ";
    $_SESSION['swal_success_msg'] = "กรุณาตรวจสอบที่อีเมล " . $email . " ของท่านเพื่อดำเนินการต่อ";
    header("Location: ../../../home/");
    
} catch (Exception $e) {
    die("ERROR! Mailer Error: $mail->ErrorInfo");
}
?>