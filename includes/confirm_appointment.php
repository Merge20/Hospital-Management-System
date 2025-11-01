<?php
include("./session_check.php");
include("./db.php");
include("./mail.php");


    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['appointment_id'])) {
        $appointment_id = intval($_POST['appointment_id']);

        $stmt = $conn->prepare("UPDATE appointments SET status = 'Confirmed' WHERE id = ?");
        $stmt->bind_param("i", $appointment_id);

        if ($stmt->execute()) {
        // Get patient email
        $getPat = $conn->prepare("
            SELECT p.email, p.first_name, p.last_name 
            FROM appointments a 
            JOIN patient p ON a.patient_id = p.id 
            WHERE a.id = ?
        ");
        $getPat->bind_param("i", $appointment_id);
        $getPat->execute();
        $patient = $getPat->get_result()->fetch_assoc();

        sendMail(
            $patient['email'],
            "Appointment Confirmed",
            "<p>Dear {$patient['first_name']} {$patient['last_name']},<br>
            Your appointment (ID: {$appointment_id}) has been <b>confirmed</b>.</p>"
        );

        echo "<script>alert('Appointment marked as Confirmed!'); window.location.href='../DOCTOR/pages/appointment.php';</script>";
    } else {
        echo "<script>alert('Error updating appointment status!'); window.location.href='../DOCTOR/pages/appointment.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>