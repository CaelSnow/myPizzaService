<?php declare(strict_types=1);

/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
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
class Kunde extends Page
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
        session_start();
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
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data

        $Pizzas = array();
        //bestell id aus session hollen
        //session_start();

        if(isset($_SESSION["bestID"])) {
            $bestID = $_SESSION["bestID"];

            $SQLquery = "SELECT a.name,o.address,oa.ordered_article_id,oa.status 
            FROM article a INNER JOIN ordered_article oa INNER JOIN ordering o
            WHERE oa.article_id = a.article_id AND o.ordering_id = oa.ordering_id AND oa.ordering_id = $bestID
            ORDER BY 3 DESC ";

            $Recordset = $this->_database->query($SQLquery);

            if(!$Recordset) throw new Exception("Fehler in Abfrage: ".$this->database->error);
            while($Record = $Recordset->fetch_assoc()) {
                $name = $Record["name"];
                $status = $Record["status"];
                $address = $Record["address"];
                $ordered_article_id = $Record["ordered_article_id"];
                $Pizzas[] = ["name" => $name, "ordered_article_id" => $ordered_article_id, "status" => $status, "address" => $address];


            }
            $Recordset->free(); //Auskommentiert aufgrund eines Fehlers
        }

        return $Pizzas;

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
        $this->generatePageHeader('Kunde');//, (string)header("refresh:5")); //to do: set optional parameters
        echo "<script src='StatusUpdate.js'></script>";
        echo "<script >handle()</script>";
        if ($data == array()) {
            echo ("<h1>Keine gültige Bestellung</h1>");
            $this->generatePageFooter();
            return;
        }
        echo <<<EOT
    <section>
      <div class="main kunde">
      <div class="background"></div>
        <div class="content">

        <h1>Lieferstatus</h1>
    EOT;

        foreach ($data as $datum) {
            //status: 0-bestellt, 1-imOfen, 2-fertig, 3-unterwegs, 4-geliefert
            switch (intval($datum["status"])) {
                case 0:
                    $status = "Bestellt";
                    break;
                case 1:
                    $status = "Im Ofen";
                    break;
                case 2:
                    $status = "Fertig";
                    break;
                case 3:
                    $status = "Unterwegs";
                    break;
                case 4:
                    $status = "Geliefert";
                    break;
                default:
                    $status = "No Status";
                // continue; // empty
            }

            $name = htmlspecialchars($datum["name"]);
            echo <<<EOT
        <p>$name: <span id="status$datum[ordered_article_id]" >$status</span></p>
      EOT;
        }
        $address = htmlspecialchars($data[0]["address"]);

        echo <<<EOT
      <p>An die Adresse: $address</p>
      <a href="Bestellung.php" class="btn new_order">Neue Bestellung</a>
      </div>
      </div>
    </section>
    EOT;
        /*  echo <<<EOT
      <a href="KundeStatus.php">KundenStatus</a>
      EOT;*/
        // to do: output view of this page

        echo <<<EOT

        <section>
        <script >handle()</script>
EOT;

        echo "</section>";

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
        /*header("Content-Type: application/json; charset=UTF-8");
        session_start();*/

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
            $page = new Kunde();
            $page->processReceivedData();
            $page->generateView();
            //window.setInterval($page->generateView(), 2000);
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();

        }
    }
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
Kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >