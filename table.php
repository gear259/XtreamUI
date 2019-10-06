<?php
include "functions.php";
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (!isset($_SESSION['user_id'])) { exit; }

if ($_GET["id"] == "users") {
    $rRegisteredUsers = getRegisteredUsernames();
    $rActivity = Array();
    $result = $db->query("SELECT `user_id`, COUNT(`activity_id`) AS `count` FROM `user_activity_now` GROUP BY `user_id`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $rActivity[$row["user_id"]] = intval($row["count"]);
        }
    }
    $rLastActivity = Array();
    $result = $db->query("SELECT `user_id`, MAX(`date_start`) AS `date_start` FROM `user_activity` GROUP BY `user_id`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $rLastActivity[$row["user_id"]] = intval($row["date_start"]);
        }
    }

    $table = 'users';
    $get = $_GET["id"];
    $primaryKey = 'id';
    $extraWhere = "";

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'username', 'dt' => 1),
        array('db' => 'member_id', 'dt' => 2,
            'formatter' => function( $d, $row ) {
                global $rRegisteredUsers;
                return $rRegisteredUsers[intval($d)]["username"];
            }
        ),
        array('db' => 'admin_enabled', 'dt' => 3,
            'formatter' => function( $d, $row ) {
                if ($d == 0) {
                    return "BANNED";
                } else {
                    if ($row["enabled"] == 0) {
                        return "DISABLED";
                    } else if (($row["exp_date"]) && ($row["exp_date"] < time())) {
                        return "EXPIRED";
                    } else {
                        return "ACTIVE";
                    }
                }
            }
        ),
        array('db' => 'id', 'dt' => 4,
            'formatter' => function( $d, $row ) {
                global $rActivity;
                return Array(0 => "OFFLINE", 1 => "ONLINE")[(int)(isset($rActivity[intval($d)]) === 'true')];
            }
        ),
        array('db' => 'exp_date', 'dt' => 5,
            'formatter' => function( $d, $row ) {
                if ($d) { return date("Y-m-d", $d); } else { return "Unlimited"; }
            }
        ),
        array('db' => 'max_connections', 'dt' => 6,
            'formatter' => function( $d, $row ) {
                global $rActivity;
                if (isset($rActivity[intval($row["id"])])) {
                    $val = $rActivity[intval($row["id"])];
                } else {
                    $val = 0;
                }
                if ($d == 0) { $d = "&infin;"; }
                return $val." / ".$d;
            }
        ),
        array('db' => 'max_connections', 'dt' => 7,
            'formatter' => function( $d, $row ) {
                global $rLastActivity;
                if (isset($rLastActivity[intval($row["id"])])) {
                    return date("Y-m-d", $rLastActivity[intval($row["id"])]);
                } else {
                    return "Never";
                }
            }
        ),
        array('db' => 'id', 'dt' => 8,
            'formatter' => function( $d, $row ) {
                $rButtons = '<a href="./user.php?id='.$d.'"><button type="button" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
                if ($row["admin_enabled"] == 1) {
                    $rButtons .= '<button type="button" class="btn btn-outline-primary waves-effect waves-light btn-xs" onClick="api('.$d.', \'ban\');""><i class="mdi mdi-power"></i></button>';
                } else {
                    $rButtons .= '<button type="button" class="btn btn-outline-primary waves-effect waves-light btn-xs" onClick="api('.$d.', \'unban\');""><i class="mdi mdi-power"></i></button>';
                }
                if ($row["enabled"] == 1) {
                    $rButtons .= '<button type="button" class="btn btn-outline-success waves-effect waves-light btn-xs" onClick="api('.$d.', \'disable\');""><i class="mdi mdi-lock"></i></button>';
                } else {
                    $rButtons .= '<button type="button" class="btn btn-outline-success waves-effect waves-light btn-xs" onClick="api('.$d.', \'enable\');""><i class="mdi mdi-lock"></i></button>';
                }
                $rButtons .= '<button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', \'delete\');""><i class="mdi mdi-close"></i></button>';
                return $rButtons;
            }
        ),
        array('db' => 'enabled', 'hide' => true),
        array('db' => 'exp_date', 'hide' => true),
    );
} else if ($_GET["id"] == "reg_users") {
    $rMemberGroups = getMemberGroups();
    
    $table = 'reg_users';
    $get = $_GET["id"];
    $primaryKey = 'id';
    $extraWhere = "";

    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'username', 'dt' => 1),
        array('db' => 'email', 'dt' => 2),
        array('db' => 'ip', 'dt' => 3),
        array('db' => 'member_group_id', 'dt' => 4,
            'formatter' => function( $d, $row ) {
                global $rMemberGroups;
                return $rMemberGroups[intval($d)]["group_name"];
            }
        ),
        array('db' => 'status', 'dt' => 5,
            'formatter' => function( $d, $row ) {
                return Array(0 => "DISABLED", 1 => "ENABLED")[$d];
            }
        ),
        array('db' => 'verified', 'dt' => 6,
            'formatter' => function( $d, $row ) {
                return Array(0 => "UNVERIFIED", 1 => "VERIFIED")[$d];
            }
        ),
        array('db' => 'last_login', 'dt' => 7,
            'formatter' => function( $d, $row ) {
                if ($d) {
                    return date("Y-m-d H:i:s", $d);
                } else {
                    return "NEVER";
                }
            }
        ),
        array('db' => 'id', 'dt' => 8,
            'formatter' => function( $d, $row ) {
                $rButtons = '<a href="./reg_user.php?id='.$d.'"><button type="button" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
                if ($row["status"] == 1) {
                    $rButtons .= '<button type="button" class="btn btn-outline-success waves-effect waves-light btn-xs" onClick="api('.$d.', \'disable\');""><i class="mdi mdi-lock"></i></button>';
                } else {
                    $rButtons .= '<button type="button" class="btn btn-outline-success waves-effect waves-light btn-xs" onClick="api('.$d.', \'enable\');""><i class="mdi mdi-lock"></i></button>';
                }
                $rButtons .= '<button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', \'delete\');""><i class="mdi mdi-close"></i></button>';
                return $rButtons;
            }
        )
    );
} else if ($_GET["id"] == "mags") {
    $table = 'mag_devices';
    $get = $_GET["id"];
    $primaryKey = 'mag_id';
    $extraWhere = "";

    $columns = array(
        array('db' => 'mag_id', 'dt' => 0),
        array('db' => 'mac', 'dt' => 1,
            'formatter' => function( $d, $row ) {
                return base64_decode($d);
            }
        ),
        array('db' => 'user_id', 'dt' => 2,
            'formatter' => function( $d, $row ) {
                return "<a href='./user.php?id=".$d."'>".getUser(intval($d))["username"]."</a>";
            }
        ),
        array('db' => 'mag_id', 'dt' => 3,
            'formatter' => function( $d, $row ) {
                $rButtons = '<a href="./mag.php?id='.$d.'"><button type="button" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
                $rButtons .= '<button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', \'delete\');""><i class="mdi mdi-close"></i></button>';
                return $rButtons;
            }
        )
    );
} else if ($_GET["id"] == "enigmas") {
    $table = 'enigma2_devices';
    $get = $_GET["id"];
    $primaryKey = 'device_id';
    $extraWhere = "";

    $columns = array(
        array('db' => 'device_id', 'dt' => 0),
        array('db' => 'mac', 'dt' => 1),
        array('db' => 'user_id', 'dt' => 2,
            'formatter' => function( $d, $row ) {
                return "<a href='./user.php?id=".$d."'>".getUser(intval($d))["username"]."</a>";
            }
        ),
        array('db' => 'device_id', 'dt' => 3,
            'formatter' => function( $d, $row ) {
                $rButtons = '<a href="./enigma.php?id='.$d.'"><button type="button" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
                $rButtons .= '<button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', \'delete\');""><i class="mdi mdi-close"></i></button>';
                return $rButtons;
            }
        )
    );
} else if ($_GET["id"] == "mag_events") {
    $table = 'mag_events';
    $get = $_GET["id"];
    $primaryKey = 'id';
    $extraWhere = "";

    $columns = array(
        array('db' => 'send_time', 'dt' => 0,
            'formatter' => function( $d, $row ) {
                return date("Y-m-d H:i:s", $d);
            }
        ),
        array('db' => 'status', 'dt' => 1),
        array('db' => 'mag_device_id', 'dt' => 2,
            'formatter' => function( $d, $row ) {
                return base64_decode(getMag($d)["mac"]);
            }
        ),
        array('db' => 'event', 'dt' => 3),
        array('db' => 'msg', 'dt' => 4),
        array('db' => 'id', 'dt' => 5,
            'formatter' => function( $d, $row ) {
                $rButtons = '<button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', \'delete\');""><i class="mdi mdi-close"></i></button>';
                return $rButtons;
            }
        )
    );
} else if ($_GET["id"] == "streams") {
    $rStreamInformation = Array();
    $table = 'streams';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category"])) && (strlen($_GET["category"]) > 0)) {
        $extraWhere = "`type` IN (1,3) AND `category_id` = ".intval($_GET["category"]);
    } else if ((isset($_GET["stream_id"])) && (strlen($_GET["stream_id"]) > 0)) {
        $extraWhere = "`type` IN (1,3) AND `id` = '".$db->real_escape_string($_GET["stream_id"])."'";
    } else {
        $extraWhere = "`type` IN (1,3)";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0,
            'formatter' => function( $d, $row, $server ) {
                return $d;
            }
        ),
        array('db' => 'stream_display_name', 'dt' => 1),
        array('db' => 'category_id', 'dt' => 2,
            'formatter' => function( $d, $row, $server ) {
                global $rCategories;
                return $rCategories[$d]["category_name"];
            }
        ),
        array('db' => 'id', 'dt' => 3,
            'formatter' => function( $d, $row, $server ) {
                global $rServers;
                if (isset($rServers[$server["server_id"]]["server_name"])) {
                    return $rServers[$server["server_id"]]["server_name"];
                } else {
                    return "No Server Selected";
                }
            }
        ),
        array('db' => 'id', 'dt' => 4,
            'formatter' => function( $d, $row, $server ) {
                return $server["active_count"];
            }
        ),
        array('db' => 'id', 'dt' => 5,
            'formatter' => function( $d, $row, $server ) {
                global $rStatusArray;
                if ($server["actual_status"] == 1) {
                    return $server["uptime_text"];
                } else {
                    return $rStatusArray[$server["actual_status"]];
                }
            }
        ),
        array('db' => 'id', 'dt' => 6,
            'formatter' => function( $d, $row, $server ) {
                if ((intval($server["actual_status"]) == 1) OR ($server["on_demand"] == 1) OR ($server["actual_status"] == 5)) { $rStatusA = " style=\"display:none;\""; } else { $rStatusA = ""; }
                if (intval($server["actual_status"]) <> 1) { $rStatusB = " style=\"display:none;\""; } else { $rStatusB = ""; }
                if (!$server["server_id"]) { $server["server_id"] = 0; }
                return '<button type="button" class="btn btn-outline-success waves-effect waves-light btn-xs api-start" onClick="api('.$d.', '.$server["server_id"].', \'start\');"'.$rStatusA.'><i class="mdi mdi-play"></i></button>
                <button type="button" class="btn btn-outline-warning waves-effect waves-light btn-xs api-stop" onClick="api('.$d.', '.$server["server_id"].', \'stop\');"'.$rStatusB.'><i class="mdi mdi-stop"></i></button>
                <button type="button" class="btn btn-outline-primary waves-effect waves-light btn-xs api-restart" onClick="api('.$d.', '.$server["server_id"].', \'restart\');"'.$rStatusB.'><i class="mdi mdi-refresh"></i></button>
                <a href="./stream.php?id='.$d.'"><button type="button" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>
                <button type="button" class="btn btn-outline-danger waves-effect waves-light btn-xs" onClick="api('.$d.', '.$server["server_id"].', \'delete\');"><i class="mdi mdi-close"></i></button>';
            }
        ),
        array('db' => 'id', 'dt' => 7,
            'formatter' => function( $d, $row, $server ) {
                return $server["stream_text"];
            }
        )
    );
} else if ($_GET["id"] == "bouquets_streams") {
    $table = 'streams';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category_id"])) && (strlen($_GET["category_id"]) > 0)) {
        $extraWhere = "(`type` = 1 OR `type` = 3) AND `category_id` = ".intval($_GET["category_id"]);
    } else {
        $extraWhere = "(`type` = 1 OR `type` = 3)";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'stream_display_name', 'dt' => 1),
        array('db' => 'category_id', 'dt' => 2,
            'formatter' => function( $d, $row) {
                global $rCategories;
                return $rCategories[$d]["category_name"];
            }
        ),
        array('db' => 'id', 'dt' => 3,
            'formatter' => function( $d, $row) {
                return '<button type="button" style="display: none;" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'stream\');"><i class="mdi mdi-minus"></i></button>
                <button type="button" style="display: none;" class="btn-add btn btn-outline-info waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'stream\');"><i class="mdi mdi-plus"></i></button>';
            }
        )
    );
} else if ($_GET["id"] == "streams_short") {
    $table = 'streams';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category_id"])) && (strlen($_GET["category_id"]) > 0)) {
        $extraWhere = "(`type` = 1 OR `type` = 3) AND `category_id` = ".intval($_GET["category_id"]);
    } else {
        $extraWhere = "(`type` = 1 OR `type` = 3)";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'stream_display_name', 'dt' => 1),
        array('db' => 'id', 'dt' => 2,
            'formatter' => function( $d, $row) {
                return '<a href="./stream.php?id='.$d.'"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Stream" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
            }
        )
    );
} else if ($_GET["id"] == "movies_short") {
    $table = 'streams';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category_id"])) && (strlen($_GET["category_id"]) > 0)) {
        $extraWhere = "`type` = 2 AND `category_id` = ".intval($_GET["category_id"]);
    } else {
        $extraWhere = "`type` = 2";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'stream_display_name', 'dt' => 1),
        array('db' => 'id', 'dt' => 2,
            'formatter' => function( $d, $row) {
                return '<a href="./movie.php?id='.$d.'"><button type="button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit Movie" class="btn btn-outline-info waves-effect waves-light btn-xs"><i class="mdi mdi-pencil-outline"></i></button></a>';
            }
        )
    );
} else if ($_GET["id"] == "bouquets_vod") {
    $rCategoriesVOD = getCategories("movie");
    $table = 'streams';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category_id"])) && (strlen($_GET["category_id"]) > 0)) {
        $extraWhere = "`type` = 2 AND `category_id` = ".intval($_GET["category_id"]);
    } else {
        $extraWhere = "`type` = 2";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'stream_display_name', 'dt' => 1),
        array('db' => 'category_id', 'dt' => 2,
            'formatter' => function( $d, $row) {
                global $rCategoriesVOD;
                return $rCategoriesVOD[$d]["category_name"];
            }
        ),
        array('db' => 'id', 'dt' => 3,
            'formatter' => function( $d, $row) {
                return '<button type="button" style="display: none;" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'vod\');"><i class="mdi mdi-minus"></i></button>
                <button type="button" style="display: none;" class="btn-add btn btn-outline-info waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'vod\');"><i class="mdi mdi-plus"></i></button>';
            }
        )
    );
} else if ($_GET["id"] == "bouquets_series") {
    $rCategoriesVOD = getCategories("series");
    $table = 'series';
    $get = $_GET["id"];
    $primaryKey = 'id';
    if ((isset($_GET["category_id"])) && (strlen($_GET["category_id"]) > 0)) {
        $extraWhere = "`category_id` = ".intval($_GET["category_id"]);
    } else {
        $extraWhere = "";
    }
    $columns = array(
        array('db' => 'id', 'dt' => 0),
        array('db' => 'title', 'dt' => 1),
        array('db' => 'category_id', 'dt' => 2,
            'formatter' => function( $d, $row) {
                global $rCategoriesVOD;
                return $rCategoriesVOD[$d]["category_name"];
            }
        ),
        array('db' => 'id', 'dt' => 3,
            'formatter' => function( $d, $row) {
                return '<button type="button" style="display: none;" class="btn-remove btn btn-outline-danger waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'series\');"><i class="mdi mdi-minus"></i></button>
                <button type="button" style="display: none;" class="btn-add btn btn-outline-info waves-effect waves-light btn-xs" onClick="toggleBouquet('.$d.', \'series\');"><i class="mdi mdi-plus"></i></button>';
            }
        )
    );
} else {
    exit;
}

