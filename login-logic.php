<?php

require_once('files/functions.php');

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if(login_user($email,$password)){
    alert('success',"Login successful");
    header('Location: account-orders.php');
    die();
} else{
    alert('danger','Invalid credentials');
    header('Location: login.php');
    die();
};


