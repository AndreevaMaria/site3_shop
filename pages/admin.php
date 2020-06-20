<?php
if(!isset($_POST['addbtn'])) {
?>
<div class="inpcard admin rounded">
<form action="index.php?page=4" method="post" enctype="multipart/form-data">
    <h5 class="mb-3">Adding new items</h5>
        <label for="catid">Category: 
            <select name="catid">
            <?php
                $pdo = Tools::connect();
                $list = $pdo->query('SELECT * FROM categories');
                while($row = $list->fetch()) {
                    echo "<option value='".$row['id']."'>".$row['category']."</option>";
                }
            ?>
            </select>
        </label>
        <div class="form-group">
            <label for="name">
                <input type="text" class="form-control" name="name" placeholder="Title of the item">
            </label>
        </div>
        <div class="form-group">
            <p>Incoming price and sale price</p>
            <div>
                <input type="number" name="pricein"><br>
                <input type="text" name="pricesale" class="mt-2">
            </div>
        </div>
        <div class="form-group">
            <label for="info"> 
                <textarea type="text" class="d-block" name="info" placeholder="Description for the item"></textarea>
            </label>
        </div>
        <div class="form-group">
            <label for="imagepath">
                <input type="file" name="imagepath">
            </label>
        </div>
    <button type="submit" class="btn btn-primary btn-lg float-right" name="addbtn">Add Good</button>
</form>
<div>
<?php
} else {
    if(is_uploaded_file($_FILES['imagepath']['tmp_name'])) {
        $path = "images/products/".$_FILES['imagepath']['name'];
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    $name = trim($_POST['name']);
    $info = trim($_POST['info']);
    $catid = $_POST['catid'];
    $pricein = $_POST['pricein'];
    $pricesale = $_POST['pricesale'];
    
    $item = new Item($name, $catid, $pricein, $pricesale, $info, $path);
    $item->intoDb();
}
?>