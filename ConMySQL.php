<?php

$hostname_MySQL = "localhost";
$database_MySQL = "xchange";
$username_MySQL = "root";
$password_MySQL = "";
$port = 3306; // Default MySQL port

// Create connection
$MySQL = new mysqli($hostname_MySQL, $username_MySQL, $password_MySQL, $database_MySQL, $port);

// Check connection
if ($MySQL->connect_error) {
    die("Connection failed: " . $MySQL->connect_error);
}
