jQuery(function($){
    let fileInput = document.getElementById("uploadImage");
    //console.log(fileInput);
    fileInput.addEventListener("change",function(e){
      let file = this.file
      showThumbnail(file)
    },false)
    
    
    function showThumbnail(file){
        let imageType = /image.*/
        if(!file.type.match(imageType)){
          console.log("Not an Image");
        }
    
        let image = document.createElement("img");
        let thumbnail = document.getElementById("thumbnail");
        image.file = file;
        thumbnail.appendChild(image)
    
        let reader = new FileReader()
        reader.onload = (function(aImg){
          return function(e){
            aImg.src = e.target.result;
          };
        }(image))
        let ret = reader.readAsDataURL(file);
        let canvas = document.createElement("canvas");
        ctx = canvas.getContext("2d");
        image.onload= function(){
          ctx.drawImage(image,100,100)
        }
      
    }
              });



function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

function order_display()
{
  //console.log("Test");

  const invoice = document.getElementById("selectOrder").value;
  // const digest  = document.getElementById("digestDisplay");
  // const userName  = document.getElementById("userName");
  // const paymentStatus  = document.getElementById("paymentStatus");

  //console.log("invoice is "+ invoice.value);
  let getOrder = new XMLHttpRequest();
  getOrder.open("POST","admin_process.php?action=order_display",true);
  getOrder.setRequestHeader("Content-type","application/x-www-form-urlencoded");

  getOrder.onload = function()
  {
    let response = this.responseText.replace('while(1);','');
    let result = JSON.parse(response);
    // digest.placeholder = result['success'][0]['digest'];
    // userName.placeholder = result['success'][0]['userName'];
    // paymentStatus.placeholder = result['success'][0]['payment_status'];
    // url = "https://secure.s29.ierg4210.ie.cuhk.edu.hk/orders_display.php?invoice="+ invoice + "&digest=" + result[0]['digest']
    // + "$userName="  +   result['success'][0]['userName'] +  "&paymentStatus=" + result['success'][0]['payment_status'];
    // window.location.href(url);
    //url = "https://secure.s29.ierg4210.ie.cuhk.edu.hk/orders_display.php"
    //window.location.href("https://secure.s29.ierg4210.ie.cuhk.edu.hk");
    console.log("You should not be here");

    
    
    
    // while(1)
    // {
      
    // }
  }

  let data = "invoice="  + invoice;
  //console.log(data);
  getOrder.send(data);
}

/*function Logout()
{
  let result = "<?php  logout(); ?>"
  //console.log("Test");
  console.log(result);
}*/