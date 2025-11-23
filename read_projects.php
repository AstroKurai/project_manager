<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: read_projects.php

require_once 'config.php';
require_once 'Project.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
    $userQuery = "SELECT id FROM users WHERE email = :email";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute(['email' => $_SESSION['email']]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    $project = new Project($pdo);
    $projects = $project->getAllByUser($userData['id']);

    echo json_encode($projects);

    $pdo = null;
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
