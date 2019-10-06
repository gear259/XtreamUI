<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_server"])) {
    $rArray = Array("server_name" => "", "domain_name" => "", "server_ip" => "", "vpn_ip" => "", "diff_time_main" => 0, "http_broadcast_port" => 25461, "total_clients" => 1000, "system_os" => "", "network_interface" => "eth0", "status" => 2, "enable_geoip" => 0, "can_delete" => 1, "rtmp_port" => 25462, "enable_isp" => 0, "boost_fpm" => 0, "network_guaranteed_speed" => 1000, "https_broadcast_port" => 25463, "whitelist_ips" => Array(), "timeshift_only" => 0);
    if (strlen($_POST["server_ip"]) == 0) {
        $_STATUS = 1;
    }
    if (isset($rServers[$_POST["edit"]]["can_delete"])) {
        $rArray["can_delete"] = intval($rServers[$_POST["edit"]]["can_delete"]);
    }
    if (isset($_POST["enabled"])) {
        $rArray["enabled"] = intval($_POST["enabled"]);
        unset($_POST["enabled"]);
    }
    if (isset($_POST["total_clients"])) {
        $rArray["total_clients"] = intval($_POST["total_clients"]);
        unset($_POST["total_clients"]);
    }
    if (isset($_POST["http_broadcast_port"])) {
        $rArray["http_broadcast_port"] = intval($_POST["http_broadcast_port"]);
        unset($_POST["http_broadcast_port"]);
    }
    if (isset($_POST["https_broadcast_port"])) {
        $rArray["https_broadcast_port"] = intval($_POST["https_broadcast_port"]);
        unset($_POST["https_broadcast_port"]);
    }
    if (isset($_POST["rtmp_port"])) {
        $rArray["rtmp_port"] = intval($_POST["rtmp_port"]);
        unset($_POST["rtmp_port"]);
    }
    if (isset($_POST["diff_time_main"])) {
        $rArray["diff_time_main"] = intval($_POST["diff_time_main"]);
        unset($_POST["diff_time_main"]);
    }
    if (isset($_POST["network_guaranteed_speed"])) {
        $rArray["network_guaranteed_speed"] = intval($_POST["network_guaranteed_speed"]);
        unset($_POST["network_guaranteed_speed"]);
    }
    if (!isset($_STATUS)) {
        foreach($_POST as $rKey => $rValue) {
            if (isset($rArray[$rKey])) {
                $rArray[$rKey] = $rValue;
            }
        }
        $rCols = implode(',', array_keys($rArray));
        foreach (array_values($rArray) as $rValue) {
            isset($rValues) ? $rValues .= ',' : $rValues = '';
            if (is_array($rValue)) {
                $rValue = json_encode($rValue);
            }
            if (is_null($rValue)) {
                $rValues .= 'NULL';
            } else {
                $rValues .= '\''.$db->real_escape_string($rValue).'\'';
            }
        }
        if (isset($_POST["edit"])) {
            $rCols = "id,".$rCols;
            $rValues = $_POST["edit"].",".$rValues;
        }
        $rQuery = "REPLACE INTO `streaming_servers`(".$rCols.") VALUES(".$rValues.");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            $_STATUS = 0;
            $rServers = getStreamingServers();
            if (!isset($_GET["id"])) {
                $_GET["id"] = $rInsertID;
            }
        } else {
            $_STATUS = 2;
        }
    }
}

if (isset($_GET["id"])) {
    $rServerArr = $rServers[$_GET["id"]];
    if (!$rServerArr) {
        exit;
    }
}

