<?php

session_start(); // Start the session to access registration data

require_once('vendor/autoload.php'); // Make sure you have Stripe's PHP library installed

\Stripe\Stripe::setApiKey('sk_test_your_secret_key'); // Use your Stripe secret key

// Retrieve the raw POST data from Stripe
$input = @file_get_contents("php://input");
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$endpoint_secret = 'whsec_your_webhook_secret'; // Your webhook secret

// Verify the webhook signature to ensure it came from Stripe
try {
    $event = \Stripe\Webhook::constructEvent($input, $sig_header, $endpoint_secret);

    // Handle the event
    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object; // Contains the payment info
            $paymentIntentId = $paymentIntent->id;

            // Retrieve registration data from the session
            if (isset($_SESSION['first_name']) && isset($_SESSION['last_name']) && isset($_SESSION['high_school'])) {
                $first_name = $_SESSION['first_name'];
                $last_name = $_SESSION['last_name'];
                $high_school = $_SESSION['high_school'];
                $email = $_SESSION['email'];
                $prep_class = $_SESSION['prep_class'];
                $prep_date = $_SESSION['prep_date'];

                // Insert the registration data into your database
                $conn = new mysqli('localhost', 'username', 'password', 'database_name');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $stmt = $conn->prepare("INSERT INTO registrations (first_name, last_name, high_school, email, prep_class, prep_date) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss', $first_name, $last_name, $high_school, $email, $prep_class, $prep_date);
                $stmt->execute();

                // Clear session data after processing the payment
                session_unset();
                session_destroy();
                
                // Respond with success
                echo json_encode(['status' => 'success']);
            } else {
                // Handle error: session data is not available
                echo json_encode(['status' => 'error', 'message' => 'Session data missing']);
            }
            break;

        // Handle other event types as needed
        default:
            // Unexpected event type
            http_response_code(400); // Return a bad request response
            exit();
    }

} catch (Exception $e) {
    // Invalid payload or signature
    http_response_code(400);
    exit();
}

http_response_code(200); // Return a success response
?>
