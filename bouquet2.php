<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_bouquet"])) {
    $rArray = Array("bouquet_name" => "", "bouquet_channels" => Array(), "bouquet_series" => Array());
    if (is_array(json_decode($_POST["bouquet_data"], True))) {
        $rBouquetData = json_decode($_POST["bouquet_data"], True);
        $rArray["bouquet_channels"] = array_values($rBouquetData["stream"]);
        $rArray["bouquet_series"] = array_values($rBouquetData["series"]);
    }
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
    $rQuery = "REPLACE INTO `bouquets`(".$rCols.") VALUES(".$rValues.");";
    if ($db->query($rQuery)) {
        if (isset($_POST["edit"])) {
            $rInsertID = intval($_POST["edit"]);
        } else {
            $rInsertID = $db->insert_id;
        }
    }
    if (isset($rInsertID)) {
        $_STATUS = 0;
        $_GET["id"] = $rInsertID;
    } else {
        $_STATUS = 1;
    }
}

if (isset($_GET["id"])) {
    $rBouquets = getBouquets();
    $rBouquetArr = $rBouquets[$_GET["id"]];
    if (!$rBouquetArr) {
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
                                    <a href="./bouquets.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to Bouquets</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rBouquetArr)) { echo "Edit"; } else { echo "Add"; } ?> Bouquet</h4>
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
                            Bouquet operation was completed successfully.
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
                                <form action="./bouquet.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="bouquet_form">
                                    <?php if (isset($rBouquetArr)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rBouquetArr["id"]?>" />
                                    <input type="hidden" id="bouquet_data" name="bouquet_data" value="" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#bouquet-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
                                            <?php if (isset($rBouquetArr)) { ?>
                                            <li class="nav-item">
                                                <a href="#channels" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-play mr-1"></i>
                                                    <span class="d-none d-sm-inline">Streams</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#vod" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-movie mr-1"></i>
                                                    <span class="d-none d-sm-inline">Movie</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#series" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-youtube-tv mr-1"></i>
                                                    <span class="d-none d-sm-inline">Series</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#review" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                                    <i class="mdi mdi-book-open-variant mr-1"></i>
                                                    <span class="d-none d-sm-inline">Review</span>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="bouquet-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="bouquet_name">Bouquet Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="bouquet_name" name="bouquet_name" value="<?php if (isset($rBouquetArr)) { echo $rBouquetArr["bouquet_name"]; } ?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <?php if (isset($rBouquetArr)) { ?>
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                        <?php } else { ?>
                                                        <input name="submit_bouquet" type="submit" class="btn btn-primary" value="Add" />
                                                        <?php } ?>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php if (isset($rBouquetArr)) { ?>
                                            <div class="tab-pane" id="channels">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="category_name">Category Name</label>
                                                            <div class="col-md-8">
                                                                <select id="category_id" class="form-control" data-toggle="select2">
                                                                    <option value="" selected>All Categories</option>
                                                                    <?php foreach ($rCategories as $rCategory) { ?>
                                                                    <option value="<?=$rCategory["id"]?>"><?=$rCategory["category_name"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="stream_search">Search</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="stream_search" value="">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <table id="datatable-streams" class="table nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">ID</th>
                                                                        <th>Stream Name</th>
                                                                        <th>Category</th>
                                                                        <th class="text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                    </li>
                                                    <li class="next list-inline-item float-right">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="vod">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="category_name">Category Name</label>
                                                            <div class="col-md-8">
                                                                <select id="category_idv" class="form-control" data-toggle="select2">
                                                                    <option value="" selected>All Categories</option>
                                                                    <?php foreach (getCategories("movie") as $rCategory) { ?>
                                                                    <option value="<?=$rCategory["id"]?>"><?=$rCategory["category_name"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="vod_search">Search</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="vod_search" value="">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <table id="datatable-vod" class="table nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">ID</th>
                                                                        <th>VOD Name</th>
                                                                        <th>Category</th>
                                                                        <th class="text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                    </li>
                                                    <li class="next list-inline-item float-right">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="series">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="category_name">Category Name</label>
                                                            <div class="col-md-8">
                                                                <select id="category_ids" class="form-control" data-toggle="select2">
                                                                    <option value="" selected>All Categories</option>
                                                                    <?php foreach (getCategories("series") as $rCategory) { ?>
                                                                    <option value="<?=$rCategory["id"]?>"><?=$rCategory["category_name"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="series_search">Search</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="series_search" value="">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <table id="datatable-series" class="table nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">ID</th>
                                                                        <th>Series Name</th>
                                                                        <th>Category</th>
                                                                        <th class="text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                    </li>
                                                    <li class="next list-inline-item float-right">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Next</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="review">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <table id="datatable-review" class="table nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">ID</th>
                                                                        <th>Type</th>
                                                                        <th>Display Name</th>
                                                                        <th class="text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                                    </li>
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_bouquet" type="submit" class="btn btn-primary" value="<?php if (isset($rBouquetArr)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php } ?>
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

        <!-- Plugins js-->
        <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

        <!-- Tree view js -->
        <script src="assets/libs/treeview/jstree.min.js"></script>
        <script src="assets/js/pages/treeview.init.js"></script>
        <script src="assets/js/pages/form-wizard.init.js"></script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
        <script>
        <?php if (isset($rBouquetArr)) {
        if (!is_array(json_decode($rBouquetArr["bouquet_series"], True))) { $rBouquetArr["bouquet_series"] = "[]"; }
        if (!is_array(json_decode($rBouquetArr["bouquet_channels"], True))) { $rBouquetArr["bouquet_channels"] = "[]"; }
        ?>
        var rBouquet = {"stream": $.parseJSON(<?=json_encode($rBouquetArr["bouquet_channels"])?>), "series": $.parseJSON(<?=json_encode($rBouquetArr["bouquet_series"])?>)};
        <?php } ?>
        function reviewBouquet() {
            var rTable = $('#datatable-review').DataTable();
            rTable.clear();
            rTable.draw();
            $.post("./api.php?action=review_bouquet", {"data": rBouquet}, function(rData) {
                if (rData.result === true) {
                    $(rData.streams).each(function(rIndex) {
                        rTable.row.add([rData.streams[rIndex].id, "Stream", rData.streams[rIndex].stream_display_name, '<button type="button" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet(' + rData.streams[rIndex].id + ', \'stream\', true);"><i class="mdi mdi-minus"></i></button>']);
                    });
                    $(rData.vod).each(function(rIndex) {
                        rTable.row.add([rData.vod[rIndex].id, "Movie", rData.vod[rIndex].stream_display_name, '<button type="button" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet(' + rData.vod[rIndex].id + ', \'vod\', true);"><i class="mdi mdi-minus"></i></button>']);
                    });
                    $(rData.series).each(function(rIndex) {
                        rTable.row.add([rData.series[rIndex].id, "Series", rData.series[rIndex].title, '<button type="button" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet(' + rData.series[rIndex].id + ', \'series\', true);"><i class="mdi mdi-minus"></i></button>']);
                    });
                } else {
                    alert("Bouquet review failed!");
                }
                rTable.draw();
            }, "json");
        }
        
        function toggleBouquet(rID, rType, rReview = false) {
            var rIndex = rBouquet[rType].indexOf(String(rID));
            if (rIndex > -1) {
                rBouquet[rType] = jQuery.grep(rBouquet[rType], function(rValue) {
                  return String(rValue) != String(rID);
                });
            } else {
                rBouquet[rType].push(String(rID));
            }
            if (rType == "stream") {
                $("#datatable-streams").DataTable().ajax.reload(null, false);
                $("#datatable-vod").DataTable().ajax.reload(null, false);
            } else {
                $("#datatable-series").DataTable().ajax.reload(null, false);
            }
            if (rReview == true) {
                reviewBouquet()
            }
        }
        
        $(document).ready(function() {
            $("#datatable-streams").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                createdRow: function(row, data, index) {
                    $(row).addClass('stream-' + data[0]);
                    var rIndex = rBouquet["stream"].indexOf(String(data[0]));
                    if (rIndex > -1) {
                        $(row).find(".btn-remove").show();
                    } else {
                        $(row).find(".btn-add").show();
                    }
                },
                bInfo: false,
                bAutoWidth: false,
                searching: true,
                pageLength: 50,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "bouquets_streams";
                        d.category_id = $("#category_id").val();
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,3]}
                ],
            });
            $("#datatable-vod").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                createdRow: function(row, data, index) {
                    $(row).addClass('vod-' + data[0]);
                    var rIndex = rBouquet["stream"].indexOf(String(data[0]));
                    if (rIndex > -1) {
                        $(row).find(".btn-remove").show();
                    } else {
                        $(row).find(".btn-add").show();
                    }
                },
                bInfo: false,
                bAutoWidth: false,
                searching: true,
                pageLength: 50,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "bouquets_vod";
                        d.category_id = $("#category_idv").val();
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,3]}
                ],
            });
            $("#datatable-series").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                createdRow: function(row, data, index) {
                    $(row).addClass('series-' + data[0]);
                    var rIndex = rBouquet["series"].indexOf(String(data[0]));
                    if (rIndex > -1) {
                        $(row).find(".btn-remove").show();
                    } else {
                        $(row).find(".btn-add").show();
                    }
                },
                bInfo: false,
                bAutoWidth: false,
                searching: true,
                pageLength: 50,
                lengthChange: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "./table.php",
                    "data": function(d) {
                        d.id = "bouquets_series";
                        d.category_id = $("#category_ids").val();
                    }
                },
                columnDefs: [
                    {"className": "dt-center", "targets": [0,3]}
                ],
            });
            $("#datatable-review").DataTable({
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function() {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                },
                bInfo: false,
                bAutoWidth: false,
                searching: true,
                pageLength: 50,
                lengthChange: false,
                columnDefs: [
                    {"className": "dt-center", "targets": [0,1,3]}
                ],
            });
            $('select').select2({width: '100%'});
            $("#category_id").on("select2:select", function(e) { 
                $("#datatable-streams").DataTable().ajax.reload(null, false);
            });
            $('#stream_search').keyup(function(){
                $('#datatable-streams').DataTable().search($(this).val()).draw();
            })
            $("#category_idv").on("select2:select", function(e) { 
                $("#datatable-vod").DataTable().ajax.reload(null, false);
            });
            $('#vod_search').keyup(function(){
                $('#datatable-vod').DataTable().search($(this).val()).draw();
            })
            $("#category_ids").on("select2:select", function(e) { 
                $("#datatable-series").DataTable().ajax.reload(null, false);
            });
            $('#series_search').keyup(function(){
                $('#datatable-series').DataTable().search($(this).val()).draw();
            })
            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if ($(e.target).attr("href") == "#review") {
                    reviewBouquet();
                }
            });
            $("#bouquet_form").submit(function(e){
                if ($("#bouquet_name").val().length == 0) {
                    e.preventDefault();
                    $.toast("Enter a bouquet name.");
                }
                $("#bouquet_data").val(JSON.stringify(rBouquet));
            });
            $("form").attr('autocomplete', 'off');
        });
        </script>
    </body>
</html>