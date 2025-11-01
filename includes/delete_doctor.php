<?php
include("./db.php");
include("./functions.php");
session_start();

if (isset($_GET['id']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['id']);

    $get = $conn->prepare("SELECT first_name, last_name FROM doctor WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $get->bind_result($first_name, $last_name);
    $get->fetch();
    $get->close();

    $stmt = $conn->prepare("DELETE FROM doctor WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $doctor_name = trim("$first_name $last_name");
        logActivity($conn, 'Doctor Deleted', "Doctor {$doctor_name} was removed by Admin.");
    }
}

header("Location: ../ADMIN/pages/doctor.php");
exit;
?>
