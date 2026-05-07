<?php
$pageTitle = 'Settings';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user = currentUser();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($new) < 6) {
            $errors[] = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $errors[] = 'Passwords do not match.';
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user['id']]);
            $success = 'Password updated successfully.';
        }
    }
}
require_once 'includes/header.php';
?>

<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Account Settings</h1>
    <?php if ($errors): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><ul class="list-disc pl-5"><?php foreach ($errors as $e) echo "<li>" . h($e) . "</li>"; ?></ul></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded"><?php echo h($success); ?></div>
    <?php endif; ?>

    <div class="mb-6">
        <h2 class="text-lg font-semibold">Profile</h2>
        <p><strong>Username:</strong> <?php echo h($user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo h($user['email']); ?></p>
        <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
    </div>
    <div>
        <h2 class="text-lg font-semibold mb-4">Change Password</h2>
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-1">Current Password</label>
                <input type="password" name="current_password" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div class="mb-4">
                <label class="block mb-1">New Password</label>
                <input type="password" name="new_password" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <div class="mb-6">
                <label class="block mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
            </div>
            <button type="submit" name="change_password" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded">Update Password</button>
        </form>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>