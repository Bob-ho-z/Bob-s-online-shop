<?php
    ini_set('display_errors',1);
    ini_set('error_reporting', E_ALL);
    error_reporting(-1);
    require __DIR__.'/db.connect.php';
    //require __DIR__.'/auth-process.php';
    include_once('logout.php');
    include_once('auth.php');
    include_once('CSRF.php');
    if(auth() == false)
    {
        header("Location: login.php");
		exit();
    }
    $cookie = json_decode(stripslashes($_COOKIE['auth']),true);
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['emailName'] = $cookie['em'];
    if($cookie['admin'] != 1)
    {
        header("Location: index.php");
		exit();
    }

    $categories = db_fetch_all_categories();
    $options = '';

    foreach ($categories as $value){
        $options .= '<option value="'.$value["catid"].'"> '.$value["name"].' </option>';
    }

    $products = db_fetch_all_products();
    $productsOptions = '';

    foreach ($products as $value){
        $productsOptions .= '<option value="'.$value["pid"].'"> '.$value["name"].' </option>';
    }



    $orders = db_fetch_all_orders();
    $ordersOptions = '';
    foreach ($orders as $value){
        $ordersOptions .= '<option value="'.$value["invoice"].'"> '.$value["invoice"].' </option>';
    }
    if(isset($_GET['logout']))
    {
        $result = logout();
    }

