<?php
include("../includes/session_check.php");
include("../includes/db.php");

$patient_id = $_SESSION['user_id'];

$sql_upcoming = "SELECT COUNT(*) AS total_upcoming 
                 FROM appointments 
                 WHERE patient_id = ? 
                 AND appointment_date >= NOW() 
                 AND status IN ('Pending', 'Confirmed')";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("i", $patient_id);
$stmt_upcoming->execute();
$total_upcoming = $stmt_upcoming->get_result()->fetch_assoc()['total_upcoming'] ?? 0;

$sql_visits = "SELECT COUNT(*) AS total_visits 
               FROM appointments 
               WHERE patient_id = ? 
               AND status = 'Confirmed'";
$stmt_visits = $conn->prepare($sql_visits);
$stmt_visits->bind_param("i", $patient_id);
$stmt_visits->execute();
$total_visits = $stmt_visits->get_result()->fetch_assoc()['total_visits'] ?? 0;

$sql_last = "SELECT appointment_date 
             FROM appointments 
             WHERE patient_id = ? 
             AND status = 'Confirmed' 
             ORDER BY appointment_date DESC 
             LIMIT 1";
$stmt_last = $conn->prepare($sql_last);
$stmt_last->bind_param("i", $patient_id);
$stmt_last->execute();
$result_last = $stmt_last->get_result();
$last_consultation = ($result_last->num_rows > 0)
    ? date("M d, Y", strtotime($result_last->fetch_assoc()['appointment_date']))
    : "—";

$sql_next = "SELECT a.id, a.appointment_date, a.status, 
                    d.first_name AS doctor_first, d.last_name AS doctor_last
             FROM appointments a
             JOIN doctor d ON a.doctor_id = d.id
             WHERE a.patient_id = ?
             AND a.appointment_date >= NOW()
             ORDER BY a.appointment_date ASC";

$stmt_next = $conn->prepare($sql_next);
$stmt_next->bind_param("i", $patient_id);
$stmt_next->execute();
$result_next = $stmt_next->get_result();

include("../includes/db.php");
$patient_id = $_SESSION['user_id'];
$sql = "SELECT a.appointment_date, a.status, d.first_name, d.last_name 
        FROM appointments a 
        JOIN doctor d ON a.doctor_id = d.id 
        WHERE a.patient_id = ? 
        AND a.appointment_date >= NOW() 
        ORDER BY a.appointment_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();


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
                <a href="./home.php" class="links a1"><img src="logo's/home-logo.svg">Home</a>
                <a href="./pages/book-appointment.php" class="links a2"><img src="logo's/book-appointment-logo.svg">Book Appointments</a>
                <a href="./pages/manage-appointment.php" class="links a3"><img src="logo's/manage-appointment-logo-2.png">Manage Appointments</a>
                <a href="./pages/report.php" class="links a4"><img src="logo's/report-logo.svg">Reports</a>
            </div>
            <div class="h-right">
                <a href="./pages/account-edit.php?role=patient&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="./logo's/profile-logo.svg">Patient</a>
                <a href="./pages/logout.php" class="links logout a6"><img src="logo's/logout-logo.svg">Logout</a>
            </div>
        </div>
        <div class="mid">
            <div class="greet">Welcome, Mr. <?php echo $_SESSION['first_name']?>!</div>
            <div class="stats">
                <div class="upcoming-appointment stat-box">
                    <div class="stat-b1"><?= $total_upcoming; ?></div>
                    <div class="stat-b2">Upcoming Appointments</div>
                </div>
                <div class="total-visits stat-box">
                    <div class="stat-b1"><?= $total_visits; ?></div>
                    <div class="stat-b2">Total Visits</div>
                </div>
                <div class="last-consultation stat-box">
                    <div class="stat-b1"><?= $last_consultation; ?></div>
                    <div class="stat-b2">Last Consultation Date</div>
                </div>
            </div>
            
            <div class="recent-appointment">
                <div class="appointment-label">Next Appointments</div>
                <div class="appointment-main">
                    <div class="appointment-box-content head">
                        <div class="content">Date</div>
                        <div class="content">Time</div>
                        <div class="content">Doctor</div>
                        <div class="content">Status</div>
                    </div>

                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $date = date("d M, Y", strtotime($row['appointment_date']));
                            $time = date("h:i A", strtotime($row['appointment_date']));
                            $doctor = "Dr. " . htmlspecialchars($row['first_name'] . " " . $row['last_name']);
                            $status = htmlspecialchars($row['status']);
                            echo '
                            <div class="appointment-box-content">
                                <div class="content">' . $date . '</div>
                                <div class="content">' . $time . '</div>
                                <div class="content">' . $doctor . '</div>
                                <div class="content">
                                    <div class="status ' . strtolower($status) . '">' . ucfirst($status) . '</div>
                                </div>
                            </div>';
                        }
                    } else {
                        echo '<div class="appointment-box-content">
                                <div class="content" colspan="4">No upcoming appointments</div>
                            </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>