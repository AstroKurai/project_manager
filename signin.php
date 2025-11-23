<?php
// Name: Jadelynn Wolden
// Date: 11/11/2025
// Filename: signin.php

require_once 'config.php';

if (isset($_POST['email']) && isset($_POST['password'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $pdo = new PDO($attr, $user, $pass, $opts);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int) $e->getCode());
    }

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['email' => $email]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['email'] = $email;
        $res['auth'] = true;
    } else {
        $res['auth'] = false;
        $res['message'] = 'Incorrect email or password.';
    }

    $pdo = null;

} else {
    $res['auth'] = false;
    $res['message'] = 'Username or password not provided.';
}

header('Content-Type: application/json');
echo json_encode($res);
?>