?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="admin_panel.css?v=<?php echo time(); ?>" type="text/css">
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="admin_panel.js?v=5 "></script>

    <title>Bob's Website Admin Panel</title>
  </head>
  <body>
    <header> 
        <h2 class="header text-center">Bob's Online shop Admin Panel</h2>
        <div class="text-center" >Hello, <?php echo $cookie['em']; ?></div>
        <div class="text-center">
        <a href='admin_panel.php?logout=true' class="btn btn-light">Logout</a>
        </div>
    </header>
    <!-- insert new control panel-->
    <div class="container control_panel">
        <div class="row">
          <div class="col control_panel_form">
            <fieldset>
                <legend>Add New Category</legend>
                <form id="category_insert" method="POST" action="admin_process.php?action=<?php echo ($action = 'category_insert');?>"
                enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="inputCategory" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputCategory" required placeholder="Please Enter new Category Name" name="name">
                        </div>
                    </div>
                    <input class="submitButton" type="submit" value="Insert" />
                    <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />
                </form>
            </fieldset>
          </div>
          <div class="col">
            <div class="col control_panel_form">
                <fieldset>
                    <legend>Add New Product</legend>
                    <form id="product_insert" method="POST" action="admin_process.php?action=<?php echo ($action = 'product_insert');?>"
                    enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="control_panel_from_row">
                                <label for="selectCategory" class="col-sm-2 col-form-label">Category*</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="selectCategory" name="catid">
                                        <?php echo $options; ?>
                                    </select>
                                </div>
                            </div>
                    
                            <div class="control_panel_from_row">
                                <label for="inputCategory" class="col-sm-2 col-form-label">Name *</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputCategory" required="true" name="name" placeholder="Please Enter new Category Name">
                                </div>
                            </div>
                        
                            <div class="control_panel_from_row">
                                <label for="inputPrice" class="col-sm-2 col-form-label">Price *</label>
                                <div class="col-sm-10">
                                <input type="number" class="form-control" id="inputPrice" required="true" name="price" placeholder="Please Enter Price" step="0.1" min="0">
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                                <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                <textarea class="form-control" id="inputDescription" name="description" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                                <label for="uploadImage" class="col-sm-2 col-form-label">Image *</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" name="image" accept="image/png, image/jpg, image/jpeg, image/gif" id="uploadImage">
                                </div>
                            </div> 
                            <div id="thumbnail"></div> 

                        </div>
                        <input class="submitButton" type="submit" value="Insert" name="submit" />
                        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />   
                            
                    </form>
                </fieldset>
              </div>
          </div>
          
        </div>
      </div>


    <!-- update control panel-->
    <div class="container control_panel">
        <div class="row">
          <div class="col control_panel_form">
            <fieldset>
                <legend>Update Category</legend>
                <form id="category_update" method="POST" action="admin_process.php?action=<?php echo ($action = 'category_update');?>"
                enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="selectOldCategory" class="col-sm-2 col-form-label">Old Category Name</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="selectOldCategory" name="catid">
                                <?php echo $options; ?>
                            </select>
                        </div>

                        <label for="inputNewCategory" class="col-sm-2 col-form-label">New Category Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="inputNewCategory" required placeholder="Please Enter new Category Name" name="newName">
                        </div>
                    </div>
                    <input class="submitButton" type="submit" value="Update" />
                    <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" /> 
                </form>
            </fieldset>
          </div>
          <div class="col">
            <div class="col control_panel_form">
                <fieldset>
                    <legend>Update Product</legend>
                    <form id="product_update" method="POST" action="admin_process.php?action=<?php echo ($action = 'product_update');?>"
                    enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="control_panel_from_row">
                                <label for="selectProduct" class="col-sm-2 col-form-label">Product</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="selectProduct" name="pid">
                                        <?php echo $productsOptions; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                                <label for="selectOldCategory" class="col-sm-2 col-form-label">New Category</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="selectOldCategory" name="catid">
                                        <?php echo $options; ?>
                                    </select>
                                </div>
                            </div>

                    
                            <div class="control_panel_from_row">
                                <label for="inputNewName" class="col-sm-2 col-form-label">New Name *</label>
                                <div class="col-sm-10">
                                <input type="text" class="form-control" id="inputNewName" required="true" name="name" placeholder="Please Enter new Category Name">
                                </div>
                            </div>

                        
                            <div class="control_panel_from_row">
                                <label for="inputPrice" class="col-sm-2 col-form-label">New Price *</label>
                                <div class="col-sm-10">
                                <input type="number" class="form-control" id="inputPrice" required="true" name="price" placeholder="Please Enter Price" step="0.1" min="0">
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                                <label for="inputDescription" class="col-sm-2 col-form-label">New Description</label>
                                <div class="col-sm-10">
                                <textarea class="form-control" id="inputDescription" name="description" rows="5"></textarea>
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                                <label for="uploadImage" class="col-sm-2 col-form-label">New Image *</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" name="image" accept="image/png, image/jpg, image/jpeg, image/gif" id="uploadImage">
                                </div>
                            </div>  

                        </div>
                        <input class="submitButton" type="submit" value="Update" name="submit" />
                        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />
                            
                    </form>
                </fieldset>
              </div>
          </div>
        </div>
    </div>



    <!-- delete panel-->
     
    <div class="container control_panel">
        <div class="row">
          <div class="col control_panel_form">
            <fieldset>
                <legend>Delete Category</legend>
                <form id="category_delete" method="POST" action="admin_process.php?action=<?php echo ($action = 'category_delete');?>"
                enctype="multipart/form-data">
                    <div class="form-group row">
                        <label for="selectCategory" class="col-sm-2 col-form-label"> Category</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="selectCategory" name="catid">
                                <?php echo $options; ?>
                            </select>
                        </div>
                    </div>
                    <input class="submitButton" type="submit" value="Delete" />
                    <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />
                </form>
            </fieldset>
          </div>
          <div class="col">
            <div class="col control_panel_form">
                <fieldset>
                    <legend>Delete Product</legend>
                    <form id="product_delete" method="POST" action="admin_process.php?action=<?php echo ($action = 'product_delete');?>"
                    enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="control_panel_from_row">
                                <label for="selectProduct" class="col-sm-2 col-form-label">Product</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="selectProduct" name="pid">
                                        <?php echo $productsOptions; ?>
                                    </select>
                                </div>
                            </div> 

                        </div>
                        <input class="submitButton" type="submit" value="Delete" name="submit" />
                        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />   
                            
                    </form>
                </fieldset>
              </div>
          </div>
        </div>
    </div>



    


        <!-- Display orders-->
     
        <div class="container control_panel">
        <div class="row">
          <div class="col">
            <div class="col control_panel_form">
                <fieldset>
                    <legend>Orders Display</legend>
                    <form id="order_display" method="POST" onsubmit="return order_display()"
                    enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="control_panel_from_row">
                                <label for="selectOrder" class="col-sm-2 col-form-label">Orders</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="selectOrder" name="invoice">
                                        <?php echo $ordersOptions; ?>
                                    </select>
                                </div>
                            </div> 

                            <!-- <div class="control_panel_from_row">
                            <label for="digestDisplay" class="form-label">Digest</label>
                                <div class="col-sm-10">
                                    
                                    <input class="form-control" id="digestDisplay" type="text" placeholder="" disabled>
                                </div>
                            </div> 

                            <div class="control_panel_from_row">
                            <label for="userName" class="form-label">User Name</label>
                                <div class="col-sm-10">
                                    
                                    <input class="form-control" id="UserName" type="text" placeholder="" disabled>
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                            <label for="paymentStatus" class="form-label">Payment Status</label>
                                <div class="col-sm-10">  
                                    <input class="form-control" id="paymentStatus" type="text" placeholder="" disabled>
                                </div>
                            </div> -->

                                </div>
                            </div> 

                        </div>
                        <input class="submitButton" type="submit" value="submit" name="submit" />
                        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>" />   
                            
                    </form>
                </fieldset>
              </div>
          </div>
        </div>
    </div>
    

  </body>
</html>