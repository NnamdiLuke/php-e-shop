<?php 
require_once('files/functions.php');
$id = $_POST['id'];

// $pro = db_select('products', " id = $id ");
$pro = get_product($id);
if($pro == null){
    die("product not found");
}

$pro['quantity'] = ((int)($_POST['quantity']));
$_SESSION['cart'][$id] = $pro;
alert('success','Product added successfully');
header("Location: product.php?id=$id");

