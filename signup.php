<?php
ob_start();
require_once 'config.php';
ob_end_clean();

header('Content-Type: application/json');

$res = array();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password'])) {
        
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            $pdo = new PDO($attr, $user, $pass, $opts);
            
            $query = "INSERT INTO users (id, firstname, lastname, email, password) VALUES (NULL, :firstname, :lastname, :email, :password)";
            
            $stmt = $pdo->prepare($query);
            
            $result = $stmt->execute([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => $password
            ]);
            
            if($result) {
                $_SESSION['email'] = $email;
                $res['auth'] = true;
            } else {
                $res['auth'] = false;
                $res['message'] = 'Registration failed';
            }
            
            $pdo = null;
            
        } catch (PDOException $e) {
            $res['auth'] = false;
            $res['message'] = 'Database error: ' . $e->getMessage();
        }
        
    } else {
        $res['auth'] = false;
        $res['message'] = 'All fields are required';
    }
} else {
    $res['auth'] = false;
    $res['message'] = 'Invalid request method';
}

echo json_encode($res);
exit;
