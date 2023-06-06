// request als globale Variable anlegen (haesslich, aber bequem)
var request = new XMLHttpRequest();
document.getElementById("body")[0].onload = init;
function init()
{
    "use strict";
    //requestData();
    window.setInterval(requestData, 2000);
}

function process(jString)
{
    "use strict";

    let container = document.getElementsByClassName("container")[0];
    if(container != null)
    {
        container.remove();
    }
    let order = JSON.parse(jString);
    console.log(order);
    order.forEach((e) => {
        console.log(e.status);
        let status = document.getElementById("status" + e.ordered_article_id);
        let statusStr = "";
        switch (parseInt(e.status)) {
            case 0:
                statusStr = "Bestellt";
                break;
            case 1:
                statusStr = "Im Ofen";
                break;
            case 2:
                statusStr = "Fertig";
                break;
            case 3:
                statusStr = "Unterwegs";
                break;
            case 4:
                statusStr = "Geliefert";
                break;
            default:
                statusStr = "No Status";
        }
        status.textContent = statusStr;
    });

}



function requestData()
{ // fordert die Daten asynchron an
    "use strict";
    request.open("GET", "KundeStatus.php"); // URL für HTTP-GET
    request.onreadystatechange = processData; //Callback-Handler zuordnen
    request.send(null); // Request abschicken
}

function processData() {
    "use strict";
    if(request.readyState == 4) { // Uebertragung = DONE
        if (request.status == 200) {   // HTTP-Status = OK
            if(request.responseText != null)
                process(request.responseText);// Daten verarbeiten
            else console.error ("Dokument ist leer");
        }
        else console.error ("Uebertragung fehlgeschlagen");
    } else ;          // Uebertragung laeuft noch
}

function nav() {
    "use strict";

    const navLinks = document.querySelector('.nav-links');
    const burger = document.querySelector('.burger-menu');
    burger.classList.toggle('change');
    navLinks.classList.toggle('open');

}
