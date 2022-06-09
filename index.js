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
        requests[i].open("POST","admin_process.php?action=user_product",true)
        requests[i].setRequestHeader("Content-type","application/x-www-form-urlencoded");

        requests[i].onload = function()
        {
            let response = this.responseText.replace('while(1);','');
            let result = JSON.parse(response);
            //console.log(response);
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
  list.open("POST","admin_process.php?action=fetch_all_categories",true);
  list.setRequestHeader("Content-type","application/x-www-form-urlencoded");

  list.onload = function()
  {
    let response = this.responseText.replace('while(1);','');
    //console.log(response);
    let result = JSON.parse(response); 
    let length = result['success'].length;
    let dropdown = '';
    let link='';
    for(let i=0;i<length;i++)
    {
      link = '';
      link = '<a class="dropdown-item" href="categories/category.php?category=' + result['success'][i]['name'] + '&catid=' + result['success'][i]['catid'] + '"> ' + result['success'][i]['name'] + ' </a>';
      dropdown = dropdown + link; 
      
    }
    dropDownPart.innerHTML = dropdown;
  }
  list.send();

}





function getFromServer()
{
  return new Promise(resolve => {

    //let returnArray = generateDigest();

    let list = new XMLHttpRequest();
    list.open("POST","admin_process.php?action=generateDigest",true);
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
          requests[i].open("POST","admin_process.php?action=user_product",true)
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

// function getFromServer()
// {
//   //console.log("Test for handles");
//   let list = new XMLHttpRequest();
//   list.open("POST","../admin_process.php?action=generateDigest",true);
//   list.setRequestHeader("Content-type","application/x-www-form-urlencoded");

//   list.onload = function()
//   {
//     let response = this.responseText.replace('while(1);','');
//     console.log(response);
//     let result = JSON.parse(response); 
//     let returnArray = {};
//     //console.log(result['success']['custom_id']);
//     returnArray['custom_id'] = result['success']['custom_id'];
//     returnArray['invoice_id'] = result['success']['invoice_id'];
//     console.log(returnArray);
//     return handleCreateOrder(returnArray);
//     //return returnArray;


//   }
//   let data = "";
//   let indexForData = 0;
//   for(let i=0;i<localStorage.length;i++)
//   {
//     let pid = localStorage.key(i);
//     if(!isNaN(pid))
//     {
//       if(indexForData!=0)
//       {
//         data = data + "&";
//       }
//       data = data + "pid_" + indexForData + "=" + pid;
//       data = data + "&qua_" + indexForData + "=" + JSON.parse(localStorage.getItem(pid));
//       indexForData++;
//     }
//   }
//   data = data + "&indexForData=" + indexForData;
//   //console.log("data is "+ data);

//   list.send(data);




// }