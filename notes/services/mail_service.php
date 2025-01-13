<?php
// mail_service.php

function sendVerificationEmail($to, $verificationCode) {
    $subject = 'Email Verification';
    $message = "
    <html>
    <head>
        <title>Email Verification</title>
    </head>
    <body>
        <p>Please click the following link to verify your email:</p>
        <a href='http://your_domain.com/verify.php?code=$verificationCode'>Verify Email</a>
    </body>
    </html>
    ";
    
    // Set content-type for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    // Additional headers
    $headers .= "From: RnT Notes <admin@rntnotes.xyz>" . "\r\n";
    $headers .= "Reply-To: admin@rntnotes.xyz" . "\r\n";
    
    // Send the email
    if(mail($to, $subject, $message, $headers)) {
        return true; // Email sent successfully
    } else {
        return false; // Email failed to send
    }
}
?>
