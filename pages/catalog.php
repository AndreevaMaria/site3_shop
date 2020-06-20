<h3 class="text-light mb-4">Catalog page</h3>
<form action="index.php?page=1" method="post">
    <div>
        <select name="catid" class="mb-3" onchange="getItemsCat(this.value)">
            <option value="0">Select category:</option>
            <?php
            $pdo = Tools::connect();
            $ps = $pdo->prepare("SELECT * FROM categories");
            $ps->execute();
            while($row = $ps->fetch()) {
                echo "<option value=".$row['id'].">".$row['category']."</option>";
            }
            ?>
        </select>
    </div>

    <?php
    echo "<div id='result' class='row mb-5'>";
    $items = Item::getItems();
    foreach ($items as $item) {
      // var_dump($item);
       // экземпляр товара вызывает метод этого класса для вывода карточки товара
      $item->drawItem(); 
    }
    echo "</div>";
    ?>
</form>
<script>
    function createCookie (ruser, id) {
        $.cookie(ruser, id, { expires: 2, path: '/' });
    }

    function getItemsCat(cat) {
        if(window.XMLHttpRequest) {
            ao = new XMLHttpRequest();
        } else {
            ao = new ActiveXObject('Microsoft/XMLHTTP');
        }
        ao.onreadystatechange = function() {
            if(ao.readyState == 4 && ao.status == 200) {
                document.querySelector('#result').innerHTML = ao.responseText;
            }
        }
        ao.open('post', 'pages/lists.php', true);
        ao.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ao.send('cat='+ cat);
    }
</script>

