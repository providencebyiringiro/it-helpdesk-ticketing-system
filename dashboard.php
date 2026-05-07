<?php
$pageTitle = 'Dashboard';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user = currentUser();
require_once 'includes/header.php';

if (isAdmin()):
    // Admin stats
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalTickets = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
    $openTickets = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status IN ('Pending','In Progress')")->fetchColumn();
    $recentActivity = $pdo->query("
        SELECT a.*, u.username, t.subject as ticket_subject
        FROM ticket_activity_log a
        JOIN users u ON a.user_id = u.id
        JOIN tickets t ON a.ticket_id = t.id
        ORDER BY a.created_at DESC LIMIT 5
    ")->fetchAll();
?>
<div>
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-gray-500 dark:text-gray-400">Total Users</p>
            <p class="text-3xl font-bold"><?php echo $totalUsers; ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-gray-500 dark:text-gray-400">Total Tickets</p>
            <p class="text-3xl font-bold"><?php echo $totalTickets; ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-gray-500 dark:text-gray-400">Open Tickets</p>
            <p class="text-3xl font-bold text-yellow-500"><?php echo $openTickets; ?></p>
        </div>
    </div>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
        <ul class="divide-y dark:divide-gray-700">
            <?php foreach ($recentActivity as $act): ?>
                <li class="py-2 flex justify-between">
                    <span><?php echo h($act['username']); ?>: <?php echo h($act['action']); ?> on <strong><?php echo h($act['ticket_subject']); ?></strong></span>
                    <span class="text-sm text-gray-500"><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php else: // User dashboard
    $myTickets = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ?");
    $myTickets->execute([$user['id']]);
    $totalMy = $myTickets->fetchColumn();
    $openMy = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status IN ('Pending','In Progress')");
    $openMy->execute([$user['id']]);
    $openCount = $openMy->fetchColumn();
?>
<div>
    <h1 class="text-2xl font-bold mb-6">My Support Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-gray-500">My Tickets</p>
            <p class="text-3xl font-bold"><?php echo $totalMy; ?></p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <p class="text-gray-500">Open Tickets</p>
            <p class="text-3xl font-bold text-yellow-500"><?php echo $openCount; ?></p>
        </div>
    </div>
    <a href="create-ticket.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded">Create New Ticket</a>
</div>
<?php endif; ?>
<?php require_once 'includes/footer.php'; ?>