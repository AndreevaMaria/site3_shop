<?php
include_once("pages/classes.php");

if(isset($_SESSION['ruser'])) {
    echo '<form action="index.php';
    if(isset($_GET['page'])) {
    echo '?page='.$_GET['page'];
    echo '" method="post" class="input-group mt-2">';
    echo "<div class='mr-2'>Hello, {$_SESSION['ruser']}!</div>";
    echo '<input type="submit" name="exit" value="Log out" class="btn btn-outline-primary btn-sm mr-5">';
    }

    if(isset($_POST['exit'])) {
        unset($_SESSION['ruser']);
        unset($_SESSION['radmin']);
        echo '<script>window.location.reload()</script>';
    } 
    echo '</form>';
} else {
    if(isset($_POST['getin'])) {
        if(Tools::login($_POST['login'], $_POST['pass'])) {
            echo '<script>window.location.reload()</script>';
        } else {
            echo "<div class='text-danger'>Логин или пароль не совпадают</div>";
        }
    } else {
        echo '<form action="index.php';
        if(isset($_GET['page'])) echo '?page='.$_GET['page'];
        echo '" method="post" class="input-group mt-2">';
        echo '<input type="text" name="login" placeholder="login">';
        echo '<input type="password" name="pass" placeholder="password">';
        echo '<input type="submit" name="getin" value="Log in" class="btn btn-outline-secondary btn-sm mr-5">';
        echo '</form>';
    }
}
?>