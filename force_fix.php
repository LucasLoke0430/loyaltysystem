<?php
// force_fix.php - One-click fixer for the database and settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>System Diagnostic & Fixer</h1>";

require_once 'db.php';

try {
    echo "<h2>1. Checking Database Connection...</h2>";
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p style='color:green;'>✅ Connection successful!</p>";

    echo "<h2>2. Fixing Database Character Set (Latin1 to UTF8MB4)...</h2>";
    $tablesToFix = ['users', 'settings', 'vouchers', 'rewards', 'receipts', 'activity_logs', 'wheel_prizes'];
    foreach ($tablesToFix as $table) {
        try {
            $pdo->exec("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "<p style='color:green;'>✅ Converted table `$table` to utf8mb4.</p>";
        } catch (Exception $e) {
            echo "<p style='color:orange;'>⚠️ Could not convert `$table`: " . $e->getMessage() . "</p>";
        }
    }

    echo "<h2>3. Resetting & Checking Settings Table...</h2>";
    // Always insert or update the default settings to ensure bottom_bar and other keys are correctly configured
    $defaultSettings = [
        'system_mode' => 'points',
        'logo_type' => 'text',
        'logo_text' => 'CASA & CO.',
        'logo_image_url' => '',
        'bottom_bar' => '[{"id":"loyalty","labelZh":"獎賞","labelEn":"Rewards","icon":"🎁","visible":true},{"id":"membership","labelZh":"會員","labelEn":"Membership","icon":"💳","visible":true},{"id":"draw","labelZh":"抽獎","labelEn":"Lucky Draw","icon":"🎡","visible":true},{"id":"vouchers","labelZh":"優惠券","labelEn":"Vouchers","icon":"🏷️","visible":true},{"id":"profile","labelZh":"個人","labelEn":"Profile","icon":"👤","visible":true}]'
    ];
    
    foreach ($defaultSettings as $key => $val) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        if ($stmt->fetchColumn() == 0) {
            $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $insert->execute([$key, $val]);
            echo "<p style='color:green;'>✅ Settings key `$key` created with default value.</p>";
        } else {
            // Force reset bottom_bar to make sure the 5 tabs are visible
            if ($key === 'bottom_bar') {
                $update = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $update->execute([$val, $key]);
                echo "<p style='color:green;'>✅ Settings key `$key` force-reset to default 5 tabs.</p>";
            } else {
                echo "<p style='color:green;'>✅ Settings key `$key` exists.</p>";
            }
        }
    }
    
    echo "<h2>4. Checking JSON Encoding...</h2>";
    $testData = ['message' => 'Hello ??? 測試'];
    $json = json_encode($testData, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($json === false) {
        echo "<p style='color:red;'>❌ JSON Encode Failed: " . json_last_error_msg() . "</p>";
    } else {
        echo "<p style='color:green;'>✅ JSON Encode is working properly.</p>";
    }

    echo "<h3>🎉 All checks passed! The system should now function normally.</h3>";
    echo "<p><a href='admin.php'>Go to Admin Panel</a> | <a href='index.php'>Go to Member Index</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ Error encountered:</h2>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>
