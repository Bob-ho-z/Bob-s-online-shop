function loadshoppingCart()
{
    const header = document.getElementById("shoppingCart");
    const list = document.getElementById("shoppingList");
    let AllTotalPrice = 0;
    let headerpart = '';
    let listContent = '';
    let num = 1;
    let requests = new Array(localStorage.length);
    for(let i=0;i<localStorage.length;i++)
    {
        let getPid = localStorage.key(i);
        if(isNaN(getPid))
        {
          continue;
        }
        num = JSON.parse(localStorage.getItem(getPid));
        requests[i]=new XMLHttpRequest();
        requests[i].open("POST","../admin_process.php?action=user_product",true)
        requests[i].setRequestHeader("Content-type","application/x-www-form-urlencoded");

        requests[i].onload = function()
        {
            let response = this.responseText.replace('while(1);','');
            let result = JSON.parse(response);
            let product = [result['success'][0]['name'],result['success'][0]['price']];
            AllTotalPrice =  AllTotalPrice + localStorage.getItem(localStorage.key(i))*product[1];
            AllTotalPrice = Math.round(AllTotalPrice * 10) / 10;
            listContent = listContent + "<p style='font-size:15px'>" + product[0] + " "  +  "<input type='number' min='0' id='pid" + localStorage.key(i) +"' value='" + localStorage.getItem(localStorage.key(i)) + "'>  " + "<button type='button' class='btn btn-outline-primary' onclick='addAtCart(" + localStorage.key(i) + ")'>Confirm</button>" + "@" + product[1] + "</p>";
            headerpart = "<h5>Shopping List (Total:$" + AllTotalPrice + ")</h5>";
            header.innerHTML = headerpart;
            list.innerHTML = listContent;
        }
        let data="pid="+getPid;
        requests[i].send(data);
        

    }

}

function loadCategoriesList()
{
  const dropDownPart = document.getElementById("dropdownList");
  let list = new XMLHttpRequest();
  list.open("POST","../admin_process.php?action=fetch_all_categories",true);
  list.setRequestHeader("Content-type","application/x-www-form-urlencoded");

  list.onload = function()
  {
    let response = this.responseText.replace('while(1);','');
    let result = JSON.parse(response); 
    let length = result['success'].length;
    let dropdown = '';
    let link='';
    for(let i=0;i<length;i++)
    {
      link = '';
      link = '<a class="dropdown-item" href="category.php?category=' + result['success'][i]['name'] + '&catid=' + result['success'][i]['catid'] + '"> ' + result['success'][i]['name'] + ' </a>';
      dropdown = dropdown + link; 
      
    }
    dropDownPart.innerHTML = dropdown;
  }
  list.send();

}

function loadnavigationMenu()
{
  const navigationMenu = document.getElementById("navigationMenu");
  let url=new URL(window.location.href);
  let category = url.searchParams.get("category");
  navigationMenu.innerHTML = '<a href="../index.php">Home</a> > ' + category;

}

function loadshoppingTable()
{
  const shoppingTable = document.getElementById("shoppingTable");
  let url=new URL(window.location.href);
  let catid = url.searchParams.get("catid");
  let category = url.searchParams.get("category");
  let table = new XMLHttpRequest();
  table.open("POST","../admin_process.php?action=fetch_all_products_for_one_catid",true);
  table.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  table.onload = function()
  {

    let response = this.responseText.replace('while(1);','');
    let result = JSON.parse(response);
    let length = result['success'].length;
    let tableContent = '';
    for(let i=0;i<length;i++)
    {
      if(i%3==0)
      {
        tableContent = tableContent + '<div class="row">';
      }
      tableContent = tableContent + 
      '<div class="col">'+
        '<a href="../products/product.php?pid=' + result['success'][i]['pid'] + '&category=' + category + '&name=' + result['success'][i]['name'] + '&catid=' + catid +'" ><img src="../images/' + category + '/' + result['success'][i]['name'].replaceAll(/[^a-zA-Z\d]/g, "").replaceAll(" ","_") + '.jpg" class="img-thumbnail" alt="tissue image"></a>' +
        '<div class="goods_name">' + 
        '<p>'+result['success'][i]['name'] + '</p>' + 
        '<p> HK$' + result['success'][i]['price'] + '</p>' + 
        '<button type="button" class="btn btn-primary" onclick="addProductToCart(' + result['success'][i]['pid'] + ')">' +
          '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">' + 
            '<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>' + 
          '</svg> '+
          'Add to Cart' +
       '</button>' +
        '</div>' +
      '</div>';

      if(i%3==2)
      {
        tableContent = tableContent + '</div>';
      }


    }
    shoppingTable.innerHTML = tableContent;
  }
  let data="catid=" + catid;
  table.send(data);

}


