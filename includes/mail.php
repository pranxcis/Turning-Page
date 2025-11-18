<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function send_mail($to, $subject, $body, $altBody = '') {
    $mail = new PHPMailer(true);
    try {
        // SMTP settings for Mailtrap
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = 'd95674c80f7694'; // replace with Mailtrap username
        $mail->Password = 'b081d1e4c0f236'; // replace with Mailtrap password
        $mail->Port = 2525;

        // Sender & recipient
        $mail->setFrom('no-reply@turningpage.com', 'Turning Page');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>
