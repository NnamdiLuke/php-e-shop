<?php 
require_once('files/functions.php');
$id = 0;
$id = ((int)($_GET['id']));
$path = $_GET['path'];
if(isset($_SESSION['cart'])){
    foreach ($_SESSION['cart'] as $key => $value) {
        if($value['pro']['id'] == $id){
            unset($_SESSION['cart'][$key]);
        }
    }
}

if($path == null){
    alert('success','Product removed successfully');
    header("Location: shop.php");
} else {
    alert('success','Product removed successfully');
    header("Location: $path");

}


