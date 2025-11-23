<?php
// Name: Jadelynn Wolden
// Date: 11/17/2025
// Filename: upload_document.php

require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or upload error']);
    exit;
}

if (!isset($_POST['project_id'])) {
    echo json_encode(['success' => false, 'error' => 'Project ID required']);
    exit;
}

$allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 
                 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$maxSize = 5 * 1024 * 1024;
//5mb max size for now
$file = $_FILES['document'];

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['success' => false, 'error' => 'File too large (max 5MB)']);
    exit;
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFilename = uniqid() . '_' . time() . '.' . $extension;
$uploadPath = 'uploads/' . $newFilename;

if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    
    try {
        $pdo = new PDO($attr, $user, $pass, $opts);

        $query = "INSERT INTO project_documents (project_id, file_name, file_path, file_type, file_size) 
                  VALUES (:project_id, :file_name, :file_path, :file_type, :file_size)";
        
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            'project_id' => $_POST['project_id'],
            'file_name' => $file['name'],
            'file_path' => $uploadPath,
            'file_type' => $file['type'],
            'file_size' => $file['size']
        ]);
        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'id' => $pdo->lastInsertId(),
                'filename' => $file['name']
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database insert failed']);
        }
        
        $pdo = null;
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
}
?>