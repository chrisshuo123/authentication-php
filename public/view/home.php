<?php
// session_start();
// var_dump($_SESSION); // temporary - remove after debugging

// session_destroy();
// header('Location: /bikin-register-login-php/index.php');
// exit();
?>

<?php
session_start(); // Needed - home.php is accessed directly, not through index.php
if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
} else {
    // header("Location: login.php");
    header("Location: /bikin-register-login-php/index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Home Page</title>
    <link rel="stylesheet" href="/bikin-register-login-php/public/css/style.css">
</head>

<body>

    <div class="user-details">
        <p>Logged in user</p>
        <?php
        echo '<p>Email: ' . $user['email'] . '</p><br>';

        echo '<p> Name: ' . $user['name'] . '</p>';

        ?>
        <a href="logout.php">Logout</a>

    </div>


</body>

</html>