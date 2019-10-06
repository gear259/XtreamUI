<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_GET["category"])) {
    if (!isset($rCategories[$_GET["category"]])) {
        exit;
    } else {
        $rCategory = $rCategories[$_GET["category"]];
    }
} else {
    $rCategory = null;
}

include "header.php";
?>        <div class="wrapper">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box">
                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li>
                                        <?php if (!$detect->isMobile()) { ?>
                                        <a href="#" onClick="toggleAuto();" style="margin-right:10px;">
                                            <button type="button" class="btn btn-dark waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-refresh"></i> <span class="auto-text">Auto-Refresh</span>
                                            </button>
                                        </a>
                                        <?php } else { ?>
                                        <a href="javascript:location.reload();" onClick="toggleAuto();" style="margin-right:10px;">
                                            <button type="button" class="btn btn-dark waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-refresh"></i> Refresh
                                            </button>
                                        </a>
                                        <?php } ?>
                                    </li>
                                    <li>
                                        <a href="stream.php<?php if($rCategory) { echo "?category=".$rCategory["id"]; }?>">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add Stream
                                            </button>
                                        </a>
                                    </li>
                                </ol>
                            </div>
                            <h4 class="page-title">Streams<?php if ($rCategory) { echo " - ".$rCategory["category_name"]; } ?></h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            <i class="mdi mdi-alert-outline mr-2"></i> Search functionality is very limited in the <strong>BETA</strong>. This will be replaced and refined shortly. Also pagination speed will improve.
                        </div>
                        <div class="card">
                            <div class="card-body" style="overflow-x:auto;">
                                <table id="datatable" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Server</th>
                                            <th class="text-center">Clients</th>
                                            <th class="text-center">Uptime</th>
                                            <th class="text-center">Actions</th>
                                            <th>Stream Info</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div> <!-- end card body-->
                        </div> <!-- end card -->
                    </div><!-- end col-->
                </div>
                <!-- end row-->
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

        <script>
        var autoRefresh = true;
        
        function toggleAuto() {
            if (autoRefresh == true) {
                autoRefresh = false;
                $(".auto-text").html("Manual Mode");
            } else {
                autoRefresh = true;
                $(".auto-text").html("Auto-Refresh");
            }
        }
        
        function api(rID, rServerID, rType) {
            if (rType == "delete") {
                if (confirm('Are you sure you want to delete this stream?') == false) {
                    return;
                }
            }
            $.getJSON("./api.php?action=stream&sub=" + rType + "&stream_id=" + rID + "&server_id=" + rServerID, function(data) {
                if (data.result == true) {
                    if (rType == "start") {
                        $.toast("Stream successfully started. It will take a minute or so before the stream becomes available.");
                    } else if (rType == "stop") {
                        $.toast("Stream successfully stopped.");
                    } else if (rType == "restart") {
                        $.toast("Stream successfully restarted. It will take a minute or so before the stream becomes available.");
                    } else if (rType == "delete") {
                        $.toast("Stream successfully deleted.");
                    }
                    $.each($('.tooltip'), function (index, element) {
                        $(this).remove();
                    });
                    $("#datatable").DataTable().ajax.reload( null, false );
                } else {
                    $.toast("An error occured while processing your request.");
                }
            }).fail(function() {
                $.toast("An error occured while processing your request.");
            });
        }

        function reloadStreams() {
            if (autoRefresh == true) {
                $("#datatable").DataTable().ajax.reload( null, false );
            }
            setTimeout(reloadStreams, 5000);
        }
        $(document).ready(function() {
            $("#datatable").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                    $('[data-toggle="tooltip"]').tooltip();
                },
                createdRow: function(row, data, index) {
                    $(row).addClass('stream-' + data[0]);
                },
                responsive: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "streams",
                        d.category = "<?=$_GET["category"]?>"
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,4,5,6]}
                ],
            });
            <?php if (!$detect->isMobile()) { ?>
            setTimeout(reloadStreams, 5000);
            <?php } ?>
        });
        </script>

        <!-- third party js -->
        <script src="assets/libs/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables/dataTables.bootstrap4.js"></script>
        <script src="assets/libs/datatables/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables/responsive.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/datatables/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables/buttons.flash.min.js"></script>
        <script src="assets/libs/datatables/buttons.print.min.js"></script>
        <script src="assets/libs/datatables/dataTables.keyTable.min.js"></script>
        <script src="assets/libs/datatables/dataTables.select.min.js"></script>
        <script src="assets/libs/pdfmake/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/vfs_fonts.js"></script>
        <!-- third party js ends -->

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
    </body>
</html>