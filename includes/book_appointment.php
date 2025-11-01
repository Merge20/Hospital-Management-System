<?php
include("./session_check.php");
include("./db.php");
include("./mail.php");


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
        // Fetch doctor email
        $getDoc = $conn->prepare("SELECT first_name, last_name, email FROM doctor WHERE id = ?");
        $getDoc->bind_param("i", $doctor_id);
        $getDoc->execute();
        $doctor = $getDoc->get_result()->fetch_assoc();

        $getPat = $conn->prepare("SELECT first_name, last_name, email FROM patient WHERE id = ?");
        $getPat->bind_param("i", $patient_id);
        $getPat->execute();
        $patient = $getPat->get_result()->fetch_assoc();

        // Notify Doctor
        sendMail(
            $doctor['email'],
            "New Appointment Booked",
            "<p>Dear Dr. {$doctor['first_name']} {$doctor['last_name']},<br>
            A new appointment (ID: {$conn->insert_id}) has been booked by {$patient['first_name']} {$patient['last_name']}.</p>"
        );

        echo json_encode(["status" => "success", "message" => "Appointment booked successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    }
}
?>
