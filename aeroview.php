<?php
/**
 * index.php
 *
 * PHP version 5.3
 *
 * @category  AeroView
 * @package   Application
 * @author    Chris Maynard <chris@revcontent.com>
 */

class AeroView
{

   /**
   * Create a valid query string
   *
   * @param none
   * @return string
   */
   public function buildQueryString() {

      $queryString = array();
      $string      = "?";
      $host = $namespace = $set = "";

      if (isset($_GET['host'])) {
         $string = $string . "host=" . $_GET['host'] . "&";
         $host = $_GET['host'];
      }

      if (isset($_GET['namespace'])) {
         $string = $string . "namespace=" . $_GET['namespace'] . "&";
         $namespace = $_GET['namespace'];
      }

      if (isset($_GET['set'])) {
         $string = $string . "set=" . $_GET['set'];
         $set = $_GET['set'];
      }

      $queryString['qs']        = $string;
      $queryString['host']      = $host;
      $queryString['namespace'] = $namespace;
      $queryString['set']       = $set;

      return $queryString;
   }

   /**
   * Validate query string, connect to aspike
   *
   * @param boolean $host
   * @param boolean $namespace
   * @param boolean $set
   * @return array
   */
   public function validateQueryString($qs, $host = false, $namespace = false, $set = false) {

      $finalhosts = array();

      if ($host && isset($_GET['host'])) {
         $hostarr = explode(",", $_GET['host']);

         foreach ($hostarr as $h) {
             $finalhosts[] = array("addr" => $h, "port" => 3000);
         }

      } else {
         echo "<div class='alert alert-info'><strong>Missing Host!</strong> Please select a <a href='/index.php";
         echo $qs['qs'] . "'>Host</a> to view explore namespaces.</div>";
      }

      if ($namespace && !isset($_GET['namespace'])) {
         echo "<div class='alert alert-info'><strong>Missing Namespace!</strong> Please select a <a href='/namespaces.php";
         echo $qs['qs'] . "'>namespace</a> to view explore sets.</div>";
      }

      if ($set && !isset($_GET['set'])) {
         echo "<div class='alert alert-info'><strong>Missing Set!</strong> Please select a <a href='/sets.php";
         echo $qs['qs'] . "'>set</a> to view explore records.</div>";
      }

      return $finalhosts;

   }

   /**
   * Validate query string, connect to aspike
   *
   * @param array $hosts
   * @return array
   */
   public function connectAero($hosts) {

      $config = array("hosts"=> $hosts);
      $opts = array(Aerospike::OPT_CONNECT_TIMEOUT => 1000000, Aerospike::OPT_WRITE_TIMEOUT => 2500);
      $db = new Aerospike($config, true, $opts);

      if (!$db->isConnected()) {
         echo "<div class='alert alert-danger'><strong>Aerospike Error!</strong> " . $db->errorno() ." : " .  $db->error() . "</div>";
         exit(1);
      }

     return $db;
   }

   /**
   * Call aspike info for namespaces
   *
   * @param array $db
   * @return array
   */
   public function getNamespaces($db) {

      $namespaces = array();

      $status = $db->info('namespaces', $response);

      if ($status == Aerospike::OK) {
         $namespaces = explode(";",str_replace("namespaces", "", $response));
      }

      return $namespaces;
   }

   /**
   * Format for set names and object count
   *
   * @param array $allNodes
   * @return array
   */
   public function getNodeSetsAndObjectCount($allNodes) {

      $final = array();

      //infoMany returns values for each node in the cluster
      foreach ($allNodes as $node) {
         $nodeSets = explode(";", $node);

         //iterate over this node's sets
         foreach ($nodeSets as $set) {
             $setInfo = explode(":", $set);
             if (isset($setInfo[1]) && isset($setInfo[2])) {
                 $setKey = str_replace("set_name=", "", $setInfo[1]);

                 //add the object count to the result set or if it's new create a key for the set name
                 if (isset($final[$setKey])) {
                     $newCount = (int) $final[$setKey] + (int) str_replace("n_objects=", "", $setInfo[2]);
                     $final[$setKey] = $newCount;
                 } else {
                     $final[$setKey] = (int) str_replace("n_objects=", "", $setInfo[2]);
                 }
             }
         }
      }

      return $final;

   }

   /**
   * Dynamic Top and Left Nav
   *
   * @param array $qs
   * @param string $active
   * @return string
   */
   public function loadNavigationView($qs, $active, $config) {

      $hostli = "<li><a href='index.php" . $qs['qs'] ."'><i class='fa fa-fw fa-server aeroview-green'></i> <span class='aeroqsinfo'>" . array_search ($qs['host'], $config) . "</span></a></li>";
      $namespaceli = "<li><a href='namespaces.php" . $qs['qs'] . "'><i class='fa fa-fw fa-database aeroview-green'></i> <span class='aeroqsinfo'>" . $qs['namespace'] . "</span></a></li>";
      $setli = "<li><a href='sets.php" . $qs['qs'] . "'><i class='fa fa-fw fa-table aeroview-green'></i> <span class='aeroqsinfo'>" . $qs['set'] . "</span></a></li>";

      $html = '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand aeroview-green" href="index.php">AeroView</a>
            </div>
            <ul class="nav navbar-right top-nav">
               '. (isset($qs['host']) && !empty($qs['host']) ? "$hostli" : "") .''.
                  (isset($qs['namespace']) && !empty($qs['namespace']) ? "$namespaceli" : "").''.
                  (isset($qs['set']) && !empty($qs['set']) ? "$setli" : "").'
            </ul>

            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
                    <li class="'. ($active == "hosts" ? "active": "") .'">
                        <a href="index.php' . $qs["qs"] .'"><i class="fa fa-fw fa-server"></i> Hosts</a>
                    </li>
                    <li class="'. ($active == "namespaces" ? "active": "") .'">
                        <a href="namespaces.php' . $qs["qs"] .'"><i class="fa fa-fw fa-database"></i> Namespaces</a>
                    </li>
                   <li class="'. ($active == "sets" ? "active": "") .'">
                        <a href="sets.php' . $qs["qs"] .'"><i class="fa fa-fw fa-table"></i> Sets</a>
                    </li>
                    <li class="'. ($active == "records" ? "active": "") .'">
                        <a href="records.php' . $qs["qs"] .'"><i class="fa fa-fw fa-file-o"></i> Records</a>
                    </li>

                </ul>
            </div>
        </nav>';

      return $html;
   }





}

?>