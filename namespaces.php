<?php
/**
 * namespaces.php
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
        <?php echo $aeroview->loadNavigationView($qs, "namespaces", $host); ?>

        <div id="page-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h2 class="page-header">
                            <i class="fa fa-database"></i> Namespaces
                        </h2>
                    </div>
                </div>

                <?php
                    $hosts = $aeroview->validateQueryString($qs, true, false, false);
                    $db = $aeroview->connectAero($hosts);
                    $namespaces = $aeroview->getNamespaces($db);
                ?>

                <!-- /.row -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                            <?php
                                if (isset($namespaces)) {
                                    echo "<thead><tr><th>Name</th><th>Action</th></tr></thead><tbody>";

                                    for($i = 0; $i < count($namespaces); ++$i) {

                                        echo "<td>" . $namespaces[$i] . "</td>";
                                        echo "<td><a href='/sets.php?host=". $qs['host'] ."&namespace=". $namespaces[$i] ."'><i class='fa fa-sign-in'></i> Explore Sets</a></td></tr>";
                                    }

                                    echo "</tbody>";
                                }
                            ?>
                            </table>
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
