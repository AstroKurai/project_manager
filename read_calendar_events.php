<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: read_calendar_events.php

require_once 'config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    
    // Get user ID
    $userQuery = "SELECT id FROM users WHERE email = :email";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute(['email' => $_SESSION['email']]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userData) {
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    $events = [];
    
    // Get project due dates
    $projectQuery = "SELECT id, project_name, due_date FROM projects 
                     WHERE user_id = :user_id AND due_date IS NOT NULL AND due_date != ''";
    $projectStmt = $pdo->prepare($projectQuery);
    $projectStmt->execute(['user_id' => $userData['id']]);
    $projects = $projectStmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($projects as $project) {
        $events[] = [
            'id' => $project['id'],
            'title' => $project['project_name'],
            'date' => $project['due_date'],
            'type' => 'project'
        ];
    }
    
    // Get task due dates (if tasks table exists)
    // Check if tasks table exists first
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'tasks'");
    if ($tableCheck->rowCount() > 0) {
        $taskQuery = "SELECT t.id, t.task_name, t.due_date, p.project_name 
                      FROM tasks t 
                      LEFT JOIN projects p ON t.project_id = p.id 
                      WHERE p.user_id = :user_id AND t.due_date IS NOT NULL AND t.due_date != ''";
        $taskStmt = $pdo->prepare($taskQuery);
        $taskStmt->execute(['user_id' => $userData['id']]);
        $tasks = $taskStmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tasks as $task) {
            $events[] = [
                'id' => $task['id'],
                'title' => $task['task_name'],
                'date' => $task['due_date'],
                'type' => 'task',
                'project' => $task['project_name']
            ];
        }
    }
    
    echo json_encode($events);
    
    $pdo = null;
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
