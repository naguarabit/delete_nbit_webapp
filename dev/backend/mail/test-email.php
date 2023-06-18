<?php 
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "remesas@naguarabit.com";
    $to = "gab.perez.py@gmail.com"; //gperez@bizlatinhub.com 
    $subject = "PHP Mail Test script";
    $message = "This is a test to check the PHP Mail functionality";
    $headers = "From:" . $from;
    if(mail($to, $subject, $message, $headers))
    	echo "Test Email sent";
	else
    echo "Email sending failed";
?>