<?php
include("./session_check.php");
include("./db.php");
include("./functions.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $role = $_SESSION['role'] ?? '';
    $user_id = $_SESSION['user_id'] ?? 0;

    if ($role === 'doctor') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND doctor_id = ?");
        $stmt->bind_param("ii", $appointment_id, $user_id);
        $folder = "DOCTOR";
    } elseif ($role === 'admin') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);
        $folder = "ADMIN";
    } elseif ($role === 'patient') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ? AND patient_id = ?");
        $stmt->bind_param("ii", $appointment_id, $user_id);
        $folder = "PATIENT";
    } else {
        die("Unauthorized");
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        logActivity($conn, 'Appointment Cancelled', "Appointment  was cancelled by {$role}.");
        echo "<script>
            alert('Appointment cancelled successfully!');
            window.location.href='../$folder/pages/appointment.php';
        </script>";
    } else {
        echo "<script>
            alert('Failed to cancel appointment or unauthorized.');
            history.back();
        </script>";
    }
}
?>
