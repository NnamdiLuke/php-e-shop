

<?php

use stefangabos\Zebra_Image\Zebra_Image;

require_once 'files/Zebra_Image.php';


if(session_status() == PHP_SESSION_NONE){
    session_start();
}
define('BASE_URL','http://localhost/e-shop');
$conn = new mysqli('localhost','root','','e-shop');

function db_inset($table_name,$data){
    $sql = "INSERT INTO $table_name";
    $column_names ="(";
    $column_values ="(";
    
    $is_first = true;
    foreach ($data as $key => $value) {
        if($is_first){
            $is_first = false;
        } else {
            $column_names .= ",";
            $column_values .= ",";
            
        }
        $column_names .= $key;
        $gettype = gettype($value);
        if($gettype == 'string'){
            $column_values .= "'$value'";
        } else {
            $column_values .= $value;
        };
        
    }
    $column_names .=")";
    $column_values .=")";
    $sql .= $column_names." VALUES ".$column_values;

    global $conn;
    if($conn->query($sql)){
        return true;
    } else {
        return false;
    }
}

// protected area
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

// logout function
function loggout(){
    if(isset($_SESSION['user'])){
        unset($_SESSION['user']);
    }
    alert('success','Logout sucessful');
    header('Location: login.php');
    die();
}

// is authenticated
function is_logged_in(){
    if(isset($_SESSION['user'])){
        return true;
    } else{
        return false;
    }
}

// Alert
function alert($type,$message){
    $_SESSION['alert']['type'] = $type;
    $_SESSION['alert']['message'] = $message;
}

// login function
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

function select_input($data,$options){
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

    $select_options = "";
    foreach ($options as $key => $val) {
        $selected = "";
        if($key == $value){
            $selected = "selected";
        }
        $select_options .= '<option value="'.$key.'">'. $val .'</option>';
    }
    $select_tag =  '<select name="'.$name.'" ' .$selected. ' class="form-control text-capitalize" type="text"  id="'.$name.'"  placeholder="'.$name.'" '.$attributes.'>
        '.$select_options.'
    </select>';

    return '
    <label class="form-label text-capitalize" for="'.$name.'">'.$label.'</label>'
    .$select_tag   
    .$error_text;


}

// function to upload image
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
            $thumb_destination = 'uploads/thumb_' . $file_name;
            
            $res = move_uploaded_file($file['tmp_name'], $destination);
            if(!$res){
                continue;
            }
            // after successful upload of image, compress it.
             $thumb_destination = create_thumb($destination,$thumb_destination);
            $img['src'] = $destination;
            $img['thumb'] = $thumb_destination;
            $upload_images[] = $img;
        }
    }

    return $upload_images;
}

// image compressor 
function create_thumb($source,$target){


    $image = new Zebra_Image();
    $image->auto_handle_exif_orientation = true;
    $image->source_path = $source;
    $image->target_path = $target;
    
    $image->preserve_aspect_ratio = true;
    $image->enlarge_smaller_images = true;
    $image->preserve_time = true;
    $image->auto_handle_exif_orientation = true;

    
    $width = 1200;
    $height = 1600;
    $image->jpeg_quality = 50;
    $image->jpeg_quality = get_jpeg_quality(filesize($source));

    // if(!$image->resize(
    //     $width,
    //     $height,
    //     ZEBRA_IMAGE_CROP_CENTER
    // )){
    if(!$image->resize()){
        return $image->source_path;
    } else {
        return $image->target_path;
    }



}

function get_jpeg_quality($size){
    $size = $size / (1024 * 1024);

    $quality = 70;

    if ($size > 5) {
        $quality = 60;
    }
    elseif ($size > 3) {
        $quality = 65;
    }
    elseif ($size > 1) {
        $quality = 70;
    }
    elseif ($size > 0.5) {
        $quality = 75;
    }
    else {
        $quality = 80;
    }

    return $quality;
}