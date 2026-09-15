

<?php

use stefangabos\Zebra_Image\Zebra_Image;

require_once 'files/Zebra_Image.php';


if(session_status() == PHP_SESSION_NONE){
    session_start();
}
define('BASE_URL','http://localhost/e-shop');
$conn = new mysqli('localhost','root','','e-shop');

function get_product($id){
    $sql = " SELECT * FROM products WHERE id = $id ";
    global $conn;
    $data['pro'] = $conn->query($sql)->fetch_assoc();
    $data['cat'] = null;
    if($data['pro'] != null){
        $cat_id = $data['pro']['category_id'];
        $sql = " SELECT * FROM categories WHERE id = $cat_id";
        $data['cat'] = $conn->query($sql)->fetch_assoc();
    }
    

    return $data;
}

function db_select($table,$condition = null){
    $sql = " SELECT * FROM $table ";
    
    if($condition != null){
        $sql = " SELECT * FROM $table WHERE $condition ";
    }
    global $conn;

    $res = $conn->query($sql);
    $rows = [];
    while($row = $res->fetch_assoc()){
        $rows[] = $row;
    }

    return $rows;
}

// Create objects
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
    <input name="'.$name.'" value="'.$value.'" class="form-control text-capitalize" type="text"  id="'.$name.'"  placeholder="'.$label.'" '.$attributes.'>'
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
    $select_tag =  '<select name="'.$name.'" ' .$selected. ' class="form-select text-capitalize" type="text"  id="'.$name.'"  placeholder="'.$name.'" '.$attributes.'>
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

// get list of photos
function get_product_photos($json){
    $img['src'] = "assets/no_image.jpg";
    $img['thumb'] = "assets/no_image.jpg";
    $photos[] = $img;

    if($json == null){
        return $photos;
    }
    if(strlen($json) < 4){
        return $photos;
    }
    $objects = json_decode($json);

    if(empty($objects)){
         return $photos;
    }
    

    return $objects;

}

// get photos
function get_product_thumbnail($json){
    $img = "assets/no_image.jpg";

    if($json == null){
        return $img;
    }
    if(strlen($img) < 4){
        return $img;
    }
    $objects = json_decode($json);

    if(empty($objects)){
         return $img;
    }
    
    if(!isset($objects[0]->thumb)){
        return $img;
    }

    return $objects[0]->thumb;

}

function product_item_ui_1($pro){
    $thumbnail = get_product_thumbnail($pro['photos']); 
    $str = <<<EOF
    <div class="col-md-4 col-sm-6 px-2 mb-4">
        <div class="card product-card">
        <button class="btn-wishlist btn-sm" type="button" data-bs-toggle="tooltip" data-bs-placement="left" title="Add to wishlist">
            <i class="ci-heart"></i>
        </button>
        <a class="card-img-top d-block overflow-hidden" href="product.php?id=$pro[id]">
            <img src="{$thumbnail}" alt="Product">
        </a>
        <div class="card-body py-2"><a class="product-meta d-block fs-xs pb-1" href="#">Sneakers &amp; Keds</a>
            <h3 class="product-title fs-sm"><a href="product.php?id={$pro['id']}">{$pro['name']}</a></h3>
            <div class="d-flex justify-content-between">
            <div class="product-price"><span class="text-accent">$$pro[price].<small>00</small></span></div>
            <div class="star-rating"><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star-filled active"></i><i class="star-rating-icon ci-star"></i>
            </div>
            </div>
        </div>
        <div class="card-body card-body-hidden">
            <div class="text-center pb-2">
            <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-75">
                <label class="form-option-label" for="s-75">7.5</label>
            </div>
            <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-80" checked>
                <label class="form-option-label" for="s-80">8</label>
            </div>
            <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-85">
                <label class="form-option-label" for="s-85">8.5</label>
            </div>
            <div class="form-check form-option form-check-inline mb-2">
                <input class="form-check-input" type="radio" name="size1" id="s-90">
                <label class="form-option-label" for="s-90">9</label>
            </div>
            </div>
            <button class="btn btn-primary btn-sm d-block w-100 mb-2" type="button"><i class="ci-cart fs-sm me-1"></i>Add to Cart</button>
            <div class="text-center"><a class="nav-link-style fs-ms" href="#quick-view" data-bs-toggle="modal"><i class="ci-eye align-middle me-1"></i>Quick view</a></div>
        </div>
        </div>
        <hr class="d-sm-none">
    </div>
    EOF;

    return $str;
}