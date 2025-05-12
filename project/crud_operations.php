<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$host = "localhost";
$db = "car_rental_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ======== HANDLE ADD CAR ========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $model = $_POST['model'];
    $type = $_POST['type'];
    $license_plate = $_POST['license_plate'];
    $rental_price = $_POST['rental_price'];
    $status = "available";

    // Upload image
    $target_dir = "image/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    if ($_FILES["car_image"]["error"] !== UPLOAD_ERR_OK) {
        die("Image upload error: " . $_FILES["car_image"]["error"]);
    }

    $unique_name = uniqid() . "_" . basename($_FILES["car_image"]["name"]);
    $target_file = $target_dir . $unique_name;

    if (!move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
        die("Failed to move uploaded file.");
    }

    $image_url = $target_file;

    $sql = "INSERT INTO cars (model, type, license_plate, rental_price, status, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdss", $model, $type, $license_plate, $rental_price, $status, $image_url);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error adding car: " . $conn->error;
    }
    $stmt->close();
}

// ======== HANDLE UPDATE CAR ========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_car'])) {
    $car_id = $_POST['car_id'];
    $model = $_POST['model'];
    $type = $_POST['type'];
    $license_plate = $_POST['license_plate'];
    $rental_price = $_POST['rental_price'];

    $image_url = "";

    if (!empty($_FILES["car_image"]["name"])) {
        $target_dir = "image/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if ($_FILES["car_image"]["error"] !== UPLOAD_ERR_OK) {
            die("Image upload error: " . $_FILES["car_image"]["error"]);
        }

        $unique_name = uniqid() . "_" . basename($_FILES["car_image"]["name"]);
        $target_file = $target_dir . $unique_name;

        if (!move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            die("Failed to move uploaded file.");
        }

        $image_url = $target_file;
    }

    if ($image_url) {
        $sql = "UPDATE cars SET model = ?, type = ?, license_plate = ?, rental_price = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $model, $type, $license_plate, $rental_price, $image_url, $car_id);
    } else {
        $sql = "UPDATE cars SET model = ?, type = ?, license_plate = ?, rental_price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $model, $type, $license_plate, $rental_price, $car_id);
    }

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Error updating car: " . $conn->error;
    }
    $stmt->close();
}

// ======== HANDLE DELETE CAR ========
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car'])) {
    $car_id = $_POST['car_id'];

    $sql = "DELETE FROM cars WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $car_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.html");
        exit();
    } else {
        echo "Error deleting car: " . $conn->error;
    }
    $stmt->close();
}

$conn->close();
?>
