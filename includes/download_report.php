<?php
include("./db.php");

if (!isset($_GET['report_id'])) {
    die("Invalid request.");
}

$report_id = intval($_GET['report_id']);

$stmt = $conn->prepare("SELECT file_path FROM reports WHERE id = ?");
$stmt->bind_param("i", $report_id);
$stmt->execute();
$stmt->bind_result($file_path);
$stmt->fetch();
$stmt->close();

if (!$file_path) {
    die("No file path found for report ID $report_id.");
}

$file = "../uploads/reports/" . $file_path;

if (!file_exists($file)) {
    die("File not found at: " . $file);
}

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
header("Content-Length: " . filesize($file));
readfile($file);
exit;
?>
