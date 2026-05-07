<?php
$pageTitle = 'Register';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $hashed]);
            setFlash('success', 'Registration successful. Please login.');
            header('Location: login.php');
            exit;
        }
    }
}
require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">Create Account</h2>
    <?php if ($error): ?> <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><?php echo h($error); ?></div> <?php endif; ?>
    <form method="POST">
        <div class="mb-4">
            <label class="block mb-1">Username</label>
            <input type="text" name="username" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <div class="mb-6">
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2 rounded">Register</button>
    </form>
    <p class="mt-4 text-center text-sm">Already registered? <a href="login.php" class="text-primary-600">Login</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>