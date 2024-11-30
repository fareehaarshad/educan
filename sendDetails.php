<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // Adjust if necessary

// Get the form data
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$subject = htmlspecialchars($_POST['subject']);
$message = htmlspecialchars($_POST['message']);

// Set up PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();  // Use SMTP to send email
    $mail->Host = 'smtp.gmail.com';  // Gmail's SMTP server
    $mail->SMTPAuth = true;  // Enable SMTP authentication
    $mail->Username = 'educan.tutoringservices@gmail.com';  // Gmail address
    $mail->Password = 'your-app-password';  // App-specific password or Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable STARTTLS encryption
    $mail->Port = 587;  // Port for TLS encryption (Gmail uses 587)

    // Recipients
    $mail->setFrom($email, $name);  // Set the sender email and name
    $mail->addAddress('educan.tutoringservices@gmail.com');  // Set the recipient

    // Content
    $mail->isHTML(false);  // Set email format to plain text
    $mail->Subject = "New Message from Contact Form: $subject";  // Subject of the email
    $mail->Body = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";  // Email body

    // Send the email
    $mail->send();
    echo 'Message has been sent.';
} catch (Exception $e) {
    echo "Message could not be sent. Error: {$mail->ErrorInfo}";
}
?>
