<?php
// use this library -> https://github.com/PHPMailer/PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function sendEmail(){
  	require 'phpmailer/vendor/autoload.php';	

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';
  try {
      $receiver = 'gperez@bizlatinhub.com';
      $name = 'Name';

      //Server settings
      $mail->SMTPDebug = 1;                      					//Enable verbose debug output
      $mail->isSMTP();                                              //Send using SMTP
      $mail->Host       = 'tls://smtp.gmail.com';                   //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                     //Enable SMTP authentication
      $mail->Username   = 'username@gmail.com';                     //SMTP username
      $mail->Password   = 'PASSWORD';                               //SMTP password
      $mail->SMTPSecure = tls;            //Enable implicit TLS encryption
      $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->From = 'test@gmail.com';
      $mail->setFrom('test@gmail.com', 'Name');
      $mail->addAddress($receiver, $name);     //Add a recipient

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'Subject';
      $mail->Body    = 'Body';

      $mail->send();
      echo 'Message has been sent';
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}