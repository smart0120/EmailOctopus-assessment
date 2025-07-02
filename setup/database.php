<?php

require_once __DIR__ . '/../src/Database/Database.php';

use App\Database\Database;

try {
    // Create database directory if it doesn't exist
    $dbDir = __DIR__ . '/../database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    // Get database connection
    $pdo = Database::getConnection();
    
    // Create contacts table
    $sql = "
        CREATE TABLE IF NOT EXISTS contact (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email_address VARCHAR(254) NOT NULL UNIQUE,
            name VARCHAR(200) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ";
    
    $pdo->exec($sql);
    
    // Insert some sample data
    $sampleData = [
        ['john@example.com', 'John Doe'],
        ['jane@example.com', 'Jane Smith'],
        ['bob@example.com', 'Bob Johnson'],
        ['alice@example.com', 'Alice Brown'],
        ['charlie@example.com', 'Charlie Wilson']
    ];
    
    $stmt = $pdo->prepare("INSERT OR IGNORE INTO contact (email_address, name) VALUES (?, ?)");
    
    foreach ($sampleData as $data) {
        $stmt->execute($data);
    }
    
    echo "Database setup completed successfully!\n";
    echo "Sample data has been inserted.\n";
    echo "You can now start the server with: php -S localhost:8000 -t public\n";
    
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
    exit(1);
} 