<?php
$pageTitle = 'Ticket Details';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user = currentUser();
$isAdmin = isAdmin();

$ticketId = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();
if (!$ticket) { setFlash('error', 'Ticket not found.'); header('Location: tickets.php'); exit; }
// Permission: only ticket owner or admin can view
if (!$isAdmin && $ticket['user_id'] != $user['id']) {
    setFlash('error', 'Access denied.');
    header('Location: tickets.php');
    exit;
}

// Handle admin status change
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'status_change') {
    $newStatus = $_POST['new_status'];
    $allowedStatuses = ['Pending', 'In Progress', 'Resolved', 'Closed'];
    if (in_array($newStatus, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $ticketId]);
        addActivity($ticketId, $user['id'], 'status_changed', "Changed status to $newStatus");
        setFlash('success', 'Status updated.');
        header("Location: ticket-details.php?id=$ticketId");
        exit;
    }
}

// Handle delete ticket (admin only)
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    // Optionally delete attachment file
    if ($ticket['attachment'] && file_exists('uploads/' . $ticket['attachment'])) {
        unlink('uploads/' . $ticket['attachment']);
    }
    $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->execute([$ticketId]);
    setFlash('success', 'Ticket deleted.');
    header('Location: tickets.php');
    exit;
}

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message']) && !empty(trim($_POST['reply_message']))) {
    $message = trim($_POST['reply_message']);
    $stmt = $pdo->prepare("INSERT INTO ticket_replies (ticket_id, user_id, message, is_admin) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ticketId, $user['id'], $message, $isAdmin ? 1 : 0]);
    addActivity($ticketId, $user['id'], 'reply_added', 'Reply added');
    // If admin replies, optionally change status to "In Progress"
    if ($isAdmin && $ticket['status'] === 'Pending') {
        $pdo->prepare("UPDATE tickets SET status = 'In Progress' WHERE id = ?")->execute([$ticketId]);
    }
    setFlash('success', 'Reply sent.');
    header("Location: ticket-details.php?id=$ticketId");
    exit;
}

// Fetch replies
$replies = $pdo->prepare("SELECT r.*, u.username, u.role FROM ticket_replies r JOIN users u ON r.user_id = u.id WHERE r.ticket_id = ? ORDER BY r.created_at ASC");
$replies->execute([$ticketId]);
$replies = $replies->fetchAll();

// Fetch activity log
$activities = $pdo->prepare("SELECT a.*, u.username FROM ticket_activity_log a JOIN users u ON a.user_id = u.id WHERE a.ticket_id = ? ORDER BY a.created_at DESC LIMIT 20");
$activities->execute([$ticketId]);
$activities = $activities->fetchAll();

require_once 'includes/header.php';
?>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold">Ticket #<?php echo $ticket['id']; ?>: <?php echo h($ticket['subject']); ?></h1>
            <p class="text-sm text-gray-500 mt-1">Created by <?php echo h($ticket['username']); ?> on <?php echo date('F j, Y g:i A', strtotime($ticket['created_at'])); ?></p>
        </div>
        <?php if ($isAdmin): ?>
        <div class="flex items-center gap-2">
            <form method="POST" class="inline">
                <input type="hidden" name="action" value="status_change">
                <select name="new_status" class="border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600" onchange="this.form.submit()">
                    <?php foreach (['Pending','In Progress','Resolved','Closed'] as $st): ?>
                        <option value="<?php echo $st; ?>" <?php echo $ticket['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <form method="POST" onsubmit="return confirm('Delete this ticket?')">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div><span class="text-gray-500">Category:</span> <?php echo h($ticket['category']); ?></div>
        <div><span class="text-gray-500">Priority:</span> <?php echo h($ticket['priority']); ?></div>
        <div><span class="text-gray-500">Status:</span> 
            <span class="px-2 py-0.5 rounded <?php echo $ticket['status'] === 'Resolved' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800'; ?>"><?php echo h($ticket['status']); ?></span>
        </div>
        <div><span class="text-gray-500">Last updated:</span> <?php echo date('M d, H:i', strtotime($ticket['updated_at'])); ?></div>
    </div>
    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded">
        <p class="whitespace-pre-wrap"><?php echo nl2br(h($ticket['description'])); ?></p>
        <?php if ($ticket['attachment']): ?>
            <div class="mt-4">
                <a href="uploads/<?php echo h($ticket['attachment']); ?>" target="_blank" class="inline-flex items-center space-x-2 bg-white dark:bg-gray-600 p-2 rounded shadow">
                    <span>📎 Attachment</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Replies -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">Replies (<?php echo count($replies); ?>)</h2>
    <?php foreach ($replies as $reply): ?>
        <div class="mb-4 pb-4 border-b dark:border-gray-700 last:border-0">
            <div class="flex items-center space-x-2 mb-1">
                <span class="font-semibold"><?php echo h($reply['username']); ?></span>
                <?php if ($reply['is_admin']): ?><span class="text-xs bg-red-100 text-red-800 px-1 rounded">Staff</span><?php endif; ?>
                <span class="text-xs text-gray-500"><?php echo date('M d, H:i', strtotime($reply['created_at'])); ?></span>
            </div>
            <p class="whitespace-pre-wrap"><?php echo nl2br(h($reply['message'])); ?></p>
        </div>
    <?php endforeach; ?>
    <form method="POST" class="mt-4">
        <label class="block mb-1 font-medium">Add Reply</label>
        <textarea name="reply_message" rows="3" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required></textarea>
        <button type="submit" class="mt-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded">Send Reply</button>
    </form>
</div>

<!-- Activity Log -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-4">Activity Log</h2>
    <ul class="space-y-2">
        <?php foreach ($activities as $act): ?>
            <li class="text-sm flex justify-between">
                <span><?php echo h($act['username']); ?> <?php echo h($act['action']); ?> - <?php echo h($act['details']); ?></span>
                <span class="text-gray-500"><?php echo date('M d, H:i', strtotime($act['created_at'])); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php require_once 'includes/footer.php'; ?>