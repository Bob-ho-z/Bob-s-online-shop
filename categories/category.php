<?php
      ini_set('display_errors',1);
      ini_set('error_reporting', E_ALL);
      error_reporting(-1);
  
      include_once('auth.php');
      include_once('../logout.php');
      if(auth() == false)
      {
          $name = 'Guest';
      }
      else
      {
          $name = auth();
      }
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bob's shopping website</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="categories.css?version=5" type="text/css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="../shoppingCart.js?v=5"></script>
  <script src="category.js?v=5"></script>


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
    <p id="navigationMenu"></p>
  </div>
    <div class="shoppingtable" id="shoppingTable">
    </div>



    </div>
      

    <div class="col-2 shopping" id="shoppingCartArea"> 
    <div id="userEmail">Hello, <?php echo $name;?></div>
    <div id="loginButton"><a href='../login.php' class="btn btn-light" >Login</a></div>
      <a href='../index.php?logout=true' class="btn btn-light" >Logout</a>
      <!--<button type="button" class="btn btn-secondary" > -->
      <div id="paypal-button-container"></div>
      <div id="shoppingCart">
      ShoppingList $0
      </div>
      <div id="shoppingList">
        <p>ShoppingList (Total:$0)</p>
        
      </div> 
      <!--</button> -->

    </div>
  </div>
</div>

<footer class="container-fluid">
  <div class="footarea">
    <p> End</p>

  </div>
</footer>

<script>
  window.onload = function()
  {
    loadshoppingCart();
    loadCategoriesList();
    loadnavigationMenu();
    loadshoppingTable();

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
