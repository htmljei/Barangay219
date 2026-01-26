<?php
/**
 * E-Barangay Information Management System
 * Root redirect to public folder
 */

// Use relative redirect to avoid issues
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$path = dirname($_SERVER['SCRIPT_NAME']);
header('Location: ' . $protocol . '://' . $host . $path . '/e-barangay-system/e-barangay-system/public/');
exit();
?>
