<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }
include "header.php";

if (isset($_GET["server_id"])) {
    $rServerID = intval($_GET["server_id"]);
}
?>        <div class="wrapper">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><i class="mdi mdi-refresh"></i> Auto-Refresh</li>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rServerID)) { echo $rServers[$rServerID]["server_name"]; } else { echo "Server Overview"; } ?></h4> 
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 

                <div class="row">
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box active-connections">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-purple rounded">
                                        <i class="fe-zap avatar-title font-22 text-purple"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                        <p class="text-muted mb-1 text-truncate">Open Connections</p>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($rServerID)) { ?>
                            <div class="mt-3">
                                <h6 class="text-uppercase">Total Connections <span class="float-right entry-percentage">0</span></h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                    <div class="col-md-6 col-xl-3">
                        <div class="card-box online-users">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-success rounded">
                                        <i class="fe-users avatar-title font-22 text-success"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                        <p class="text-muted mb-1 text-truncate">Online Users</p>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($rServerID)) { ?>
                            <div class="mt-3">
                                <h6 class="text-uppercase">Total Active <span class="float-right entry-percentage">0</span></h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                    <div class="col-md-6 col-xl-3">
                        <div class="card-box input-flow">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-primary rounded">
                                        <i class="fe-trending-up avatar-title font-22 text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span> <small>Mbps</small></h3>
                                        <p class="text-muted mb-1 text-truncate">Input Flow</p>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($rServerID)) { ?>
                            <div class="mt-3">
                                <h6 class="text-uppercase">Network Load <span class="float-right entry-percentage">0%</span></h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                    <div class="col-md-6 col-xl-3">
                        <div class="card-box output-flow">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-info rounded">
                                        <i class="fe-trending-down avatar-title font-22 text-info"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span> <small>Mbps</small></h3>
                                        <p class="text-muted mb-1 text-truncate">Output Flow</p>
                                    </div>
                                </div>
                            </div>
                            <?php if (isset($rServerID)) { ?>
                            <div class="mt-3">
                                <h6 class="text-uppercase">Network Load <span class="float-right entry-percentage">0%</span></h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->
                    
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box active-streams">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-purple rounded">
                                        <i class="fe-arrow-up-right avatar-title font-22 text-purple"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span></h3>
                                        <p class="text-muted mb-1 text-truncate">Active Streams</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="text-uppercase">Total Streams <span class="float-right entry-percentage">0</span></h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                    <div class="col-md-6 col-xl-3">
                        <div class="card-box cpu-usage">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-success rounded">
                                        <i class="fe-cpu avatar-title font-22 text-success"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span><small>%</small></h3>
                                        <p class="text-muted mb-1 text-truncate"><?php if (!isset($rServerID)) { echo "Avg "; }?>CPU Usage</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="text-uppercase">&nbsp;</h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                    <div class="col-md-6 col-xl-3">
                        <div class="card-box mem-usage">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-primary rounded">
                                        <i class="fe-terminal avatar-title font-22 text-primary"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1"><span data-plugin="counterup" class="entry">0</span><small>%</small></h3>
                                        <p class="text-muted mb-1 text-truncate"><?php if (!isset($rServerID)) { echo "Avg "; }?>Memory Usage</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="text-uppercase">&nbsp;</h6>
                                <div class="progress progress-sm m-0">
                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        <span class="sr-only">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->
                   
                    <div class="col-md-6 col-xl-3">
                        <div class="card-box uptime">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-sm bg-soft-info rounded">
                                        <i class="fe-power avatar-title font-22 text-info"></i>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-right">
                                        <h3 class="text-dark my-1 entry">--</h3>
                                        <p class="text-muted mb-1 text-truncate">System Uptime</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h6 class="text-uppercase">&nbsp;</span></h6>
                                <div class="progress-sm m-0"></div>
                            </div>
                        </div> <!-- end card-box-->
                    </div> <!-- end col -->

                </div>
                <!-- end row -->
               
            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->

        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12  text-center">Xtream Codes - Admin UI</div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->

        <!-- Vendor js -->
        <script src="assets/js/vendor.min.js"></script>

        <!-- Third Party js-->
        <script src="assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <script src="assets/libs/peity/jquery.peity.min.js"></script>
        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/libs/jquery-number/jquery.number.js"></script>
        <script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <!-- third party js ends -->

        <!-- Dashboard init -->
        <script src="assets/js/pages/dashboard.init.js"></script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
        <script>
        function getStats() {
            var rStart = Date.now();
            $.getJSON("./api.php?action=stats<?php if (isset($rServerID)) { echo "&server_id=".$rServerID; } ?>", function(data) {
                // Open Connections
                var rCapacity = Math.ceil((data.open_connections / data.total_connections) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".active-connections .entry").html($.number(data.open_connections, 0));
                $(".active-connections .entry-percentage").html($.number(data.total_connections, 0));
                $(".active-connections .progress-bar").prop("aria-valuenow", rCapacity);
                $(".active-connections .progress-bar").css("width", rCapacity.toString() + "%");
                $(".active-connections .sr-only").html(rCapacity.toString() + "%");
                // Online Users
                var rCapacity = Math.ceil((data.online_users / data.total_users) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".online-users .entry").html($.number(data.online_users, 0));
                $(".online-users .entry-percentage").html($.number(data.total_users, 0));
                $(".online-users .progress-bar").prop("aria-valuenow", rCapacity);
                $(".online-users .progress-bar").css("width", rCapacity.toString() + "%");
                $(".online-users .sr-only").html(rCapacity.toString() + "%");
                // Network Load - Input
                var rCapacity = Math.ceil((Math.ceil(data.bytes_received / 8) / data.network_guaranteed_speed) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".input-flow .entry").html($.number(Math.ceil(data.bytes_received / 8), 0));
                $(".input-flow .entry-percentage").html(rCapacity.toString() + "%");
                $(".input-flow .progress-bar").prop("aria-valuenow", rCapacity);
                $(".input-flow .progress-bar").css("width", rCapacity.toString() + "%");
                $(".input-flow .sr-only").html(rCapacity.toString() + "%");
                // Network Load - Output
                var rCapacity = Math.ceil((Math.ceil(data.bytes_sent / 8) / data.network_guaranteed_speed) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".output-flow .entry").html($.number(Math.ceil(data.bytes_sent / 8), 0));
                $(".output-flow .entry-percentage").html(rCapacity.toString() + "%");
                $(".output-flow .progress-bar").prop("aria-valuenow", rCapacity);
                $(".output-flow .progress-bar").css("width", rCapacity.toString() + "%");
                $(".output-flow .sr-only").html(rCapacity.toString() + "%");
                // Active Streams
                var rCapacity = Math.ceil((data.total_running_streams / data.total_streams) * 100);
                if (isNaN(rCapacity)) { rCapacity = 0; }
                $(".active-streams .entry").html($.number(data.total_running_streams, 0));
                $(".active-streams .entry-percentage").html($.number(data.total_streams, 0));
                $(".active-streams .progress-bar").prop("aria-valuenow", rCapacity);
                $(".active-streams .progress-bar").css("width", rCapacity.toString() + "%");
                $(".active-streams .sr-only").html(rCapacity.toString() + "%");
                // CPU Usage
                $(".cpu-usage .entry").html(data.cpu);
                $(".cpu-usage .entry-percentage").html(data.cpu.toString() + "%");
                $(".cpu-usage .progress-bar").prop("aria-valuenow", data.cpu);
                $(".cpu-usage .progress-bar").css("width", data.cpu.toString() + "%");
                $(".cpu-usage .sr-only").html(data.cpu.toString() + "%");
                // Memory Usage
                $(".mem-usage .entry").html(data.mem);
                $(".mem-usage .entry-percentage").html(data.mem.toString() + "%");
                $(".mem-usage .progress-bar").prop("aria-valuenow", data.mem);
                $(".mem-usage .progress-bar").css("width", data.mem.toString() + "%");
                $(".mem-usage .sr-only").html(data.mem.toString() + "%");
                // Uptime
                $(".uptime .entry").html(data.uptime.split(" ").slice(0,2).join(" "));
                if (Date.now() - rStart < 1000) {
                    setTimeout(getStats, 1000 - (Date.now() - rStart));
                } else {
                    getStats();
                }
            }).fail(function() {
                setTimeout(getStats, 1000);
            });
        }
        
        $(document).ready(function() {
            getStats();
        });
        </script>
    </body>
</html>