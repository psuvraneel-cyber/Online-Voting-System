<?php
// test_permissions.php
echo "<h3>Testing File Permissions</h3>";

$projectRoot = realpath(dirname(__FILE__));
$dbDir = $projectRoot . '/database';
$databaseFile = $dbDir . '/voting_system.sqlite';

echo "<p>Project Root: " . $projectRoot . "</p>";
echo "<p>Database Directory: " . $dbDir . "</p>";
echo "<p>Database File: " . $databaseFile . "</p>";

// Check directory
if (!is_dir($dbDir)) {
    echo "<p style='color: orange;'>⚠️ Database directory doesn't exist. Creating...</p>";
    if (mkdir($dbDir, 0755, true)) {
        echo "<p style='color: green;'>✅ Database directory created successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create database directory!</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Database directory exists</p>";
}

// Check if directory is writable
if (is_writable($dbDir)) {
    echo "<p style='color: green;'>✅ Database directory is writable</p>";
} else {
    echo "<p style='color: red;'>❌ Database directory is NOT writable</p>";
    echo "<p>Try running: <code>chmod 755 " . $dbDir . "</code></p>";
}

// Test creating database file
try {
    $pdo = new PDO('sqlite:' . $databaseFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE TABLE IF NOT EXISTS test (id INTEGER)');
    echo "<p style='color: green;'>✅ Database file created and accessible!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr><h4>Next Steps:</h4>";
echo "<p>If you see errors above, try these solutions:</p>";
echo "<ol>
<li>Manually create a folder called 'database' in your project</li>
<li>Check folder permissions (should be 755 or 777)</li>
<li>Make sure XAMPP has write permissions to the folder</li>
</ol>";
?>