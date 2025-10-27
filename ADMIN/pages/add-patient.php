<?php
include("../../includes/session_check.php");
include("../../includes/db.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/add-patient.css">
    <title>Add patient's | Hospital</title>
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
            <div class="title">Add patient's</div>

            <form action="../../php/register.php" method="POST"class="mid-box">
                <div class="current-credentials">
                    <div class="name">
                        <div class="first-box">
                            First Name
                            <input type="text" name="first_name" class="name-box" required>
                        </div>
                        <div class="first-box">
                            Last Name
                            <input type="text" name="last_name" class="name-box" required>
                        </div>
                    </div>
                    <div class="number same">
                        Number
                        <input type="tel" name="phone" class="number-box same-2" maxlength="10" required>
                    </div>
                    <div class="email same">
                        Email
                        <input type="email" name="email" class="email-box same-2" required>
                    </div>
                    <div class="pass same">
                        Password
                        <input type="text" name="password" class="pass-box same-2" required>
                    </div>
                </div>
                <div class="save">
                    <button>Add patient</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>