<?php 
require_once('files/functions.php');
$id = 0;
$id = ((int)($_GET['id']));
if(isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $key => $value) {
        if($value['pro']['id'] == $id){
            unset($_SESSION['cart'][$key]);
        }
    }
}

alert('success','Product removed successfully');
header("Location: shop.php");

