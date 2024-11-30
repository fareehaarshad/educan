<?php
include('header.php');
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_auth";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student ID from URL query string
$student_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Query to fetch student details for display
$student_sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($student_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student_data = $student_result->fetch_assoc();

// Query to fetch progress details for the student
$progress_sql = "SELECT id, subject, points, comments FROM progress WHERE student_id = ?";
$stmt = $conn->prepare($progress_sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$progress_result = $stmt->get_result();

// Handle form submission for adding a new progress row
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject'], $_POST['points'])) {
    $subject = $_POST['subject'];
    $points = $_POST['points'];
    $comments = isset($_POST['comments']) ? $_POST['comments'] : '';

    // Insert new progress row into the progress table
    $insert_sql = "INSERT INTO progress (student_id, subject, points, comments) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("isis", $student_id, $subject, $points, $comments);

    if ($stmt->execute()) {
        header("Location: student.php?id=" . $student_id); // Refresh the page to show the new row
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle row deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the specified progress row
    $delete_sql = "DELETE FROM progress WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        header("Location: student.php?id=" . $student_id); // Refresh the page after deletion
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1></h1>
	
	<h2>Add New Progress</h2>
    <form id="progressForm" method="POST" action="student.php?id=<?php echo $student_id; ?>">
        <input id="progressSubject" type="text" name="subject" placeholder="Subject" required>
        <input id="progressPoints" type="number" name="points" placeholder="Points" required>
        <input id="progressComments" type="text" name="comments" placeholder="Comments (Optional)"></input>
        <input id="blueButton" type="submit" value="Add Progress">
    </form>

    <h2>Progress Details - <?php echo htmlspecialchars($student_data['first_name']); ?> <?php echo htmlspecialchars($student_data['last_name']); ?> </h2>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Points</th>
                <th>Comments</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($progress_result->num_rows > 0) {
                // Loop through all progress records and display them
                while ($row = $progress_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['subject']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['points']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['comments']) . "</td>";
                    // Add confirmation dialog for deletion
                    echo "<td><a href='#' class='action-link' onclick='confirmDelete(" . $row['id'] . ")'>Delete</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No progress data available for this student.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    

    <!-- JavaScript to confirm deletion -->
    <script type="text/javascript">
        function confirmDelete(progressId) {
            var confirmed = confirm("Are you sure you want to delete this record? This action cannot be undone.");
            if (confirmed) {
                // Redirect to the delete URL if confirmed
                window.location.href = 'student.php?id=<?php echo $student_id; ?>&delete_id=' + progressId;
            }
        }
    </script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
