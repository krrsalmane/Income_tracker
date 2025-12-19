<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // 🔧 Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'karroumsalmane@gmail.com';   // 🔴 CHANGE
    $mail->Password   = 'mbmfpappfmttwpct';      // 🔴 CHANGE
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // 📩 Email info
    $mail->setFrom('karroumsalmane@gmail.com', 'Income Management');
    $mail->addAddress('karroumsalmane@gmail.com'); // 🔴 CHANGE

    // ✉️ Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body    = '<h2>PHPMailer is working ✅</h2><p>This is a test email.</p>';
    $mail->AltBody = 'PHPMailer is working - test email';

    $mail->send();
    echo "✅ Email sent successfully!";
} catch (Exception $e) {
    echo "❌ Email failed: {$mail->ErrorInfo}";
}
