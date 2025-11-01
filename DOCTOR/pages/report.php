<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$doctor_id = $_SESSION['user_id'];

$query = "
    SELECT
        a.id AS appointment_id,
        p.first_name AS patient_fname,
        p.last_name AS patient_lname,
        a.appointment_date,
        r.id AS report_id,
        IF(r.id IS NOT NULL, r.report_type, '—') AS report_type,
        IF(r.id IS NOT NULL, 'Uploaded', 'Not Uploaded') AS status
    FROM appointments a
    JOIN patient p ON a.patient_id = p.id
    LEFT JOIN reports r ON a.id = r.appointment_id
    WHERE a.doctor_id = ?
      AND a.status = 'Confirmed'
    ORDER BY a.appointment_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
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
                <a href="./report.php" class="links a3"><img src="../logo's/report-logo.svg">Reports</a>
            </div>
            <div class="h-right">
                <a href="./account-edit.php?role=doctor&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="../logo's/profile-logo.svg">Doctor</a>
                <a href="./logout.php" class="links logout a6"><img src="../logo's/logout-logo.svg">Logout</a>
            </div>
        </div>

        <div class="mid">
            <div class="title">Reports</div>
            <div class="report-box">
                <div class="report-box-label">Patient Reports</div>
                <div class="report-box-main">
                    <div class="report-box-content head">
                        <div class="content">Patient Name</div>
                        <div class="content">Appointment Date</div>
                        <div class="content">Report Type</div>
                        <div class="content">Status</div>
                        <div class="content">Action</div>
                    </div>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="report-box-content">
                                <div class="content"><?php echo htmlspecialchars($row['patient_fname'] . ' ' . $row['patient_lname']); ?></div>
                                <div class="content"><?php echo date("d M, Y", strtotime($row['appointment_date'])); ?></div>
                                <div class="content">
                                    <?php if ($row['status'] === 'Uploaded'): ?>
                                        <?php echo htmlspecialchars($row['report_type']); ?>
                                    <?php else: ?>
                                        <input type="text" id="report_type_<?php echo $row['appointment_id']; ?>" name="report_type_<?php echo $row['appointment_id']; ?>" placeholder="Enter Report Type..." class="report-input">
                                    <?php endif; ?>
                                </div>
                                <div class="content status">
                                    <?php if ($row['status'] === 'Uploaded'): ?>
                                        <div class="uploaded">Uploaded</div>
                                    <?php else: ?>
                                        <div class="not-uploaded">Not Uploaded</div>
                                    <?php endif; ?>
                                </div>
                                <div class="content action">
                                    <?php if ($row['status'] === 'Uploaded'): ?>
                                        <button class="upload2" onclick="uploadReport(<?= $row['appointment_id'] ?>)">Upload</button>
                                    <?php else: ?>
                                        <button class="upload" onclick="uploadReport(<?= $row['appointment_id'] ?>)">Upload</button>
                                    <?php endif; ?>
                                        <?php if ($row['status'] === 'Uploaded'): ?>
                                            <button class="download" onclick="downloadReport(<?= $row['report_id'] ?>)">Download</button>
                                        <?php else: ?>
                                            <button class="download" disabled>Download</button>
                                        <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="report-box-content">
                            <div class="content" style="width:100%; justify-content:center;">No appointments found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script>
function uploadReport(appointmentId) {
    const reportTypeInput = document.querySelector(`#report_type_${appointmentId}`);
    const reportType = reportTypeInput ? reportTypeInput.value.trim() : '';

    if (!reportType) {
        alert("Please enter report type before uploading.");
        return;
    }

    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'application/pdf';
    input.onchange = () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('report_type', reportType);
        formData.append('file', file);

        fetch('../../includes/upload_report.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                alert('Report uploaded successfully.');
                location.reload();
            } else {
                alert(data);
            }
        })
        .catch(() => alert('Upload failed.'));
    };
    input.click();
}

function downloadReport(reportId) {
    window.location.href = '../../includes/download_report.php?report_id=' + reportId;
}
</script>


</body>
</html>