function getFromServer()
{
  return new Promise(resolve => {

    //let returnArray = generateDigest();

    let list = new XMLHttpRequest();
    list.open("POST","../admin_process.php?action=generateDigest",true);
    list.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  
    list.onload = function()
    {
      let response = this.responseText.replace('while(1);','');
      //console.log(response);
      let result = JSON.parse(response); 
      let returnArray = {};
      //console.log(result['success']['custom_id']);
      returnArray['custom_id'] = result['success']['custom_id'];
      returnArray['invoice_id'] = result['success']['invoice_id'];
      //console.log(returnArray);
      //return handleCreateOrder(returnArray);
      //return returnArray;

      //console.log("return array is " + returnArray);

      let requests = new Array(localStorage.length);
      let products = [];
      let index = 0;
      let AllTotalPrice = 0;
      //console.log("Step 1");
  
      for(let i=0;i<localStorage.length;i++)
      {
          let getPid = localStorage.key(i);
          if( isNaN(getPid))
          {
            if(i==localStorage.length-1)
            {
              let itemsBefore = [];
              for(let i=0; i<products.length; i++)
              {
                ItemValue = parseFloat(products[i][1]).toFixed(1) 
                ItemValue = Number(ItemValue);
                let item = 
                {
                  name: products[i][0],
                  unit_amount: 
                  {
                    currency_code: 'HKD',
                    value: ItemValue
                  },
                  quantity: Number(products[i][2])
                }
                itemsBefore.push(item);
              }
              //console.log("itenm are ");
              //console.log(itemsBefore);
              //console.log("Step 6");
              setTimeout(() => {
                resolve(JSON.stringify({
                  purchase_units: [{
                    amount: { currency_code: 'HKD', value: Number(parseFloat(AllTotalPrice).toFixed(1)), breakdown: { item_total: { currency_code: 'HKD', value: Number(parseFloat(AllTotalPrice).toFixed(1)) } } },
                    items: itemsBefore  
                  }]
                }));
              }, 100);
            }
                
            continue;
          }
          num = JSON.parse(localStorage.getItem(getPid));
          requests[i]=new XMLHttpRequest();
          requests[i].open("POST","../admin_process.php?action=user_product",true)
          requests[i].setRequestHeader("Content-type","application/x-www-form-urlencoded");
          //console.log("Step 2");
          requests[i].onload = function()
          {
              let response = this.responseText.replace('while(1);','');
              let result = JSON.parse(response);
              products[index] = [result['success'][0]['name'],result['success'][0]['price'],localStorage.getItem(localStorage.key(i))];
              AllTotalPrice =  AllTotalPrice + localStorage.getItem(localStorage.key(i))*products[index][1];
              AllTotalPrice = Math.round(AllTotalPrice * 10) / 10;
              index++;
              //console.log("Step 3");
              if(i==localStorage.length-1)
              {
                //console.log("Step 4");
                //console.log("Step 5");
                let itemsBefore = [];
                for(let i=0; i<products.length; i++)
                {
                  ItemValue = parseFloat(products[i][1]).toFixed(1) 
                  ItemValue = Number(ItemValue);
                  let item = 
                  {
                    name: products[i][0],
                    unit_amount: 
                    {
                      currency_code: 'HKD',
                      value: ItemValue
                    },
                    quantity: Number(products[i][2])
                  }
                  itemsBefore.push(item);
                }
                //console.log("itenm are ");
                //console.log(itemsBefore);
                //console.log("Step 6");
                
                // clear the shopping Cart

                // for(let j = 0;j<localStorage.length;j++)
                // {
                //   let key = localStorage.key(j);
                //   if(!isNaN(key))
                //   {
                //     localStorage.removeItem(key);
                //   }
                // }
                localStorage.clear();

                setTimeout(() => {
                  resolve(JSON.stringify({
                    purchase_units: [{
                      custom_id: returnArray['custom_id'],
                      invoice_id: returnArray['invoice_id'],
                      amount: { currency_code: 'HKD', value: Number(parseFloat(AllTotalPrice).toFixed(1)), breakdown: { item_total: { currency_code: 'HKD', value: Number(parseFloat(AllTotalPrice).toFixed(1)) } } },
                      items: itemsBefore  
                    }]
                  }));
                }, 100);
              }             
          }
          let data="pid="+getPid;
          requests[i].send(data);
      }
  
  
    }
    let data = "";
    let indexForData = 0;
    for(let i=0;i<localStorage.length;i++)
    {
      let pid = localStorage.key(i);
      if(!isNaN(pid))
      {
        if(indexForData!=0)
        {
          data = data + "&";
        }
        data = data + "pid_" + indexForData + "=" + pid;
        data = data + "&qua_" + indexForData + "=" + JSON.parse(localStorage.getItem(pid));
        indexForData++;
      }
    }
    data = data + "&indexForData=" + indexForData;
    //console.log("data is "+ data);
  
    list.send(data);




  });

}