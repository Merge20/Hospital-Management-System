<?php
include("./db.php");

header("Content-Type: application/json");

$response = [];

if (isset($_GET['doctor_id']) && isset($_GET['date'])) {
    $doctor_id = intval($_GET['doctor_id']);
    $date = $_GET['date'];

    $start = $date . " 00:00:00";
    $end = $date . " 23:59:59";

    $stmt = $conn->prepare("SELECT TIME(appointment_date) AS time_slot FROM appointments WHERE doctor_id = ? AND appointment_date BETWEEN ? AND ?");
    $stmt->bind_param("iss", $doctor_id, $start, $end);
    $stmt->execute();
    $result = $stmt->get_result();

    $booked = [];
    while ($row = $result->fetch_assoc()) {
        $booked[] = date("g:i A", strtotime($row["time_slot"]));
    }

    $response['booked_slots'] = $booked;
} else {
    $result = $conn->query("SELECT id, first_name, last_name FROM doctor ORDER BY first_name ASC");
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            "id" => $row["id"],
            "name" => "Dr. " . $row["first_name"] . " " . $row["last_name"]
        ];
    }
    $response['doctors'] = $doctors;
}

echo json_encode($response);
?>
