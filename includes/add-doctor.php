<?php
include("../includes/session_check.php");
include("../includes/db.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $check = $conn->prepare("SELECT * FROM doctor WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO doctor (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $password);

    if ($stmt->execute()) {
        echo "<script>alert('doctor added successfully!'); window.location.href='../ADMIN/pages/doctor.php';</script>";
    } else {
        echo "<script>alert('Error adding doctor!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}

?>
