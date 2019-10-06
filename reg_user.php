<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_user"])) {
    if (isset($_POST["edit"])) {
        $rArray = getRegisteredUser($_POST["edit"]);
        unset($rArray["id"]);
    } else {
        $rArray = Array("username" => "", "password" => "", "email" => "", "member_group_id" => 1);
    }
    if ((strlen($_POST["username"]) == 0) OR ((strlen($_POST["password"]) == 0) AND (!isset($_POST["edit"])))) {
        $_STATUS = 1;
    }
    if (!isset($_STATUS)) {
        if (isset($_POST["verified"])) {
            $rArray["verified"] = 1;
            unset($_POST["verified"]);
        } else {
            $rArray["verified"] = 0;
        }
        if (strlen($_POST["password"]) == 0) {
        } else {
            $rArray["password"] = cryptPassword($_POST["password"]);
        }
        unset($_POST["password"]);
        foreach($_POST as $rKey => $rValue) {
            if (isset($rArray[$rKey])) {
                $rArray[$rKey] = $rValue;
            }
        }
        $rCols = "`".implode('`,`', array_keys($rArray))."`";
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
            $rCols = "`id`,".$rCols;
            $rValues = $_POST["edit"].",".$rValues;
        }
        $rQuery = "REPLACE INTO `reg_users`(".$rCols.") VALUES(".$rValues.");";
        if ($db->query($rQuery)) {
            if (isset($_POST["edit"])) {
                $rInsertID = intval($_POST["edit"]);
            } else {
                $rInsertID = $db->insert_id;
            }
            $_STATUS = 0;
        } else {
            $_STATUS = 2;
        }
        if (!isset($_GET["id"])) {
            $_GET["id"] = $rInsertID;
        }
    }
}

if (isset($_GET["id"])) {
    $rUser = getRegisteredUser($_GET["id"]);
    if (!$rUser) {
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
                                    <a href="./reg_users.php"><li class="breadcrumb-item"><i class="mdi mdi-backspace"></i> Back to Registered Users</li></a>
                                </ol>
                            </div>
                            <h4 class="page-title"><?php if (isset($rUser)) { echo "Edit"; } else { echo "Add"; } ?> Registered User</h4>
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
                            User operation was completed successfully.
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
                                <form action="./reg_user.php<?php if (isset($_GET["id"])) { echo "?id=".$_GET["id"]; } ?>" method="POST" id="user_form">
                                    <?php if (isset($rUser)) { ?>
                                    <input type="hidden" name="edit" value="<?=$rUser["id"]?>" />
                                    <input type="hidden" name="status" value="<?=$rUser["status"]?>" />
                                    <?php } ?>
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#user-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
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
                                            <div class="tab-pane" id="user-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="username">Username</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="username" name="username" value="<?php if (isset($rUser)) { echo $rUser["username"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="password"><?php if (isset($rUser)) { ?>Change <?php } ?>Password</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="password" name="password" value="">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="email">Email Address</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="email" name="email" value="<?php if (isset($rUser)) { echo $rUser["email"]; } ?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="member_group_id">Member Group</label>
                                                            <div class="col-md-8">
                                                                <select name="member_group_id" id="member_group_id" class="form-control select2" data-toggle="select2">
                                                                    <?php foreach (getMemberGroups() as $rGroup) { ?>
                                                                    <option <?php if (isset($rUser)) { if (intval($rUser["member_group_id"]) == intval($rGroup["group_id"])) { echo "selected "; } } ?>value="<?=$rGroup["group_id"]?>"><?=$rGroup["group_name"]?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="verified">Verified</label>
                                                            <div class="col-md-2">
                                                                <input name="verified" id="verified" type="checkbox"<?php if ((isset($rUser)) && ($rUser["verified"] == 1)) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="next list-inline-item float-right">
                                                        <input name="submit_user" type="submit" class="btn btn-primary" value="<?php if (isset($rUser)) { echo "Edit"; } else { echo "Add"; } ?>" />
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-pane" id="advanced-options">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="sub-header text-center">
                                                            Advanced options and reseller options are coming in a future release. Stay tuned.
                                                        </p>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                                <ul class="list-inline wizard mb-0">
                                                    <li class="previous list-inline-item">
                                                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
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
        
        function selectAll() {
            $(".bouquet-checkbox").each(function() {
                $(this).prop('checked', true);
            });
        }
        
        function selectNone() {
            $(".bouquet-checkbox").each(function() {
                $(this).prop('checked', false);
            });
        }
        function isValidDate(dateString) {
              var regEx = /^\d{4}-\d{2}-\d{2}$/;
              if(!dateString.match(regEx)) return false;  // Invalid format
              var d = new Date(dateString);
              var dNum = d.getTime();
              if(!dNum && dNum !== 0) return false; // NaN value, Invalid date
              return d.toISOString().slice(0,10) === dateString;
        }
        
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
            
            $("#max_connections").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("form").attr('autocomplete', 'off');
        });
        </script>
    </body>
</html>