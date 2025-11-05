<?php
/**
 * Activities List Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Activity.php';

$pageTitle = 'Activities';

$database = new Database();
$activityModel = new Activity($database);

// Get filters
$filters = [];
if (!empty($_GET['type'])) {
    $filters['type'] = sanitize($_GET['type']);
}
if (!empty($_GET['status'])) {
    $filters['status'] = sanitize($_GET['status']);
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalRecords = $activityModel->count($filters);
$pagination = paginate($totalRecords, $page);

// Get activities
$activities = $activityModel->getAll($filters, $pagination['records_per_page'], $pagination['offset']);

include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Activity form not yet implemented in this demo. Use contact form as reference.'); return false;">Add New Activity</a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <option value="call" <?php echo ($_GET['type'] ?? '') === 'call' ? 'selected' : ''; ?>>Call</option>
                    <option value="meeting" <?php echo ($_GET['type'] ?? '') === 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                    <option value="email" <?php echo ($_GET['type'] ?? '') === 'email' ? 'selected' : ''; ?>>Email</option>
                    <option value="task" <?php echo ($_GET['type'] ?? '') === 'task' ? 'selected' : ''; ?>>Task</option>
                    <option value="note" <?php echo ($_GET['type'] ?? '') === 'note' ? 'selected' : ''; ?>>Note</option>
                </select>
            </div>
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="scheduled" <?php echo ($_GET['status'] ?? '') === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="completed" <?php echo ($_GET['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="activities.php" class="btn btn-light">Clear</a>
        </div>
    </form>
</div>

<!-- Activities Table -->
<?php if (!empty($activities)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>Contact</th>
                    <th>Company</th>
                    <th>Scheduled</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo ucfirst($activity['type']); ?></td>
                        <td><strong><?php echo htmlspecialchars($activity['subject']); ?></strong></td>
                        <td><?php echo htmlspecialchars($activity['contact_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($activity['company_name'] ?? '-'); ?></td>
                        <td><?php echo formatDate($activity['scheduled_at'], DISPLAY_DATETIME_FORMAT); ?></td>
                        <td><?php echo getStatusBadge($activity['status']); ?></td>
                        <td><?php echo htmlspecialchars($activity['assigned_to_name'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['has_previous']): ?>
                <a href="?page=<?php echo ($pagination['current_page'] - 1); ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"
                   class="btn btn-sm">Previous</a>
            <?php endif; ?>

            <span class="pagination-info">
                Page <?php echo $pagination['current_page']; ?> of <?php echo $pagination['total_pages']; ?>
                (<?php echo number_format($totalRecords); ?> total)
            </span>

            <?php if ($pagination['has_next']): ?>
                <a href="?page=<?php echo ($pagination['current_page'] + 1); ?><?php echo !empty($filters) ? '&' . http_build_query($filters) : ''; ?>"
                   class="btn btn-sm">Next</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="empty-state">
        <p>No activities found.</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
