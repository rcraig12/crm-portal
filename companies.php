<?php
/**
 * Companies List Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Company.php';

$pageTitle = 'Companies';

$database = new Database();
$companyModel = new Company($database);

// Get filters
$filters = [];
if (!empty($_GET['search'])) {
    $filters['search'] = sanitize($_GET['search']);
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalRecords = $companyModel->count($filters);
$pagination = paginate($totalRecords, $page);

// Get companies
$companies = $companyModel->getAll($filters, $pagination['records_per_page'], $pagination['offset']);

include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-actions">
        <a href="#" class="btn btn-primary" onclick="alert('Company form not yet implemented in this demo. Use contact form as reference.'); return false;">Add New Company</a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search companies..."
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="companies.php" class="btn btn-light">Clear</a>
        </div>
    </form>
</div>

<!-- Companies Table -->
<?php if (!empty($companies)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Industry</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($company['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($company['industry'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($company['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($company['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($company['city'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($company['country'] ?? '-'); ?></td>
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
        <p>No companies found.</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
