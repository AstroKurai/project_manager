<?php
// Name: Jadelynn Wolden
// Date: 11/11/2025
// Filename: verifyAuth.php

require_once 'config.php';

$res = array();

if(isset($_SESSION['email'])) {
    try {
        $pdo = new PDO($attr, $user, $pass, $opts);
        
        $query = "SELECT firstname FROM users WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['email' => $_SESSION['email']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $res['email'] = $_SESSION['email'];
        $res['firstname'] = $userData['firstname'] ?? 'User';
        $res['auth'] = true;
        
        $pdo = null;
    } catch (PDOException $e) {
        $res['email'] = $_SESSION['email'];
        $res['firstname'] = 'User';
        $res['auth'] = true;
    }
} else {
    $res['auth'] = false;
}

echo json_encode($res);
?>