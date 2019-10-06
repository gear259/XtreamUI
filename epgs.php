<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }
$rEPGs = getEPGs();
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
                                        <button type="button" class="btn btn-dark waves-effect waves-light btn-sm" onClick="forceUpdate();" style="margin-right:10px;">
                                            <i class="mdi mdi-refresh"></i> Force EPG Reload
                                        </button>
                                    </li>
                                    <li>
                                        <a href="epg.php">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add EPG
                                            </button>
                                        </a>
                                    </li>
                                </ol>
                            </div>
                            <h4 class="page-title">EPG's</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body" style="overflow-x:auto;">
                                <table id="datatable" class="table dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">ID</th>
                                            <th>EPG Name</th>
                                            <th class="text-center">Days to Keep</th>
                                            <th class="text-center">Last Updated</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rEPGs as $rEPG) {
                                        ?>
                                        <tr id="server-<?=$rEPG["id"]?>">
                                            <td class="text-center"><?=$rEPG["id"]?></td>
                                            <td><?=$rEPG["epg_name"]?></td>
                                            <td class="text-center"><?=$rEPG["days_keep"]?></td>
                                            <td class="text-center"><?php if ($rEPG["last_updated"]) { echo date("Y-m-d H:i:s", $rEPG["last_updated"]); } else { echo "Never"; } ?></td>
                                            <td class="text-center">
                                                <a href="./epg.php?id=<?=$rEPG["id"]?>"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit EPG" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>
                                                <button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete EPG" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api(<?=$rEPG["id"]?>, 'delete');"><i class="mdi mdi-close"></i></button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
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

        <script>
        function api(rID, rType) {
            if (rType == "delete") {
                if (confirm('Are you sure you want to delete this EPG? This cannot be undone!') == false) {
                    return;
                }
            }
            $.getJSON("./api.php?action=epg&sub=" + rType + "&epg_id=" + rID, function(data) {
                if (data.result === true) {
                    if (rType == "delete") {
                        $("#server-" + rID).remove();
                        $.toast("Server successfully deleted.");
                    }
                    $.each($('.tooltip'), function (index, element) {
                        $(this).remove();
                    });
                    $('[data-toggle="tooltip"]').tooltip();
                } else {
                    $.toast("An error occured while processing your request.");
                }
            });
        }
        
        function forceUpdate() {
            $.toast("Updating EPG in the background...");
            $.getJSON("./api.php?action=force_epg", function(data) {
                $.toast("EPG update complete!");
            });
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
                },
                responsive: false
            });
        });
        </script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>