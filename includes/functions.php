<?php
/**
 * Helper Functions
 *
 * Common utility functions used throughout the application
 */

/**
 * Sanitize input data
 *
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Check if user is logged in
 *
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 *
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 *
 * @return array|null
 */
function getCurrentUser() {
    return $_SESSION['user_data'] ?? null;
}

/**
 * Check if current user has role
 *
 * @param string|array $roles
 * @return bool
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }

    $userRole = $_SESSION['user_data']['role'] ?? '';

    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }

    return $userRole === $roles;
}

/**
 * Redirect to a page
 *
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Format date for display
 *
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }

    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Format currency
 *
 * @param float $amount
 * @param string $currency
 * @return string
 */
function formatCurrency($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Get status badge HTML
 *
 * @param string $status
 * @return string
 */
function getStatusBadge($status) {
    $badges = [
        'lead' => '<span class="badge badge-info">Lead</span>',
        'prospect' => '<span class="badge badge-warning">Prospect</span>',
        'customer' => '<span class="badge badge-success">Customer</span>',
        'inactive' => '<span class="badge badge-secondary">Inactive</span>',
        'active' => '<span class="badge badge-success">Active</span>',
        'scheduled' => '<span class="badge badge-primary">Scheduled</span>',
        'completed' => '<span class="badge badge-success">Completed</span>',
        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
    ];

    return $badges[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Get deal stage badge HTML
 *
 * @param string $stage
 * @return string
 */
function getDealStageBadge($stage) {
    $badges = [
        'qualification' => '<span class="badge badge-info">Qualification</span>',
        'proposal' => '<span class="badge badge-primary">Proposal</span>',
        'negotiation' => '<span class="badge badge-warning">Negotiation</span>',
        'closed_won' => '<span class="badge badge-success">Closed Won</span>',
        'closed_lost' => '<span class="badge badge-danger">Closed Lost</span>',
    ];

    return $badges[$stage] ?? '<span class="badge badge-secondary">' . ucfirst(str_replace('_', ' ', $stage)) . '</span>';
}

/**
 * Set flash message
 *
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 *
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Display flash message HTML
 *
 * @return string
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];

        $class = $alertClass[$flash['type']] ?? 'alert-info';

        return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($flash['message'])
            . '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            . '<span aria-hidden="true">&times;</span></button></div>';
    }
    return '';
}

/**
 * Generate CSRF token
 *
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 *
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Paginate results
 *
 * @param int $totalRecords
 * @param int $currentPage
 * @param int $recordsPerPage
 * @return array
 */
function paginate($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $recordsPerPage;

    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}
