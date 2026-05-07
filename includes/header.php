<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en" class="<?php echo isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true' ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Helpdesk - <?php echo $pageTitle ?? 'Support'; ?></title>
    <!-- Tailwind CSS CDN with dark mode class strategy -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { "50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#1e3a8a" }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/custom.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    <div class="flex min-h-screen">
        <?php if (isLoggedIn()) require_once __DIR__ . '/sidebar.php'; ?>
        <main class="flex-1 p-4 md:p-6 lg:p-8 ml-0 lg:ml-64 transition-all duration-300">
        <?php
        // Display flash message if any
        $flash = getFlash();
        if ($flash): ?>
            <div id="flashMessage" class="mb-4 p-4 rounded-lg <?php echo $flash['type'] === 'success' ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100' : 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100'; ?>">
                <?php echo h($flash['message']); ?>
            </div>
            <script>
                setTimeout(() => document.getElementById('flashMessage')?.remove(), 5000);
            </script>
        <?php endif; ?>