$sql_details = array(
    'user' => $_INFO["db_user"],
    'pass' => $_INFO["db_pass"],
    'db'   => $_INFO["db_name"],
    'host' => $_INFO["host"].":".$_INFO["db_port"]
);
 
class SSP {
    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data    Data from the SQL get
     * @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     * @return array Formatted data in a row based format
     */
    static function data_output ( $columns, $data, $isJoin = false )
    {
        global $get;
        global $rStreamInformation;
        $out = array();
        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();
            if ($get == "streams") {
                $rStreamInformation[intval($data[$i]["id"])] = getStreams(null, true, Array($data[$i]["id"]))[0];
                if (count($rStreamInformation[intval($data[$i]["id"])]["servers"]) == 0) {
                    $rStreamInformation[intval($data[$i]["id"])]["servers"][] = Array("id" => 0, "active_count" => 0, "stream_text" => "Not Available", "uptime_text" => "--", "actual_status" => 0);
                }
                foreach ($rStreamInformation[intval($data[$i]["id"])]["servers"] as $rServer) {
                    for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                        $column = $columns[$j];
                        // Is there a formatter?
                        if ( isset( $column['formatter'] ) ) {
                            $row[ $column['dt'] ] = ($isJoin) ? $column['formatter']( $data[$i][ $column['field'] ], $data[$i], $rServer ) : $column['formatter']( $data[$i][ $column['db'] ], $data[$i], $rServer );
                        } else if (!isset($column["hide"])) {
                            $row[ $column['dt'] ] = ($isJoin) ? $data[$i][ $columns[$j]['field'] ] : $data[$i][ $columns[$j]['db'] ];
                        }
                    }
                    $out[] = $row;
                }
            } else {
                for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                    $column = $columns[$j];
                    // Is there a formatter?
                    if ( isset( $column['formatter'] ) ) {
                        $row[ $column['dt'] ] = ($isJoin) ? $column['formatter']( $data[$i][ $column['field'] ], $data[$i] ) : $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                    } else if (!isset($column["hide"])) {
                        $row[ $column['dt'] ] = ($isJoin) ? $data[$i][ $columns[$j]['field'] ] : $data[$i][ $columns[$j]['db'] ];
                    }
                }
                $out[] = $row;
            }
        }
        return $out;
    }
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit ( $request, $columns )
    {
        $limit = '';
        if ( isset($request['start']) && $request['length'] != -1 ) {
            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        } else {
            $limit = "LIMIT 50";
        }
        return $limit;
    }
    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL order by clause
     */
    static function order ( $request, $columns, $isJoin = false )
    {
        $order = '';
        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = SSP::pluck( $columns, 'dt' );
            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = ($isJoin) ? $column['db'].' '.$dir : '`'.$column['db'].'` '.$dir;
                }
            }
            $order = 'ORDER BY '.implode(', ', $orderBy);
        }
        return $order;
    }
    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the sql_exec() function
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL where clause
     */
    static function filter ( $request, $columns, &$bindings, $isJoin = false, $table = null)
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = SSP::pluck( $columns, 'dt' );
        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];
            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];
                if ( $requestColumn['searchable'] == 'true' ) {
                    if (($column["db"] == "mac") && ($table == "mag_devices")) { $str = base64_encode($str); }
                    $binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $globalSearch[] = ($isJoin) ? $column['db']." LIKE ".$binding : "`".$column['db']."` LIKE ".$binding;
                }
            }
        }
        // Individual column filtering
        for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search( $requestColumn['data'], $dtColumns );
            $column = $columns[ $columnIdx ];
            $str = $requestColumn['search']['value'];
            if ( $requestColumn['searchable'] == 'true' &&
                $str != '' ) {
                if (($column["db"] == "mac") && ($table == "mag_devices")) { $str = base64_encode($str); }
                $binding = SSP::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                $columnSearch[] = ($isJoin) ? $column['db']." LIKE ".$binding : "`".$column['db']."` LIKE ".$binding;
            }
        }
        // Combine the filters into a single string
        $where = '';
        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }
        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }
        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }
        return $where;
    }
    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an SSP request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $sql_details SQL connection details - see sql_connect()
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @param  array $joinQuery Join query String
     *  @param  string $extraWhere Where query String
     *  @param  string $groupBy groupBy by any field will apply
     *  @param  string $having HAVING by any condition will apply
     *
     *  @return array  Server-side processing response array
     *
     */
    static function simple ( $request, $sql_details, $table, $primaryKey, $columns, $joinQuery = NULL, $extraWhere = '', $groupBy = '', $having = '')
    {
        $bindings = array();
        $db = SSP::sql_connect( $sql_details );
        // Build the SQL query string from the request
        $limit = SSP::limit( $request, $columns );
        $order = SSP::order( $request, $columns, $joinQuery );
        $where = SSP::filter( $request, $columns, $bindings, $joinQuery, $table);
		// IF Extra where set then set and prepare query
        if($extraWhere)
            $extraWhere = ($where) ? ' AND '.$extraWhere : ' WHERE '.$extraWhere;
        $groupBy = ($groupBy) ? ' GROUP BY '.$groupBy .' ' : '';
        $having = ($having) ? ' HAVING '.$having .' ' : '';
        // Main query to actually get the data
        if($joinQuery){
            $col = SSP::pluck($columns, 'db', $joinQuery);
            $query =  "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", $col)."
			 $joinQuery
			 $where
			 $extraWhere
			 $groupBy
       $having
			 $order
			 $limit";
        }else{
            $query =  "SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", SSP::pluck($columns, 'db'))."`
			 FROM `$table`
			 $where
			 $extraWhere
			 $groupBy
       $having
			 $order
			 $limit";
        }
        $data = SSP::sql_exec( $db, $bindings,$query);
        // Data set length after filtering
        $resFilterLength = SSP::sql_exec( $db,
            "SELECT FOUND_ROWS()"
        );
        $recordsFiltered = $resFilterLength[0][0];
        // Total data set length
        $resTotalLength = SSP::sql_exec( $db,
            "SELECT COUNT(`{$primaryKey}`)
			 FROM   `$table`"
        );
        $recordsTotal = $resTotalLength[0][0];
        /*
         * Output
         */
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => SSP::data_output( $columns, $data, $joinQuery )
        );
    }
    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    static function sql_connect ( $sql_details )
    {
        try {
            $db = @new PDO(
                "mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
                $sql_details['user'],
                $sql_details['pass'],
                array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
            );
            $db->query("SET NAMES 'utf8'");
        }
        catch (PDOException $e) {
            SSP::fatal(
                "An error occurred while connecting to the database. ".
                "The error reported by the server was: ".$e->getMessage()
            );
        }
        return $db;
    }
    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    static function sql_exec ( $db, $bindings, $sql=null )
    {
        // Argument shifting
        if ( $sql === null ) {
            $sql = $bindings;
        }
        $stmt = $db->prepare( $sql );
        //echo $sql;
        // Bind parameters
        if ( is_array( $bindings ) ) {
            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
                $binding = $bindings[$i];
                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
            }
        }
        // Execute
        try {
            $stmt->execute();
        }
        catch (PDOException $e) {
            SSP::fatal( "An SQL error occurred: ".$e->getMessage() );
        }
        // Return all
        return $stmt->fetchAll();
    }
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */
    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal ( $msg )
    {
        echo json_encode( array(
            "error" => $msg
        ) );
        exit(0);
    }
    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind ( &$a, $val, $type )
    {
        $key = ':binding_'.count( $a );
        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );
        return $key;
    }
    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *  @return array        Array of property values
     */
    static function pluck ( $a, $prop, $isJoin = false )
    {
        $out = array();
        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = ($isJoin && isset($a[$i]['as'])) ? $a[$i][$prop]. ' AS '.$a[$i]['as'] : $a[$i][$prop];
        }
        return $out;
    }
}

echo json_encode(SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, "", $extraWhere));
?>