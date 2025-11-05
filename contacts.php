<?php
/**
 * Contacts List Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Contact.php';

$pageTitle = 'Contacts';

// Initialize database and model
$database = new Database();
$contactModel = new Contact($database);

// Get filters
$filters = [];
if (!empty($_GET['status'])) {
    $filters['status'] = sanitize($_GET['status']);
}
if (!empty($_GET['search'])) {
    $filters['search'] = sanitize($_GET['search']);
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalRecords = $contactModel->count($filters);
$pagination = paginate($totalRecords, $page);

// Get contacts
$contacts = $contactModel->getAll($filters, $pagination['records_per_page'], $pagination['offset']);

include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-actions">
        <a href="contact_form.php" class="btn btn-primary">Add New Contact</a>
    </div>
</div>

<!-- Filters -->
<div class="filters">
    <form method="GET" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <input type="text" name="search" class="form-control"
                       placeholder="Search contacts..."
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="lead" <?php echo ($_GET['status'] ?? '') === 'lead' ? 'selected' : ''; ?>>Lead</option>
                    <option value="prospect" <?php echo ($_GET['status'] ?? '') === 'prospect' ? 'selected' : ''; ?>>Prospect</option>
                    <option value="customer" <?php echo ($_GET['status'] ?? '') === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="contacts.php" class="btn btn-light">Clear</a>
        </div>
    </form>
</div>

<!-- Contacts Table -->
<?php if (!empty($contacts)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Company</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td>
                            <a href="contact_view.php?id=<?php echo $contact['id']; ?>">
                                <?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($contact['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($contact['phone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($contact['company_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($contact['position'] ?? '-'); ?></td>
                        <td><?php echo getStatusBadge($contact['status']); ?></td>
                        <td class="actions">
                            <a href="contact_view.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-info">View</a>
                            <a href="contact_form.php?id=<?php echo $contact['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="contact_delete.php?id=<?php echo $contact['id']; ?>"
                               class="btn btn-sm btn-danger delete-btn">Delete</a>
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
        <p>No contacts found.</p>
        <a href="contact_form.php" class="btn btn-primary">Add Your First Contact</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
