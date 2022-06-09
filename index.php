<?php
    //ini_set('display_errors',1);
    //ini_set('error_reporting', E_ALL);
    //error_reporting(-1);

    require __DIR__.'/db.connect.php';
    include_once('auth.php');
    include_once('logout.php');
    if(auth() == false)
    {
        $name = 'Guest';
    }
    else
    {
        $name = auth();
        session_start();
        $_SESSION['emailName'] = $name;
    }

    if(isset($_GET['logout']))
    {
        $result = logout();
    }

    $orders = showOrdersForUser($name);
    $ordersShow = '';
    if(strcmp("Guest",$name) != 0)
    {
      foreach ($orders as $value){
        $ordersShow .= '<span'
        .'<p> User Name = ' .$value['userName']. '</p>' 
          .'<p> Digest = ' .$value['digest']. '</p>' 
          .'<p> Payment Status = ' .$value['payment_status']. '</p>' 
        
        . '</span>';
    }
      error_log(print_r($orders,true));
    }


?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bob's shopping website</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="index.css?version=14" type="text/css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="shoppingCart.js?v=1"></script>
  <script src="index.js?v=10"></script>

</head>

<body>
    <!-- Include the PayPal JavaScript SDK; replace "test" with your own sandbox Business account app client ID -->
    <script src="https://www.paypal.com/sdk/js?client-id=AeOqVgSf-ubsOX7Kk2XiCEVlVgrRDgO2Biy0TCi6-xJbCcuUXNuYuVmSb_DDAISkDsJeHwnNzJ_Xw4Ej&currency=HKD"></script>
  <!--YOU CONTENT HERE!-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
        <div class="dropdown show" id="dropdown">
        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-text-left" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
          </svg>
        </a>

        
        <div id="dropdownList" class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        </div> 
        
      </div>

  </div>
 <div class="col-7">
   <div id="history">
    <p >Home</p>
  </div>
    <div class="shoppingtable">
    <div class="row">

      <div class="col">
        <a href="categories/category.php?category=Tissues&catid=1" ><img src="images/Tissues/ROLLTISSUEORIGINALFULLCASESINGLEROLL27S.jpg" class="img-thumbnail" alt="roll tissue image"></a>
        <a href="categories/category.php?category=Tissues&catid=1"><p class="goods_name">Toilet Roll</p></a>
 
 
       </div>
       <div class="col">
         <a href="categories/category.php?category=Tissues&catid=1"><img src="images/Tissues/SELECTFACIALTISSUE3PLYNEUTRAL5S.jpg" class="img-thumbnail" alt="facial tissue image"></a>
         <a href="categories/category.php?category=Tissues&catid=1" ><p class="goods_name">Facial Tissue</p></a>
 
       </div>
       <div class="col">
         <a href="categories/category.php?category=Tissues&catid=1"><img src="images/Tissues/kitchen_roll.jpg" class="img-thumbnail" alt="kitchen roll image"></a>
         <a href="categories/category.php?category=Tissues&catid=1" ><p class="goods_name">kitchen roll</p></a>
       </div>
       <div class="col">
         <a href="categories/category.php?category=Tissues&catid=1"><img src="images/Tissues/wet_wipes.jpg" class="img-thumbnail" alt="wet wipes image"></a>
         <a href="categories/category.php?category=Tissues&catid=1" ><p class="goods_name">Wet Wipes</p></a>
       </div>

    </div>

    <div class="row">
      <div class="col">
       <a href="#" ><img src="images/Rice,Oil,Noodles/rice.jpg" class="img-thumbnail" alt="rice image"></a>
       <p class="goods_name">Rice</p>

      </div>
      <div class="col">
        <a href="#"><img src="images/Rice,Oil,Noodles/oil.jpg" class="img-thumbnail" alt="Oil image"></a>
        <p class="goods_name">Oil</p>

      </div>
      <div class="col">
        <a href="#"><img src="images/Rice,Oil,Noodles/noodle.jpg" class="img-thumbnail" alt="noodle image"></a>
        <p class="goods_name">Noodles</p>
      </div>
    </div>

    <div class="row">
      <div class="col">
       <a href="#" ><img src="images/Snacks/biscuit.jpg" class="img-thumbnail" alt="Biscuit image"></a>
       <p class="goods_name">Biscuits</p>

      </div>
      <div class="col">
        <a href="#"><img src="images/Snacks/chocolates.jpg" class="img-thumbnail" alt="Chocolates image"></a>
        <p class="goods_name">Chocolates</p>

      </div>
      <div class="col">
        <a href="#"><img src="images/Snacks/crisps.jpg" class="img-thumbnail" alt="Crisps image"></a>
        <p class="goods_name">Crisps</p>
      </div>
      <div class="col">
        <a href="#"><img src="images/Snacks/nuts.jpg" class="img-thumbnail" alt="nuts image"></a>
        <p class="goods_name">Nuts</p>
      </div>
    </div>

    <div class="row">
      <div class="col">
       <a href="#" ><img src="images/Beverages/juice.jpg" class="img-thumbnail" alt="Juice image"></a>
       <p class="goods_name">Juice</p>

      </div>
      <div class="col">
        <a href="#"><img src="images/Beverages/energy_drink.jpg" class="img-thumbnail" alt="Energy drink image"></a>
        <p class="goods_name">Energy Drinks</p>


    </div>

    <div class="row">
      <div class="col">
      <?php echo $ordersShow; ?>
      </div>
    </div>

  </div>
