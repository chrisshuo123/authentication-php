<?php

class UserValidator {
    public function validateSignIn(array $post): array {
        $errors = [];
        $email = filter_var($post['email'], FILTER_SANITIZE_EMAIL);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid Email Format';
        }
        if(empty($post['password'])) {
            $errors['password'] = 'Password cannot be empty';
        }

        return $errors;
    }

    public function validateSignUp() {
        $errors = [];
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Sanitize to avoid SQL Injection
        $name = $_POST['name'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $created_at = date('Y-m-d H:i:s');

        // Validate email sign-up
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate to ensure this is real email. Because !filter_var, it means that the email is not correct and shows error.
            $errors['email'] = 'Invalid Email Format';
        }
        // Validate Name Sign-Up
        if(empty($name)) {
            $errors['name'] = 'Name is required';
        }
        // Validate Password Sign-up
        if(strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        }
        // Validate password confirmation sign-up
        if($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        return $errors;
    }
}