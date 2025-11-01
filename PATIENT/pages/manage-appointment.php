<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$patient_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['cancel_id'])) {
    $cancel_id = intval($_POST['cancel_id']);
    $update = $conn->prepare("UPDATE appointments SET status='Cancelled' WHERE id=? AND patient_id=?");
    $update->bind_param("ii", $cancel_id, $patient_id);
    if ($update->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?cancelled=1");
        exit;
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=1");
        exit;
    }
}

$query = "
    SELECT a.id, a.appointment_date, a.status, 
           d.first_name AS doc_fname, d.last_name AS doc_lname
    FROM appointments a
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
    <link rel="stylesheet" href="../styles/manage-appointment.css">
    <title>Manage Appointment | Hospital</title>
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
            <div class="title">Manage Appointments</div>
            <div class="manage-box">
                <div class="manage-box-label">Current Appointments</div>
                <div class="manage-box-main">
                    <div class="manage-box-content head">
                        <div class="content">Doctor Name</div>
                        <div class="content">Date</div>
                        <div class="content">Time</div>
                        <div class="content">Status</div>
                        <div class="content">Action</div>
                    </div>

                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): 
                            $datetime = strtotime($row['appointment_date']);
                            $date = date("d M, Y", $datetime);
                            $time = date("g:i A", $datetime);
                            $status = ucfirst($row['status']);
                            $statusClass = strtolower($status);
                        ?>
                        <div class="manage-box-content">
                            <div class="content">Dr. <?= htmlspecialchars($row['doc_fname'] . " " . $row['doc_lname']) ?></div>
                            <div class="content"><?= $date ?></div>
                            <div class="content"><?= $time ?></div>
                            <div class="content status">
                                <div class="<?= $statusClass ?>"><?= $status ?></div>
                            </div>
                            <div class="content action">
                                <?php if ($status !== "Cancelled"): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="cancel_id" value="<?= $row['id'] ?>">
                                        <button class="cancel" type="submit">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <button class="cancel" disabled>Cancel</button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="manage-box-content" style="text-align:center; padding:15px;">
                            No appointments found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
    <?php if (isset($_GET['cancelled'])): ?>
        <script>alert('Appointment cancelled successfully!');</script>
    <?php elseif (isset($_GET['error'])): ?>
        <script>alert('Error cancelling appointment');</script>
    <?php endif; ?>
</body>
</html>
