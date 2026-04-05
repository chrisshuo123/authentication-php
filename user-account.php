<?php
require_once 'dbConnect.php';
session_start();

$errors = [];

// 1 - UNTUK MELAKUKAN SIGN-UP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Sanitize to avoid SQL Injection
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $created_at = date('Y-m-d H:i:s');

    // Validate Email Sign-Up
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate to ensure this is real email. Because !filter_var, it means that the email is not correct and shows error.
        $errors['email'] = 'Invalid email format';
    }
    // Validate Name Sign-Up
    if(empty($name)){
        $errors['name']='Name is required';
    }
    // Validate Password Sign-Up
    if (strlen($password) < 8 ) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }
    // Validate Password Confirmation Sign-Up
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    // Run the DB SQL Query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]); // Binding to prevent SQL Injection
    // Validate if There's previous user or not (because WHERE email, it checks the email.)
    if ($stmt->fetch()) {
        $errors['user_exist'] = 'Email is already registered';
    }
    // If the $errors variable not Empty, head to the register.php page (refreshes the same page back, where register is signup).
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: register.php');
        exit();
    }

    // Hash the Password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password,name,created_at) VALUES (:email, :password, :name, :created_at)");
    $stmt->execute(['email' => $email, 'password' => $hashedPassword, 'name'=>$name,'created_at'=>$created_at]); // To avoid SQL Injection

    // Head towards the main home page
    header('Location: index.php');
    exit();
}

// 2 - UNTUK MELAKUKAN SIGN-IN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate Email Checking
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    // Validate Password Empty or Not (wrong password in the code below, generated 'invalid email or password')
    if (empty($password)) {
        $errors['password'] = 'Password cannot be empty';
    }

    // Checking for another possible errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }

    // Run the DB SQL Query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Check the user and password. If correct, headed to home.php.
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'name'=>$user['name'],
            'created_at' => $user['created_at']
        ];

        header('Location: home.php');
        exit();
    } else {
        $errors['login'] = 'Invalid email or password';
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }
}
