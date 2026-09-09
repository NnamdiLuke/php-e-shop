<?php

require_once('files/functions.php');

$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$password_1 = trim($_POST['password_1']);
$phone_number = trim($_POST['phone_number']);


// check if the password doesn't match
if($password != $password_1 ){
    alert('danger',"Password doesn't match");
    header('Location: login.php');
    die();
};

// check if email already exist or not?
$sql = "SELECT * FROM users WHERE email = '{$email}'";
$res = $conn->query($sql);

if($res->num_rows > 0){
    alert('danger',"User with same email already exist");
    header('Location: login.php');
    die();
};

// hash password
$password = password_hash($password,PASSWORD_DEFAULT);
$created = time();

// add user to database
$sql = "INSERT INTO users (
    first_name,
    last_name,
    email,
    phone_number,
    password,
    user_type,
    created
) VALUES (
    '{$first_name}',
    '{$last_name}',
    '{$email}',
    '{$phone_number}',
    '{$password}',
    'customer',
    '{$created}'
)";

if($conn->query($sql)){
    login_user($email,$password);
    alert('success',"Account created and login successful");
    header('Location: account-orders.php');
    die();
} else{
    alert('danger',"Failed to create account");
    header('Location:  login.php');
    die();
};


die();
