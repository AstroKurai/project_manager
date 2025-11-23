<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: update_project.php

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

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    
    $project = new Project($pdo);
    
    $data = [
        'project_name' => $_POST['project_name'],
        'description' => $_POST['description'],
        'priority' => $_POST['priority'],
        'status' => $_POST['status'],
        'due_date' => $_POST['due_date'],
        'start_time' => $_POST['start_time'],
        'budget' => $_POST['budget'],
        'project_type' => $_POST['project_type']
    ];
    
    $result = $project->update($_POST['id'], $data);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed']);
    }
    
    $pdo = null;
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>