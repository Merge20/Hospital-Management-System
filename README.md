# Hospital Management System

## Overview

The Hospital Management System is a web-based application developed to manage core hospital operations such as user registration, authentication, and role-based dashboard access. The system is designed to support three primary user roles: Admin, Doctor, and Patient.

This project demonstrates backend development using PHP with MySQL database integration and a structured frontend built using HTML and CSS.

---

## Features

- Role-based authentication system
- Separate dashboards for Admin, Doctor, and Patient
- Patient registration and login
- Doctor account management
- Appointment handling functionality
- Database-driven architecture
- Organized and modular file structure

---

## Technology Stack

- Backend: PHP  
- Database: MySQL  
- Frontend: HTML, CSS  
- Server: Apache (XAMPP / WAMP / LAMP / MAMP)

---

## Project Structure

Hospital-Management-System/

├── ADMIN/               Admin dashboard files  
├── DOCTOR/              Doctor dashboard files  
├── PATIENT/             Patient dashboard files  
├── includes/            Shared components and configuration files  
├── php/                 Backend processing scripts  
├── index.php            Main login page  
├── register.html        User registration page  
├── hospital_db.sql      Database schema and sample data  
├── test_mail.php        Email testing script  
├── login.css            Login page styling  
├── register.css         Registration page styling  
└── logo.png             Project logo  

---

## Installation and Setup

### 1. Clone the Repository

git clone https://github.com/Merge20/Hospital-Management-System.git  
cd Hospital-Management-System  

### 2. Set Up Local Server

- Install XAMPP, WAMP, LAMP, or any Apache + MySQL environment.
- Move the project folder to:
  - htdocs (for XAMPP)
  - www (for WAMP)

### 3. Configure Database

1. Start Apache and MySQL services.
2. Open phpMyAdmin.
3. Create a new database (e.g., hospital_db).
4. Import the hospital_db.sql file.
5. Update database credentials in the configuration file if required.

### 4. Run the Application

Open your browser and navigate to:

http://localhost/Hospital-Management-System

---

## User Roles and Functionalities

### Admin
- Manage doctor accounts  
- Manage patient records  
- Monitor system data  

### Doctor
- View assigned patients  
- Manage appointments  
- Update patient records  

### Patient
- Register and log in  
- Book appointments  
- View appointment history  

---

## Future Enhancements

- Password hashing implementation  
- Input validation and prepared statements  
- Email notification system  
- Calendar-based appointment scheduling  
- Payment gateway integration  
- Improved UI/UX design  

---

## Contribution

Contributions are welcome.

1. Fork the repository  
2. Create a new branch  
3. Commit your changes  
4. Open a Pull Request  

---

## License

This project is developed for educational and academic purposes.
