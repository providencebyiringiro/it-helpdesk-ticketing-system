<?php
$pageTitle = 'New Ticket';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $priority = $_POST['priority'] ?? '';
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    if (empty($category) || !in_array($category, ['Network','Printer','Hardware','Software','Power','Account Access','Other']))
        $errors[] = 'Please select a valid category.';
    if (empty($priority) || !in_array($priority, ['Low','Medium','High','Critical']))
        $errors[] = 'Please select a valid priority.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($description)) $errors[] = 'Description is required.';

    // File upload handling
    $attachmentName = null;
    if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Only JPG, PNG, GIF files are allowed.';
        } elseif ($_FILES['attachment']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'File size must be under 2MB.';
        } else {
            $newName = randomString(15) . '.' . $ext;
            $dest = __DIR__ . '/uploads/' . $newName;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $dest)) {
                $attachmentName = $newName;
            } else {
                $errors[] = 'Failed to upload file.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, category, priority, subject, description, attachment) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $category, $priority, $subject, $description, $attachmentName]);
        $ticketId = $pdo->lastInsertId();
        addActivity($ticketId, $user['id'], 'ticket_created', "Ticket #$ticketId created");
        setFlash('success', 'Ticket created successfully.');
        header("Location: ticket-details.php?id=$ticketId");
        exit;
    }
}
require_once 'includes/header.php';
?>

<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Create New Support Ticket</h1>
    <?php if ($errors): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
            <ul class="list-disc pl-5"><?php foreach ($errors as $e) echo "<li>" . h($e) . "</li>"; ?></ul>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="">Select...</option>
                    <?php foreach (['Network','Printer','Hardware','Software','Power','Account Access','Other'] as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Critical">Critical</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Subject</label>
            <input type="text" name="subject" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Description</label>
            <textarea name="description" rows="5" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required></textarea>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Screenshot/Image (max 2MB, JPG/PNG/GIF)</label>
            <div class="file-upload-wrapper inline-block">
                <input type="file" name="attachment" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"/>
            </div>
        </div>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded" onclick="setLoading(this)">Submit Ticket</button>
    </form>
</div>
<?php require_once 'includes/footer.php'; ?>