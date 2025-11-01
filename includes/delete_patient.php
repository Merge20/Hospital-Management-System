<?php
include("./db.php");
include("./functions.php");
session_start();

if (isset($_GET['id']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['id']);

    $get = $conn->prepare("SELECT first_name, last_name FROM patient WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $get->bind_result($first_name, $last_name);
    $get->fetch();
    $get->close();

    $stmt = $conn->prepare("DELETE FROM patient WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $patient_name = trim("$first_name $last_name");
        logActivity($conn, 'Patient Deleted', "Patient {$patient_name} was removed by Admin.");
    }
}

header("Location: ../ADMIN/pages/patient.php");
exit;
?>
