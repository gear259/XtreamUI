<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

$rCategories = getCategories();

if (isset($_POST["categories"])) {
    $rPostCategories = json_decode($_POST["categories"], True);
    if (count($rPostCategories) > 0) {
        $rKeep = Array();
        foreach ($rPostCategories as $rOrder => $rPostCategory) {
            $db->query("UPDATE `stream_categories` SET `cat_order` = ".(intval($rOrder)+1).", `parent_id` = 0 WHERE `id` = ".intval($rPostCategory["id"]).";");
            $rKeep[] = $rPostCategory["id"];
            if (isset($rPostCategory["children"])) {
                foreach ($rPostCategory["children"] as $rChildOrder => $rChildCategory) {
                    $db->query("UPDATE `stream_categories` SET `cat_order` = ".(intval($rChildOrder)+1).", `parent_id` = ".intval($rPostCategory["id"])." WHERE `id` = ".intval($rChildCategory["id"]).";");
                    $rKeep[] = $rChildCategory["id"];
                }
            }
        }
        foreach ($rCategories as $rCategoryID => $rCategoryData) {
            if (!in_array($rCategoryID, $rKeep)) {
                $db->query("DELETE FROM `stream_categories` WHERE `id` = ".intval($rCategoryID).";");
                $db->query("UPDATE `streams` SET `category_id` = 0 WHERE `category_id` = ".intval($rCategoryID).";");
            }
        }
        $rCategories = getCategories(); // Update
    }
}

$rMainCategories = Array(); $rSubCategories = Array();
foreach ($rCategories as $rCategoryID => $rCategoryData) {
    if ($rCategoryData["parent_id"] <> 0) {
        $rSubCategories[$rCategoryData["parent_id"]][] = $rCategoryData;
    } else {
        $rMainCategories[] = $rCategoryData;
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
                                    <li>
                                        <a href="stream_category.php">
                                            <button type="button" class="btn btn-success waves-effect waves-light btn-sm">
                                                <i class="mdi mdi-plus"></i> Add Category
                                            </button>
                                        </a>
                                    </li>
                                </ol>
                            </div>
                            <h4 class="page-title">Stream Categories</h4>
                        </div>
                    </div>
                </div>     
                <!-- end page title --> 
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="./stream_categories.php" method="POST" id="stream_categories_form">
                                    <input type="hidden" id="categories_input" name="categories" value="" />
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#category-order" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-format-list-bulleted mr-1"></i>
                                                    <span class="d-none d-sm-inline">Category Order</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="category-order">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="sub-header">
                                                            To re-order a category, drag it up or down the list using the <i class="mdi mdi-view-sequential"></i> icon. Categories can be added as a subcategory by dragging it right to offset it, then up or down to the category it belongs in. Click Save Changes at the bottom once finished.
                                                        </p>
                                                        <div class="custom-dd dd" id="category_order">
                                                            <ol class="dd-list">
                                                                <?php foreach ($rMainCategories as $rCategory) { ?>
                                                                <li class="dd-item dd3-item category-<?=$rCategory["id"]?>" data-id="<?=$rCategory["id"]?>">
                                                                    <div class="dd-handle dd3-handle"></div>
                                                                    <div class="dd3-content"><?=$rCategory["category_name"]?>
                                                                        <span style="float:right;">
                                                                            <a href="./stream_category.php?id=<?=$rCategory["id"]?>"><button type="button" class="btn btn-outline-info waves-effect waves-light"><i class="mdi mdi-pencil-outline"></i></button></a>
                                                                            <button type="button" class="btn btn-outline-danger waves-effect waves-light" onClick="deleteCategory(<?=$rCategory["id"]?>)"><i class="mdi mdi-close"></i></button>
                                                                        </span>
                                                                    </div>
                                                                    <?php if (isset($rSubCategories[$rCategory["id"]])) { ?>
                                                                    <ol class="dd-list">
                                                                        <?php foreach ($rSubCategories[$rCategory["id"]] as $rSubCategory) { ?>
                                                                        <li class="dd-item dd3-item category-<?=$rSubCategory["id"]?>" data-id="<?=$rSubCategory["id"]?>">
                                                                            <div class="dd-handle dd3-handle"></div>
                                                                            <div class="dd3-content"><?=$rSubCategory["category_name"]?>
                                                                                <span style="float:right;">
                                                                                    <a href="./stream_category.php?id=<?=$rSubCategory["id"]?>"><button type="button" class="btn btn-outline-info waves-effect waves-light"><i class="mdi mdi-pencil-outline"></i></button></a>
                                                                                    <button type="button" class="btn btn-outline-danger waves-effect waves-light" onClick="deleteCategory(<?=$rSubCategory["id"]?>)"><i class="mdi mdi-close"></i></button>
                                                                                </span>
                                                                            </div>
                                                                        </li>
                                                                        <?php } ?>
                                                                    </ol>
                                                                <?php } ?>
                                                                </li>
                                                                <?php } ?>
                                                            </ol>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0 add-margin-top-20">
                                                    <li class="next list-inline-item float-right">
                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
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
        <script src="assets/libs/nestable2/jquery.nestable.min.js"></script>

        <!-- Plugins js-->
        <script src="assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>

        <!-- Tree view js -->
        <script src="assets/libs/treeview/jstree.min.js"></script>
        <script src="assets/js/pages/treeview.init.js"></script>
        <script src="assets/js/pages/form-wizard.init.js"></script>

        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
        
        <script>
        function deleteCategory(rID) {
            if (confirm("Are you sure you want to delete this category? All streams attached will be uncategorised.")) {
                $.getJSON("./api.php?action=category&sub=delete&category_id=" + rID, function(data) {
                    if (data.result === true) {
                        $(".category-" + rID).remove();
                        $.toast("Category successfully deleted.");
                        $.each($('.tooltip'), function (index, element) {
                            $(this).remove();
                        });
                        $('[data-toggle="tooltip"]').tooltip();
                    } else {
                        $.toast("An error occured while processing your request.");
                    }
                });
            }
        }
        $(document).ready(function() {
            $("#category_order").nestable({maxDepth: 2});
            $("#stream_categories_form").submit(function(e){
                $("#categories_input").val(JSON.stringify($('.dd').nestable('serialize')));
            });
            
        });
        </script>
    </body>
</html>