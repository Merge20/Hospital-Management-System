<?php
session_start();
include("./db.php");
include("./functions.php");
include("./mail.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Invalid request method");
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit("Unauthorized");
}

$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$report_type    = isset($_POST['report_type']) ? trim($_POST['report_type']) : '';

if ($appointment_id <= 0) {
    exit("Invalid appointment id");
}

if ($report_type === '') {
    exit("Report type required");
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    exit("No file selected or upload error");
}

$file = $_FILES['file'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    exit("Only PDF allowed");
}

$getAppointment = $conn->prepare("SELECT doctor_id, patient_id FROM appointments WHERE id = ?");
if ($getAppointment === false) {
    exit("DB error: " . $conn->error);
}
$getAppointment->bind_param("i", $appointment_id);
if (!$getAppointment->execute()) {
    exit("DB error: " . $getAppointment->error);
}
$res = $getAppointment->get_result();
if ($res->num_rows === 0) {
    $getAppointment->close();
    exit("Appointment not found");
}
$row = $res->fetch_assoc();
$getAppointment->close();

$doctor_id  = (int)$row['doctor_id'];
$patient_id = (int)$row['patient_id'];

if ($_SESSION['role'] !== 'admin') {
    $doctor_id = (int)$_SESSION['user_id'];
}

$check = $conn->prepare("SELECT id FROM reports WHERE appointment_id = ?");
if ($check === false) {
    exit("DB error: " . $conn->error);
}
$check->bind_param("i", $appointment_id);
if (!$check->execute()) {
    exit("DB error: " . $check->error);
}
$checkRes = $check->get_result();
if ($checkRes->num_rows > 0) {
    $check->close();
    exit("Report already uploaded");
}
$check->close();

$newName = uniqid("report_", true) . ".pdf";
$uploadDir = __DIR__ . "/../uploads/reports/";
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    exit("Failed to create upload directory");
}
$targetPath = $uploadDir . $newName;
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    exit("Failed to save file");
}

$file_name = basename($file['name']);
$file_path = $newName;

$getInfo = $conn->prepare("
    SELECT p.email, p.first_name AS p_fname, p.last_name AS p_lname, d.first_name AS d_fname, d.last_name AS d_lname
    FROM appointments a
    JOIN patient p ON a.patient_id = p.id
    JOIN doctor d ON a.doctor_id = d.id
    WHERE a.id = ?
");
if ($getInfo === false) {
    exit("DB error: " . $conn->error);
}
$getInfo->bind_param("i", $appointment_id);
if (!$getInfo->execute()) {
    exit("DB error: " . $getInfo->error);
}
$infoRes = $getInfo->get_result();
if ($infoRes->num_rows === 0) {
    $getInfo->close();
    exit("Unable to fetch patient info");
}
$info = $infoRes->fetch_assoc();
$getInfo->close();

sendMail(
    $info['email'],
    "Your Report is Ready",
    "<p>Dear {$info['p_fname']} {$info['p_lname']},<br>
    Your report for appointment has been uploaded by Dr. {$info['d_fname']} {$info['d_lname']}.</p>
    <p>You can download it from your dashboard.</p>"
);

$stmt = $conn->prepare("INSERT INTO reports (appointment_id, doctor_id, patient_id, report_type, file_name, file_path) VALUES (?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    exit("DB error: " . $conn->error);
}
if (!$stmt->bind_param("iiisss", $appointment_id, $doctor_id, $patient_id, $report_type, $file_name, $file_path)) {
    exit("DB error: " . $stmt->error);
}
if ($stmt->execute()) {
    $stmt->close();
    $update = $conn->prepare("UPDATE appointments SET report_uploaded = 1 WHERE id = ?");
    if ($update !== false) {
        $update->bind_param("i", $appointment_id);
        $update->execute();
        $update->close();
    }
    logActivity($conn, 'Report Uploaded', "Report uploaded for appointment ID {$appointment_id} by user ID {$_SESSION['user_id']}.");
    echo "success";
} else {
    $err = $stmt->error;
    $stmt->close();
    exit("Database error: " . $err);
}
?>
