<?php
include("../../includes/session_check.php");
include("../../includes/db.php");

if (!isset($_GET['role']) || !isset($_GET['id'])) {
    echo "<script>alert('Invalid access!'); window.location.href='../home.php';</script>";
    exit;
}

$role = $_GET['role'];
$id = intval($_GET['id']);

$sql = "SELECT * FROM $role WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('User not found!'); window.location.href='../home.php';</script>";
    exit;
}

$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    $update = $conn->prepare("UPDATE $role SET first_name=?, last_name=?, email=?, phone=?, password=? WHERE id=?");
    $update->bind_param("sssssi", $first_name, $last_name, $email, $phone, $password, $id);
    $update->execute();

    if($role == "admin"){
        echo "<script>alert('User details updated successfully!'); window.location.href='../home.php';</script>";
    }
    else if($role=="doctor"){
        echo "<script>alert('User details updated successfully!'); window.location.href='./$role.php';</script>";
    }
    else if($role=="patient"){
        echo "<script>alert('User details updated successfully!'); window.location.href='./$role.php';</script>";
    }
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/account-edit.css">
    <title>Edit Profile | Hospital</title>
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
            <div class="title">Edit Account</div>
            <form method="POST" class="mid-box">
                <div class="b1">
                    <div class="current-credentials">
                        <div class="title-2">Current Credentials</div>
                        <div class="name">
                            <div class="first-box">
                                First Name
                                <div class="name-box"><?php echo htmlspecialchars($data['first_name']); ?></div>
                            </div>
                            <div class="first-box">
                                Last Name
                                <div class="name-box"><?php echo htmlspecialchars($data['last_name']); ?></div>
                            </div>
                        </div>
                        <div class="number same">
                            Number
                            <div class="number-box same-2"><?php echo htmlspecialchars($data['phone']); ?></div>
                        </div>
                        <div class="email same">
                            Email
                            <div class="email-box same-2"><?php echo htmlspecialchars($data['email']); ?></div>
                        </div>
                        <div class="pass same">
                            Password
                            <div class="pass-box same-2"><?php echo htmlspecialchars($data['password']); ?></div>
                        </div>
                    </div>
    
                    <div class="change-to">
                        <div class="title-2">Change To</div>
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
                            <input type="text" name="phone" class="number-box same-2" required>
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
                </div>

                <div class="save">
                    <button type="submit">Save Changes</button>
                </div>
            </form>
        </div>
        <div class="footer"></div>
    </div>
</body>
</html>
