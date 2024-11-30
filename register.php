<?php
session_start(); // Start the session to store data temporarily

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $high_school = mysqli_real_escape_string($conn, $_POST['high_school']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $prep_class = mysqli_real_escape_string($conn, $_POST['prep_class']);
    $prep_date = mysqli_real_escape_string($conn, $_POST['prep_date']);

    // Store the form data in the session to access it later in the webhook
    $_SESSION['first_name'] = $first_name;
    $_SESSION['last_name'] = $last_name;
    $_SESSION['high_school'] = $high_school;
    $_SESSION['email'] = $email;
    $_SESSION['prep_class'] = $prep_class;
    $_SESSION['prep_date'] = $prep_date;

    // You can also store any other information you want in the session

    // Close the connection
    $conn->close();
}
?>



<br>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Information</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include any CSS styles here -->
</head>
<body>
    <div class="review-info-container">
        <h2>Review Information</h2>
		
		<p>To make any changes, please go back to the form.</p>
        <!-- List all parameters -->
        <ul>
            <li><strong>First Name:</strong> <?php echo htmlspecialchars($first_name); ?></li>
            <li><strong>Last Name:</strong> <?php echo htmlspecialchars($last_name); ?></li>
            <li><strong>High School:</strong> <?php echo htmlspecialchars($high_school); ?></li>
            <li><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></li>
            <li><strong>Prep Class:</strong> <?php echo htmlspecialchars($prep_class); ?></li>
            <li><strong>Prep Date:</strong> <?php echo htmlspecialchars($prep_date); ?></li>
        </ul>

		<script async src="https://js.stripe.com/v3/buy-button.js"></script>

        <stripe-buy-button
            buy-button-id="buy_btn_1QQl5KFw4pnNWc18mRsDt4Hi"
            publishable-key="pk_live_51Jt8D8Fw4pnNWc18EHZGvud7SS6KWzVsz9ySZLtgG9SsbcechWes1TvZqhUtjn0tqni8TeLH1g1Yw2fB3YqJOq5E00TzUW4TMW"
        >
        </stripe-buy-button>

    </div>
</body>
</html>
