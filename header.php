<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Xtream UI</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- third party css -->
        <link href="assets/libs/jquery-nice-select/nice-select.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/jquery-toast/jquery.toast.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/treeview/style.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/clockpicker/bootstrap-clockpicker.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/nestable2/jquery.nestable.min.css" rel="stylesheet" />
        <!-- third party css end -->

        <!-- App css -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/app.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <!-- Navigation Bar-->
        <header id="topnav">
            <!-- Topbar Start -->
            <div class="navbar-custom">
                <div class="container-fluid">
                    <ul class="list-unstyled topnav-menu float-right mb-0">

                        <li class="dropdown notification-list">
                            <!-- Mobile menu toggle-->
                            <a class="navbar-toggle nav-link">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </a>
                            <!-- End mobile menu toggle-->
                        </li>
            
                        <li class="notification-list">
                            <a href="./settings.php" class="nav-link right-bar-toggle waves-effect">
                                <i class="fe-settings noti-icon"></i>
                            </a>
                        </li>
                        <li class="notification-list">
                            <a href="./logout.php" class="nav-link right-bar-toggle waves-effect">
                                <i class="fe-power noti-icon"></i>
                            </a>
                        </li>
                    </ul>

                    <!-- LOGO -->
                    <div class="logo-box">
                        <a href="dashboard.php" class="logo text-center">
                            <span class="logo-lg">
                                <img src="assets/images/logo-back.png" alt="" height="26">
                                <!-- <span class="logo-lg-text-dark">Upvex</span> -->
                            </span>
                            <span class="logo-sm">
                                <!-- <span class="logo-sm-text-dark">X</span> -->
                                <img src="assets/images/logo-back.png" alt="" height="28">
                            </span>
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- end Topbar -->
        
            <div class="topbar-menu">
                <div class="container-fluid">
                    <div id="navigation">
                        <!-- Navigation Menu-->
                        <ul class="navigation-menu">

                            <li class="has-submenu">
                                <a href="#"><i class="la la-dashboard"></i>Dashboard <div class="arrow-down"></div></a>
                                <ul class="submenu megamenu">
                                    <li>
                                        <ul>
                                            <li><a href="./dashboard.php">Server Overview</a></li>
                                            <?php $i = 0; foreach ($rServers as $rServer) { $i ++; ?>
                                            <li><a href="./dashboard.php?server_id=<?=$rServer["id"]?>"><?=$rServer["server_name"]?></a></li>
                                            <?php if ($i == 12) {
                                                    echo "</ul></li><li><ul>";
                                                    $i = 0;
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="has-submenu">
                                <a href="#"><i class="la la-server"></i>Servers <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./server.php">Add Server</a></li>
                                    <li><a href="./servers.php">Manage Servers</a></li>
                                    <li class="separator"></li>
                                    <li><a href="#">Live Connections <i class="la la-exclamation-triangle"></i></a></li>
                                </ul>
                            </li>

                            <li class="has-submenu">
                                <a href="#"> <i class="la la-user"></i>Users <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./user.php">Add User</a></li>
                                    <li><a href="./users.php">Manage Users</a></li>
                                    <li class="separator"></li>
                                    <li><a href="./reg_user.php">Add Registered User</a></li>
                                    <li><a href="./reg_users.php">Manage Registered Users</a></li>
                                    <li><a href="#">Manage Group Members <i class="la la-exclamation-triangle"></i></a></li>
                                    <li class="separator"></li>
                                    <li><a href="#">Client Logs <i class="la la-exclamation-triangle"></i></a></li>
                                    <li><a href="#">User Activity <i class="la la-exclamation-triangle"></i></a></li>
                                </ul>
                            </li>
                            
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-tablet"></i>Devices <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./mag.php">Add MAG Device</a></li>
                                    <li><a href="./mags.php">Manage MAG Devices</a></li>
                                    <li><a href="./mag_events.php">Manage MAG Events</a></li>
                                    <li class="separator"></li>
                                    <li><a href="./enigma.php">Add Enigma Device</a></li>
                                    <li><a href="./enigmas.php">Manage Enigma Devices</a></li>
                                </ul>
                            </li>
                            
                            <!-- Reseller coming in later release -->
                            
                            <!-- <li class="has-submenu">
                                <a href="#"> <i class="la la-briefcase"></i>Packages <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="#">Add Package <i class="la la-exclamation-triangle"></i></a></li>
                                    <li><a href="#">Manage Packages <i class="la la-exclamation-triangle"></i></a></li>
                                </ul>
                            </li> -->
                            
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-video-camera"></i>VOD <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="#">Add Movie <i class="la la-exclamation-triangle"></i></a></li>
                                    <li><a href="#">Manage Movies <i class="la la-exclamation-triangle"></i></a></li>
                                    <li class="separator"></li>
                                    <li><a href="./movie_category.php">Add Movie Category</a></li>
                                    <li><a href="./movie_categories.php">Manage Movie Categories</a></li>
                                    <li class="separator"></li>
                                    <li><a href="#">Add TV Series <i class="la la-exclamation-triangle"></i></a></li>
                                    <li><a href="#">Manage TV Series <i class="la la-exclamation-triangle"></i></a></li>
                                    <li class="separator"></li>
                                    <li><a href="#">Add TV Episode <i class="la la-exclamation-triangle"></i></a></li>
                                    <li><a href="#">Manage TV Episodes <i class="la la-exclamation-triangle"></i></a></li>
                                </ul>
                            </li>
                            
                            <li class="has-submenu">
                                <a href="#"> <i class="la la-tasks"></i>Bouquets <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./bouquet.php">Add Bouquet</a></li>
                                    <li><a href="./bouquets.php">Manage Bouquets</a></li>
                                </ul>
                            </li>
                            
                            <li class="has-submenu">
                                <a href="#"> <i class="mdi mdi-television-guide"></i>EPG <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./epg.php">Add EPG</a></li>
                                    <li><a href="./epgs.php">Manage EPG's</a></li>
                                </ul>
                            </li>

                            <li class="has-submenu">
                                <a href="#"> <i class="la la-play-circle-o"></i>Streams <div class="arrow-down"></div></a>
                                <ul class="submenu">
                                    <li><a href="./stream.php">Add Stream</a></li>
                                    <?php if (count($rCategories) > 0) { ?>
                                    <li><a href="#" data-toggle="modal" data-target="#streamCategories">Manage Streams</a></li>
                                    <?php } ?>
                                    <li><a href="./streams.php">Manage All Streams</a></li>
                                    <li class="separator"></li>
                                    <li><a href="./stream_category.php">Add Stream Category</a></li>
                                    <li><a href="./stream_categories.php">Manage Stream Categories</a></li>
                                    <li class="separator"></li>
                                    <li><a href="#">Stream Logs <i class="la la-exclamation-triangle"></i></a></li>
                                </ul>
                            </li>

                        </ul>
                        <!-- End navigation menu -->

                        <div class="clearfix"></div>
                    </div>
                    <!-- end #navigation -->
                </div>
                <!-- end container -->
            </div>
            <!-- end navbar-custom -->

        </header>
        <!-- End Navigation Bar-->
        <div class="modal fade" id="streamCategories" tabindex="-1" role="dialog" aria-labelledby="streamCategories" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="streamCategories">Select a Category:</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($rCategories as $rCategoryHead) { ?>
                        <div class="col-md-12">
                            <a href="./streams.php?category=<?=$rCategoryHead["id"]?>">
                            <div class="card bg-secondary text-white">
                                <div class="card-body">
                                    <div class="card-widgets"><i class="mdi mdi-chevron-right-circle"></i></div>
                                    <p class="card-text"><?=$rCategoryHead["category_name"]?></p>
                                </div>
                            </div>
                            </a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>