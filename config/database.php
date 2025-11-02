<?php
// config/database.php - SQLite Version with Organized Positions

// Get the absolute path to the project root
$projectRoot = realpath(dirname(__FILE__) . '/..');
$dbDir = $projectRoot . '/database';
$databaseFile = $dbDir . '/voting_system.sqlite';

// Create database directory if it doesn't exist
if (!is_dir($dbDir)) {
    if (!mkdir($dbDir, 0755, true)) {
        die("Failed to create database directory: " . $dbDir);
    }
}

// Check if directory is writable
if (!is_writable($dbDir)) {
    die("Database directory is not writable: " . $dbDir);
}

try {
    $pdo = new PDO('sqlite:' . $databaseFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Create tables
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(10) DEFAULT 'voter',
            has_voted BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS candidates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(100) NOT NULL,
            photo_url VARCHAR(500),
            bio TEXT,
            votes INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS votes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            voter_id INTEGER,
            candidate_id INTEGER,
            position VARCHAR(100),
            voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (voter_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (candidate_id) REFERENCES candidates(id) ON DELETE CASCADE
        )
    ");
    
    // Check if admin user exists, if not insert
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $adminStmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $adminStmt->execute([
            'admin', 
            'admin@college.edu', 
            password_hash('password', PASSWORD_DEFAULT), 
            'admin'
        ]);
    }
    
    // Check if candidates exist, if not insert with organized positions
    $stmt = $pdo->query("SELECT COUNT(*) FROM candidates");
    if ($stmt->fetchColumn() == 0) {
        $candidates = [
            // King Of Westeros
            [
                'Jon Snow', 
                'King Of Westeros', 
                'Experienced leader with honor and integrity. Known for making tough decisions for the greater good.',
                'https://platform.vox.com/wp-content/uploads/sites/2/chorus/uploads/chorus_asset/file/15329653/Jon_snow.0.1536999998.jpg?quality=90&strip=all&crop=0,3.4613147178592,100,93.077370564282'
            ],
            [
                'Daenerys Targaryen', 
                'King Of Westeros', 
                'Visionary leader with revolutionary ideas. Advocate for radical change and equality.',
                'https://imgix.bustle.com/uploads/image/2018/1/30/8e4ed0db-0ce8-454d-9680-9b318ab85ba9-b4a5a5fdc72295984961ec4d2bf7a2b62911af77979e9a28fd800ef61cb11a920f24ce9423e62b834403fad70b217cec.jpg?w=1200&h=1200&fit=crop&crop=faces&fm=jpg'
            ],
            
            // Hand Of the King
            [
                'Sansa Stark', 
                'Hand Of the King', 
                'Master of administration and diplomacy. Excellent at managing complex situations.',
                'https://platform.vox.com/wp-content/uploads/sites/2/chorus/uploads/chorus_asset/file/16289239/_20__Helen_Sloan___HBO.jpg?quality=90&strip=all&crop=0.079239302694134,0,99.841521394612,100'
            ],
            [
                'Tyrion Lannister', 
                'Hand Of the King', 
                'Strategic thinker with exceptional problem-solving skills. Expert in negotiation.',
                'https://static.wikia.nocookie.net/gameofthrones/images/9/95/HandoftheKingTyrionLannister.PNG/revision/latest?cb=20190520175204'
            ],
            
            // Grand-Maestar
            [
                'Samwell Tarly', 
                'Grand-Maestar', 
                'Organized and diligent. Excellent record keeper with attention to detail.',
                'https://hips.hearstapps.com/hmg-prod/images/john-bradley-1558391237.png?crop=0.502xw:1.00xh;0.0170xw,0&resize=640:*'
            ],
            [
                'Missandei', 
                'Grand-Maestar', 
                'Multilingual and efficient. Excellent communicator and coordinator.',
                'https://static.wikia.nocookie.net/gameofthrones/images/d/de/Missandei8X04.PNG/revision/latest?cb=20190722224410'
            ],
            
            // Master Of Coin
            [
                'Petyr Baelish', 
                'Master Of Coin', 
                'Financial expert with extensive experience in budget management and resource allocation.',
                'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVrvk0NX3KvOluJjH2zrgSCAvY-dCTyBGYYw&s'
            ],
            [
                'Lord Mace Tyrell', 
                'Master Of Coin', 
                'Master of resources and networks. Committed to transparent financial management.',
                'https://static.wikia.nocookie.net/gameofthrones/images/e/e2/MaceTyrell-Profile.PNG/revision/latest?cb=20160719041706'
            ]
        ];
        
        $candidateStmt = $pdo->prepare("INSERT INTO candidates (name, position, bio, photo_url) VALUES (?, ?, ?, ?)");
        foreach ($candidates as $candidate) {
            $candidateStmt->execute($candidate);
        }
    }
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage() . " - File: " . $databaseFile);
}
?>