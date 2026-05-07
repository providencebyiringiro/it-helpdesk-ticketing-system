<?php
// Sanitize output
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Generate a random string for filenames
function randomString($length = 10) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Add ticket activity log
function addActivity($ticket_id, $user_id, $action, $details = '') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO ticket_activity_log (ticket_id, user_id, action, details) VALUES (?, ?, ?, ?)");
    $stmt->execute([$ticket_id, $user_id, $action, $details]);
}

// Flash message helper using session
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>