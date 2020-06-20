<ul class="nav nav-pills d-flex w-100 justify-content-around">
    <li class="nav-item"><a href="index.php?page=1" class="nav-link">Catalog</a></li>
    <li class="nav-item"><a href="index.php?page=2" class="nav-link">Cart</a></li>
    <li class="nav-item"><a href="index.php?page=3" class="nav-link">Registration</a></li>
<?php
    if(isset($_SESSION['radmin'])) {
        echo '<li class="nav-item"><a href="index.php?page=4" class="nav-link">Admin Forms</a></li>';
}
?>
