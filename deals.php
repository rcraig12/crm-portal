<?php
/**
 * Deals List Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Deal.php';

$pageTitle = 'Deals';

$database = new Database();
$dealModel = new Deal($database);

// Get filters
$filters = [];
if (!empty($_GET['stage'])) {
    $filters['stage'] = sanitize($_GET['stage']);
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalRecords = $dealModel->count($filters);
$pagination = paginate($totalRecords, $page);

// Get deals
$deals = $dealModel->getAll($filters, $pagination['records_per_page'], $pagination['offset']);

include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Deal form not yet implemented in this demo. Use contact form as reference.'); return false;">Add New Deal</a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <select name="stage" class="form-control">
                    <option value="">All Stages</option>
                    <option value="qualification" <?php echo ($_GET['stage'] ?? '') === 'qualification' ? 'selected' : ''; ?>>Qualification</option>
                    <option value="proposal" <?php echo ($_GET['stage'] ?? '') === 'proposal' ? 'selected' : ''; ?>>Proposal</option>
                    <option value="negotiation" <?php echo ($_GET['stage'] ?? '') === 'negotiation' ? 'selected' : ''; ?>>Negotiation</option>
                    <option value="closed_won" <?php echo ($_GET['stage'] ?? '') === 'closed_won' ? 'selected' : ''; ?>>Closed Won</option>
                    <option value="closed_lost" <?php echo ($_GET['stage'] ?? '') === 'closed_lost' ? 'selected' : ''; ?>>Closed Lost</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="deals.php" class="btn btn-light">Clear</a>
        </div>
    </form>
</div>

<!-- Deals Table -->
<?php if (!empty($deals)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Contact</th>
                    <th>Company</th>
                    <th>Value</th>
                    <th>Probability</th>
                    <th>Stage</th>
                    <th>Expected Close</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deals as $deal): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($deal['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($deal['contact_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($deal['company_name'] ?? '-'); ?></td>
                        <td><?php echo formatCurrency($deal['value']); ?></td>
                        <td><?php echo $deal['probability']; ?>%</td>
                        <td><?php echo getDealStageBadge($deal['stage']); ?></td>
                        <td><?php echo formatDate($deal['expected_close_date']); ?></td>
                        <td class="actions">
                            <a href="#" class="btn btn-sm btn-info" onclick="alert('View functionality not yet implemented.'); return false;">View</a>
                        </td>
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
        <p>No deals found.</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
