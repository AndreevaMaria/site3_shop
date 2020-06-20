<h3 class="text-light mb-4">Cart</h3>
<?php
echo '<form action="index.php?page=2" method="post">';
echo '<table class="table table-borderless"><tbody>';
$ruser = 'cart';
$total = 0;
foreach($_COOKIE as $k => $v) {
    //echo $k . '----' $v'<br>';
    $pos = strpos($k, "_");
    if(substr($k, 0, $pos) === $ruser) {
        $id = substr($k, $pos+1);
        $item = Item::fromDb($id);
        $total += $item->pricesale;
        $item->drawForCart();
    }
}
echo '</tbody></table><hr>';
echo '<div class="d-block float-right mb-4 mr-2">';
echo '<p class="text-light my-0 total">Total price: '.$total.'</p>';
echo '<button type="submit" class="btn btn-primary btn-lg mt-5 float-right" name="suborder" onclick=eraseCookie("'.$ruser.'")>Purchase order</button>';
echo '</div>';
echo '</form>';
?>
<script>
function eraseCookie(ruser) {
    $.removeCookie(ruser, { path: '/' });
}
</script>