<?php
function logActivity($conn, $activity_type, $description) {
    $user_id = $_SESSION['user_id'] ?? null;
    $role = $_SESSION['role'] ?? null;

    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, role, activity_type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $role, $activity_type, $description);
    $stmt->execute();
    $stmt->close();
}
?>
