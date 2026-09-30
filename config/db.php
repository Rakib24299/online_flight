<?php
/**
 * Database Connection Configuration
 * Online Flight Booking System
 */
$servername  = "localhost";
$db_username = "root";
$db_password = "";
$db_name     = 'ofbsphp';

$conn = mysqli_connect($servername, $db_username, $db_password, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
