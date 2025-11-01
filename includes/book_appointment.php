<?php
include("./session_check.php");
include("./db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = intval($_POST['doctor_id']);
    $date = $_POST['date'];
    $time = $_POST['time'];

    if (empty($doctor_id) || empty($date) || empty($time)) {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    $datetime = date("Y-m-d H:i:s", strtotime("$date $time"));
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $datetime);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    }
}
?>
