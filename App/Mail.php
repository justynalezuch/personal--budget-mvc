<?php

namespace App;

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail {

    public static function send($to, $subject, $body, $altbody) {
        // $to, $subject, $text, $html

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'justynakuta.pl';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'justynak';                             // SMTP username
            $mail->Password   = 'jiD2d656If';                           // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;


            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            //Recipients
            $mail->setFrom('no-reply@justynakuta.pl', 'Password Reset - finances.justynakuta.pl');
            $mail->addAddress('justynalezuch@gmail.com', '');

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $altbody;

            $mail->send();

//            echo 'Message has been sent';

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";

        }
    }
}

