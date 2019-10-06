<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_GET["id"])) {
    $rEditID = $_GET["id"];
}

if (isset($_POST["submit_mag"])) {
    if (filter_var($_POST["mac"], FILTER_VALIDATE_MAC)) {
        if ($rArray = getUser($_POST["paired_user"])) {
            if ((isset($_POST["edit"])) && (strlen($_POST["edit"]))) {
                $rCurMag = getMag($_POST["edit"]);
                $db->query("DELETE FROM `users` WHERE `id` = ".intval($rCurMag["user_id"]).";"); // Delete existing user.
                $db->query("DELETE FROM `user_output` WHERE `user_id` = ".intval($rCurMag["user_id"]).";");
            }
            $rArray["username"] .= rand(0,999999);
            $rArray["is_mag"] = 1;
            $rArray["pair_id"] = $rArray["id"];
            unset($rArray["id"]);
            // Create new user.
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
            $rQuery = "INSERT INTO `users`(".$rCols.") VALUES(".$rValues.");";
            if ($db->query($rQuery)) {
                $rNewID = $db->insert_id;
                $rArray = Array("user_id" => $rNewID, "mac" => base64_encode($_POST["mac"]));
                // Create / Edit MAG.
                if (isset($_POST["edit"])) {
                    $db->query("UPDATE `mag_devices` SET `user_id` = ".intval($rNewID).", `mac` = '".base64_encode($db->real_escape_string($_POST["mac"]))."' WHERE `mag_id` = ".intval($_POST["edit"]).";");
                    $rEditID = $_POST["edit"];
                } else {
                    $db->query("INSERT INTO `mag_devices`(`user_id`, `mac`) VALUES(".intval($rNewID).", '".$db->real_escape_string(base64_encode($_POST["mac"]))."');");
                    $rEditID = $db->insert_id;
                }
                $db->query("INSERT INTO `user_output`(`user_id`, `access_output_id`) VALUES(".intval($rNewID).", 2);");
                $_STATUS = 0;
            }
        } else if ((isset($_POST["edit"])) && (strlen($_POST["edit"]))) {
            // Don't create a new user, legacy support for device.
            $db->query("UPDATE `mag_devices` SET `mac` = '".base64_encode($db->real_escape_string($_POST["mac"]))."' WHERE `mag_id` = ".intval($_POST["edit"]).";");
            $rEditID = $_POST["edit"];
            $_STATUS = 0;
        }
    } else {
        $rMagArr = Array("mac" => base64_encode($_POST["mac"]), "paired_user" => $_POST["paired_user"]);
        $_STATUS = 1;
    }
}

if ((isset($rMagArr["paired_user"])) && (!isset($rMagArr["username"]))) {
    // Edit failed, get username.
    $rMagArr["username"] = getUser($rMagArr["paired_user"])["username"];
}

if ((isset($rEditID)) && (!isset($rMagArr))) {
    $rMagArr = getMag($rEditID);
    if (!$rMagArr) {
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
                                    <a href="./mags.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to MAG Devices</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rMagArr)) { echo "Edit"; } else { echo "Add"; } ?> MAG Device</h4>
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
                            Device operation was completed successfully.
                        </div>
                        <?php } else if ((isset($_STATUS)) && ($_STATUS > 0)) { ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            Please check the entered details, they appear to be invalid.
                        </div>
                        <?php } ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="./mag.php<?php if (isset($rEditID)) { echo "?id=".$rEditID; } ?>" method="POST" id="server_form">
                                    <?php if (isset($rMagArr)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rEditID?>" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#mag-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">Details</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="mag-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="sub-header">
                                                            In order to attach a MAG device, please create a normal user in the database and attach it below. The device and user will then be linked.
                                                        </p>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="mac">MAC Address</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="mac" name="mac" value="<?php if (isset($rMagArr)) { echo base64_decode($rMagArr["mac"]); } else { echo "00:1A:79:"; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="paired_user">Paired User</label>
                                                            <div class="col-md-8">
                                                                <select id="paired_user" name="paired_user" class="form-control" data-toggle="select2">
                                                                    <?php if (isset($rMagArr)) { ?>
                                                                    <option value="<?=$rMagArr["paired_user"]?>" selected="selected"><?=$rMagArr["username"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_mag" type="submit" class="btn btn-primary" value="<?php if (isset($rMagArr)) { echo "Edit"; } else { echo "Add"; } ?>" />
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
        $(document).ready(function() {
            $('#paired_user').select2({
              ajax: {
                url: './api.php',
                dataType: 'json',
                data: function (params) {
                  return {
                    search: params.term,
                    action: 'userlist',
                    page: params.page
                  };
                },
                processResults: function (data, params) {
                  params.page = params.page || 1;
                  return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 100) < data.total_count
                    }
                  };
                },
                cache: true,
                width: "100%"
              },
              placeholder: 'Search for a user to pair with...'
            });

            $(document).keypress(function(event){
                if (event.which == '13') {
                    event.preventDefault();
                }
            });
            
            $("form").attr('autocomplete', 'off');
        });
        </script>
    </body>
</html>