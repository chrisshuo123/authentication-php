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