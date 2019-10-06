<?php
include "./functions.php";
if (!isset($_SESSION['user_id'])) { exit; }

if (isset($_GET["action"])) {
    if ($_GET["action"] == "stream") {
        $rStreamID = intval($_GET["stream_id"]);
        $rServerID = intval($_GET["server_id"]);
        $rSub = $_GET["sub"];
        $rAPI = "http://".$rServers[$_INFO["server_id"]]["server_ip"].":".$rServers[$_INFO["server_id"]]["http_broadcast_port"]."/api.php";
        if (in_array($rSub, Array("start", "stop"))) {
            $rURL = $rAPI."?action=stream&sub=".$rSub."&stream_ids[]=".$rStreamID."&servers[]=".$rServerID;
            echo file_get_contents($rURL);exit;
        } else if ($rSub == "restart") {
            if (json_decode(file_get_contents($rAPI."?action=stream&sub=stop&stream_ids[]=".$rStreamID."&servers[]=".$rServerID), True)["result"]) {
                if (json_decode(file_get_contents($rAPI."?action=stream&sub=start&stream_ids[]=".$rStreamID."&servers[]=".$rServerID), True)["result"]) {
                    echo json_encode(Array("result" => True));exit;
                }
            }
            echo json_encode(Array("result" => False));exit;
        } else if ($rSub == "delete") {
            $db->query("DELETE FROM `streams_sys` WHERE `stream_id` = ".$db->real_escape_string($rStreamID)." AND `server_id` = ".$db->real_escape_string($rServerID).";");
            $result = $db->query("SELECT COUNT(`server_stream_id`) AS `count` FROM `streams_sys` WHERE `stream_id` = ".$db->real_escape_string($rStreamID).";");
            if ($result->fetch_assoc()["count"] == 0) {
                $db->query("DELETE FROM `streams` WHERE `id` = ".$db->real_escape_string($rStreamID).";");
            }
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "user") {
        $rUserID = intval($_GET["user_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `users` WHERE `id` = ".$db->real_escape_string($rUserID).";");
            $db->query("DELETE FROM `user_output` WHERE `user_id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "enable") {
            $db->query("UPDATE `users` SET `enabled` = 1 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "disable") {
            $db->query("UPDATE `users` SET `enabled` = 0 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "ban") {
            $db->query("UPDATE `users` SET `admin_enabled` = 0 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "unban") {
            $db->query("UPDATE `users` SET `admin_enabled` = 1 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "reg_user") {
        $rUserID = intval($_GET["user_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `reg_users` WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "enable") {
            $db->query("UPDATE `reg_users` SET `status` = 1 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else if ($rSub == "disable") {
            $db->query("UPDATE `reg_users` SET `status` = 0 WHERE `id` = ".$db->real_escape_string($rUserID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "mag") {
        $rMagID = intval($_GET["mag_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $rMagDetails = getMag($rMagID);
            if (isset($rMagDetails["user_id"])) {
                $db->query("DELETE FROM `users` WHERE `id` = ".$db->real_escape_string($rMagDetails["user_id"]).";");
                $db->query("DELETE FROM `user_output` WHERE `user_id` = ".$db->real_escape_string($rMagDetails["user_id"]).";");
            }
            $db->query("DELETE FROM `mag_devices` WHERE `mag_id` = ".$db->real_escape_string($rMagID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "mag_event") {
        $rMagID = intval($_GET["mag_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `mag_events` WHERE `id` = ".$db->real_escape_string($rMagID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "epg") {
        $rEPGID = intval($_GET["epg_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `epg` WHERE `id` = ".$db->real_escape_string($rEPGID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "enigma") {
        $rEnigmaID = intval($_GET["enigma_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $rEnigmaDetails = getEnigma($rEnigmaID);
            if (isset($rEnigmaDetails["user_id"])) {
                $db->query("DELETE FROM `users` WHERE `id` = ".$db->real_escape_string($rEnigmaDetails["user_id"]).";");
                $db->query("DELETE FROM `user_output` WHERE `user_id` = ".$db->real_escape_string($rEnigmaDetails["user_id"]).";");
            }
            $db->query("DELETE FROM `enigma2_devices` WHERE `device_id` = ".$db->real_escape_string($rEnigmaID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "server") {
        $rServerID = intval($_GET["server_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            if ($rServers[$_GET["server_id"]]["can_delete"] == 1) {
                $db->query("DELETE FROM `streaming_servers` WHERE `id` = ".$db->real_escape_string($rServerID).";");
                $db->query("DELETE FROM `streams_sys` WHERE `server_id` = ".$db->real_escape_string($rServerID).";");
                echo json_encode(Array("result" => True));exit;
            } else {
                echo json_encode(Array("result" => False));exit;
            }
        } else if ($rSub == "kill") {
            $rResult = $db->query("SELECT `pid` FROM `user_activity_now` WHERE `server_id` = ".$db->real_escape_string($rServerID).";");
            if (($rResult) && ($rResult->num_rows > 0)) {
                while ($rRow = $rResult->fetch_assoc()) {
                    exec("kill -9 ".$rRow["pid"]);
                }
            }
            $db->query("DELETE FROM `user_activity_now` WHERE `server_id` = ".$db->real_escape_string($rServerID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "bouquet") {
        $rBouquetID = intval($_GET["bouquet_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `bouquets` WHERE `id` = ".$db->real_escape_string($rBouquetID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "category") {
        $rCategoryID = intval($_GET["category_id"]);
        $rSub = $_GET["sub"];
        if ($rSub == "delete") {
            $db->query("DELETE FROM `stream_categories` WHERE `id` = ".$db->real_escape_string($rCategoryID).";");
            echo json_encode(Array("result" => True));exit;
        } else {
            echo json_encode(Array("result" => False));exit;
        }
    } else if ($_GET["action"] == "streams") {
        $rData = [];
        $rStreamIDs = json_decode($_GET["stream_ids"], True);
        $rStreams = getStreams(null, false, $rStreamIDs);
        echo json_encode(Array("result" => True, "data" => $rStreams));
        exit;
    } else if ($_GET["action"] == "stats") {
        $return = Array("cpu" => 0, "mem" => 0, "uptime" => "--", "total_running_streams" => 0, "bytes_sent" => 0, "bytes_received" => 0);
        if (isset($_GET["server_id"])) {
            $rServerID = intval($_GET["server_id"]);
            $rWatchDog = json_decode($rServers[$rServerID]["watchdog_data"], True);
            if (is_array($rWatchDog)) {
                $return["uptime"] = $rWatchDog["uptime"];
                $return["mem"] = intval($rWatchDog["total_mem_used_percent"]);
                $return["cpu"] = intval($rWatchDog["cpu_avg"]);
                $return["total_running_streams"] = intval(trim($rWatchDog["total_running_streams"]));
                $return["bytes_received"] = intval($rWatchDog["bytes_received"]);
                $return["bytes_sent"] = intval($rWatchDog["bytes_sent"]);
            }
            $result = $db->query("SELECT `activity_id` FROM `user_activity_now` WHERE `server_id` = ".$rServerID.";");
            $return["open_connections"] = $result->num_rows;
            $result = $db->query("SELECT `activity_id` FROM `user_activity_now`;");
            $return["total_connections"] = $result->num_rows;
            $result = $db->query("SELECT `activity_id` FROM `user_activity_now` WHERE `server_id` = ".$rServerID." GROUP BY `user_id`;");
            $return["online_users"] = $result->num_rows;
            $result = $db->query("SELECT `activity_id` FROM `user_activity_now` GROUP BY `user_id`;");
            $return["total_users"] = $result->num_rows;
            $result = $db->query("SELECT `server_stream_id` FROM `streams_sys` WHERE `server_id` = ".$rServerID." AND `stream_status` <> 2;");
            $return["total_streams"] = $result->num_rows;
            $return["network_guaranteed_speed"] = $rServers[$rServerID]["network_guaranteed_speed"];
        } else {
            $rUptime = 0;
            foreach (array_keys($rServers) as $rServerID) {
                $rWatchDog = json_decode($rServers[$rServerID]["watchdog_data"], True);
                if (is_array($rWatchDog)) {
                    foreach (explode(" ", $rWatchDog["uptime"]) as $rPart) {
                        if (substr($rPart, -1) == "d") {
                            $rUptime += intval(substr($rPart, 0, -1)) * 86400;
                        } else if (substr($rPart, -1) == "h") {
                            $rUptime += intval(substr($rPart, 0, -1)) * 3600;
                        } else if (substr($rPart, -1) == "m") {
                            $rUptime += intval(substr($rPart, 0, -1)) * 60;
                        } else if (substr($rPart, -1) == "s") {
                            $rUptime += intval(substr($rPart, 0, -1));
                        }
                    }
                    $return["mem"] += intval($rWatchDog["total_mem_used_percent"]);
                    $return["cpu"] += intval($rWatchDog["cpu_avg"]);
                    $return["total_running_streams"] += intval(trim($rWatchDog["total_running_streams"]));
                    $return["bytes_received"] += intval($rWatchDog["bytes_received"]);
                    $return["bytes_sent"] += intval($rWatchDog["bytes_sent"]);
                }
                $result = $db->query("SELECT `activity_id` FROM `user_activity_now` WHERE `server_id` = ".$rServerID.";");
                $return["open_connections"] += $result->num_rows;
                $result = $db->query("SELECT `activity_id` FROM `user_activity_now`;");
                $return["total_connections"] = $result->num_rows;
                $result = $db->query("SELECT `activity_id` FROM `user_activity_now` WHERE `server_id` = ".$rServerID." GROUP BY `user_id`;");
                $return["online_users"] += $result->num_rows;
                $result = $db->query("SELECT `activity_id` FROM `user_activity_now` GROUP BY `user_id`;");
                $return["total_users"] = $result->num_rows;
                $result = $db->query("SELECT `server_stream_id` FROM `streams_sys` WHERE `server_id` = ".$rServerID." AND `stream_status` <> 2;");
                $return["total_streams"] += $result->num_rows;
                $return["network_guaranteed_speed"] += $rServers[$rServerID]["network_guaranteed_speed"];
            }
            $return["mem"] = intval($return["mem"] / count($rServers));
            $return["cpu"] = intval($return["cpu"] / count($rServers));
            $return["uptime"] = "";
            $rUptime = secondsToTime($rUptime);
            if ($rUptime["d"] > 0) { $return["uptime"] .= $rUptime["d"]."d "; }
            if (($rUptime["h"] > 0) OR (strlen($return["uptime"]) > 0)) { $return["uptime"] .= $rUptime["h"]."h "; }
            if (($rUptime["m"] > 0) OR (strlen($return["uptime"]) > 0)) { $return["uptime"] .= $rUptime["m"]."m "; }
            if (($rUptime["s"] > 0) OR (strlen($return["uptime"]) > 0)) { $return["uptime"] .= $rUptime["s"]."s "; }
            
        }
        echo json_encode($return);exit;
    } else if ($_GET["action"] == "review_bouquet") {
        $return = Array("streams" => Array(), "vod" => Array(), "series" => Array(), "result" => true);
        if (isset($_POST["data"]["stream"])) {
            foreach ($_POST["data"]["stream"] as $rStreamID) {
                $rResult = $db->query("SELECT `id`, `stream_display_name`, `type` FROM `streams` WHERE `id` = ".intval($rStreamID).";");
                if (($rResult) && ($rResult->num_rows == 1)) {
                    $rData = $rResult->fetch_assoc();
                    if ($rData["type"] == 2) {
                        $return["vod"][] = $rData;
                    } else {
                        $return["streams"][] = $rData;
                    }
                }
            }
        }
        if (isset($_POST["data"]["series"])) {
            foreach ($_POST["data"]["series"] as $rSeriesID) {
                $rResult = $db->query("SELECT `id`, `title` FROM `series` WHERE `id` = ".intval($rSeriesID).";");
                if (($rResult) && ($rResult->num_rows == 1)) {
                    $rData = $rResult->fetch_assoc();
                    $return["series"][] = $rData;
                }
            }
        }
        echo json_encode($return);exit;
    } else if ($_GET["action"] == "userlist") {
        $return = Array("total_count" => 0, "items" => Array(), "result" => true);
        if (isset($_GET["search"])) {
            if (isset($_GET["page"])) {
                $rPage = intval($_GET["page"]);
            } else {
                $rPage = 1;
            }
            $rResult = $db->query("SELECT COUNT(`id`) AS `id` FROM `users` WHERE `username` LIKE '%".$db->real_escape_string($_GET["search"])."%';");
            $return["total_count"] = $rResult->fetch_assoc()["id"];
            $rResult = $db->query("SELECT `id`, `username` FROM `users` WHERE `username` LIKE '%".$db->real_escape_string($_GET["search"])."%' ORDER BY `username` ASC LIMIT ".(($rPage-1) * 100).", 100;");
            if (($rResult) && ($rResult->num_rows > 0)) {
                while ($rRow = $rResult->fetch_assoc()) {
                    $return["items"][] = Array("id" => $rRow["id"], "text" => $rRow["username"]);
                }
            }
        }
        echo json_encode($return);exit;
    } else if ($_GET["action"] == "force_epg") {
        echo exec("/home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/crons/epg.php");
        echo json_encode(Array("result" => True));exit;
    }
}
echo json_encode(Array("result" => False));
?>