<?php
$pageTitle = 'Login';
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            setFlash('success', 'Welcome back, ' . $user['username'] . '!');
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
require_once 'includes/header.php';
?>

<div class="max-w-md mx-auto mt-10 bg-white dark:bg-gray-800 p-8 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6 text-center">IT Helpdesk Login</h2>
    <?php if ($error): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded"><?php echo h($error); ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <div class="mb-6">
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:border-gray-600" required>
        </div>
        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2 rounded">Login</button>
    </form>
    <p class="mt-4 text-center text-sm">Don't have an account? <a href="register.php" class="text-primary-600">Register</a></p>
</div>
<?php require_once 'includes/footer.php'; ?>