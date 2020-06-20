<?php
include_once('classes.php');

$cat = $_POST['cat'];
$pdo = Tools::connect();
$items = [];
$items = Item::getItems($cat);

if($items === null) exit;

//выводим товары
foreach($items as $item) {
    $item->drawItem();
}