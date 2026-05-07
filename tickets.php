<?php
$pageTitle = 'Tickets';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();
$user = currentUser();
$isAdmin = isAdmin();

// Pagination & filters
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';

$where = [];
$params = [];
if (!$isAdmin) {
    $where[] = "t.user_id = ?";
    $params[] = $user['id'];
}
if ($search) {
    $where[] = "(t.id LIKE ? OR t.subject LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter) {
    $where[] = "t.status = ?";
    $params[] = $statusFilter;
}
if ($categoryFilter) {
    $where[] = "t.category = ?";
    $params[] = $categoryFilter;
}
if ($priorityFilter) {
    $where[] = "t.priority = ?";
    $params[] = $priorityFilter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets t $whereSQL");
$countStmt->execute($params);
$totalTickets = $countStmt->fetchColumn();
$totalPages = ceil($totalTickets / $perPage);

$offset = ($page - 1) * $perPage;
$tickets = $pdo->prepare("SELECT t.*, u.username FROM tickets t JOIN users u ON t.user_id = u.id $whereSQL ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset");
$tickets->execute($params);
$tickets = $tickets->fetchAll();

require_once 'includes/header.php';
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <h1 class="text-2xl font-bold"><?php echo $isAdmin ? 'All Tickets' : 'My Tickets'; ?></h1>
    <a href="create-ticket.php" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded">New Ticket</a>
</div>

<!-- Filters -->
<form method="GET" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-end">
    <div>
        <label class="block text-sm">Search</label>
        <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Ticket ID or subject" class="border rounded px-3 py-1 dark:bg-gray-700 dark:border-gray-600">
    </div>
    <div>
        <label class="block text-sm">Status</label>
        <select name="status" class="border rounded px-3 py-1 dark:bg-gray-700 dark:border-gray-600">
            <option value="">All</option>
            <option value="Pending" <?php echo $statusFilter==='Pending'?'selected':''; ?>>Pending</option>
            <option value="In Progress" <?php echo $statusFilter==='In Progress'?'selected':''; ?>>In Progress</option>
            <option value="Resolved" <?php echo $statusFilter==='Resolved'?'selected':''; ?>>Resolved</option>
            <option value="Closed" <?php echo $statusFilter==='Closed'?'selected':''; ?>>Closed</option>
        </select>
    </div>
    <div>
        <label class="block text-sm">Category</label>
        <select name="category" class="border rounded px-3 py-1 dark:bg-gray-700 dark:border-gray-600">
            <option value="">All</option>
            <?php $cats = ['Network','Printer','Hardware','Software','Power','Account Access','Other'];
            foreach ($cats as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $categoryFilter===$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label class="block text-sm">Priority</label>
        <select name="priority" class="border rounded px-3 py-1 dark:bg-gray-700 dark:border-gray-600">
            <option value="">All</option>
            <option value="Low" <?php echo $priorityFilter==='Low'?'selected':''; ?>>Low</option>
            <option value="Medium" <?php echo $priorityFilter==='Medium'?'selected':''; ?>>Medium</option>
            <option value="High" <?php echo $priorityFilter==='High'?'selected':''; ?>>High</option>
            <option value="Critical" <?php echo $priorityFilter==='Critical'?'selected':''; ?>>Critical</option>
        </select>
    </div>
    <button type="submit" class="bg-primary-600 text-white px-4 py-1 rounded">Filter</button>
</form>

<!-- Tickets table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Subject</th>
                <?php if ($isAdmin): ?><th class="px-4 py-3">User</th><?php endif; ?>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Priority</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y dark:divide-gray-700">
            <?php foreach ($tickets as $ticket): ?>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                <td class="px-4 py-2">#<?php echo $ticket['id']; ?></td>
                <td class="px-4 py-2 font-medium"><?php echo h($ticket['subject']); ?></td>
                <?php if ($isAdmin): ?><td class="px-4 py-2"><?php echo h($ticket['username']); ?></td><?php endif; ?>
                <td class="px-4 py-2"><?php echo h($ticket['category']); ?></td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded text-xs <?php 
                        echo $ticket['priority'] === 'Critical' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                            ($ticket['priority'] === 'High' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'); ?>">
                        <?php echo h($ticket['priority']); ?>
                    </span>
                </td>
                <td class="px-4 py-2">
                    <span class="px-2 py-1 rounded text-xs <?php 
                        echo $ticket['status'] === 'Resolved' ? 'bg-green-100 text-green-800' : 
                            ($ticket['status'] === 'Closed' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-800'); ?>">
                        <?php echo h($ticket['status']); ?>
                    </span>
                </td>
                <td class="px-4 py-2 text-sm"><?php echo date('M d, Y', strtotime($ticket['created_at'])); ?></td>
                <td class="px-4 py-2">
                    <a href="ticket-details.php?id=<?php echo $ticket['id']; ?>" class="text-primary-600 hover:underline">View</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($tickets)): ?>
                <tr><td colspan="8" class="px-4 py-4 text-center text-gray-500">No tickets found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="mt-4 flex justify-center space-x-2">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_filter(['search'=>$search,'status'=>$statusFilter,'category'=>$categoryFilter,'priority'=>$priorityFilter])); ?>" 
           class="px-3 py-1 rounded <?php echo $i==$page ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 border'; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>