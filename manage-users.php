<?php
$pageTitle = 'Manage Users';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireAdmin();

// Delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = (int)$_POST['user_id'];
    if ($userId != currentUser()['id']) { // cannot delete self
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        setFlash('success', 'User deleted.');
    } else {
        setFlash('error', 'You cannot delete your own account.');
    }
    header('Location: manage-users.php');
    exit;
}

$users = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY role DESC, username ASC")->fetchAll();
require_once 'includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold">Manage Users</h1>
</div>
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role</th>
                <th class="px-4 py-3">Joined</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            <?php foreach ($users as $u): ?>
            <tr>
                <td class="px-4 py-2"><?php echo $u['id']; ?></td>
                <td class="px-4 py-2"><?php echo h($u['username']); ?></td>
                <td class="px-4 py-2"><?php echo h($u['email']); ?></td>
                <td class="px-4 py-2"><span class="px-2 py-0.5 rounded <?php echo $u['role']==='admin'?'bg-red-100 text-red-800':'bg-blue-100 text-blue-800'; ?>"><?php echo $u['role']; ?></span></td>
                <td class="px-4 py-2 text-sm"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                <td class="px-4 py-2">
                    <?php if ($u['id'] != currentUser()['id']): ?>
                    <form method="POST" onsubmit="return confirm('Delete user <?php echo h($u['username']); ?>?')">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="delete_user" class="text-red-600 hover:underline">Delete</button>
                    </form>
                    <?php else: ?>
                        <span class="text-gray-400">You</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once 'includes/footer.php'; ?>