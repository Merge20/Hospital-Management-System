<?php
include("../../includes/session_check.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/book-appointment.css">
    <link rel="icon" href="../logo's/favicon.ico">
    <title>Book Appointment | Hospital</title>
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
            <div class="title">Book Appointment</div>

            <div class="mid-items">
                <div class="mid-main1">
                    <div class="time-title">Time Slot</div>

                    <div id="time-message" style="text-align:center; color:#012743; font-weight:500; font-size:15px; margin-bottom:10px;">
                        Please select a doctor and date to enable time slots
                    </div>

                    <div class="mid-main1-box1 time">
                        <button class="available-time" disabled>9:00 AM</button>
                        <button class="available-time" disabled>9:30 AM</button>
                        <button class="available-time" disabled>10:00 AM</button>
                    </div>
                    <div class="mid-main1-box2 time">
                        <button class="available-time" disabled>11:30 AM</button>
                        <button class="available-time" disabled>12:00 PM</button>
                        <button class="available-time" disabled>12:30 PM</button>
                    </div>
                    <div class="mid-main1-box3 time">
                        <button class="available-time" disabled>1:00 PM</button>
                        <button class="available-time" disabled>1:30 PM</button>
                        <button class="available-time" disabled>2:00 PM</button>
                    </div>
                    <div class="mid-main1-box4 time">
                        <button class="available-time" disabled>2:30 PM</button>
                        <button class="available-time" disabled>3:00 PM</button>
                        <button class="available-time" disabled>3:30 PM</button>
                    </div>
                    <div class="mid-main1-box5 time">
                        <button class="available-time" disabled>4:00 PM</button>
                        <button class="available-time" disabled>4:30 PM</button>
                        <button class="available-time" disabled>5:00 PM</button>
                    </div>
                </div>

                <div class="mid-main2">
                    <div class="book-title">Book Appointment</div>
                    <div class="info">
                        <div class="doctor sizing">
                            Doctor
                            <div class="doc-choose input-size"></div>
                        </div>
                        <div class="date sizing">
                            Date
                            <div class="date-choose input-size"></div>
                        </div>
                    </div>
                    <div class="book">
                        <button>Book Appointment</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", async () => {
    const docDiv = document.querySelector(".doc-choose");
    const dateDiv = document.querySelector(".date-choose");
    const allTimeButtons = document.querySelectorAll(".available-time");
    const timeMessage = document.getElementById("time-message");
    const bookButton = document.querySelector(".book button");

    let selectedTime = null;

    async function loadDoctors() {
        const res = await fetch("../../includes/fetch_data.php");
        const data = await res.json();
        let html = `<select id="doctor-select" style="width:100%;height:100%;border:none;border-radius:5px;padding:5px;">
                        <option value="">Select Doctor</option>`;
        data.doctors.forEach(doc => {
            html += `<option value="${doc.id}">${doc.name}</option>`;
        });
        html += `</select>`;
        docDiv.innerHTML = html;
    }

    async function loadBookedSlots() {
        const doctorSelect = document.getElementById("doctor-select");
        const dateSelect = document.getElementById("date-select");
        const doctor_id = doctorSelect.value;
        const date = dateSelect.value;

        if (!doctor_id || !date) {
            allTimeButtons.forEach(btn => {
                btn.disabled = true;
                btn.style.cursor = "not-allowed";
            });
            timeMessage.textContent = "Please select a doctor and date to enable time slots";
            return;
        }

        allTimeButtons.forEach(btn => {
            btn.disabled = false;
            btn.style.opacity = "1";
            btn.style.cursor = "pointer";
            btn.style.background = "";
        });

        const res = await fetch(`../../includes/fetch_data.php?doctor_id=${doctor_id}&date=${date}`);
        const data = await res.json();
        const booked = data.booked_slots || [];

        booked.forEach(time => {
            allTimeButtons.forEach(btn => {
                if (btn.textContent.trim() === time) {
                    btn.disabled = true;
                    btn.style.opacity = "0.4";
                    btn.style.cursor = "not-allowed";
                    btn.style.background = "#ccc";
                }
            });
        });

        timeMessage.textContent = booked.length > 0
            ? "Some time slots are already booked."
            : "Select a time slot for your appointment.";
    }

    const today = new Date().toISOString().split("T")[0];
    dateDiv.innerHTML = `<input type="date" id="date-select" min="${today}" style="width:100%;height:100%;border:none;border-radius:5px;padding:5px;">`;

    await loadDoctors();

    const doctorSelect = document.getElementById("doctor-select");
    const dateSelect = document.getElementById("date-select");

    doctorSelect.addEventListener("change", loadBookedSlots);
    dateSelect.addEventListener("change", loadBookedSlots);

    allTimeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            if (btn.disabled) return;
            // allTimeButtons.forEach(b => b.style.background = "");
            // btn.style.background = "#007BFF";
            // btn.style.color = "#fff";
            selectedTime = btn.textContent.trim();
        });
    });

    bookButton.addEventListener("click", async () => {
        const doctor_id = doctorSelect.value;
        const date = dateSelect.value;

        if (!doctor_id || !date || !selectedTime) {
            alert("Please select doctor, date, and time slot.");
            return;
        }

        const formData = new FormData();
        formData.append("doctor_id", doctor_id);
        formData.append("date", date);
        formData.append("time", selectedTime);

        const res = await fetch("../../includes/book_appointment.php", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        if (data.status === "success") {
            alert("Appointment booked successfully!");
            loadBookedSlots();
            selectedTime = null;
            allTimeButtons.forEach(b => b.style.background = "");
        } else {
            alert(data.message || "Failed to book appointment.");
        }
    });
});
</script>


</body>
</html>
