<?php
if(!isset($_POST['addbtn'])) {
?>
<form action="index.php?page=4" method="post" enctype="multipart/form-data">
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
                <input type="text" class="form-control" name="name">
            </label>
        </div>
        <div class="form-group">
            <p>Incoming price and sale price</p>
            <div>
                <input type="number"name="pricein">
                <input type="text"name="pricesale">
            </div>
        </div>
        <div class="form-group">
            <label for="info"> 
                <textarea type="text" class="d-block" name="info"></textarea>
            </label>
        </div>
        <div class="form-group">
            <label for="imagepath">
                <input type="file" name="imagepath">
            </label>
        </div>
    <button type="submit" class="btn btn-primary" name="addbtn">Add Good</button>
</form>
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