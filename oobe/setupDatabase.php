<?php

if (!isset($_POST['dummyData'])) {
    http_response_code(400);
    echo "Missing parameters.";
    die("Missing parameters.");
}

// Try load config file

try {
    require_once __DIR__ . "/../config.php";
} catch (Exception $e) {
    http_response_code(500);
    echo "config.php file not found.";
    die("Failed to load config file: " . $e->getMessage());
}

// Try to connect to the database

try {
    $pdo = new PDO("mysql:host=" . $db_server . ";dbname=" . $db_database_name, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Failed to connect to the database.";
    die("Connection failed: " . $e->getMessage());
}

// Check SQL file exists

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/setup/evotechDB.sql")) {
    http_response_code(500);
    echo "SQL file not found.";
    die("SQL file not found.");
}

// Load SQL file

$sql = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/setup/evotechDB.sql");

// Execute SQL

try {
    $pdo->exec($sql);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Failed to create database.";
    die("Failed to create database: " . $e->getMessage());
}

// Check if dummy data is required

if ($_POST['dummyData'] == "true") {
    // Check SQL file exists

    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/setup/dummyProductData.sql")) {
        http_response_code(500);
        echo "Dummy data SQL file not found.";
        die("Dummy data SQL file not found.");
    }

    // Load SQL file

    $sql = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/setup/dummyProductData.sql");

    // Execute SQL

    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Failed to create dummy data.";
        die("Failed to create dummy data: " . $e->getMessage());
    }

    // Copy file and folder structure from /setup/examplePhotos/products/* to /view/images/products/*

    $source = $_SERVER['DOCUMENT_ROOT'] . "/setup/examplePhotos/products";
    $destination = $_SERVER['DOCUMENT_ROOT'] . "/view/images/products";

    if (!file_exists($destination)) {
        mkdir($destination, 0777, true);
    }

    $files = scandir($source);

    foreach ($files as $file) {
        if ($file == "." || $file == "..") {
            continue;
        }

        copy($source . "/" . $file, $destination . "/" . $file);
    }
}

// Disconnect from the database
$pdo = null;

// Everything was successful
http_response_code(200);
echo "Database created successfully.";

