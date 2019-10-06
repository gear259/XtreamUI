<?php
$rRelease = 2;

session_start();
set_time_limit(0);
ini_set('default_socket_timeout', 10);

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);

define("MAIN_DIR", "/home/xtreamcodes/iptv_xtream_codes/");
define("CONFIG_CRYPT_KEY", "5709650b0d7806074842c6de575025b1");

include "./mobiledetect.php";
$detect = new Mobile_Detect;

$rStatusArray = Array(0 => "Stopped", 1 => "Running", 2 => "Starting", 3 => "<strong style='color:#cc9999'>DOWN</strong>", 4 => "On Demand", 5 => "Direct");

function xor_parse($data, $key) {
    $i = 0;
    $output = '';
    foreach (str_split($data) as $char) {
	    $output.= chr(ord($char) ^ ord($key[$i++ % strlen($key)]));
    }
    return $output;
}

function getTimezone() {
    global $db;
    $result = $db->query("SELECT `default_timezone` FROM `settings`;");
    return $result->fetch_assoc()["default_timezone"];
}

$_INFO = json_decode(xor_parse(base64_decode(file_get_contents(MAIN_DIR . "config")), CONFIG_CRYPT_KEY), True);
$db = new mysqli($_INFO["host"], $_INFO["db_user"], $_INFO["db_pass"], $_INFO["db_name"], $_INFO["db_port"]);
$db->set_charset("utf8");
date_default_timezone_set(getTimezone());

function checkUpdate() {
    global $rRelease;
    if (intval(json_decode(file_get_contents("http://xtreamcodes.org/update/update.php"), True)["release"]) > $rRelease) {
        return true;
    } else {
        return false;
    }
}

function getStreamingServers() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streaming_servers` ORDER BY `id` ASC;");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}

function getSettings() {
    global $db;
    $result = $db->query("SELECT * FROM `settings` LIMIT 1;");
    return $result->fetch_assoc();
}

function getStreams($category_id=null, $full=false, $stream_ids=null) {
    global $db;
    $return = Array();
    if ($stream_ids) {
        $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 AND `id` IN (".join(",", $stream_ids).") ORDER BY `id` ASC;");
    } else {
        if ($category_id) {
            $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 AND `category_id` = ".intval($category_id)." ORDER BY `id` ASC;");
        } else {
            $result = $db->query("SELECT * FROM `streams` WHERE `type` = 1 ORDER BY `id` ASC;");
        }
    }
    $stream_ids = Array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($full) {
                $return[] = $row;
            } else {
                $return[] = Array("id" => $row["id"]);
            }
            $stream_ids[] = $row["id"];
        }
    }
    $streams_sys = Array();
    $result = $db->query("SELECT * FROM `streams_sys` WHERE `stream_id` IN (".join(",", $stream_ids).");");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $streams_sys[intval($row["stream_id"])][intval($row["server_id"])] = $row;
        }
    }
    $activity = Array();
    $result = $db->query("SELECT `stream_id`, `server_id`, COUNT(`activity_id`) AS `active` FROM `user_activity_now` WHERE `stream_id` IN (".join(",", $stream_ids).") GROUP BY `stream_id`, `server_id`;");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $activity[intval($row["stream_id"])][intval($row["server_id"])] = $row["active"];
        }
    }
    if (count($return) > 0) {
        foreach (range(0, count($return)-1) as $i) {
            $return[$i]["servers"] = Array();
            foreach($streams_sys[intval($return[$i]["id"])] as $rServerID => $rStreamSys) {
                $rServerArray = Array("server_id" => $rServerID);
                if (isset($activity[intval($return[$i]["id"])][$rServerID])) {
                    $rServerArray["active_count"] = $activity[intval($return[$i]["id"])][$rServerID];
                } else {
                    $rServerArray["active_count"] = 0;
                }
                $rServerArray["uptime"] = 0;
                if (intval($return[$i]["direct_source"]) == 1) {
                    // Direct
                    $rServerArray["actual_status"] = 5;
                } else if ($rStreamSys["monitor_pid"]) {
                    // Started
                    if (($rStreamSys["pid"]) && ($rStreamSys["pid"] > 0)) {
                        // Running
                        $rServerArray["actual_status"] = 1;
                        $rServerArray["uptime"] = time() - intval($rStreamSys["stream_started"]);
                    } else {
                        if (intval($rStreamSys["stream_status"]) == 0) {
                            // Starting
                            $rServerArray["actual_status"] = 2;
                        } else {
                            // Stalled
                            $rServerArray["actual_status"] = 3;
                        }
                    }
                } else if (intval($rStreamSys["on_demand"]) == 1) {
                    // On Demand
                    $rServerArray["actual_status"] = 4;
                } else {
                    // Stopped
                    $rServerArray["actual_status"] = 0;
                }
                $rServerArray["uptime_text"] = sprintf('%02dh %02dm %02ds', ($rServerArray["uptime"]/3600),($rServerArray["uptime"]/60%60), ($rServerArray["uptime"]%60));
                $rServerArray["on_demand"] = $rStreamSys["on_demand"];
                $rStreamInfo = json_decode($rStreamSys["stream_info"], True);
                $rServerArray["stream_text"] = "Not Available";
                if ($rServerArray["actual_status"] == 1) {
                    if ((isset($rStreamInfo["codecs"]["video"])) && (isset($rStreamInfo["codecs"]["audio"]))) {
                        $rServerArray["stream_text"] = sprintf("%s Kbps - %dx%d - %s - %s", number_format($rStreamSys["bitrate"], 0), $rStreamInfo["codecs"]["video"]["width"], $rStreamInfo["codecs"]["video"]["height"], $rStreamInfo["codecs"]["video"]["codec_name"], $rStreamInfo["codecs"]["audio"]["codec_name"]);
                    } else if (isset($rStreamInfo["codecs"]["video"])) {
                        $rServerArray["stream_text"] = sprintf("%s Kbps - %dx%d - %s - No Audio", number_format($rStreamSys["bitrate"], 0), $rStreamInfo["codecs"]["video"]["width"], $rStreamInfo["codecs"]["video"]["height"], $rStreamInfo["codecs"]["video"]["codec_name"]);
                    } else if (isset($rStreamInfo["codecs"]["audio"])) {
                        $rServerArray["stream_text"] = sprintf("%s Kbps - No Video - %s", number_format($rStreamSys["bitrate"], 0), $rStreamInfo["codecs"]["audio"]["codec_name"]);
                    }
                }
                $return[$i]["servers"][] = $rServerArray;
            }
        }
    }
    return $return;
}

function getConnections($rServerID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `server_id` = '".$db->real_escape_string($rServerID)."';");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getUserConnections($rUserID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `user_activity_now` WHERE `user_id` = '".$db->real_escape_string($rUserID)."';");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getEPGSources() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `epg`;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["id"]] = $row;
        }
    }
    return $return;
}

function getStreamArguments() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_arguments` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[$row["argument_key"]] = $row;
        }
    }
    return $return;
}

