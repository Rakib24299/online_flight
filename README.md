<div align="center">

# ✈️ SkyWings — Online Flight Booking System (OFBS)

[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%207.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Frontend-Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Design](https://img.shields.io/badge/UI_Style-Flat_Modernist-0f172a?style=flat-square)](https://tailwindcss.com/)
[![Responsive](https://img.shields.io/badge/Responsive-Yes-success?style=flat-square)](https://tailwindcss.com/)

**A modern, full-featured web-based Flight Reservation & Airline Operations Management System designed with a flat modernist Tailwind CSS architecture.**

[Key Features](#-key-features) • [Installation Guide](#-installation--setup) • [Default Credentials](#-default-credentials) • [Project Structure](#-project-structure)

</div>

---

## 📖 Overview

**SkyWings (OFBS)** is a comprehensive flight booking and airline management platform built using native **PHP**, **MySQL**, and **Tailwind CSS**. It delivers a seamless, responsive, and sharp user experience for both travelers booking flights and administrators managing aviation operations.

The system features a **Passenger Portal** for searching flights, selecting cabin classes, submitting passenger manifests, and generating print-ready 1-page digital boarding passes, alongside an **Admin Operations Console** for live flight scheduling, airline fleet management, delay reporting, and customer feedback monitoring.

---

## 🌟 Key Features

### 👤 Passenger Experience
* **Live Flight Search:** Filter one-way and round-trip flights by origin, destination, departure date, passenger count, and cabin class (Economy/Business).
* **Dynamic Booking Manifest:** Multi-passenger information entry with real-time route invoice summary.
* **Instant Digital E-Tickets:** Auto-generated boarding passes with official barcodes, seat allocation, and boarding time calculations.
* **1-Page Print / PDF:** Specially engineered `@media print` rules ensuring boarding passes fit perfectly on a single A4 page without page spilling.
* **Live Flight Status Tracking:** Real-time flight tracking indicators (`Scheduled`, `In Flight / Departed`, `Arrived`, `Delayed`).
* **Booking History:** Detailed history of all past and upcoming flights with fare receipts and ticket management.
* **Passenger Feedback System:** Interactive 5-star review portal with pre-filled profile information.

### 🛡️ Admin Operations Console
* **Real-Time Analytics Dashboard:** Metric overview of Total Registered Passengers, Gross Ticket Revenue, Active Scheduled Flights, and Partner Airline Fleets.
* **Live Flight Control Table:** Tabbed operational controls (`All`, `Scheduled`, `Delayed`, `Departed`, `Arrived`) with live search.
* **Instant Status Updates:** One-click actions to mark flights as *Departed*, *Arrived*, or report *Schedule Delays* with automatic timestamp adjustments.
* **Passenger Manifest Viewer:** Detailed list of all booked passengers, contact information, DOB, seat numbers, and fares paid for any specific flight.
* **Airline Fleet Management:** Register partner airlines and configure maximum aircraft seating capacities.
* **Customer Review Center:** Monitor passenger impressions, discovery sources, and average satisfaction scores.

---

## 🛠️ Technology Stack

| Component | Technology | Description |
| :--- | :--- | :--- |
| **Backend Engine** | PHP 7.4 / 8.2+ | Native PHP with secure MySQLi prepared statements & session management |
| **Database** | MySQL | Relational schema with normalized tables (`flight`, `ticket`, `users`, `passenger_profile`, `airline`, `payment`, `feedback`) |
| **Styling Framework** | Tailwind CSS CDN | Custom flat modernist configuration (`rounded-none`, `shadow-none`, subtle borders) |
| **Icons & Typography**| FontAwesome 6 & Google Fonts | Plus Jakarta Sans, Montserrat, and JetBrains Mono |
| **Mailing Service** | PHPMailer | Automated email notification capabilities |

---

## 🚀 Installation & Setup

Follow these simple steps to run the project locally on your machine using **XAMPP**:

### 1. Prerequisites
* Install [XAMPP](https://www.apachefriends.org/) (with Apache & MySQL).
* Web browser (Google Chrome, Microsoft Edge, or Mozilla Firefox).

### 2. Clone / Copy Repository
Clone or extract this project folder into your XAMPP `htdocs` directory:
```bash
cd c:/xampp/htdocs/
git clone https://github.com/your-username/online_flight.git
```

### 3. Database Configuration
1. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open your browser and navigate to `http://localhost/phpmyadmin/`.
3. Create a new database named: **`ofbsphp`**.
4. Click on **Import** tab and select the SQL dump file located at:
   ```text
   database/ofbsphp.sql
   ```
5. Click **Go / Import** to execute the database schema.

### 4. Database Connection Settings
Database configuration is cleanly centralized in `config/db.php`:
```php
$servername = "localhost";
$db_uname = "root";
$db_pass = "";      // Default is empty in XAMPP
$db_name = "ofbsphp";
```

### 5. Launch the Application
Open your browser and visit:
```text
http://localhost/online_flight/
```

---

## 🔑 Default Credentials

For quick testing and evaluation, use the following pre-configured credentials:

| Portal | URL | Username / Email | Password | Role |
| :--- | :--- | :--- | :--- | :--- |
| **Passenger Portal** | `/login.php` | `christine` | `123456789` | Verified Passenger |
| **Admin Console** | `/admin/login.php` | `admin` | `12345678` | System Administrator |

---

## 📁 Project Structure

```text
online_flight/
│
├── admin/                     # Administrator Console & Management
│   ├── index.php              # Operations Dashboard & Live Flight Controls
│   ├── login.php              # Secure Admin Authentication
│   ├── flight.php             # New Flight Route Scheduling Form
│   ├── all_flights.php        # Master Flight Records Catalog
│   ├── list_airlines.php      # Airline Fleet & Capacity Manager
│   ├── pass_list.php          # Passenger Manifest Viewer
│   ├── review.php             # Customer Feedback & Ratings Center
│   └── header.php             # Admin Dashboard Navigation Header
│
├── config/                    # System Configurations
│   └── db.php                 # Centralized MySQL Database Connection
│
├── database/                  # Database Schema
│   └── ofbsphp.sql            # MySQL Database SQL Dump
│
├── includes/                  # Backend Processing Handlers
│   ├── login.inc.php          # Passenger Authentication Script
│   ├── register.inc.php       # Account Registration Handler
│   ├── pass_detail.inc.php    # Passenger Manifest Processor
│   ├── payment.inc.php        # Payment Checkout & Seat Allocator
│   ├── feedback.inc.php       # Feedback Submission Handler
│   └── admin/                 # Admin Operational Action Handlers
│
├── layouts/                   # Global Frontend Templates
│   ├── header.php             # Dynamic Responsive Navbar (Guest/User)
│   └── footer.php             # Global Footer & Scripts
│
├── assets/                    # Static Assets (Images, Icons, Fonts)
│
├── index.php                  # Public Landing Page & Flight Search Engine
├── dashboard.php              # Logged-in Passenger Portal & Metrics
├── book.php                   # Flight Search Results & Route Selector
├── passengers.php             # Passenger Details Entry Form
├── payment.php                # 256-Bit SSL Payment Checkout Form
├── pay_success.php            # Booking Confirmation Screen
├── ticket.php                 # Passenger Boarding Pass Dashboard
├── e_ticket.php               # Print-Optimized 1-Page Boarding Pass
├── booking_history.php        # Travel History & Real-Time Flight Statuses
├── feedback.php               # Passenger Experience & 5-Star Rating Page
├── login.php                  # Passenger Sign In Page
├── register.php               # Passenger Account Registration
├── reset-pwd.php              # Password Reset Request Page
└── create-new-pwd.php         # New Password Configuration Page
```

---

## 🎨 Design Philosophy

This project strictly adheres to a **Flat Modernist Aesthetic**:
* **Sharp Geometry:** `rounded-none` across all containers, inputs, badges, and buttons.
* **Flat Surfaces:** Zero drop shadows (`shadow-none`) for a crisp, professional, and fast-rendering UI.
* **Subtle Architecture:** Light, elegant border accents (`border-slate-300` / `border-slate-800`).
* **100% Mobile Responsive:** Fully optimized fluid layouts for mobile, tablet, and ultra-wide displays.

---

## 📄 License & Credits

* Developed as an academic and professional flight reservation management demonstration.
* Designed & modernized using [Tailwind CSS](https://tailwindcss.com/) and [Font Awesome](https://fontawesome.com/).
* Open source for educational and portfolio demonstration purposes.

<div align="center">
  <sub>Built with ❤️ for aviation software enthusiasts.</sub>
</div>
