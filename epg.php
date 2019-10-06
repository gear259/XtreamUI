<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_epg"])) {
    $rArray = Array("epg_name" => "", "epg_file" => "", "days_keep" => 7, "data" => "");
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
    $rQuery = "REPLACE INTO `epg`(".$rCols.") VALUES(".$rValues.");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        header("Location: ./epgs.php");exit;
    } else {
        $_STATUS = 1;
    }
}

if (isset($_GET["id"])) {
    $rEPGArr = getEPG($_GET["id"]);
    if (!$rEPGArr) {
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
                                    <a href="./epgs.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to EPG's</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rEPGArr)) { echo "Edit"; } else { echo "Add"; } ?> EPG</h4>
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
                            EPG operation was completed successfully.
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
                                <form action="./epg.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="category_form">
                                    <?php if (isset($rEPGArr)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rEPGArr["id"]?>" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#category-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
                                            <?php if (isset($rEPGArr)) { ?>
                                            <li class="nav-item">
                                                <a href="#view-channels" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-play mr-1"></i>
                                                    <span class="d-none d-sm-inline">View Channels</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="category-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="epg_name">EPG Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="epg_name" name="epg_name" value="<?php if (isset($rEPGArr)) { echo $rEPGArr["epg_name"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="epg_file">EPG URL</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="epg_file" name="epg_file" value="<?php if (isset($rEPGArr)) { echo $rEPGArr["epg_file"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="days_keep">Days to Keep</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="days_keep" name="days_keep" value="<?php if (isset($rEPGArr)) { echo $rEPGArr["days_keep"]; } else { echo "7"; } ?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_epg" type="submit" class="btn btn-primary" value="<?php if (isset($rEPGArr)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="view-channels">
                                                <div class="row">
                                                    <div class="col-12" style="overflow-x:auto;">
                                                        <table id="datatable" class="table dt-responsive nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th>Key</th>
                                                                    <th>Channel Name</th>
                                                                    <th>Languages</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $rEPGData = Array();
                                                                if (isset($rEPGArr["data"])) {
                                                                    $rEPGData = json_decode($rEPGArr["data"], True);
                                                                }
                                                                foreach ($rEPGData as $rEPGKey => $rEPGRow) { ?>
                                                                <tr>    
                                                                    <td><?=$rEPGKey?></td>
                                                                    <td><?=$rEPGRow["display_name"]?></td>
                                                                    <td><?=join(", ", $rEPGRow["langs"])?></td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
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

        <!-- Plugins js-->
        <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="assets/js/pages/form-wizard.init.js"></script>
        
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
            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
            $("form").attr('autocomplete', 'off');
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
                responsive: false,
                bAutoWidth: false,
                bInfo: false
            });
            $("#days_keep").inputFilter(function(value) { return /^\d*$/.test(value); });
        });
        </script>
        
        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>