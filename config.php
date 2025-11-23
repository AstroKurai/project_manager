<?php
// Name: Jadelynn Wolden
// Date: 11/11/2025
// Filename: config.php
session_start();
$host = 'localhost';
$data = 'project_manager';
$user = 'admin';
$pass = 'password';
$chrs = 'utf8mb4';
$attr = "mysql:host=$host;dbname=$data;charset=$chrs";
$opts = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
