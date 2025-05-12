<?php
session_start();

// Database connection
$host = "localhost";
$db = "car_rental_db";
$user = "root";
$pass = "";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to select user from database
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verify the password using password_verify
        if ($password === $row['password']) {
            // If the password is correct, set the session and redirect to dashboard
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid username or password');</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password');</script>";
    }
    $stmt->close();
}

$conn->close();
?>
