<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, start session and store the role
        $user_data = $result->fetch_assoc();
        $_SESSION['username'] = $user;
        $_SESSION['role'] = $user_data['role']; // Store role in session

        echo "success"; // Return success message to JavaScript
    } else {
        // Incorrect login, return error message
        echo "failure"; // Return failure message to JavaScript
    }

    $stmt->close();
}
$conn->close();
?>
