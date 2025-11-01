<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$patient_id = $_SESSION['user_id'];

$query = "
    SELECT 
        r.id AS report_id,
        d.first_name AS doc_fname,
        d.last_name AS doc_lname,
        a.appointment_date,
        r.report_type,
        r.file_path
    FROM reports r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN doctor d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/report.css">
    <title>Report | Hospital</title>
</head>
<body>
    <div class="main">
        <div class="header">
            <div class="h-left"><img src="../img's/logo.png"></div>
            <div class="h-mid">
                <a href="../home.php" class="links a1"><img src="../logo's/home-logo.svg">Home</a>
                <a href="./book-appointment.php" class="links a2"><img src="../logo's/book-appointment-logo.svg">Book Appointments</a>
                <a href="./manage-appointment.php" class="links a3"><img src="../logo's/manage-appointment-logo-2.png">Manage Appointments</a>
                <a href="./report.php" class="links a4"><img src="../logo's/report-logo.svg">Reports</a>
            </div>
            <div class="h-right">
                <a href="./account-edit.php?role=patient&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="../logo's/profile-logo.svg">Patient</a>
                <a href="./logout.php" class="links logout a6"><img src="../logo's/logout-logo.svg">Logout</a>
            </div>
        </div>
        <div class="mid">
            <div class="title">Reports</div>
            <div class="report-box">
                <div class="report-box-main">
                    <div class="report-box-content head">
                        <div class="content">Report ID</div>
                        <div class="content">Doctor Name</div>
                        <div class="content">Appointment Date</div>
                        <div class="content">Report Type</div>
                        <div class="content">Action</div>
                    </div>

                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="report-box-content">
                                <div class="content"><?php echo 'RPT-' . htmlspecialchars($row['report_id']); ?></div>
                                <div class="content"><?php echo 'Dr. ' . htmlspecialchars($row['doc_fname'] . ' ' . $row['doc_lname']); ?></div>
                                <div class="content"><?php echo date("d M, Y", strtotime($row['appointment_date'])); ?></div>
                                <div class="content"><?php echo htmlspecialchars($row['report_type']); ?></div>
                                <div class="content">
                                    <button class="download" onclick="downloadReport(<?= $row['report_id'] ?>)">Download</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="report-box-content">
                            <div class="content" style="width:100%; justify-content:center;">No reports available.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>

<script>
function downloadReport(reportId) {
    window.location.href = '../../includes/download_report.php?report_id=' + reportId;
}
</script>

</body>
</html>