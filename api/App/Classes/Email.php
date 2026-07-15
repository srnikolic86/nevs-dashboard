<?php

namespace App\Classes;

use Nevs\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public static function Send($subject, $body, $recipients, $cc = [], $bcc = [], $attachments = []): void
    {
        if (Config::Get('mail.demo_recipient') != '') {
            $body .= '<hr />' . json_encode($recipients);
            $recipients = [Config::Get('mail.demo_recipient')];
        }

        if (count($cc) != 0) {
            if (Config::Get('mail.demo_recipient') != '') {
                $body .= '<hr />' . json_encode($cc);
                $cc = [];
            }
        }

        if (count($bcc) != 0) {
            if (Config::Get('mail.demo_recipient') != '') {
                $body .= '<hr />' . json_encode($bcc);
                $bcc = [];
            }
        }

        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = "UTF-8";
            $mail->Debugoutput = 'error_log';
//                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = Config::Get('mail.host');           //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = Config::Get('mail.username');       //SMTP username
            $mail->Password   = Config::Get('mail.password');      //SMTP password
            $mail->SMTPSecure = Config::Get('mail.encryption');            //Enable implicit TLS encryption
            $mail->Port       = Config::Get('mail.port');           //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(Config::Get('mail.from'), Config::Get('mail.from_name'));
            foreach($recipients as $recipient) {
                $mail->addAddress($recipient);
            }
            foreach($cc as $cc_address) {
                $mail->addCC($cc_address);
            }

            foreach($bcc as $bcc_address) {
                $mail->addBCC($bcc_address);
            }

            //Attachments
            foreach ($attachments as $file) {
                $mail->addAttachment($file, basename($file));
            }

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log ("Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
