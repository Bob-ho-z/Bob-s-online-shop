function addProductToCart(pid)
{
    const header = document.getElementById("shoppingCart");
    const list = document.getElementById("shoppingList");
    let AllTotalPrice = 0;
    let headerpart = '';
    let listContent = '';
    let num = 1;

    if(localStorage.getItem(pid) !== null)
    {
        num = JSON.parse(localStorage.getItem(pid));
        num = num + 1;
    }
    localStorage.setItem(pid,JSON.stringify(num));
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

function addAtCart(pid)
{
    const header = document.getElementById("shoppingCart");
    const list = document.getElementById("shoppingList");
    let AllTotalPrice = 0;
    let headerpart = '';
    let listContent = '';
    let num = 1;
    num = parseInt(document.getElementById("pid"+pid).value);
    if(num<0)
    {
        num = 0;
    }
    localStorage.setItem(pid,JSON.stringify(num));
    let requests = new Array(localStorage.length);
    for(let i=0;i<localStorage.length;i++)
    {
        let getPid = localStorage.key(i);
        if(isNaN(getPid))
        {
          continue;
        }
        num = JSON.parse(localStorage.getItem(getPid));
        if(num==0)
        {
            localStorage.removeItem(getPid);
            continue;
        }
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

function addAtProductPage(pid)
{
    const header = document.getElementById("shoppingCart");
    const list = document.getElementById("shoppingList");
    let productNum = document.getElementById("productNumber").value;
    productNum = parseInt(productNum);
    if(productNum<0)
    {
        productNum = 0;
    }
    let AllTotalPrice = 0;
    let headerpart = '';
    let listContent = '';
    let num = productNum;

    if(localStorage.getItem(pid) !== null)
    {
        num = JSON.parse(localStorage.getItem(pid));
        num = num + productNum;
        
    }
    localStorage.setItem(pid,JSON.stringify(num));
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
        requests[i].open("POST","../../admin_process.php?action=user_product",true)
        requests[i].setRequestHeader("Content-type","application/x-www-form-urlencoded");

        requests[i].onload = function()
        {
            let response = this.responseText.replace('while(1);','');
            let result = JSON.parse(response);
            let product = [result['success'][0]['name'],result['success'][0]['price']];
            AllTotalPrice =  AllTotalPrice + localStorage.getItem(localStorage.key(i))*product[1];
            AllTotalPrice = Math.round(AllTotalPrice * 10) / 10;
            listContent = listContent + "<p style='font-size:15px'>" + product[0] + " "  +  "<input type='number' min='0' id='pid" + localStorage.key(i) +"' value='" + localStorage.getItem(localStorage.key(i)) + "'>  " + "<button type='button' class='btn btn-outline-primary' onclick='addAtProductCart(" + localStorage.key(i) + ")'>Confirm</button>" + "@" + product[1] + "</p>";
            headerpart = "<h5>Shopping List (Total:$" + AllTotalPrice + ")</h5>";
            header.innerHTML = headerpart;
            list.innerHTML = listContent;
        }
        let data="pid="+getPid;
        requests[i].send(data);
        

    }
}

function addAtProductCart(pid)
{
    const header = document.getElementById("shoppingCart");
    const list = document.getElementById("shoppingList");
    let AllTotalPrice = 0;
    let headerpart = '';
    let listContent = '';
    let num = 1;
    num = parseInt(document.getElementById("pid"+pid).value);
    if(num<0)
    {
        num = 0;
    }
    localStorage.setItem(pid,JSON.stringify(num));
    let requests = new Array(localStorage.length);
    for(let i=0;i<localStorage.length;i++)
    {
        let getPid = localStorage.key(i);
        if(isNaN(getPid))
        {
          continue;
        }
        num = JSON.parse(localStorage.getItem(getPid));

        if(num==0)
        {
            localStorage.removeItem(getPid);
            continue;
        }
        requests[i]=new XMLHttpRequest();
        requests[i].open("POST","../../admin_process.php?action=user_product",true)
        requests[i].setRequestHeader("Content-type","application/x-www-form-urlencoded");

        requests[i].onload = function()
        {
            let response = this.responseText.replace('while(1);','');
            let result = JSON.parse(response);
            let product = [result['success'][0]['name'],result['success'][0]['price']];
            AllTotalPrice =  AllTotalPrice + localStorage.getItem(localStorage.key(i))*product[1];
            AllTotalPrice = Math.round(AllTotalPrice * 10) / 10;
            listContent = listContent + "<p style='font-size:15px'>" + product[0] + " "  +  "<input type='number' min='0' id='pid" + localStorage.key(i) +"' value='" + localStorage.getItem(localStorage.key(i)) + "'>  " + "<button type='button' class='btn btn-outline-primary' onclick='addAtProductCart(" + localStorage.key(i) + ")'>Confirm</button>" + "@" + product[1] + "</p>";
            headerpart = "<h5>Shopping List (Total:$" + AllTotalPrice + ")</h5>";
            header.innerHTML = headerpart;
            list.innerHTML = listContent;
        }
        let data="pid="+getPid;
        requests[i].send(data);
        

    }
}