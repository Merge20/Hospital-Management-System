<?php
include("../includes/session_check.php");
include("../includes/db.php");

function getCount($conn, $query, $label) {
    $result = $conn->query($query);
    if (!$result) {
        die("Query failed for $label: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    return $row[array_keys($row)[0]] ?? 0;
}

$total_doctors = getCount($conn, "SELECT COUNT(*) AS total_doctors FROM doctor", "doctor");
$total_patients = getCount($conn, "SELECT COUNT(*) AS total_patients FROM patient", "patient");
$total_appointments = getCount($conn, "SELECT COUNT(*) AS total_appointments FROM appointments", "appointments");

// For reports, since you don’t have a reports table, use appointments.report_uploaded
$total_reports = getCount($conn, "SELECT COUNT(*) AS total_reports FROM appointments WHERE report_uploaded = 1", "reports");
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
                <a href="./pages/doctor.php" class="links a3"><img src="./logo's/doctor-logo.png">Doctor's</a>
                <a href="./pages/patient.php" class="links a4"><img src="./logo's/patient-logo.svg">Patient's</a>
                <a href="./pages/report.php" class="links a5"><img src="./logo's/report-logo.svg">Reports</a>    
            </div>
            <div class="h-right">
                <a href="./pages/account-edit.php?role=admin&id=<?php echo $_SESSION['user_id']; ?>" class="links a6 profile"><img src="./logo's/profile-logo.svg">Admin</a>
                <a href="./pages/logout.php" class="links logout a7"><img src="./logo's/logout-logo.svg">Logout</a>
            </div>
        </div>
        <div class="mid">   
            <div class="greet">Welcome, <?php echo $_SESSION['last_name']; ?></div>
            <div class="stats">
                <div class="stat-box">
                    <div class="stat-card">
                        <div class="stat-logo"><img src="./logo's/doctor-logo.png"></div>
                        <div class="">
                            <div class="stat-number"><?php echo $total_doctors; ?></div>
                            <div class="stat-label">Total Doctors</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-logo logo2"><img src="./logo's/manage-appointment-logo-2.png"></div>
                        <div class="">
                            <div class="stat-number"><?php echo $total_appointments; ?></div>
                            <div class="stat-label">Total Appointment</div>
                        </div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-card">
                        <div class="stat-logo"><img src="./logo's/patient-logo.svg"></div>
                        <div class="">
                            <div class="stat-number"><?php echo $total_patients; ?></div>
                            <div class="stat-label">Total Patients</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-logo"><img src="./logo's/report-logo.svg"></div>
                        <div class="">
                            <div class="stat-number"><?php echo $total_reports; ?></div>
                            <div class="stat-label">Reports Uploaded</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="recent-activity">
                <div class="activity-label">Recent Activities</div>
                <div class="activity-main">
                    <div class="activity-box-content head">
                        <div class="content activity">Activity</div>
                        <div class="content ">Description</div>
                        <div class="content date">Date</div>
                        <div class="content time">Time</div>
                    </div>
                    <?php
                    $activityQuery = "SELECT activity_type, description, created_at FROM activity_log ORDER BY created_at DESC LIMIT 10";
                    $activityResult = $conn->query($activityQuery);

                    if ($activityResult && $activityResult->num_rows > 0) {
                        while ($activity = $activityResult->fetch_assoc()) {
                            $date = date("d M, Y", strtotime($activity['created_at']));
                            $time = date("h:i A", strtotime($activity['created_at']));
                            echo "
                                <div class='activity-box-content'>
                                    <div class='content activity'>{$activity['activity_type']}</div>
                                    <div class='content desc'>{$activity['description']}</div>
                                    <div class='content date'>{$date}</div>
                                    <div class='content time'>{$time}</div>
                                </div>
                            ";
                        }
                    } else {
                        echo "
                            <div class='activity-box-content'>
                                <div class='content' colspan='4'>No recent activity found.</div>
                            </div>
                        ";
                    }
                    ?>
                </div>
            </div>


        </div>
        <div class="footer"></div>
    </div>
</body>
</html>