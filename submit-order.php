<?php 
    require_once('files/functions.php');
    protect_area();
    $total_price = 0; 
    foreach ($_SESSION['cart'] as $key => $value) {
        
        $total_price += $value['pro']['price'] * $value['quantity']; 
        
    }
      

    db_inset(
        'ordersx',
        [
           'constumer_id'=>$_SESSION['user']['id'],
           'order_status'=> 1,
           'shipping'=> json_encode($_SESSION['shipping']),
           'cart'=> json_encode($_SESSION['cart']),
           'user'=> json_encode($_SESSION['user']),
           'order_date'=> time(),
           'total_price'=> $total_price,
        ]
    );

    $_SESSION['cart'] = null;
    $_SESSION['shipping'] = null;
    unset($_SESSION['cart']);
    unset($_SESSION['shipping']);

    header('Location: checkout-complete.php')

?>