<?php
require_once 'src/config/config.php';
require_once 'src/Core/Database.php';
require_once 'src/Controllers/AuthController.php';
require_once 'src/Models/UserRepository.php';
require_once 'src/Services/SessionManager.php';
require_once 'src/Services/UserValidator.php';

// Boot session
$session = new SessionManager();
$session->start();

// Boot Dependencies
$db = new Database();
$pdo = $db->getConnection();
$users = new UserRepository($pdo);
$validator = new UserValidator();
$controller = new AuthController($users, $validator, $session);

// Route the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['signin'])) {
        $controller->handleSignIn();
    } elseif(isset($_POST['signup'])) {
        $controller->handleSignUp();
    }
}

// Default: show the login page
require_once 'public/view/login.php'; // Supposedly login.php