<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_category"])) {
    $rArray = Array("category_type" => "movie", "category_name" => "", "parent_id" => 0, "cat_order" => 99);
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
    $rQuery = "REPLACE INTO `stream_categories`(".$rCols.") VALUES(".$rValues.");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        header("Location: ./movie_categories.php");exit;
    } else {
        $_STATUS = 1;
    }
}

if (isset($_GET["id"])) {
    $rCategories = getCategories("movie");
    $rCategoryArr = $rCategories[$_GET["id"]];
    if (!$rCategoryArr) {
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
                                    <a href="./movie_categories.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to Categories</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rCategoryArr)) { echo "Edit"; } else { echo "Add"; } ?> Category</h4>
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
                            Category operation was completed successfully.
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
                                <form action="./movie_category.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="category_form">
                                    <?php if (isset($rCategoryArr)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rCategoryArr["id"]?>" />
                                    <input type="hidden" name="cat_order" value="<?=$rCategoryArr["cat_order"]?>" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#category-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
                                            <?php if (isset($rCategoryArr)) { ?>
                                            <li class="nav-item">
                                                <a href="#view-channels" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-play mr-1"></i>
                                                    <span class="d-none d-sm-inline">View Movies</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="category-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="category_name">Category Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="category_name" name="category_name" value="<?php if (isset($rCategoryArr)) { echo $rCategoryArr["category_name"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="parent">Parent</label>
                                                            <div class="col-md-8">
                                                                <select name="parent_id" id="parent_id" class="form-control" data-toggle="select2">
                                                                    <option <?php if (isset($rCategoryArr)) { if (intval($rCategoryArr["parent_id"]) == 0) { echo "selected "; } } ?>value="0">No Parent Category</option>
                                                                    <?php foreach ($rCategories as $rCategory) { ?>
                                                                    <option <?php if (isset($rCategoryArr)) { if (intval($rCategoryArr["parent_id"]) == intval($rCategory["id"])) { echo "selected "; } } ?>value="<?=$rCategory["id"]?>"><?=$rCategory["category_name"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_category" type="submit" class="btn btn-primary" value="<?php if (isset($rCategoryArr)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="view-channels">
                                                <div class="row">
                                                    <div class="col-12" style="overflow-x:auto;">
                                                        <table id="datatable" class="table dt-responsive nowrap">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">Movie ID</th>
                                                                    <th>Movie Name</th>
                                                                    <th class="text-center">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
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
        $(document).ready(function() {
            $('select').select2({width: '100%'})
            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
            $("form").attr('autocomplete', 'off');
            <?php if (isset($rCategoryArr)) { ?>
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
                bInfo: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "movies_short";
                        d.category_id = <?=$rCategoryArr["id"]?>;
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,2]}
                ],
            });
            <?php } ?>
        });
        </script>
        
        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>