<?php
include("./includes/mail.php");

if (sendMail("Keshavr7200@gmail.com", "Test Email", "<h3>Mail system works ✅</h3>")) {
    echo "Mail sent successfully!";
} else {
    echo "Mail failed.";
}
?>
