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