<?php
/**
 * Contact Add/Edit Form
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Contact.php';
require_once 'models/Company.php';
require_once 'models/User.php';

$database = new Database();
$contactModel = new Contact($database);
$companyModel = new Company($database);
$userModel = new User($database);

$isEdit = false;
$contact = null;
$errors = [];

// Check if editing
if (isset($_GET['id'])) {
    $isEdit = true;
    $contactId = (int)$_GET['id'];
    $contact = $contactModel->getById($contactId);

    if (!$contact) {
        setFlashMessage('error', 'Contact not found.');
        redirect('contacts.php');
    }
}

$pageTitle = $isEdit ? 'Edit Contact' : 'Add New Contact';

// Get companies and users for dropdowns
$companies = $companyModel->getForSelect();
$users = $userModel->getAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'first_name' => sanitize($_POST['first_name'] ?? ''),
        'last_name' => sanitize($_POST['last_name'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'mobile' => sanitize($_POST['mobile'] ?? ''),
        'position' => sanitize($_POST['position'] ?? ''),
        'company_id' => !empty($_POST['company_id']) ? (int)$_POST['company_id'] : null,
        'address' => sanitize($_POST['address'] ?? ''),
        'city' => sanitize($_POST['city'] ?? ''),
        'state' => sanitize($_POST['state'] ?? ''),
        'country' => sanitize($_POST['country'] ?? ''),
        'postal_code' => sanitize($_POST['postal_code'] ?? ''),
        'status' => sanitize($_POST['status'] ?? 'lead'),
        'source' => sanitize($_POST['source'] ?? ''),
        'notes' => sanitize($_POST['notes'] ?? ''),
        'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : getCurrentUserId(),
    ];

    // Validation
    if (empty($data['first_name'])) {
        $errors[] = 'First name is required.';
    }
    if (empty($data['last_name'])) {
        $errors[] = 'Last name is required.';
    }

    if (empty($errors)) {
        if ($isEdit) {
            // Update contact
            if ($contactModel->update($contactId, $data)) {
                setFlashMessage('success', 'Contact updated successfully.');
                redirect('contact_view.php?id=' . $contactId);
            } else {
                $errors[] = 'Failed to update contact.';
            }
        } else {
            // Create new contact
            $data['created_by'] = getCurrentUserId();
            $newId = $contactModel->create($data);

            if ($newId) {
                setFlashMessage('success', 'Contact created successfully.');
                redirect('contact_view.php?id=' . $newId);
            } else {
                $errors[] = 'Failed to create contact.';
            }
        }
    }
} else {
    // Populate data for editing
    if ($isEdit) {
        $data = $contact;
    } else {
        $data = [];
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <a href="contacts.php" class="btn btn-secondary">Back to Contacts</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" class="form">
    <div class="form-section">
        <h3>Basic Information</h3>

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" class="form-control"
                       value="<?php echo htmlspecialchars($data['first_name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" class="form-control"
                       value="<?php echo htmlspecialchars($data['last_name'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control"
                       value="<?php echo htmlspecialchars($data['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="mobile">Mobile</label>
                <input type="text" id="mobile" name="mobile" class="form-control"
                       value="<?php echo htmlspecialchars($data['mobile'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="company_id">Company</label>
                <select id="company_id" name="company_id" class="form-control">
                    <option value="">-- Select Company --</option>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo $company['id']; ?>"
                            <?php echo ($data['company_id'] ?? '') == $company['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($company['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" class="form-control"
                       value="<?php echo htmlspecialchars($data['position'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="lead" <?php echo ($data['status'] ?? 'lead') === 'lead' ? 'selected' : ''; ?>>Lead</option>
                    <option value="prospect" <?php echo ($data['status'] ?? '') === 'prospect' ? 'selected' : ''; ?>>Prospect</option>
                    <option value="customer" <?php echo ($data['status'] ?? '') === 'customer' ? 'selected' : ''; ?>>Customer</option>
                    <option value="inactive" <?php echo ($data['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="source">Source</label>
                <input type="text" id="source" name="source" class="form-control"
                       value="<?php echo htmlspecialchars($data['source'] ?? ''); ?>"
                       placeholder="e.g., Website, Referral, Cold Call">
            </div>

            <div class="form-group">
                <label for="assigned_to">Assigned To</label>
                <select id="assigned_to" name="assigned_to" class="form-control">
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"
                            <?php echo ($data['assigned_to'] ?? getCurrentUserId()) == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Address Information</h3>

        <div class="form-group">
            <label for="address">Street Address</label>
            <input type="text" id="address" name="address" class="form-control"
                   value="<?php echo htmlspecialchars($data['address'] ?? ''); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control"
                       value="<?php echo htmlspecialchars($data['city'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="state">State/Province</label>
                <input type="text" id="state" name="state" class="form-control"
                       value="<?php echo htmlspecialchars($data['state'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" class="form-control"
                       value="<?php echo htmlspecialchars($data['country'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" class="form-control"
                       value="<?php echo htmlspecialchars($data['postal_code'] ?? ''); ?>">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>Additional Notes</h3>

        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" class="form-control" rows="4"><?php echo htmlspecialchars($data['notes'] ?? ''); ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?php echo $isEdit ? 'Update Contact' : 'Create Contact'; ?>
        </button>
        <a href="contacts.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php include 'includes/footer.php'; ?>