function getTranscodeProfiles() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `transcoding_profiles` ORDER BY `profile_id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[] = $row;
        }
    }
    return $return;
}

function getStream($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `streams` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getUser($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `users` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getRegisteredUser($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `reg_users` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getEPG($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `epg` WHERE `id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        return $result->fetch_assoc();
    }
    return False;
}

function getStreamOptions($rID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_options` WHERE `stream_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["argument_id"])] = $row;
        }
    }
    return $return;
}

function getStreamSys($rID) {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `streams_sys` WHERE `stream_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["server_id"])] = $row;
        }
    }
    return $return;
}

function getRegisteredUsers() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `reg_users` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getMemberGroups() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `member_groups` ORDER BY `group_id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["group_id"])] = $row;
        }
    }
    return $return;
}

function getRegisteredUsernames() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT `username` FROM `reg_users` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getOutputs($rUser=null) {
    global $db;
    $return = Array();
    if ($rUser) {
        $result = $db->query("SELECT `access_output_id` FROM `user_output` WHERE `user_id` = ".intval($rUser).";");
    } else {
        $result = $db->query("SELECT * FROM `access_output` ORDER BY `access_output_id` ASC;");
    }
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            if ($rUser) {
                $return[] = $row["access_output_id"];
            } else {
                $return[] = $row;
            }
        }
    }
    return $return;
}

function getBouquets() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `bouquets` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getEPGs() {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `epg` ORDER BY `id` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getCategories($rType="live") {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '".$db->real_escape_string($rType)."' ORDER BY `cat_order` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getChannels($rType="live") {
    global $db;
    $return = Array();
    $result = $db->query("SELECT * FROM `stream_categories` WHERE `category_type` = '".$db->real_escape_string($rType)."' ORDER BY `cat_order` ASC;");
    if (($result) && ($result->num_rows > 0)) {
        while ($row = $result->fetch_assoc()) {
            $return[intval($row["id"])] = $row;
        }
    }
    return $return;
}

function getMag($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `mag_devices` WHERE `mag_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = ".intval($row["user_id"]).";");
        if (($result) && ($result->num_rows == 1)) {
            $magrow = $result->fetch_assoc();
            $row["paired_user"] = $magrow["pair_id"];
            $row["username"] = getUser($row["paired_user"])["username"];
        }
        return $row;
    }
    return Array();
}

function getEnigma($rID) {
    global $db;
    $result = $db->query("SELECT * FROM `enigma2_devices` WHERE `device_id` = ".intval($rID).";");
    if (($result) && ($result->num_rows == 1)) {
        $row = $result->fetch_assoc();
        $result = $db->query("SELECT `pair_id` FROM `users` WHERE `id` = ".intval($row["user_id"]).";");
        if (($result) && ($result->num_rows == 1)) {
            $e2row = $result->fetch_assoc();
            $row["paired_user"] = $e2row["pair_id"];
            $row["username"] = getUser($row["paired_user"])["username"];
        }
        return $row;
    }
    return Array();
}

function cryptPassword($password, $salt="xtreamcodes", $rounds=20000) {
    if ($salt == "") {
        $salt = substr(bin2hex(openssl_random_pseudo_bytes(16)),0,16);
    }
    $hash = crypt($password, sprintf('$6$rounds=%d$%s$', $rounds, $salt));
    return $hash;
}

function getIP(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function doLogin($rUsername, $rPassword) {
    global $db;
    $result = $db->query("SELECT `id`, `username`, `password` FROM `reg_users` WHERE `status` = 1 AND `member_group_id` = 1 AND `username` = '".$db->real_escape_string($rUsername)."' LIMIT 1;");
    if (($result) && ($result->num_rows == 1)) {
        $rRow = $result->fetch_assoc();
        if (cryptPassword($rPassword) == $rRow["password"]) {
            $db->query("UPDATE `reg_users` SET `last_login` = UNIX_TIMESTAMP(), `ip` = '".$db->real_escape_string(getIP())."' WHERE `id` = ".intval($rRow["id"]).";");
            $_SESSION['user_id'] = $rRow["id"];
            return True;
        }
    }
    return False;
}

function secondsToTime($inputSeconds) {
    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;
    $days = floor($inputSeconds / $secondsInADay);
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    return $obj;
}

if (isset($_SESSION['user_id'])) {
    $rCategories = getCategories();
    $rServers = getStreamingServers();
}
?>