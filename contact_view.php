<?php
/**
 * Contact View Page
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Contact.php';
require_once 'models/Activity.php';

$pageTitle = 'View Contact';

$database = new Database();
$contactModel = new Contact($database);
$activityModel = new Activity($database);

$contactId = (int)($_GET['id'] ?? 0);
$contact = $contactModel->getById($contactId);

if (!$contact) {
    setFlashMessage('error', 'Contact not found.');
    redirect('contacts.php');
}

// Get contact activities
$activities = $activityModel->getAll(['contact_id' => $contactId], 10);

include 'includes/header.php';
?>

<div class="page-header">
    <div class="page-actions">
        <a href="contacts.php" class="btn btn-secondary">Back to Contacts</a>
        <a href="contact_form.php?id=<?php echo $contact['id']; ?>" class="btn btn-warning">Edit</a>
        <a href="contact_delete.php?id=<?php echo $contact['id']; ?>" class="btn btn-danger delete-btn">Delete</a>
    </div>
</div>

<div class="view-container">
    <div class="view-header">
        <h2><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></h2>
        <?php echo getStatusBadge($contact['status']); ?>
    </div>

    <div class="view-grid">
        <div class="view-section">
            <h3>Contact Information</h3>
            <dl class="detail-list">
                <dt>Email:</dt>
                <dd><?php echo $contact['email'] ? htmlspecialchars($contact['email']) : '-'; ?></dd>

                <dt>Phone:</dt>
                <dd><?php echo $contact['phone'] ? htmlspecialchars($contact['phone']) : '-'; ?></dd>

                <dt>Mobile:</dt>
                <dd><?php echo $contact['mobile'] ? htmlspecialchars($contact['mobile']) : '-'; ?></dd>

                <dt>Company:</dt>
                <dd><?php echo $contact['company_name'] ? htmlspecialchars($contact['company_name']) : '-'; ?></dd>

                <dt>Position:</dt>
                <dd><?php echo $contact['position'] ? htmlspecialchars($contact['position']) : '-'; ?></dd>

                <dt>Source:</dt>
                <dd><?php echo $contact['source'] ? htmlspecialchars($contact['source']) : '-'; ?></dd>
            </dl>
        </div>

        <div class="view-section">
            <h3>Address</h3>
            <dl class="detail-list">
                <dt>Street:</dt>
                <dd><?php echo $contact['address'] ? htmlspecialchars($contact['address']) : '-'; ?></dd>

                <dt>City:</dt>
                <dd><?php echo $contact['city'] ? htmlspecialchars($contact['city']) : '-'; ?></dd>

                <dt>State:</dt>
                <dd><?php echo $contact['state'] ? htmlspecialchars($contact['state']) : '-'; ?></dd>

                <dt>Country:</dt>
                <dd><?php echo $contact['country'] ? htmlspecialchars($contact['country']) : '-'; ?></dd>

                <dt>Postal Code:</dt>
                <dd><?php echo $contact['postal_code'] ? htmlspecialchars($contact['postal_code']) : '-'; ?></dd>
            </dl>
        </div>

        <div class="view-section">
            <h3>Additional Information</h3>
            <dl class="detail-list">
                <dt>Assigned To:</dt>
                <dd><?php echo $contact['assigned_to_name'] ? htmlspecialchars($contact['assigned_to_name']) : '-'; ?></dd>

                <dt>Created By:</dt>
                <dd><?php echo $contact['created_by_name'] ? htmlspecialchars($contact['created_by_name']) : '-'; ?></dd>

                <dt>Created At:</dt>
                <dd><?php echo formatDate($contact['created_at'], DISPLAY_DATETIME_FORMAT); ?></dd>

                <dt>Last Updated:</dt>
                <dd><?php echo formatDate($contact['updated_at'], DISPLAY_DATETIME_FORMAT); ?></dd>
            </dl>
        </div>

        <?php if ($contact['notes']): ?>
            <div class="view-section full-width">
                <h3>Notes</h3>
                <p><?php echo nl2br(htmlspecialchars($contact['notes'])); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activities -->
    <?php if (!empty($activities)): ?>
        <div class="view-section full-width">
            <h3>Recent Activities</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Scheduled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?php echo ucfirst($activity['type']); ?></td>
                            <td><?php echo htmlspecialchars($activity['subject']); ?></td>
                            <td><?php echo formatDate($activity['scheduled_at'], DISPLAY_DATETIME_FORMAT); ?></td>
                            <td><?php echo getStatusBadge($activity['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
