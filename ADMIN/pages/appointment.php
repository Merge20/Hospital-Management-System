<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$query = "
    SELECT 
        a.id,
        p.first_name AS patient_fname, p.last_name AS patient_lname,
        d.first_name AS doctor_fname, d.last_name AS doctor_lname,
        a.appointment_date,
        a.status
    FROM appointments a
    JOIN patient p ON a.patient_id = p.id
    JOIN doctor d ON a.doctor_id = d.id
    ORDER BY a.appointment_date DESC
";

$result = $conn->query($query);
if (!$result) {
    die('Query failed: ' . $conn->error);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/appointment.css">
    <title>Appointments | Hospital</title>
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
            <div class="title">Manage Appointments</div>
            <div class="manage-box">
                <div class="manage-box-label">Current Appointments</div>
                <div class="manage-box-main">
                    <div class="manage-box-content head">
                        <div class="content">Patient Name</div>
                        <div class="content">Doctor Name</div>
                        <div class="content">Date</div>
                        <div class="content">Time</div>
                        <div class="content">Status</div>
                        <div class="content">Action</div>
                    </div>

                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $datetime = strtotime($row['appointment_date']);
                                $date = date("d M, Y", $datetime);
                                $time = date("h:i A", $datetime);
                                $status = $row['status'];
                            ?>
                            <div class="manage-box-content">
                                <div class="content"><?php echo htmlspecialchars($row['patient_fname'] . ' ' . $row['patient_lname']); ?></div>
                                <div class="content"><?php echo htmlspecialchars('Dr. ' . $row['doctor_fname'] . ' ' . $row['doctor_lname']); ?></div>
                                <div class="content"><?php echo $date; ?></div>
                                <div class="content"><?php echo $time; ?></div>
                                <div class="content status">
                                    <div class="<?php echo strtolower($status); ?>"><?php echo $status; ?></div>
                                </div>
                                <div class="content action">
                                    <?php if ($status !== 'Cancelled'): ?>
                                        <form method="post" action="../../includes/cancel-appointment.php" onsubmit="return confirm('Cancel this appointment?');">
                                            <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="cancel">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="cancel" disabled>Cancelled</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="manage-box-content">
                            <div class="content" colspan="6">No appointments found</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
