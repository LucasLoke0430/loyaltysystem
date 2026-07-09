<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>API Test</h1>";

require_once 'db.php';

echo "<h2>Fetching Admin Data...</h2>";
try {
    $totalMembers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "<p>Total Members: $totalMembers</p>";

    $settingsRaw = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    echo "<p>Settings fetched successfully.</p>";

    $users = $pdo->query("SELECT id, name, username, points, stamps, joined_date, qr_code FROM users ORDER BY id DESC")->fetchAll();
    echo "<p>Users fetched successfully.</p>";

    $vouchers = $pdo->query("SELECT v.*, u.name as member_name FROM vouchers v JOIN users u ON v.user_id = u.id ORDER BY v.id DESC")->fetchAll();
    echo "<p>Vouchers fetched successfully.</p>";

    $rewards = $pdo->query("SELECT * FROM rewards ORDER BY cost ASC")->fetchAll();
    echo "<p>Rewards fetched successfully.</p>";

    $wheel_prizes = $pdo->query("SELECT * FROM wheel_prizes ORDER BY id ASC")->fetchAll();
    echo "<p>Wheel Prizes fetched successfully.</p>";

    $logs = $pdo->query("SELECT * FROM activity_logs ORDER BY id DESC LIMIT 20")->fetchAll();
    echo "<p>Activity logs fetched successfully.</p>";

    $receipts = $pdo->query("SELECT r.*, u.name as member_name FROM receipts r JOIN users u ON r.user_id = u.id ORDER BY r.id DESC")->fetchAll();
    echo "<p>Receipts fetched successfully.</p>";

    $data = [
        'success' => true,
        'stats' => ['totalMembers' => $totalMembers],
        'members' => $users,
        'logs' => $logs
    ];

    echo "<h2>Testing JSON Encode...</h2>";
    $json = json_encode($data);
    if ($json === false) {
        echo "<p style='color:red;'>JSON Encode Failed: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color:green;'>JSON Encode Successful!</p>";
        echo "<textarea style='width:100%; height:200px;'>" . htmlspecialchars($json) . "</textarea>";
    }

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?>
