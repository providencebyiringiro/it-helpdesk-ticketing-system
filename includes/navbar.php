<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="px-4 py-3 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <button id="sidebarToggle" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="text-xl font-bold text-primary-700 dark:text-primary-400">IT Helpdesk</span>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Dark mode toggle -->
            <button id="darkModeToggle" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 focus:outline-none">
                <svg id="darkIcon" class="w-5 h-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
                <svg id="lightIcon" class="w-5 h-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>
            </button>

            <?php if (isLoggedIn()): ?>
                <div class="relative group">
                    <button class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-primary-600">
                        <div class="w-8 h-8 bg-primary-100 dark:bg-primary-800 rounded-full flex items-center justify-center">
                            <span class="text-sm font-semibold text-primary-700 dark:text-primary-200">
                                <?php echo strtoupper(substr(currentUser()['username'], 0, 1)); ?>
                            </span>
                        </div>
                        <span class="hidden sm:block"><?php echo h(currentUser()['username']); ?></span>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 hidden group-hover:block z-10">
                        <a href="settings.php" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="text-primary-600 hover:underline">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>