<?php
include("../includes/db.php");
include("../includes/session_check.php");

$doctor_id = $_SESSION['user_id'];

$sql_today = "SELECT COUNT(*) AS total_today FROM appointments WHERE doctor_id = ? AND DATE(appointment_date) = CURDATE()";
$stmt_today = $conn->prepare($sql_today);
$stmt_today->bind_param("i", $doctor_id);
$stmt_today->execute();
$result_today = $stmt_today->get_result()->fetch_assoc();
$total_today = $result_today['total_today'] ?? 0;

$sql_patients = "SELECT COUNT(DISTINCT patient_id) AS total_patients FROM appointments WHERE doctor_id = ?";
$stmt_patients = $conn->prepare($sql_patients);
$stmt_patients->bind_param("i", $doctor_id);
$stmt_patients->execute();
$result_patients = $stmt_patients->get_result()->fetch_assoc();
$total_patients = $result_patients['total_patients'] ?? 0;

$sql_pending = "SELECT COUNT(*) AS total_pending FROM appointments WHERE doctor_id = ? AND status = 'Pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param("i", $doctor_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result()->fetch_assoc();
$total_pending = $result_pending['total_pending'] ?? 0;

$sql_next = "SELECT a.appointment_date, a.status, 
                    p.first_name, p.last_name 
             FROM appointments a
             JOIN patient p ON a.patient_id = p.id
             WHERE a.doctor_id = ? 
             AND a.appointment_date >= NOW()
             ORDER BY a.appointment_date
             LIMIT 5";

$stmt_next = $conn->prepare($sql_next);
if (!$stmt_next) {
    die('SQL prepare failed: ' . $conn->error);
}
$stmt_next->bind_param("i", $doctor_id);
$stmt_next->execute();
$result_next = $stmt_next->get_result();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/styles.css">
    <title>Home | Hospital</title>
</head>
<body>
    <div class="main">
        <div class="header">
            <div class="h-left"><img src="./img's/logo.png"></div>
            <div class="h-mid">
                <a href="./home.php" class="links a1"><img src="./logo's/home-logo.svg">Home</a>
                <a href="./pages/appointment.php" class="links a2"><img src="./logo's/manage-appointment-logo-2.png">Appointments</a>
                <a href="./pages/report.php" class="links a3"><img src="./logo's/report-logo.svg">Reports</a>    
            </div>
            <div class="h-right">
                <a href="../account-edit.php?role=doctor&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="./logo's/profile-logo.svg">Doctor</a>
                <a href="./pages/logout.php" class="links logout a6"><img src="./logo's/logout-logo.svg">Logout</a>
            </div>
        </div>
        <div class="mid">
            <div class="greet">Welcome, Dr. <?php echo $_SESSION['first_name']?>!</div>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_today; ?></div>
                    <div class="stat-label">Appointments Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_patients; ?></div>
                    <div class="stat-label">Total Patients</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_pending; ?></div>
                    <div class="stat-label">Pending Appointments</div>
                </div>
            </div>
            <div class="upcoming-appointment">
                <div class="appointment-label">Next Appointments</div>
                <div class="appointment-main">
                    <div class="appointment-box-content head">
                        <div class="content">Patient Name</div>
                        <div class="content">Date</div>
                        <div class="content">Time</div>
                        <div class="content">Status</div>
                    </div>

                    <?php if ($result_next && $result_next->num_rows > 0): ?>
                        <?php while  ($row = $result_next->fetch_assoc()): ?>
                            <div class="appointment-box-content">
                                <div class="content"><?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></div>
                                <div class="content"><?php echo date("d M, Y", strtotime($row['appointment_date'])); ?></div>
                                <div class="content"><?= date("h:i A", strtotime($row['appointment_date'])); ?></div>
                                <div class="content">
                                    <div class="status <?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="appointment-box-content">
                            <div class="content" colspan="4">No upcoming appointments</div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>