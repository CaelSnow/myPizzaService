function add_warenkorb(pizza)
{

    "use strict"
    console.log("der pizza name ist: "+pizza[0]);
    //find form select
    const subList = document.getElementById("select_pizza");

    //create new element and text
    const newElement = document.createElement("option");
    const newText = document.createTextNode(pizza[1]);
    newElement.setAttribute("value", pizza[0]);
    newElement.setAttribute("data-preis", pizza[3]);

    console.log("der pizza name ist: "+pizza[0]);
    //append everything
    newElement.appendChild(newText);
    subList.appendChild(newElement);

    //calculate price after new pizza was added
    calculate_price(pizza);
}

function calculate_price(array_pizza)
{
    "use strict";
    //find current price
    let total_price = parseFloat(document.getElementById("price_id").textContent);
    //save price from array
    let unit_price = parseFloat(array_pizza[3]);
    //calculate full price
    total_price = total_price + unit_price;

    document.getElementById("price_id").textContent = total_price.toFixed(2);

}

function remove_element()
{
    "use strict";
    let pizzas = document.getElementById("select_pizza");

    for(let i = 0; i < pizzas.options.length; i++)
    {
        if(pizzas.options[i].selected == true)
        {
            //get price of current pizza
            let preis = parseFloat(pizzas.options[i].dataset.preis);
            //calculate new price
            let total_price = parseFloat(document.getElementById("price_id").textContent);
            total_price = total_price - preis;
            document.getElementById("price_id").textContent = total_price.toFixed(2);

            pizzas.options[i].remove();
            //move pointer since node was deleted
            i--;
        }
    }
    check_addr();
    check_min_elements();
}

function remove_all()
{
    "use strict";

    let pizzas = document.getElementById("select_pizza");
    for(let i = 0; i <  pizzas.options.length; i++)
    {
        pizzas.options[i].remove();
        i--;
    }
    //set price to zero after removing
    document.getElementById("price_id").textContent = 0.00.toFixed(2);
    check_addr();
    check_min_elements();
}

function check_formular()
{
    "use strict";
    if((document.getElementById("Adresse").value == "") || (document.getElementById("select_pizza").options.length < 1))
    {
        alert('Adresse muss ausgefüllt sein und mindestens eine Pizza im Warenkorb! ');
        return false;
    } else {
        select_all_products();
        return true;
    }


}
window.setInterval(check_addr,1000);
function check_addr()
{
    "use strict";
    let disabled = true;
    if( (document.getElementById("Adresse").value != "") &&
        (document.getElementById("select_pizza").options.length > 0))
    {
        disabled = false;
    }

    document.getElementById("submit").disabled = disabled;

}

function check_min_elements()
{
    "use strict";
    let disabled = true;
    if(document.getElementById("select_pizza".options.length >= 1))
    {
        disabled = false;
    }
    document.getElementById("submit").disabled = disabled;
}

function select_all_products()
{
    "use strict";
    let pizzas = document.getElementById("select_pizza");

    for(let i = 0; i < pizzas.options.length; i++)
    {
        pizzas.options[i].selected = true;
    }

}

