<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: delete_project.php

require_once 'config.php';
require_once 'Project.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Project ID required']);
    exit;
}

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    
    $project = new Project($pdo);
    $result = $project->delete($_POST['id']);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Delete failed']);
    }
    
    $pdo = null;
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>