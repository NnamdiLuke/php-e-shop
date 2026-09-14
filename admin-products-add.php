<?php 

    require_once('files/functions.php');
    protect_area();

    // fetch categories
    $rows = db_select('categories','parent_id != 0');
    $categories = [];
    $categories[0] = "Select Child Category";
    foreach ($rows as $val) {
      $categories[$val['id']] = $val['name'];
    }
    
   
    
    // create products
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      $_SESSION['form']['value'] = $_POST;
      
      $imgs = upload_images($_FILES);
      // $imgs = [];
      $data['name'] = $_POST['name'];
      $data['buying_price'] = $_POST['buying_price'];
      $data['price'] = $_POST['price'];
      $data['photos'] = json_encode($imgs);
      $data['category_id'] = (int)( $_POST['parent_id']);
      $data['description'] = $_POST['description'];
      $data['user_id'] = $_SESSION['user']['id'];
      
  

      if(db_inset('products',$data)){
        alert('success','Product created successfuly.');
        header('Location: admin-products.php');
        unset($_SESSION['form']);
      } else {
        alert('danger','Failed to create product, please try again.');
        header('Location: admin-products-add.php');
      }
      die();
    }

    require_once('files/header.php');
?>


      <!-- Page Title-->
      <div class="page-title-overlap bg-dark pt-4">
        <div class="container d-lg-flex justify-content-between py-2 py-lg-3">
          <div class="order-lg-2 mb-3 mb-lg-0 pt-lg-2">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb breadcrumb-light flex-lg-nowrap justify-content-center justify-content-lg-start">
                <li class="breadcrumb-item"><a class="text-nowrap" href="index-2.html"><i class="ci-home"></i>Home</a></li>
                <li class="breadcrumb-item text-nowrap"><a href="#">Account</a>
                </li>
                <li class="breadcrumb-item text-nowrap active" aria-current="page">products</li>
              </ol>
            </nav>
          </div>
          <div class="order-lg-1 pe-lg-4 text-center text-lg-start">
            <h1 class="h3 text-light mb-0">Create Product</h1>
          </div>
        </div>
      </div>

      <div class="container pb-5 mb-2 mb-md-4">
        <div class="row">
          <!-- Sidebar-->
          <?php 
          require_once("files/account-sidebar.php")
          ?>
          <!-- Content  -->
          <section class="col-lg-8 pt-lg-4 pb-4 mb-3">
              <div class="pt-2 px-4 ps-lg-0 pe-xl-5">
                <!-- Title-->
                <div class="d-sm-flex flex-wrap justify-content-between align-items-center pb-2">
                  <h2 class="h3 py-2 me-2 mt-5 text-center text-sm-start">Add New Product</h2>
                  <div class="py-2">
                    <?= select_input([
                      'name' => 'parent_id',
                      'label' => 'Parent Category',
                    ],$categories) ?>
                  </div>
                </div>
                <form action="admin-products-add.php" method="post" enctype="multipart/form-data">
                  <div class="mb-3 pb-2">
                  

                    <div class="row mt-4">
                      <div class="col-12">
                        <div class="form-group">
                          <?= text_input([
                            'name' => 'name',
                            'label' => 'Product Name'
                          ]) ?>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mt-2">
                      <div class="col-md-6">
                        <div class="form-group">
                          <?= text_input([
                            'name' => 'buying_price',
                            'label' => 'Buying Price'
                          ]) ?>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <?= text_input([
                            'name' => 'price',
                            'label' => 'Selling Price'
                          ]) ?>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-4">
                      <div class="col-md-6">
                        <div class="form-group">
                          <?= select_input([
                            'name' => 'parent_id',
                            'label' => 'Parent Category',
                          ],$categories) ?>
                        </div>
                      </div>
                    </div>

                    <div class="row g-2 mt-3">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 1</label>
                          <input type="file" name="photo_1" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 2</label>
                          <input type="file" name="photo_2" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 3</label>
                          <input type="file" name="photo_3" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 4</label>
                          <input type="file" name="photo_4" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 5</label>
                          <input type="file" name="photo_5" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="photo">Product Photo 6</label>
                          <input type="file" name="photo_6" accept=".jpg,.jpeg,.png" class="form-control">
                        </div>
                      </div>
                    </div>

                    <!-- description -->
                    <div class="row mt-4">
                      <div class="col-12">
                        <div class="form-group">
                          <label for="description">Description</label>
                          <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                      </div>
                      
                    </div>
                  </div>
                  <button class="btn btn-primary d-block w-100" type="submit"><i class="ci-cloud-upload fs-lg me-2"></i>Submit</button>
                </form>
              </div>
            </section>
        </div>
      </div>

<?php 
    require_once('files/footer.php');
?>
