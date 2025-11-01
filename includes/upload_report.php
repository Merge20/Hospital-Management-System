<?php
include("./db.php");
include("./functions.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) die("Unauthorized");

    $appointment_id = intval($_POST['appointment_id']);
    if ($_SESSION['role'] === 'admin') {
        $getDoc = $conn->prepare("SELECT doctor_id FROM appointments WHERE id = ?");
        $getDoc->bind_param("i", $appointment_id);
        $getDoc->execute();
        $getDoc->bind_result($doctor_id);
        $getDoc->fetch();
        $getDoc->close();
    } else {
        $doctor_id = $_SESSION['user_id'];
    }

    $report_type = trim($_POST['report_type']);
    if ($report_type === '') die("Report type required");

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) die("No file selected or upload error");

    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') die("Only PDF allowed");

    $check = $conn->prepare("SELECT id FROM reports WHERE appointment_id = ?");
    $check->bind_param("i", $appointment_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) die("Report already uploaded");

    $newName = uniqid("report_", true) . ".pdf";
    $targetPath = "../uploads/reports/" . $newName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) die("Failed to save file");

    $stmt = $conn->prepare("
        INSERT INTO reports (appointment_id, doctor_id, patient_id, report_type, file_path)
        SELECT a.id, a.doctor_id, a.patient_id, ?, ?
        FROM appointments a
        WHERE a.id = ?
    ");
    $stmt->bind_param("ssi", $report_type, $newName, $appointment_id);

    if ($stmt->execute()) {
        $update = $conn->prepare("UPDATE appointments SET report_uploaded = 1 WHERE id = ?");
        $update->bind_param("i", $appointment_id);
        $update->execute();

        logActivity($conn, 'Report Uploaded', "Report uploaded for appointment by Doctor.");
        echo "success";
    } else {
        echo "Database error";
    }
}
?>
