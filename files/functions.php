<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
define('BASE_URL','http://localhost/e-shop');
$conn = new mysqli('localhost','root','','e-shop');

function protect_area(){
    if(!isset($_SESSION['user'])){
        alert('warning','unauthorized access');
        header('Location: login.php');
        die();
    }
}

function url($path = "/"){
    return BASE_URL . $path;
}

function loggout(){
    if(isset($_SESSION['user'])){
        unset($_SESSION['user']);
    }
    alert('success','Logout sucessful');
    header('Location: login.php');
    die();
}

function is_logged_in(){
    if(isset($_SESSION['user'])){
        return true;
    } else{
        return false;
    }
}

function alert($type,$message){
    $_SESSION['alert']['type'] = $type;
    $_SESSION['alert']['message'] = $message;
}


function login_user($email,$password){
    // check if email exist or not?
    global $conn;
    $sql = "SELECT * FROM users WHERE email = '{$email}'";
    $res = $conn->query($sql);

    if($res->num_rows < 1){
        return false;
    };

    $row = $res->fetch_assoc();

    if(!password_verify($password, $row['password'])){
        return false;
    }

    // store the user in a session
    $_SESSION['user'] = $row;

    return true;

}

function text_input($data){
    $name = (isset($data['name'])) ? $data['name'] : "";
    $attributes = (isset($data['attributes'])) ? $data['attributes'] : "";

    $value = "";
    $error = "";
    $error_text = "";
    if(isset($_SESSION['form'])){
        if(isset($_SESSION['form']['value'])){
            if(isset($_SESSION['form']['value'][$name])){
                $value = $_SESSION['form']['value'][$name];
            }
        }
    };

    if(isset($_SESSION['form'])){
        if(isset($_SESSION['form']['error'])){
            if(isset($_SESSION['form']['error'][$name])){
                $error = $_SESSION['form']['error'][$name];
                $error_text  = '<div class="form-text text-danger">'.$error.'.</div>';
            }
        }
    };
    
    $label = (isset($data['label'])) ? $data['label'] : $name;
    $value = (isset($data['value'])) ? $data['value'] : $value;
    $error = (isset($data['error'])) ? $data['error'] : $error;
    return '
    <label class="form-label text-capitalize" for="'.$name.'">'.$label.'</label>
    <input name="'.$name.'" value="'.$value.'" class="form-control text-capitalize" type="text"  id="'.$name.'"  placeholder="'.$name.'" '.$attributes.'>'
    .$error_text;
}

function upload_images($files){
    ini_set('memory_limit','512M');

    if($files == null || empty($files)){
        return [];
    }


    $upload_images = array();

    foreach($files as $file){
  

        if(
            isset($file['name']) &&
            isset($file['type']) &&
            isset($file['tmp_name']) &&
            isset($file['error']) &&
            isset($file['size']) 
        ){
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_name = time() . "-" .rand(10000,1000000). "." . $ext;
            $destination = 'uploads/' . $file_name;
            
            $res = move_uploaded_file($file['tmp_name'], $destination);
            if(!$res){
                continue;
            }

            $img['src'] = $destination;
            $upload_images[] = $img;
        }
    }

    return $upload_images;
}