</div>

</div>

<div class="col-2" > 
  <div id="userEmail">Hello, <?php echo $name;?>
  <div id="loginButton"><a href='login.php' class="btn btn-light" >Login</a></div>
  <a href='index.php?logout=true' class="btn btn-light" >Logout</a>
  <div id="paypal-button-container"></div>
</div>
  
  <div id="shoppingCart">
    ShoppingList $0
    </div>
    <div id="shoppingList">
       <p>ShoppingList (Total:$0)</p>
       
    </div> 
  
  </div>

</div>
</div>



<footer class="container-fluid">
  <div class="footarea">
    <p> End of Bob's Website</p>

  </div>
</footer>


<script>
window.onload = function ()
{
  loadshoppingCart();
  loadCategoriesList();
}



paypal.Buttons({

      style: {
        layout: 'horizontal',
        color: 'black',
        shape: 'pill',
        label: 'checkout'
      },
      /* Sets up the transaction when a payment button is clicked */
      createOrder: async (data, actions) => { /* async is required to use await in a function */
        /* Use AJAX to get required data from the server; For dev/demo purposes: */
        //let custom_idAndInvoice_id = await generateDigest();
        //console.log("return is " + custom_idAndInvoice_id);
        let order_details = await getFromServer()
          .then(data => JSON.parse(data));

        /* Use fetch() instead in real code to get server resources */
        // let order_details = await fetch(/* resource url*/)
        //     .then(response => response.json()) /* json string to javascript object */
        //     .then(data => {
        //         /* process over data */
        //         return /* return value */;
        //     });

        return actions.order.create(order_details);
      },

      /* Finalize the transaction after payer approval */
      onApprove: (data, actions) => {
        return actions.order.capture().then(function (orderData) {
          /* Successful capture! For dev/demo purposes: */
          //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
          const transaction = orderData.purchase_units[0].payments.captures[0];
          //alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
          console.log("Success");
          //header('index.php');
          //exit();
          /* When ready to go live, remove the alert and show a success message within this page. For example: */
          // const element = document.getElementById('paypal-button-container');
          // element.innerHTML = '<h3>Thank you for your payment!</h3>';
          /* Or go to another URL:  */
          // actions.redirect('thank_you.html');
        });
      },
    }).render('#paypal-button-container');

</script>

</body>
</html>
