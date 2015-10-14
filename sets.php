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

require 'config.php';
require 'aeroview.php';

$aeroview = new AeroView();
$qs = $aeroview->buildQueryString();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>AeroView - An AreoSpike GUI</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- AeroView CSS -->
    <link href="css/aeroview.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <?php echo $aeroview->loadNavigationView($qs, "sets", $host); ?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">
                            <i class="fa fa-table"></i> Sets
                        </h2>
                    </div>
                </div>

                <?php
                    $hosts = $aeroview->validateQueryString($qs, true, true, false);
                    $db = $aeroview->connectAero($hosts);
                    //$allNodes = $db->infoMany("sets/" . $qs['namespace'], array("hosts"=>array(array("addr"=>$qs['host'], "port"=>3000))), array(Aerospike::OPT_READ_TIMEOUT => 2000000000));
                    $allNodes = $db->infoMany("sets/" . $qs['namespace']);
                    if ($allNodes == NULL) {
                        echo "<div class='alert alert-danger'><strong>Aerospike Error!</strong> " . $db->errorno() ." : " .  $db->error() . "</div>";
                        exit(1);
                    }
                ?>
                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <?php if (isset($allNodes)) { ?>
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <!--<th>Record Count</th>-->
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php


                                    $final = $aeroview->getNodeSetsAndObjectCount($allNodes);

                                    //echo results, divide object count by replication factor
                                    $counter = 1;
                                    foreach ($final as $key => $value) {
                                        echo "<tr><td>" . $counter . "</td>";
                                        echo "<td>" . $key . "</td>";
                                       // echo "<td>" . $value / $replicationFactor . "</td>";
                                        echo "<td><a href='/records.php?host=". $qs['host'] ."&namespace=". $qs['namespace'] ."&set=". $key ."'><i class='fa fa-sign-in'></i> Explore Records</a></td></tr>";
                                        $counter++;
                                    }

                                     ?>
                                </tbody>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
