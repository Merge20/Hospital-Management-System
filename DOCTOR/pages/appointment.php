<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$doctor_id = $_SESSION['user_id'];

$sql = "SELECT a.id, p.first_name, p.last_name, p.phone, a.appointment_date, a.status FROM appointments a JOIN patient p ON a.patient_id = p.id WHERE a.doctor_id = ? ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
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
                <a href="./appointments.php" class="links a2 active"><img src="../logo's/manage-appointment-logo-2.png">Appointments</a>
                <a href="./report.php" class="links a3"><img src="../logo's/report-logo.svg">Report</a>
            </div>
            <div class="h-right">
                <a href="./account-edit.php?role=doctor&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="../logo's/profile-logo.svg">Doctor</a>
                <a href="./logout.php" class="links logout a6"><img src="../logo's/logout-logo.svg">Logout</a>
            </div>
        </div>

        <div class="mid">
            <div class="title">Appointments</div>
            <div class="manage-box">
                <div class="manage-box-label">Manage Appointments</div>
                <div class="manage-box-main">
                    <div class="manage-box-content head">
                        <div class="content">Patient Name</div>
                        <div class="content">Appointment Date</div>
                        <div class="content">Appointment Time</div>
                        <div class="content">Contact</div>
                        <div class="content">Status</div>
                        <div class="content">Action</div>
                    </div>

                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $date = date("d M, Y", strtotime($row['appointment_date']));
                                $time = date("h:i A", strtotime($row['appointment_date']));
                                $status_class = strtolower($row['status']);
                            ?>
                            <div class="manage-box-content">
                                <div class="content"><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?></div>
                                <div class="content"><?= $date; ?></div>
                                <div class="content"><?= $time; ?></div>
                                <div class="content"><?= htmlspecialchars($row['phone']); ?></div>
                                <div class="content status">
                                    <div class="<?= $status_class; ?>"><?= htmlspecialchars($row['status']); ?></div>
                                </div>
                                <div class="content action">
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <form method="POST" action="../../includes/confirm_appointment.php">
                                            <input type="hidden" name="appointment_id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="confirm">Confirm</button>
                                        </form>
                                        <form method="POST" action="../../includes/cancel-appointment.php">
                                            <input type="hidden" name="appointment_id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="cancel">Cancel</button>
                                        </form>
                                    <?php elseif ($row['status'] === 'Confirmed'): ?>
                                        <button class="confirm-2" disabled>Confirmed</button>
                                    <?php elseif ($row['status'] === 'Cancelled'): ?>
                                        <button class="cancel-2" disabled>Cancelled</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="manage-box-content">
                            <div class="content" style="grid-column: span 6; text-align:center;">No appointments found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer"></div>
    </div>
</body>
</html>
