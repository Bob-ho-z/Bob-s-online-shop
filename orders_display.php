<?php
    ini_set('display_errors',1);
    ini_set('error_reporting', E_ALL);
    error_reporting(-1);
    include_once('auth.php');
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

    $invoice = $_GET['invoice'];
    $digest = $_GET['digest'];
    $userName = $_GET['userName'];
    $paymentStatus = $_GET['paymentStatus']

    

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
        <h2 class="header text-center">Bob's Online shop Admin Panel Order Display</h2>
        <div class="text-center" >Hello, <?php echo $cookie['em']; ?></div>
        <div class="text-center">
        <a href='admin_panel.php?logout=true' class="btn btn-light">Logout</a>
        </div>
    </header>
        <!-- Display orders-->
     
        <div class="container control_panel">
        <div class="row">
          <div class="col">
            <div class="col control_panel_form">
                <fieldset>
                    <legend>Orders Display</legend>
                    <form id="order_display" method="POST"
                    enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="control_panel_from_row">
                                <label for="invoiceDisplay" class="col-sm-2 col-form-label">Orders</label>
                                <div class="col-sm-10">
                                <input class="form-control" id="invoiceDisplay" type="text" placeholder=" <?php echo $invoice ?>" disabled>
                                </div>
                            </div> 

                            <div class="control_panel_from_row">
                            <label for="digestDisplay" class="form-label">Digest</label>
                                <div class="col-sm-10">
                                    
                                    <input class="form-control" id="digestDisplay" type="text" placeholder="<?php echo $digest ?>" disabled>
                                </div>
                            </div> 

                            <div class="control_panel_from_row">
                            <label for="userName" class="form-label">User Name</label>
                                <div class="col-sm-10">
                                    
                                    <input class="form-control" id="UserName" type="text" placeholder="<?php echo $userName ?>" disabled>
                                </div>
                            </div>

                            <div class="control_panel_from_row">
                            <label for="paymentStatus" class="form-label">Payment Status</label>
                                <div class="col-sm-10">  
                                    <input class="form-control" id="paymentStatus" type="text" placeholder="<?php echo $paymentStatus ?>" disabled>
                                </div>
                            </div>

                                </div>
                            </div> 

                        </div> 
                            
                    </form>
                </fieldset>
              </div>
          </div>
        </div>
    </div>
    

  </body>
</html>