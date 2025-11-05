<?php
/**
 * Contact Delete Handler
 */

session_start();

require_once 'config/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';
require_once 'models/Contact.php';

$database = new Database();
$contactModel = new Contact($database);

$contactId = (int)($_GET['id'] ?? 0);

if ($contactId > 0) {
    $contact = $contactModel->getById($contactId);

    if ($contact) {
        if ($contactModel->delete($contactId)) {
            setFlashMessage('success', 'Contact deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete contact.');
        }
    } else {
        setFlashMessage('error', 'Contact not found.');
    }
} else {
    setFlashMessage('error', 'Invalid contact ID.');
}

redirect('contacts.php');
