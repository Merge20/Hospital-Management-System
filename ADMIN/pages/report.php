<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$query = "
    SELECT 
        r.id AS report_id,
        CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
        CONCAT(d.first_name, ' ', d.last_name) AS doctor_name,
        a.appointment_date,
        r.report_type,
        r.file_path
    FROM reports r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN patient p ON r.patient_id = p.id
    JOIN doctor d ON r.doctor_id = d.id
    ORDER BY r.uploaded_at DESC
";

$result = $conn->query($query);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/report.css">
    <title>Reports | Hospital</title>
</head>
<body>
    <div class="main">
        <div class="header">
            <div class="h-left"><img src="../img's/logo.png"></div>
            <div class="h-mid">
                <a href="../home.php" class="links a1"><img src="../logo's/home-logo.svg">Home</a>
                <a href="./appointment.php" class="links a2"><img src="../logo's/manage-appointment-logo-2.png">Appointments</a>
                <a href="./doctor.php" class="links a3"><img src="../logo's/doctor-logo.png">Doctor's</a>
                <a href="./patient.php" class="links a4"><img src="../logo's/patient-logo.svg">Patient's</a>
                <a href="./report.php" class="links a5"><img src="../logo's/report-logo.svg">Reports</a>    
            </div>
            <div class="h-right">
                <a href="./account-edit.php?role=admin&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="../logo's/profile-logo.svg">Admin</a>
                <a href="./logout.php" class="links logout a7"><img src="../logo's/logout-logo.svg">Logout</a>
            </div>
        </div>

        <div class="mid">
            <div class="title">Manage Reports</div>
            <div class="report-box">
                <div class="report-box-label">Patient Reports</div>
                <div class="report-box-main">
                    <div class="report-box-content head">
                        <div class="content">Patient Name</div>
                        <div class="content">Doctor Name</div>
                        <div class="content">Appointment Date</div>
                        <div class="content">Report Type</div>
                        <div class="content">Status</div>
                        <div class="content">Action</div>
                    </div>

                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="report-box-content">
                                <div class="content"><?= htmlspecialchars($row['patient_name']); ?></div>
                                <div class="content">Dr. <?= htmlspecialchars($row['doctor_name']); ?></div>
                                <div class="content"><?= date("d M, Y", strtotime($row['appointment_date'])); ?></div>
                                <div class="content"><?= htmlspecialchars($row['report_type']); ?></div>
                                <div class="content status">
                                    <div class="uploaded">Uploaded</div>
                                </div>
                                <div class="content action">
                                    <button class="download" onclick="downloadReport(<?= $row['report_id'] ?>)">Download</button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="report-box-content">
                            <div class="content" style="width:100%; justify-content:center;">No reports found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script>
function downloadReport(reportId) {
    window.location.href = '../../includes/download_report.php?report_id=' + reportId;
}
</script>
</body>
</html>
