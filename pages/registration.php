<?php
echo '<h4>Registration</h4>';

if(!isset($_POST['regbtn'])) {
?>
<form action="index.php?page=3" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="login">Login: 
            <input type="text" class="form-control" name="login">
        </label>
    </div>
    <div class="form-group">
        <label for="pass1">Pass: 
            <input type="password" class="form-control" name="pass1">
        </label>
    </div>
    <div class="form-group">
        <label for="pass2">Confirm pass: 
            <input type="password" class="form-control" name="pass2">
        </label>
    </div>
    <div class="form-group">
        <label for="imagepath">Select image: 
            <input type="file" class="form-control" name="imagepath">
        </label>
    </div>
    <input type="submit" class="btn btn-primary" name="regbtn">
</form>
<?php
} else {
    if(is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
        $path = "images/users/".$_FILES['imagepath']['name'];
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    if(Tools::register($_POST['login'], $_POST['pass1'], $path)) {
        echo $path;
        echo '<h5 class="text-success">New user added</h5>';
    }
}