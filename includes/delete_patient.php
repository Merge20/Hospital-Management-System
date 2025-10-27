<?php
include("../includes/db.php");
session_start();

if (isset($_GET['id']) && $_SESSION['role'] == 'admin') {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM patient WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: ../ADMIN/pages/patient.php");
exit;
?>
