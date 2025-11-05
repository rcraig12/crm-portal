<?php
/**
 * Common Header Template
 */

if (!defined('APP_NAME')) {
    die('Direct access not permitted');
}

if (!isLoggedIn()) {
    redirect('login.php');
}

$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h3><?php echo APP_NAME; ?></h3>
            </div>

            <ul class="sidebar-menu">
                <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <a href="dashboard.php">
                        <span class="icon">📊</span> Dashboard
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'contacts' ? 'active' : ''; ?>">
                    <a href="contacts.php">
                        <span class="icon">👥</span> Contacts
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'companies' ? 'active' : ''; ?>">
                    <a href="companies.php">
                        <span class="icon">🏢</span> Companies
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'deals' ? 'active' : ''; ?>">
                    <a href="deals.php">
                        <span class="icon">💼</span> Deals
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'activities' ? 'active' : ''; ?>">
                    <a href="activities.php">
                        <span class="icon">📅</span> Activities
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-info">
                    <strong><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></strong>
                    <small><?php echo htmlspecialchars($currentUser['role']); ?></small>
                </div>
                <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <h2><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
                <div class="user-welcome">
                    Welcome, <?php echo htmlspecialchars($currentUser['first_name']); ?>!
                </div>
            </div>

            <div class="content">
                <?php echo displayFlashMessage(); ?>
