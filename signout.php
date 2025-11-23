<?php
// Name: Jadelynn Wolden
// Date: 11/11/2025
// Filename: signout.php

require_once 'config.php';

session_destroy();
header('Location: index.html');
?>
