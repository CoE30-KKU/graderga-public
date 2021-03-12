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
$message = "กรุณายืนยันตัวตนเพื่อปลดล็อกการใช้งานฟังก์ชั่นบางอย่างในเว็บไซต์"; // required

$email_message = file_get_contents('email.html');
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
    $mail->Username   = '<<SMTP USERNAME>>';                     // SMTP username
    $mail->Password   = '<<SMTP PASSWORD>>';                               // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom($email_from, '<<EMAIL NAME>>');
    $mail->addAddress($email_to, $fullname);     // Add the recipient

    //Content
    $mail->isHTML(true);                         // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $email_message;

    $mail->send();
    if (isset($_GET['method'])) {
        if ($_GET['method'] == "reg") {
            header("Location: ../../home");    
        } else if ($_GET['method'] == "changeEmail") {
            //Something could be happen here next day...
            $_SESSION['swal_warning'] = "คุณได้ทำการเปลี่ยนแปลงอีเมล";
            $_SESSION['swal_warning_msg'] = "อย่าลืมเข้าไปยืนยันตัวตนด้วยอีเมลใหม่ด้วยนะครับ";
        }
    } else {
        echo "SUCCESS";
    }
} catch (Exception $e) {
    die("ERROR! Mailer Error: $mail->ErrorInfo");
}
?>