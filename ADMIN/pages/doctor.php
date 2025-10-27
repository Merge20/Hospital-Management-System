<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

$doctors = $conn->query("SELECT * FROM doctor ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/doctor.css">
    <title>Doctor's | Hospital</title>
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
            <div class="title">
                Manage Doctors
                <a class="add" href="./add-doctor.php">Add Doctor</a>
            </div>
            <div class="manage-doctor">
                <div class="doctor-label">Current Doctor's</div>
                <div class="manage-main">
                    <div class="manage-box-content head">
                        <div class="content">Name</div>
                        <div class="content">Email</div>
                        <div class="content">Phone</div>
                        <div class="content">Action</div>
                    </div>
                    <?php if ($doctors->num_rows > 0): ?>
                    <?php while ($row = $doctors->fetch_assoc()): ?>
                        <div class="manage-box-content">
                            <div class="content"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></div>
                            <div class="content"><?php echo htmlspecialchars($row['email']); ?></div>
                            <div class="content"><?php echo htmlspecialchars($row['phone']); ?></div>
                            <div class="content">
                                <div class="Action">
                                    <a class="edit" href="account-edit.php?role=doctor&id=<?php echo $row['id']; ?>">Edit</a>
                                    <a class="delete" href="../../includes/delete_doctor.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this doctor?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <div class="manage-box-content">
                            <div class="content" colspan="4">No doctors found</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>