# Self Project: Authentication Modification (Credit: francis-njenga)
### Front-End: Create the Front-End UI According with my Own Version
1. Applying rich UI's that makes visual more better
2. Tools: HTML5, CSS3, JS, Bootstrap
3. Next Tools Development: ReactJS, ViteJS, TypeScript

### Back-End: Self Challenge
1. Trying to create the PHP Login system from scratch
2. Add the Admin & User Column DB
3. While login, the system understands whether it's a User or an Admin
4. Tools: PHP, XAMPP
5. Next Tools Development: Laravel, Laragon

#### Part 1: Create OOP & MVC Structures for the PHP Login & Register Authentication
Implementing Authentication in PHP primarily using OOP & MVC Principles may be difficult, where it also requires another additional MVC folder, which is a Services, that act as session manager _(where most register & login auth uses session often)_ and validator.<br><br>

##### Before Implement OOP & MVC
The code in user-account.php mainly act as If Controller before Implement OOP & MVC:<br>
```
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
```<br><br>

Additional Description _(still adhoc, some are important for the code above)_: <br><br>
For the Sign-In:
```
if checker untuk post sign-in tidak diperlukan, diganti dengan method handleSignIn()
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    Validate Email Checking
    Already validated in the validateSignIn() above

    Validate Password Empty or Not (wrong password in the code below, generated 'invalid email or password')
    Already validated in the validateSignIn() above

    Checking for another possible errors
    Handled in src/Controllers/AuthController.php

    Previously in the user-account.php (before implement OOP), we use this code:
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php');
        exit();
    }
    Now in the AuthController.php, this is the code:
    public function handleSignIn() {
        $errors = $this->validator->validateSignIn($_POST);
        if($errors) {
            $this->session->set('errors', $errors);
            header('Location: index.php');
            exit();
        }
    }
    var $errors calls a method in this class to validate errors.

    Run the DB SQL Query
    Terdapat pada src/Services/UserValidator.php

    Check the user and password. If correct, headed to home.php.
    This is done using if-else checker to makesure user and password are correct. 
    This is the updated code in the AuthController.php:
    public function handleSignIn() {
        ...
        $user = $this->users->findByEmail($_POST['email']);
        if($user && password_verify($_POST['password'], $user['password'])) {
            $this->session->set('user', $user);
            header('Location: home.php');
            exit();
        }
        $this->session->set('errors', ['login' => 'Invalid email or password']);
        header('Location: index.php');
        exit();
    }
    This was the last code used to validate:
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
```<br>
For the Sign-Up:<br>
```
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
    Run the DB SQL Query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]); // Binding to prevent SQL Injection
    Validate if There's previous user or not (because WHERE email, it checks the email.)
    if ($stmt->fetch()) {
        $errors['user_exist'] = 'Email is already registered';
    }
    If the $errors variable not Empty, head to the register.php page (refreshes the same page back, where register is signup).
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: register.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password,name,created_at) VALUES (:email, :password, :name, :created_at)");
    $stmt->execute(['email' => $email, 'password' => $hashedPassword, 'name'=>$name,'created_at'=>$created_at]); // To avoid SQL Injection
}
```

##### After Implement OOP & MVC
The previously user-account.php, that now is src/Controllers/AuthController.php:
```
<?php

class AuthController {
    public function __construct(
        private UserRepository $users,
        private UserValidator $validator,
        private SessionManager $session
    ) {}

    public function handleSignIn(): void {
        $errors = $this->validator->validateSignIn($_POST);
        if($errors) {
            $this->session->set('errors', $errors);
            header('Location: /bikin-register-login-php/index.php');
            exit();
        }
        $user = $this->users->findByEmail($_POST['email']);
        if($user && password_verify($_POST['password'], $user['password'])) {
            $this->session->set('user', $user); // <- is this actually saving?
            //var_dump($_SESSION); // Temporary
            //exit(); 
            header('Location: /bikin-register-login-php/public/view/home.php');
            exit();
        }
        $this->session->set('errors', ['login' => 'Invalid email or password']);
        header('Location: /bikin-register-login-php/index.php');
        exit();
    }

    public function handleSignUp(): void {
        // Validate input format first
        $errors = $this->validator->validateSignUp($_POST);
        // Check if email already exists (needs DB, so this validation stays in controller)
        if(!$errors) {
            $existingUsers = $this->users->findByEmail($_POST['email']);
            if($existingUsers) {
                $errors['user_exist'] = 'Email is already registered';
            }
        }
        
        // If there's any error, redirect back
        if($errors) {
            $this->session->set('errors', $errors);
            header('Location: register.php');
            exit();
        }
        
        // Hash password and create user
        // Hash the Password
        $hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $this->users->createUser(
            $_POST['email'],
            $_POST['name'],
            $hashedPassword,
            date('Y-m-d H:i:s'),
        );

        // Head towards the main home page
        header('Location: index.php');
        exit();
    }
}
```