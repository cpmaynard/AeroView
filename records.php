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

    <title>AeroView - AeroSpike GUI</title>

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
        <?php echo $aeroview->loadNavigationView($qs, "records", $host); ?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">
                            <i class="fa fa-file-o"></i> Records
                        </h2>
                    </div>
                </div>

                <?php
                    $hosts = $aeroview->validateQueryString($qs, true, true, true);
                    $db = $aeroview->connectAero($hosts);
                ?>

                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <?php if (isset($qs['namespace']) && isset($qs['set']) && !empty($qs['namespace']) && !empty($qs['set'])) { ?>
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <?php

                                            $processed = 0;
                                            $status = $db->scan($qs['namespace'], $qs['set'], function ($record) use (&$processed) {

                                                foreach ($record['bins'] as $key => $value) {
                                                    echo "<th>" . $key . "</th>";

                                                }

                                                if ($processed++ >= 0) return false;
                                            });

                                            if ($status !== Aerospike::OK) {
                                                echo "<div class='alert alert-danger'><strong>Aerospike Error!</strong> " . $db->errorno() ." : " .  $db->error() . "</div>";
                                                exit(1);
                                            }

                                        ?>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php

                                        $result = array();

                                        $status = $db->scan($qs['namespace'], $qs['set'], function ($record) use (&$result) {

                                            echo "<tr>";
                                            foreach ($record['bins'] as $key => $value) {
                                                if (gettype($value) == "string") {
                                                    $value = htmlentities($value);
                                                }
                                                echo "<td>";
                                                print_r($value);
                                                echo "</td>";
                                            }
                                            echo "</tr>";
                                        });
                                    ?>

                                </tbody>
                            </table>
                            <? } ?>
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
