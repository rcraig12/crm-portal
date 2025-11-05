<?php
/**
 * Dashboard Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Contact.php';
require_once 'models/Company.php';
require_once 'models/Deal.php';
require_once 'models/Activity.php';

$pageTitle = 'Dashboard';

// Initialize database and models
$database = new Database();
$contactModel = new Contact($database);
$companyModel = new Company($database);
$dealModel = new Deal($database);
$activityModel = new Activity($database);

// Get statistics
$totalContacts = $contactModel->count();
$totalCompanies = $companyModel->count();
$totalDeals = $dealModel->count();
$totalActivities = $activityModel->count(['status' => 'scheduled']);

$contactsByStatus = $contactModel->countByStatus();
$dealsByStage = $dealModel->countByStage();
$dealValueByStage = $dealModel->getValueByStage();

// Get recent activities
$recentActivities = $activityModel->getAll(['assigned_to' => getCurrentUserId()], 5);

// Get recent contacts
$recentContacts = $contactModel->getAll([], 5);

// Get upcoming activities
$upcomingActivities = $activityModel->getAll(['status' => 'scheduled', 'assigned_to' => getCurrentUserId()], 5);

include 'includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <h3><?php echo number_format($totalContacts); ?></h3>
            <p>Total Contacts</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-info">
            <h3><?php echo number_format($totalCompanies); ?></h3>
            <p>Companies</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">💼</div>
        <div class="stat-info">
            <h3><?php echo number_format($totalDeals); ?></h3>
            <p>Active Deals</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
            <h3><?php echo number_format($totalActivities); ?></h3>
            <p>Scheduled Activities</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Contacts by Status -->
    <div class="dashboard-panel">
        <div class="panel-header">
            <h3>Contacts by Status</h3>
        </div>
        <div class="panel-body">
            <?php if (!empty($contactsByStatus)): ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($contactsByStatus as $status => $count): ?>
                            <tr>
                                <td><?php echo getStatusBadge($status); ?></td>
                                <td class="text-right"><strong><?php echo number_format($count); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No contacts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Deals by Stage -->
    <div class="dashboard-panel">
        <div class="panel-header">
            <h3>Deals by Stage</h3>
        </div>
        <div class="panel-body">
            <?php if (!empty($dealsByStage)): ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($dealsByStage as $stage => $count): ?>
                            <tr>
                                <td><?php echo getDealStageBadge($stage); ?></td>
                                <td class="text-right">
                                    <strong><?php echo number_format($count); ?></strong>
                                    <?php if (isset($dealValueByStage[$stage])): ?>
                                        <small class="text-muted">(<?php echo formatCurrency($dealValueByStage[$stage]); ?>)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No deals yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Contacts -->
    <div class="dashboard-panel">
        <div class="panel-header">
            <h3>Recent Contacts</h3>
            <a href="contacts.php" class="btn btn-sm">View All</a>
        </div>
        <div class="panel-body">
            <?php if (!empty($recentContacts)): ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($recentContacts as $contact): ?>
                            <tr>
                                <td>
                                    <a href="contact_view.php?id=<?php echo $contact['id']; ?>">
                                        <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>
                                    </a>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($contact['company_name'] ?? 'No company'); ?></small>
                                </td>
                                <td><?php echo getStatusBadge($contact['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No contacts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Activities -->
    <div class="dashboard-panel">
        <div class="panel-header">
            <h3>Upcoming Activities</h3>
            <a href="activities.php" class="btn btn-sm">View All</a>
        </div>
        <div class="panel-body">
            <?php if (!empty($upcomingActivities)): ?>
                <table class="table">
                    <tbody>
                        <?php foreach ($upcomingActivities as $activity): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($activity['subject']); ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php echo ucfirst($activity['type']); ?> -
                                        <?php echo formatDate($activity['scheduled_at'], DISPLAY_DATETIME_FORMAT); ?>
                                    </small>
                                </td>
                                <td><?php echo getStatusBadge($activity['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted">No upcoming activities.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
