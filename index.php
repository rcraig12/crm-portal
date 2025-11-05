<?php
/**
 * CRM Portal - Entry Point
 *
 * This file serves as the entry point and redirects to login or dashboard
 */

session_start();

// Redirect to dashboard if logged in, otherwise to login page
if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
