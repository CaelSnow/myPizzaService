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
class Fahrer extends Page
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
     * @return array An array containing the requested data.
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData():array
    {
        // to do: fetch data for this view from the database
        // to do: return array containing data

        $orders = array();
        // to do: fetch data for this view from the database

        $SQLQuery = "SELECT  o.address, o.ordering_id,oa.status, a.name,oa.ordered_article_id
        FROM ordering o INNER JOIN ordered_article oa
        ON oa.ordering_id = o.ordering_id
        INNER JOIN article a ON a.article_id = oa.article_id
        GROUP BY o.address, o.ordering_id HAVING SUM(status <= 1 OR status >= 4) = 0 ORDER BY o.ordering_time";

        $Recordset = $this->_database->query($SQLQuery);

        if(!$Recordset)
            throw new Exception("Query failed: ".$this->_database->error);
        else {
            while($Record = $Recordset->fetch_assoc()) {
                $orders[] = array($Record["address"], $Record["ordering_id"],$Record["status"],$Record["name"],$Record["ordered_article_id"]);
            }
        }
        $Recordset->free();
        return $orders;

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
        $this->generatePageHeader('Fahrer',(string)header("refresh:10")); //to do: set optional parameters
        // to do: output view of this page

        $Lieferbereit = 2;
        $InLieferung = 3;
        $Geliefert = 4;
        echo <<<EOT
<section>
    <div class="main fahrer">
    <div class="background"></div>
    <div class="content">

    <div class="container">
        <h1>Fahrer</h1>
EOT;

        if (empty($data)) {
            echo '<p> Es gibt momentan kein Pizza zu liefern </p>';
        }else {
            foreach($data as $Order) {
                //XSS Protection
                $address = htmlspecialchars($Order[0]);
                $ordering_id = $Order[1];
                $status = $Order[2];
                $name = htmlspecialchars($Order[3]);
                $ordered_article_id = $Order[4];
                echo <<<EOT

            <fieldset>
                <legend>Bestellung $Order[1]</legend>
                <div class="order">
                <form id="$ordering_id" action="Fahrer.php" method="POST" accept-charset="UTF-8"> 
                <table>
                <p>$address</p>
                <p>Status</p>
EOT;
                if($status == $Lieferbereit){
                    echo <<< EOT
                       <input type="radio" id="lieferbereit" name="lieferstatus" value="2" checked />
                       <label for="Fertig">Lieferbereit</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio" id="lieferbereit" name="lieferstatus" value="2" />
                       <label for="Fertig">Lieferbereit</label>
EOT;
                }if($status == $InLieferung){
                    echo <<< EOT
                       <input type="radio" id="Unterwegs" name="lieferstatus" value="3" checked />
                       <label for="Unterwegs">Unterwegs</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio" id="Unterwegs" name="lieferstatus" onclick="this.form.submit()" value="3" />
                       <label for="Unterwegs">Unterwegs</label>
EOT;
                }if($status == $Geliefert){
                    echo <<< EOT
                       <input type="radio" id="Geliefert" name="lieferstatus"   value="4" checked />
                       <label for="Geliefert">Geliefert</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio" id="Geliefert" name="lieferstatus" onclick="this.form.submit()" value="4" />
                       <label for="Geliefert">Geliefert</label>
EOT;
                }
                echo <<<EOT
                    <input type="hidden" name="ordering_id" value="$ordering_id">
                </table>    
                </form>
                </div>
            </fieldset>

EOT;
            }
        }

        echo <<<EOT
           </div>
           </div>
           </div>
           </section>
    EOT;



        $this->generatePageFooter();
        //window.setInterval($this->generateView(), 10000);
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

        if(isset($_POST['ordering_id']) && isset($_POST['lieferstatus'])) {
            $ordering_id = mysqli_real_escape_string($this->_database, $_POST["ordering_id"]);
            /*if (!isset($_POST[$ordered_article_id])) {
                throw new Exception("Query failed: ");
                //return;
            }*/
            $status =  mysqli_real_escape_string($this->_database, $_POST['lieferstatus']);

            $SQLQuery = "UPDATE ordered_article  SET status = $status WHERE ordering_id = $ordering_id;";

            if(!$this->_database->query($SQLQuery)) {
                throw new Exception("Query failed: ");//.$Connection->error);
            }

            header('Location: fahrer.php');
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
            $page = new Fahrer();
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
Fahrer::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >