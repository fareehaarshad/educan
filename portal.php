<?php

include('header.php');

// Check if the user is logged in and has a valid role
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the user's role from the session
$user_role = $_SESSION['role'];

// Database connection
$servername = "localhost"; // Update with your server details
$username = "root"; // Update with your database username
$password = ""; // Update with your database password
$dbname = "user_auth"; // Ensure this matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding a new student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['first_name'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $grade = $_POST['grade'];
    $parent_name = $_POST['parent_name'];
    $parent_email = $_POST['parent_email'];
    $parent_phone = $_POST['parent_phone'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, grade, parent_name, parent_email, parent_phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $first_name, $last_name, $grade, $parent_name, $parent_email, $parent_phone);

    if ($stmt->execute()) {
        // Successfully added student, refresh the page to show updated list
        header("Location: portal.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle student deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare and execute deletion query
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        // Successfully deleted the student, refresh the page
        header("Location: portal.php");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

// Query to fetch students' details
$sql = "SELECT id, first_name, last_name, grade, parent_name, parent_email, parent_phone FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher's Portal</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // JavaScript function to confirm before deleting a student
        function confirmDelete(studentId) {
            const confirmAction = confirm("Are you sure you want to delete this student's info?");
            if (confirmAction) {
                // Redirect to delete the student record
                window.location.href = "portal.php?delete_id=" + studentId;
            }
        }
    </script>
</head>
<body>

<?php if ($user_role == "admin") : ?>
    <!-- Show the form for adding a new student for admin -->
    <h2>Add New Student</h2>
    <form id="studentForm" action="" method="POST">
        <div>
            <input id="studentFirstName" type="text" name="first_name" placeholder="First Name" required>
            <input id="studentLastName" type="text" name="last_name" placeholder="Last Name" required>
            <input id="grade" type="number" name="grade" placeholder="Grade" required>
            <input id="parentName" type="text" name="parent_name" placeholder="Parent's Name" required>
            <input id="parentEmail" type="email" name="parent_email" placeholder="Parent's Email" required>
            <input id="parentPhone" type="tel" name="parent_phone" placeholder="Parent's Phone #" required>
            <input id="blueButton" type="submit" value="Add Student">
        </div>
    </form>
<?php endif; ?>

<h2>Student List</h2>

<table id="studentTable">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Grade</th>
            <?php if ($user_role == "admin") : ?>
                <th>Parent's Name</th>
                <th>Parent's Email</th>
                <th>Parent's Phone</th>
            <?php endif; ?>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Display results in table rows
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                if ($user_role == "admin") {
                    echo "<td>" . htmlspecialchars($row['parent_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['parent_email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['parent_phone']) . "</td>";
                }
                echo "<td>
                        <a href='student.php?id=" . $row['id'] . "' class='action-link'>View</a> |
                        <a href='#' class='action-link' onclick='confirmDelete(" . $row['id'] . ")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='" . ($user_role == "admin" ? 7 : 4) . "'>No students found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
