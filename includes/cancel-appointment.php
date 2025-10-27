<?php
include("./session_check.php");
include("./db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $doctor_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND doctor_id = ?");
    $stmt->bind_param("ii", $appointment_id, $doctor_id);
    $stmt->execute();

    echo "<script>alert('Appointment cancelled successfully!'); window.location.href='../DOCTOR/pages/appointment.php';</script>";
}
?>
