<?php declare(strict_types=1);

/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';


/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Bestellung extends Page
{
    // to do: declare reference variables for members
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array|null An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): ?array
    {
        $pizzas = array();
        // fetch data for this view from the database
        $SQLabfrage = "SELECT article_id,name,picture, price from article";
        $Recordset = $this->_database->query($SQLabfrage);
        if(!$Recordset)
            throw new Exception("Query failed: ");//$Connection->error);
        else {
            while($Record = $Recordset->fetch_assoc()) {
                $pizzas[] = array($Record["article_id"], $Record["name"], $Record["picture"],$Record["price"]);
            }
        }
        $Recordset->free();
        return $pizzas;

    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    protected function generateView():void
    {
        $data = $this->getViewData(); //NOSONAR ignore unused $data
        $this->generatePageHeader('Bestellung'); //to do: set optional parameters
        // to do: output view of this page
        //generate pizza menu
        echo <<<HTML
                <h1>Bestellung</h1>
                <script src="js_functions.js"></script>
                <section>
                    <div class="pizza_select left">
                        <h2>Speisekarte</h2>

HTML;
        foreach($data as $Pizza) {
            //format to 2 digits
            $price = $Pizza[3];
            $image = 'images/salami.png';
            if( $Pizza[0] == 2){
                $image = 'images/vegetaria.png';
            }else if($Pizza[0] == 3){
                $image = 'images/hawaii.png';
            }
            //array cast for java script
            $js_pizza_array = json_encode($Pizza);

            echo <<<HTML
                    <div class="pizza">
                        <img class="img" style='background-image: url("  $image ")' src=$image alt=$Pizza[1] onclick='add_warenkorb($js_pizza_array)'/> 
                       
                       
                         <div class="body">
                           <span class="pizza_name">$Pizza[1]</span>
                           <span class="pizza_price">   Price: $price</span>
                         </div>     
                    </div>
HTML;
        }
        echo <<<EOT
            </div>
            <div class="right">
                <form onsubmit="return check_formular()" action="./Bestellung.php" method="POST" accept-charset="UTF-8" class="order">
                    <h3  >Warenkorb</h3>
                    <div class="pizza_order_cart_box">
                        <label for="select_pizza">Ihre Auswahl: </label>
                        <select id="select_pizza" name="Selection[]" size="10" tabindex="1" multiple></select>
                     </div>

                    <p class="total_price">Gesamtpreis:  <span id="price_id" >0,00</span> €</p>

                    <h3 >Adresse: </h3>
                    <input type="text" id="Adresse" placeholder="Ihre Adresse" value="" name="Address" onchange='check_addr()'   required />
                    <button class="delete_order btn" type="reset" name="alle_loeschen" value="Alle löschen" onclick="remove_all()">Alle löschen</button>
                    <button class="delete_order_selected btn" type="button" name="auswhal_loeschen" value="Auswahl löschen" onclick="remove_element()">Auswahl löschen</button>
                    <input class="btn" type="submit" id="submit" value="Bestellen" disabled/>
                </form>
            </div>
        </section>
EOT;


        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members


        // to do: call processData() for all members
        if (isset($_POST["Selection"]) && isset($_POST["Address"])) {// Kunde Eintragen

            session_start();
            $adresse = mysqli_real_escape_string($this->_database, $_POST["Address"]);

            //get last BestId and increment
            $bestID = $this->_database->insert_id;


            $sqlOrder = "INSERT INTO ordering SET "."address = \"$adresse\"";
            if ($this->_database->query($sqlOrder) === FALSE) {  // if insert of new order was successful -> insert ordered items
                throw new Exception("Bestellung konnte nicht registriert werden");
            }


                $ordered_article_id = $this->_database->insert_id;
                /*$queryOrderdArticleId = "SELECT MAX(ordering_id) from ordering ";
                $lastOrderdArticleId = $this->_database->query($queryOrderdArticleId);
                if($lastOrderdArticleId){
                    $ordered_article_id = intval($lastOrderdArticleId->fetch_row()[0])+1;
                }*/
            $queryBestId = "SELECT MAX(ordering_id) from ordering ";
            $lastBestId = $this->_database->query($queryBestId);
            if($lastBestId){
                $bestID = intval($lastBestId->fetch_row()[0]);
            }
            foreach($_POST["Selection"] as $pizza){
                $pizza_esc =mysqli_real_escape_string($this->_database, $pizza);

                $Query = "SELECT article_id FROM article WHERE name = '$pizza_esc'";

                $article_id = intval($pizza);

                $sqlPizza = "INSERT INTO ordered_article SET ".
                                  "article_id = \"$article_id\", ordering_id = \"$bestID\"";

                if($this->_database->query($sqlPizza) === false){
                    throw new Exception("Ordered Article konnte nicht registriert werden".$bestID);
                }
            }
            $_SESSION['bestID'] = $bestID;
            header('Location: Bestellung.php');
            die();

        }

    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main():void
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >