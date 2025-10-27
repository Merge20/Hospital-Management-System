<?php
include("../includes/db.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $tables = [
        'admin' => '../ADMIN/home.php',
        'doctor' => '../DOCTOR/home.php',
        'patient' => '../PATIENT/home.php'
    ];

    foreach ($tables as $table => $redirect) {
        $sql = "SELECT * FROM $table WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $table;

                header("Location: $redirect");
                exit;
            } else {
                echo "<script>alert('Incorrect password!'); window.location.href='../index.php';</script>";
                exit;
            }
        }
    }

    echo "<script>alert('No account found with that email!'); window.location.href='../index.php';</script>";
}
?>
