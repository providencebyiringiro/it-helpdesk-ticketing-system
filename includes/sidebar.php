<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 z-20 pt-16">
    <div class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="dashboard.php" class="flex items-center p-2 rounded-lg <?php echo $currentPage == 'dashboard.php' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                    <span>📊</span> <span class="ml-3">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="tickets.php" class="flex items-center p-2 rounded-lg <?php echo $currentPage == 'tickets.php' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                    <span>🎫</span> <span class="ml-3">Tickets</span>
                </a>
            </li>
            <li>
                <a href="create-ticket.php" class="flex items-center p-2 rounded-lg <?php echo $currentPage == 'create-ticket.php' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                    <span>➕</span> <span class="ml-3">New Ticket</span>
                </a>
            </li>
            <?php if (isAdmin()): ?>
                <li class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
                    <span class="px-2 text-xs font-semibold text-gray-500 uppercase">Admin</span>
                </li>
                <li>
                    <a href="manage-users.php" class="flex items-center p-2 rounded-lg <?php echo $currentPage == 'manage-users.php' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                        <span>👥</span> <span class="ml-3">Manage Users</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="settings.php" class="flex items-center p-2 rounded-lg <?php echo $currentPage == 'settings.php' ? 'bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-200' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                    <span>⚙️</span> <span class="ml-3">Settings</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
<!-- Overlay for mobile sidebar -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden lg:hidden z-10" onclick="document.getElementById('sidebar').classList.add('-translate-x-full');this.classList.add('hidden');"></div>