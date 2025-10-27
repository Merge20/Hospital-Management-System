<?php
include("./session_check.php");
include("./db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);

    $stmt = $conn->prepare("UPDATE appointments SET status = 'Confirmed' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        $log = $conn->prepare("INSERT INTO activity_log (activity_type, description) VALUES ('Appointment', CONCAT('Appointment ID ', ?, ' marked as Completed'))");
        $log->bind_param("i", $appointment_id);
        $log->execute();
        $log->close();

        echo "<script>alert('Appointment marked as Confirmed!'); window.location.href='../DOCTOR/pages/appointment.php';</script>";
    } else {
        echo "<script>alert('Error updating appointment status!'); window.location.href='../DOCTOR/pages/appointment.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>