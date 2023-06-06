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
class Baecker extends Page
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
        $Pizzas = array();
        // to do: fetch data for this view from the database
        /*$SQLQuery = "SELECT  name,ordered_article_id , status FROM article ,ordered_article , ordering
        WHERE ordering.ordering_id = ordered_article.ordering_id AND ordered_article.article_id = article.article_id AND ordered_article.status <= 2 
        ORDER BY ordering_time";*/
        $SQLQuery = "SELECT a.name,oa.ordered_article_id , oa.status 
             FROM article a INNER JOIN ordered_article oa
             WHERE oa.article_id = a.article_id AND oa.status < 2 ORDER BY oa.ordered_article_id";

        $Recordset = $this->_database->query($SQLQuery);
        if(!$Recordset) {
            throw new Exception("Query failed: ");//.$Connection->error);
        } else {
            while($Record = $Recordset->fetch_assoc()) {
                $Pizzas[] = array($Record["name"], $Record["ordered_article_id"], $Record["status"]);
            }
        }
        $Recordset->free();
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
        $this->generatePageHeader('Baecker', (string)header("refresh:10")); //to do: set optional parameters
        // to do: output view of this page
        echo <<< EOT
        <script>
          function updateDatabase(ordered_article_id, status) {
            console.log(ordered_article_id, status);
            let str = 'B' + ordered_article_id;
            document.forms[str].submit();
            header("refresh:0");
          }
          </script>
EOT;

        $Backbereit = 0;
        $ImOffen= 1;
        $Fertig = 2;
        echo <<<EOT
        
        <section>
          <div class="main customer">
            <div class="background"></div>
            <div class="content">
            <section>
                <h1>Baecker</h1>
EOT;
        if (empty($data)) {
            echo '<p> Es gibt momentan kein Pizza zu backen </p>';
        }else {
            foreach($data as $Pizza) {

                $name = $Pizza[0];
                $ordered_article_id = $Pizza[1];
                $status = $Pizza[2];
                echo <<<EOT
              <fieldset>
                <legend>Bestellung  Pizza $ordered_article_id $name </legend>
                  <form id="$ordered_article_id" action="Baecker.php" method="POST" accept-charset="UTF-8"> 
                    <table>
EOT;
                if($status == $Backbereit){
                    echo <<< EOT
                       <input type="radio" name="backerstatus" value="0" id="bestellt"  checked />
                       <label for="Backbereit">Backbereit</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio"  name="backerstatus" value="0" id="bestellt" onclick="this.form.submit()" />
                       <label for="Backbereit">Backbereit</label>
EOT;
                }if($status == $ImOffen){
                    echo <<< EOT
                       <input type="radio" name="backerstatus"  id="ofen" value="1" checked />
                       <label for="ImOffen">Im Offen</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio"  name="backerstatus"  id="ofen" onclick="this.form.submit()" value="1" />
                       <label for="ImOffen">Im Offen</label>
EOT;
                }if($status == $Fertig){
                    echo <<< EOT
                       <input type="radio" id="fertig" name="backerstatus"  value="2" checked />
                       <label for="Fertig">Fertig</label>
EOT;
                }else {
                    echo <<< EOT
                       <input type="radio" id="fertig" name="backerstatus" onclick="this.form.submit()" value="2"/>
                       <label for="Fertig">Fertig</label>
EOT;
                }
                echo <<<EOT
                    <input type="hidden" name="ordered_article_id" value="$ordered_article_id">
                   </table>
                  </form>
                 </fieldset>
            
EOT;
            }
        }
        echo <<<EOT
            </section>
           </div>
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

        if(isset($_POST['ordered_article_id']) && isset($_POST['backerstatus'])) {
            $ID_Pizza = mysqli_real_escape_string($this->_database, $_POST['ordered_article_id']);
            /*if (!isset($_POST[$ID_Pizza])) {
                throw new Exception("Query failed2: ");
                //return;
            }*/
            $Status = mysqli_real_escape_string($this->_database, $_POST['backerstatus']);

            $SQLQuery = "UPDATE ordered_article SET status = $Status WHERE ordered_article_id = $ID_Pizza";

            if(!$this->_database->query($SQLQuery)) {
                throw new Exception("Query failed: ");//.$Connection->error);
            }

            header('Location: baecker.php');
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
            $page = new Baecker();
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
Baecker::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends).
// Not specifying the closing ? >  helps to prevent accidents
// like additional whitespace which will cause session
// initialization to fail ("headers already sent").
//? >