include "header.php"; ?>
        <div class="wrapper boxed-layout">
            <div class="container-fluid">
                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <a href="./servers.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to Servers</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rServerArr)) { echo "Edit"; } else { echo "Add"; } ?> Server</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 
                <div class="row">
                    <div class="col-xl-12">
                        <?php if ((isset($_STATUS)) && ($_STATUS == 0)) { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            Server operation was completed successfully.
                        </div>
                        <?php } else if ((isset($_STATUS)) && ($_STATUS > 0)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            There was an error performing this operation! Please check the form entry and try again.
                        </div>
                        <?php } ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="./server.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="server_form">
                                    <?php if (isset($rServerArr)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rServerArr["id"]?>" />
                                    <input type="hidden" name="status" value="<?=$rServerArr["status"]?>" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#server-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#advanced-options" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-folder-alert-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Advanced</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="server-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="server_name">Server Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="server_name" name="server_name" value="<?php if (isset($rServerArr)) { echo $rServerArr["server_name"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="domain_name">Domain Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="domain_name" name="domain_name" placeholder="www.example.com" value="<?php if (isset($rServerArr)) { echo $rServerArr["domain_name"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="server_ip">Server IP</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="server_ip" name="server_ip" value="<?php if (isset($rServerArr)) { echo $rServerArr["server_ip"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="vpn_ip">VPN IP</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="vpn_ip" name="vpn_ip" value="<?php if (isset($rServerArr)) { echo $rServerArr["vpn_ip"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="total_clients">Max Clients</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="total_clients" name="total_clients" value="<?php if (isset($rServerArr)) { echo $rServerArr["total_clients"]; } else { echo "1000"; } ?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="timeshift_only">Timeshift Only</label>
                                                            <div class="col-md-2">
                                                                <input name="timeshift_only" id="timeshift_only" type="checkbox" <?php if (isset($rServerArr)) { if ($rServerArr["timeshift_only"] == 1) { echo "checked "; } } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="advanced-options">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="http_broadcast_port">HTTP Port</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="http_broadcast_port" name="http_broadcast_port" value="<?php if (isset($rServerArr)) { echo $rServerArr["http_broadcast_port"]; } else { echo "25461"; } ?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="https_broadcast_port">HTTPS Port</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="https_broadcast_port" name="https_broadcast_port" value="<?php if (isset($rServerArr)) { echo $rServerArr["https_broadcast_port"]; } else { echo "25463"; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="rtmp_port">RTMP Port</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="rtmp_port" name="rtmp_port" value="<?php if (isset($rServerArr)) { echo $rServerArr["rtmp_port"]; } else { echo "25462"; } ?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="diff_time_main">Time Difference - Seconds</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="diff_time_main" name="diff_time_main" value="<?php if (isset($rServerArr)) { echo $rServerArr["diff_time_main"]; } else { echo "0"; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="network_interface">Network Interface</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="network_interface" name="network_interface" value="<?php if (isset($rServerArr)) { echo $rServerArr["network_interface"]; } else { echo "eth0"; } ?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="network_guaranteed_speed">Network Speed - Mbps</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="network_guaranteed_speed" name="network_guaranteed_speed" value="<?php if (isset($rServerArr)) { echo $rServerArr["network_guaranteed_speed"]; } else { echo "1000"; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="system_os">Operating System</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="system_os" name="system_os" value="<?php if (isset($rServerArr)) { echo $rServerArr["system_os"]; } else { echo "Ubuntu 14.04.5 LTS"; } ?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                    </li>
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_server" type="submit" class="btn btn-primary" value="<?php if (isset($rServerArr)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </div> <!-- tab-content -->
                                    </div> <!-- end #basicwizard-->
                                </form>

                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div>
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
        <script src="assets/libs/jquery-toast/jquery.toast.min.js"></script>
        <script src="assets/libs/jquery-nice-select/jquery.nice-select.min.js"></script>
        <script src="assets/libs/switchery/switchery.min.js"></script>
        <script src="assets/libs/select2/select2.min.js"></script>
        <script src="assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
        <script src="assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
        <script src="assets/libs/clockpicker/bootstrap-clockpicker.min.js"></script>
        <script src="assets/libs/moment/moment.min.js"></script>
        <script src="assets/libs/daterangepicker/daterangepicker.js"></script>

        <!-- Plugins js-->
        <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

        <!-- Tree view js -->
        <script src="assets/libs/treeview/jstree.min.js"></script>
        <script src="assets/js/pages/treeview.init.js"></script>
        <script src="assets/js/pages/form-wizard.init.js"></script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
        <script>
        (function($) {
          $.fn.inputFilter = function(inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
              if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
              } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
              }
            });
          };
        }(jQuery));
        
        $(document).ready(function() {
            $('select.select2').select2({width: '100%'})
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
              var switchery = new Switchery(html);
            });
            
            $('#exp_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: new Date(),
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            
            $("#no_expire").change(function() {
                if ($(this).prop("checked")) {
                    $("#exp_date").prop("disabled", true);
                } else {
                    $("#exp_date").removeAttr("disabled");
                }
            });
            
            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
            
            $("#total_clients").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#http_broadcast_port").inputFilter(function(value) { return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 65535); });
            $("#https_broadcast_port").inputFilter(function(value) { return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 65535); });
            $("#rtmp_port").inputFilter(function(value) { return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 65535); });
            $("#diff_time_main").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#network_guaranteed_speed").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("form").attr('autocomplete', 'off');
        });
        </script>
    </body>
</html>