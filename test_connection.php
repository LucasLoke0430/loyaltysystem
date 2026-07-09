<?php
// test_connection.php - Helper script to debug database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

require_once 'db.php';

if (isset($pdo)) {
    echo "<h2 style='color:green;'>✅ Connection Successful!</h2>";
    echo "<p>Connected to database: <strong>" . htmlspecialchars($db_name) . "</strong></p>";
    
    // Test if tables exist
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tables found:</h3><ul>";
    foreach ($tables as $t) {
        echo "<li>$t</li>";
    }
    echo "</ul>";
    
    if (empty($tables)) {
        echo "<p style='color:orange;'>⚠️ No tables found. Did you import the SQL file?</p>";
    }
} else {
    echo "<h2 style='color:red;'>❌ Connection Failed</h2>";
    echo "<p>Please check your db.php file.</p>";
}
?>
