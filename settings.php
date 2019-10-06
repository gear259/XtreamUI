<?php
include "functions.php";
if (!isset($_SESSION['user_id'])) { header("Location: ./login.php"); exit; }

if (isset($_POST["submit_settings"])) {
    $rArray = getSettings();
    foreach (Array("disallow_empty_user_agents", "show_all_category_mag", "show_not_on_air_video", "show_banned_video", "show_expired_video") as $rSetting) {
        if (isset($_POST[$rSetting])) {
            $rArray[$rSetting] = 1;
            unset($_POST[$rSetting]);
        } else {
            $rArray[$rSetting] = 0;
        }
    }
    foreach($_POST as $rKey => $rValue) {
        if (isset($rArray[$rKey])) {
            $rArray[$rKey] = $rValue;
        }
    }
    $rValues = Array();
    foreach ($rArray as $rKey => $rValue) {
        if (is_array($rValue)) {
            $rValue = json_encode($rValue);
        }
        if (is_null($rValue)) {
            $rValues[] = '`'.$db->real_escape_string($rKey).'` = NULL';
        } else {
            $rValues[] = '`'.$db->real_escape_string($rKey).'` = \''.$db->real_escape_string($rValue).'\'';
        }
    }
    $rQuery = "UPDATE `settings` SET ".join(", ", $rValues).";";
    if ($db->query($rQuery)) {
        $_STATUS = 0;
    } else {
        $_STATUS = 1;
    }
}

$rSettings = getSettings();
include "header.php"; ?>
        <div class="wrapper boxed-layout">
            <form action="./settings.php" method="POST" id="category_form">
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <input name="submit_settings" type="submit" class="btn btn-primary btn-xs" value="Save Changes" />
                                    </ol>
                                </div>
                                <h4 class="page-title">Settings</h4>
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
                                Settings sucessfully updated!
                            </div>
                            <?php } else if ((isset($_STATUS)) && ($_STATUS > 0)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                There was an error saving settings! Please check the form entry and try again.
                            </div>
                            <?php } ?>
                            <div class="card">
                                <div class="card-body">
                                    <div id="basicwizard">
                                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                            <li class="nav-item">
                                                <a href="#general-details" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-account-card-details-outline mr-1"></i>
                                                    <span class="d-none d-sm-inline">General</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#ddos" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-hazard-lights mr-1"></i>
                                                    <span class="d-none d-sm-inline">DDOS</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#streaming" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-play mr-1"></i>
                                                    <span class="d-none d-sm-inline">Streaming</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="#video" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-file-video mr-1"></i>
                                                    <span class="d-none d-sm-inline">Video</span>
                                                </a>
                                            </li>
                                            <!-- <li class="nav-item">
                                                <a href="#mail" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2"> 
                                                    <i class="mdi mdi-email mr-1"></i>
                                                    <span class="d-none d-sm-inline">Mail</span>
                                                </a>
                                            </li> -->
                                        </ul>
                                        <div class="tab-content b-0 mb-0 pt-0">
                                            <div class="tab-pane" id="general-details">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="server_name">Server Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="server_name" name="server_name" value="<?=$rSettings["server_name"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="default_timezone">Timezone</label>
                                                            <div class="col-md-8">
                                                                <select name="default_timezone" id="default_timezone" class="form-control" data-toggle="select2">
                                                                    <?php
                                                                    $rTimeZones = Array("Africa/Abidjan" => "Africa/Abidjan [GMT  00:00]", "Africa/Accra" => "Africa/Accra [GMT  00:00]", "Africa/Addis_Ababa" => "Africa/Addis_Ababa [EAT +03:00]", "Africa/Algiers" => "Africa/Algiers [CET +01:00]", "Africa/Asmara" => "Africa/Asmara [EAT +03:00]", "Africa/Bamako" => "Africa/Bamako [GMT  00:00]", "Africa/Bangui" => "Africa/Bangui [WAT +01:00]", "Africa/Banjul" => "Africa/Banjul [GMT  00:00]", "Africa/Bissau" => "Africa/Bissau [GMT  00:00]", "Africa/Blantyre" => "Africa/Blantyre [CAT +02:00]", "Africa/Brazzaville" => "Africa/Brazzaville [WAT +01:00]", "Africa/Bujumbura" => "Africa/Bujumbura [CAT +02:00]", "Africa/Cairo" => "Africa/Cairo [EET +02:00]", "Africa/Casablanca" => "Africa/Casablanca [WEST +01:00]", "Africa/Ceuta" => "Africa/Ceuta [CEST +02:00]", "Africa/Conakry" => "Africa/Conakry [GMT  00:00]", "Africa/Dakar" => "Africa/Dakar [GMT  00:00]", "Africa/Dar_es_Salaam" => "Africa/Dar_es_Salaam [EAT +03:00]", "Africa/Djibouti" => "Africa/Djibouti [EAT +03:00]", "Africa/Douala" => "Africa/Douala [WAT +01:00]", "Africa/El_Aaiun" => "Africa/El_Aaiun [WEST +01:00]", "Africa/Freetown" => "Africa/Freetown [GMT  00:00]", "Africa/Gaborone" => "Africa/Gaborone [CAT +02:00]", "Africa/Harare" => "Africa/Harare [CAT +02:00]", "Africa/Johannesburg" => "Africa/Johannesburg [SAST +02:00]", "Africa/Juba" => "Africa/Juba [EAT +03:00]", "Africa/Kampala" => "Africa/Kampala [EAT +03:00]", "Africa/Khartoum" => "Africa/Khartoum [EAT +03:00]", "Africa/Kigali" => "Africa/Kigali [CAT +02:00]", "Africa/Kinshasa" => "Africa/Kinshasa [WAT +01:00]", "Africa/Lagos" => "Africa/Lagos [WAT +01:00]", "Africa/Libreville" => "Africa/Libreville [WAT +01:00]", "Africa/Lome" => "Africa/Lome [GMT  00:00]", "Africa/Luanda" => "Africa/Luanda [WAT +01:00]", "Africa/Lubumbashi" => "Africa/Lubumbashi [CAT +02:00]", "Africa/Lusaka" => "Africa/Lusaka [CAT +02:00]", "Africa/Malabo" => "Africa/Malabo [WAT +01:00]", "Africa/Maputo" => "Africa/Maputo [CAT +02:00]", "Africa/Maseru" => "Africa/Maseru [SAST +02:00]", "Africa/Mbabane" => "Africa/Mbabane [SAST +02:00]", "Africa/Mogadishu" => "Africa/Mogadishu [EAT +03:00]", "Africa/Monrovia" => "Africa/Monrovia [GMT  00:00]", "Africa/Nairobi" => "Africa/Nairobi [EAT +03:00]", "Africa/Ndjamena" => "Africa/Ndjamena [WAT +01:00]", "Africa/Niamey" => "Africa/Niamey [WAT +01:00]", "Africa/Nouakchott" => "Africa/Nouakchott [GMT  00:00]", "Africa/Ouagadougou" => "Africa/Ouagadougou [GMT  00:00]", "Africa/Porto-Novo" => "Africa/Porto-Novo [WAT +01:00]", "Africa/Sao_Tome" => "Africa/Sao_Tome [GMT  00:00]", "Africa/Tripoli" => "Africa/Tripoli [EET +02:00]", "Africa/Tunis" => "Africa/Tunis [CET +01:00]", "Africa/Windhoek" => "Africa/Windhoek [WAST +02:00]", "America/Adak" => "America/Adak [HADT -09:00]", "America/Anchorage" => "America/Anchorage [AKDT -08:00]", "America/Anguilla" => "America/Anguilla [AST -04:00]", "America/Antigua" => "America/Antigua [AST -04:00]", "America/Araguaina" => "America/Araguaina [BRT -03:00]", "America/Argentina/Buenos_Aires" => "America/Argentina/Buenos_Aires [ART -03:00]", "America/Argentina/Catamarca" => "America/Argentina/Catamarca [ART -03:00]", "America/Argentina/Cordoba" => "America/Argentina/Cordoba [ART -03:00]", "America/Argentina/Jujuy" => "America/Argentina/Jujuy [ART -03:00]", "America/Argentina/La_Rioja" => "America/Argentina/La_Rioja [ART -03:00]", "America/Argentina/Mendoza" => "America/Argentina/Mendoza [ART -03:00]", "America/Argentina/Rio_Gallegos" => "America/Argentina/Rio_Gallegos [ART -03:00]", "America/Argentina/Salta" => "America/Argentina/Salta [ART -03:00]", "America/Argentina/San_Juan" => "America/Argentina/San_Juan [ART -03:00]", "America/Argentina/San_Luis" => "America/Argentina/San_Luis [ART -03:00]", "America/Argentina/Tucuman" => "America/Argentina/Tucuman [ART -03:00]", "America/Argentina/Ushuaia" => "America/Argentina/Ushuaia [ART -03:00]", "America/Aruba" => "America/Aruba [AST -04:00]", "America/Asuncion" => "America/Asuncion [PYT -04:00]", "America/Atikokan" => "America/Atikokan [EST -05:00]", "America/Bahia" => "America/Bahia [BRT -03:00]", "America/Bahia_Banderas" => "America/Bahia_Banderas [CDT -05:00]", "America/Barbados" => "America/Barbados [AST -04:00]", "America/Belem" => "America/Belem [BRT -03:00]", "America/Belize" => "America/Belize [CST -06:00]", "America/Blanc-Sablon" => "America/Blanc-Sablon [AST -04:00]", "America/Boa_Vista" => "America/Boa_Vista [AMT -04:00]", "America/Bogota" => "America/Bogota [COT -05:00]", "America/Boise" => "America/Boise [MDT -06:00]", "America/Cambridge_Bay" => "America/Cambridge_Bay [MDT -06:00]", "America/Campo_Grande" => "America/Campo_Grande [AMT -04:00]", "America/Cancun" => "America/Cancun [CDT -05:00]", "America/Caracas" => "America/Caracas [VET -04:30]", "America/Cayenne" => "America/Cayenne [GFT -03:00]", "America/Cayman" => "America/Cayman [EST -05:00]", "America/Chicago" => "America/Chicago [CDT -05:00]", "America/Chihuahua" => "America/Chihuahua [MDT -06:00]", "America/Costa_Rica" => "America/Costa_Rica [CST -06:00]", "America/Creston" => "America/Creston [MST -07:00]", "America/Cuiaba" => "America/Cuiaba [AMT -04:00]", "America/Curacao" => "America/Curacao [AST -04:00]", "America/Danmarkshavn" => "America/Danmarkshavn [GMT  00:00]", "America/Dawson" => "America/Dawson [PDT -07:00]", "America/Dawson_Creek" => "America/Dawson_Creek [MST -07:00]", "America/Denver" => "America/Denver [MDT -06:00]", "America/Detroit" => "America/Detroit [EDT -04:00]", "America/Dominica" => "America/Dominica [AST -04:00]", "America/Edmonton" => "America/Edmonton [MDT -06:00]", "America/Eirunepe" => "America/Eirunepe [ACT -05:00]", "America/El_Salvador" => "America/El_Salvador [CST -06:00]", "America/Fortaleza" => "America/Fortaleza [BRT -03:00]", "America/Glace_Bay" => "America/Glace_Bay [ADT -03:00]", "America/Godthab" => "America/Godthab [WGST -02:00]", "America/Goose_Bay" => "America/Goose_Bay [ADT -03:00]", "America/Grand_Turk" => "America/Grand_Turk [AST -04:00]", "America/Grenada" => "America/Grenada [AST -04:00]", "America/Guadeloupe" => "America/Guadeloupe [AST -04:00]", "America/Guatemala" => "America/Guatemala [CST -06:00]", "America/Guayaquil" => "America/Guayaquil [ECT -05:00]", "America/Guyana" => "America/Guyana [GYT -04:00]", "America/Halifax" => "America/Halifax [ADT -03:00]", "America/Havana" => "America/Havana [CDT -04:00]", "America/Hermosillo" => "America/Hermosillo [MST -07:00]", "America/Indiana/Indianapolis" => "America/Indiana/Indianapolis [EDT -04:00]", "America/Indiana/Knox" => "America/Indiana/Knox [CDT -05:00]", "America/Indiana/Marengo" => "America/Indiana/Marengo [EDT -04:00]", "America/Indiana/Petersburg" => "America/Indiana/Petersburg [EDT -04:00]", "America/Indiana/Tell_City" => "America/Indiana/Tell_City [CDT -05:00]", "America/Indiana/Vevay" => "America/Indiana/Vevay [EDT -04:00]", "America/Indiana/Vincennes" => "America/Indiana/Vincennes [EDT -04:00]", "America/Indiana/Winamac" => "America/Indiana/Winamac [EDT -04:00]", "America/Inuvik" => "America/Inuvik [MDT -06:00]", "America/Iqaluit" => "America/Iqaluit [EDT -04:00]", "America/Jamaica" => "America/Jamaica [EST -05:00]", "America/Juneau" => "America/Juneau [AKDT -08:00]", "America/Kentucky/Louisville" => "America/Kentucky/Louisville [EDT -04:00]", "America/Kentucky/Monticello" => "America/Kentucky/Monticello [EDT -04:00]", "America/Kralendijk" => "America/Kralendijk [AST -04:00]", "America/La_Paz" => "America/La_Paz [BOT -04:00]", "America/Lima" => "America/Lima [PET -05:00]", "America/Los_Angeles" => "America/Los_Angeles [PDT -07:00]", "America/Lower_Princes" => "America/Lower_Princes [AST -04:00]", "America/Maceio" => "America/Maceio [BRT -03:00]", "America/Managua" => "America/Managua [CST -06:00]", "America/Manaus" => "America/Manaus [AMT -04:00]", "America/Marigot" => "America/Marigot [AST -04:00]", "America/Martinique" => "America/Martinique [AST -04:00]", "America/Matamoros" => "America/Matamoros [CDT -05:00]", "America/Mazatlan" => "America/Mazatlan [MDT -06:00]", "America/Menominee" => "America/Menominee [CDT -05:00]", "America/Merida" => "America/Merida [CDT -05:00]", "America/Metlakatla" => "America/Metlakatla [PST -08:00]", "America/Mexico_City" => "America/Mexico_City [CDT -05:00]", "America/Miquelon" => "America/Miquelon [PMDT -02:00]", "America/Moncton" => "America/Moncton [ADT -03:00]", "America/Monterrey" => "America/Monterrey [CDT -05:00]", "America/Montevideo" => "America/Montevideo [UYT -03:00]", "America/Montserrat" => "America/Montserrat [AST -04:00]", "America/Nassau" => "America/Nassau [EDT -04:00]", "America/New_York" => "America/New_York [EDT -04:00]", "America/Nipigon" => "America/Nipigon [EDT -04:00]", "America/Nome" => "America/Nome [AKDT -08:00]", "America/Noronha" => "America/Noronha [FNT -02:00]", "America/North_Dakota/Beulah" => "America/North_Dakota/Beulah [CDT -05:00]", "America/North_Dakota/Center" => "America/North_Dakota/Center [CDT -05:00]", "America/North_Dakota/New_Salem" => "America/North_Dakota/New_Salem [CDT -05:00]", "America/Ojinaga" => "America/Ojinaga [MDT -06:00]", "America/Panama" => "America/Panama [EST -05:00]", "America/Pangnirtung" => "America/Pangnirtung [EDT -04:00]", "America/Paramaribo" => "America/Paramaribo [SRT -03:00]", "America/Phoenix" => "America/Phoenix [MST -07:00]", "America/Port-au-Prince" => "America/Port-au-Prince [EDT -04:00]", "America/Port_of_Spain" => "America/Port_of_Spain [AST -04:00]", "America/Porto_Velho" => "America/Porto_Velho [AMT -04:00]", "America/Puerto_Rico" => "America/Puerto_Rico [AST -04:00]", "America/Rainy_River" => "America/Rainy_River [CDT -05:00]", "America/Rankin_Inlet" => "America/Rankin_Inlet [CDT -05:00]", "America/Recife" => "America/Recife [BRT -03:00]", "America/Regina" => "America/Regina [CST -06:00]", "America/Resolute" => "America/Resolute [CDT -05:00]", "America/Rio_Branco" => "America/Rio_Branco [ACT -05:00]", "America/Santa_Isabel" => "America/Santa_Isabel [PDT -07:00]", "America/Santarem" => "America/Santarem [BRT -03:00]", "America/Santiago" => "America/Santiago [CLST -03:00]", "America/Santo_Domingo" => "America/Santo_Domingo [AST -04:00]", "America/Sao_Paulo" => "America/Sao_Paulo [BRT -03:00]", "America/Scoresbysund" => "America/Scoresbysund [EGST  00:00]", "America/Sitka" => "America/Sitka [AKDT -08:00]", "America/St_Barthelemy" => "America/St_Barthelemy [AST -04:00]", "America/St_Johns" => "America/St_Johns [NDT -02:30]", "America/St_Kitts" => "America/St_Kitts [AST -04:00]", "America/St_Lucia" => "America/St_Lucia [AST -04:00]", "America/St_Thomas" => "America/St_Thomas [AST -04:00]", "America/St_Vincent" => "America/St_Vincent [AST -04:00]", "America/Swift_Current" => "America/Swift_Current [CST -06:00]", "America/Tegucigalpa" => "America/Tegucigalpa [CST -06:00]", "America/Thule" => "America/Thule [ADT -03:00]", "America/Thunder_Bay" => "America/Thunder_Bay [EDT -04:00]", "America/Tijuana" => "America/Tijuana [PDT -07:00]", "America/Toronto" => "America/Toronto [EDT -04:00]", "America/Tortola" => "America/Tortola [AST -04:00]", "America/Vancouver" => "America/Vancouver [PDT -07:00]", "America/Whitehorse" => "America/Whitehorse [PDT -07:00]", "America/Winnipeg" => "America/Winnipeg [CDT -05:00]", "America/Yakutat" => "America/Yakutat [AKDT -08:00]", "America/Yellowknife" => "America/Yellowknife [MDT -06:00]", "Antarctica/Casey" => "Antarctica/Casey [AWST +08:00]", "Antarctica/Davis" => "Antarctica/Davis [DAVT +07:00]", "Antarctica/DumontDUrville" => "Antarctica/DumontDUrville [DDUT +10:00]", "Antarctica/Macquarie" => "Antarctica/Macquarie [MIST +11:00]", "Antarctica/Mawson" => "Antarctica/Mawson [MAWT +05:00]", "Antarctica/McMurdo" => "Antarctica/McMurdo [NZDT +13:00]", "Antarctica/Palmer" => "Antarctica/Palmer [CLST -03:00]", "Antarctica/Rothera" => "Antarctica/Rothera [ROTT -03:00]", "Antarctica/Syowa" => "Antarctica/Syowa [SYOT +03:00]", "Antarctica/Troll" => "Antarctica/Troll [CEST +02:00]", "Antarctica/Vostok" => "Antarctica/Vostok [VOST +06:00]", "Arctic/Longyearbyen" => "Arctic/Longyearbyen [CEST +02:00]", "Asia/Aden" => "Asia/Aden [AST +03:00]", "Asia/Almaty" => "Asia/Almaty [ALMT +06:00]", "Asia/Amman" => "Asia/Amman [EEST +03:00]", "Asia/Anadyr" => "Asia/Anadyr [ANAT +12:00]", "Asia/Aqtau" => "Asia/Aqtau [AQTT +05:00]", "Asia/Aqtobe" => "Asia/Aqtobe [AQTT +05:00]", "Asia/Ashgabat" => "Asia/Ashgabat [TMT +05:00]", "Asia/Baghdad" => "Asia/Baghdad [AST +03:00]", "Asia/Bahrain" => "Asia/Bahrain [AST +03:00]", "Asia/Baku" => "Asia/Baku [AZST +05:00]", "Asia/Bangkok" => "Asia/Bangkok [ICT +07:00]", "Asia/Beirut" => "Asia/Beirut [EEST +03:00]", "Asia/Bishkek" => "Asia/Bishkek [KGT +06:00]", "Asia/Brunei" => "Asia/Brunei [BNT +08:00]", "Asia/Chita" => "Asia/Chita [IRKT +08:00]", "Asia/Choibalsan" => "Asia/Choibalsan [CHOT +08:00]", "Asia/Colombo" => "Asia/Colombo [IST +05:30]", "Asia/Damascus" => "Asia/Damascus [EEST +03:00]", "Asia/Dhaka" => "Asia/Dhaka [BDT +06:00]", "Asia/Dili" => "Asia/Dili [TLT +09:00]", "Asia/Dubai" => "Asia/Dubai [GST +04:00]", "Asia/Dushanbe" => "Asia/Dushanbe [TJT +05:00]", "Asia/Gaza" => "Asia/Gaza [EET +02:00]", "Asia/Hebron" => "Asia/Hebron [EET +02:00]", "Asia/Ho_Chi_Minh" => "Asia/Ho_Chi_Minh [ICT +07:00]", "Asia/Hong_Kong" => "Asia/Hong_Kong [HKT +08:00]", "Asia/Hovd" => "Asia/Hovd [HOVT +07:00]", "Asia/Irkutsk" => "Asia/Irkutsk [IRKT +08:00]", "Asia/Jakarta" => "Asia/Jakarta [WIB +07:00]", "Asia/Jayapura" => "Asia/Jayapura [WIT +09:00]", "Asia/Jerusalem" => "Asia/Jerusalem [IDT +03:00]", "Asia/Kabul" => "Asia/Kabul [AFT +04:30]", "Asia/Kamchatka" => "Asia/Kamchatka [PETT +12:00]", "Asia/Karachi" => "Asia/Karachi [PKT +05:00]", "Asia/Kathmandu" => "Asia/Kathmandu [NPT +05:45]", "Asia/Khandyga" => "Asia/Khandyga [YAKT +09:00]", "Asia/Kolkata" => "Asia/Kolkata [IST +05:30]", "Asia/Krasnoyarsk" => "Asia/Krasnoyarsk [KRAT +07:00]", "Asia/Kuala_Lumpur" => "Asia/Kuala_Lumpur [MYT +08:00]", "Asia/Kuching" => "Asia/Kuching [MYT +08:00]", "Asia/Kuwait" => "Asia/Kuwait [AST +03:00]", "Asia/Macau" => "Asia/Macau [CST +08:00]", "Asia/Magadan" => "Asia/Magadan [MAGT +10:00]", "Asia/Makassar" => "Asia/Makassar [WITA +08:00]", "Asia/Manila" => "Asia/Manila [PHT +08:00]", "Asia/Muscat" => "Asia/Muscat [GST +04:00]", "Asia/Nicosia" => "Asia/Nicosia [EEST +03:00]", "Asia/Novokuznetsk" => "Asia/Novokuznetsk [KRAT +07:00]", "Asia/Novosibirsk" => "Asia/Novosibirsk [NOVT +06:00]", "Asia/Omsk" => "Asia/Omsk [OMST +06:00]", "Asia/Oral" => "Asia/Oral [ORAT +05:00]", "Asia/Phnom_Penh" => "Asia/Phnom_Penh [ICT +07:00]", "Asia/Pontianak" => "Asia/Pontianak [WIB +07:00]", "Asia/Pyongyang" => "Asia/Pyongyang [KST +09:00]", "Asia/Qatar" => "Asia/Qatar [AST +03:00]", "Asia/Qyzylorda" => "Asia/Qyzylorda [QYZT +06:00]", "Asia/Rangoon" => "Asia/Rangoon [MMT +06:30]", "Asia/Riyadh" => "Asia/Riyadh [AST +03:00]", "Asia/Sakhalin" => "Asia/Sakhalin [SAKT +10:00]", "Asia/Samarkand" => "Asia/Samarkand [UZT +05:00]", "Asia/Seoul" => "Asia/Seoul [KST +09:00]", "Asia/Shanghai" => "Asia/Shanghai [CST +08:00]", "Asia/Singapore" => "Asia/Singapore [SGT +08:00]", "Asia/Srednekolymsk" => "Asia/Srednekolymsk [SRET +11:00]", "Asia/Taipei" => "Asia/Taipei [CST +08:00]", "Asia/Tashkent" => "Asia/Tashkent [UZT +05:00]", "Asia/Tbilisi" => "Asia/Tbilisi [GET +04:00]", "Asia/Tehran" => "Asia/Tehran [IRST +03:30]", "Asia/Thimphu" => "Asia/Thimphu [BTT +06:00]", "Asia/Tokyo" => "Asia/Tokyo [JST +09:00]", "Asia/Ulaanbaatar" => "Asia/Ulaanbaatar [ULAT +08:00]", "Asia/Urumqi" => "Asia/Urumqi [XJT +06:00]", "Asia/Ust-Nera" => "Asia/Ust-Nera [VLAT +10:00]", "Asia/Vientiane" => "Asia/Vientiane [ICT +07:00]", "Asia/Vladivostok" => "Asia/Vladivostok [VLAT +10:00]", "Asia/Yakutsk" => "Asia/Yakutsk [YAKT +09:00]", "Asia/Yekaterinburg" => "Asia/Yekaterinburg [YEKT +05:00]", "Asia/Yerevan" => "Asia/Yerevan [AMT +04:00]", "Atlantic/Azores" => "Atlantic/Azores [AZOST  00:00]", "Atlantic/Bermuda" => "Atlantic/Bermuda [ADT -03:00]", "Atlantic/Canary" => "Atlantic/Canary [WEST +01:00]", "Atlantic/Cape_Verde" => "Atlantic/Cape_Verde [CVT -01:00]", "Atlantic/Faroe" => "Atlantic/Faroe [WEST +01:00]", "Atlantic/Madeira" => "Atlantic/Madeira [WEST +01:00]", "Atlantic/Reykjavik" => "Atlantic/Reykjavik [GMT  00:00]", "Atlantic/South_Georgia" => "Atlantic/South_Georgia [GST -02:00]", "Atlantic/St_Helena" => "Atlantic/St_Helena [GMT  00:00]", "Atlantic/Stanley" => "Atlantic/Stanley [FKST -03:00]", "Australia/Adelaide" => "Australia/Adelaide [ACDT +10:30]", "Australia/Brisbane" => "Australia/Brisbane [AEST +10:00]", "Australia/Broken_Hill" => "Australia/Broken_Hill [ACDT +10:30]", "Australia/Currie" => "Australia/Currie [AEDT +11:00]", "Australia/Darwin" => "Australia/Darwin [ACST +09:30]", "Australia/Eucla" => "Australia/Eucla [ACWST +08:45]", "Australia/Hobart" => "Australia/Hobart [AEDT +11:00]", "Australia/Lindeman" => "Australia/Lindeman [AEST +10:00]", "Australia/Lord_Howe" => "Australia/Lord_Howe [LHDT +11:00]", "Australia/Melbourne" => "Australia/Melbourne [AEDT +11:00]", "Australia/Perth" => "Australia/Perth [AWST +08:00]", "Australia/Sydney" => "Australia/Sydney [AEDT +11:00]", "Europe/Amsterdam" => "Europe/Amsterdam [CEST +02:00]", "Europe/Andorra" => "Europe/Andorra [CEST +02:00]", "Europe/Athens" => "Europe/Athens [EEST +03:00]", "Europe/Belgrade" => "Europe/Belgrade [CEST +02:00]", "Europe/Berlin" => "Europe/Berlin [CEST +02:00]", "Europe/Bratislava" => "Europe/Bratislava [CEST +02:00]", "Europe/Brussels" => "Europe/Brussels [CEST +02:00]", "Europe/Bucharest" => "Europe/Bucharest [EEST +03:00]", "Europe/Budapest" => "Europe/Budapest [CEST +02:00]", "Europe/Busingen" => "Europe/Busingen [CEST +02:00]", "Europe/Chisinau" => "Europe/Chisinau [EEST +03:00]", "Europe/Copenhagen" => "Europe/Copenhagen [CEST +02:00]", "Europe/Dublin" => "Europe/Dublin [IST +01:00]", "Europe/Gibraltar" => "Europe/Gibraltar [CEST +02:00]", "Europe/Guernsey" => "Europe/Guernsey [BST +01:00]", "Europe/Helsinki" => "Europe/Helsinki [EEST +03:00]", "Europe/Isle_of_Man" => "Europe/Isle_of_Man [BST +01:00]", "Europe/Istanbul" => "Europe/Istanbul [EEST +03:00]", "Europe/Jersey" => "Europe/Jersey [BST +01:00]", "Europe/Kaliningrad" => "Europe/Kaliningrad [EET +02:00]", "Europe/Kiev" => "Europe/Kiev [EEST +03:00]", "Europe/Lisbon" => "Europe/Lisbon [WEST +01:00]", "Europe/Ljubljana" => "Europe/Ljubljana [CEST +02:00]", "Europe/London" => "Europe/London [BST +01:00]", "Europe/Luxembourg" => "Europe/Luxembourg [CEST +02:00]", "Europe/Madrid" => "Europe/Madrid [CEST +02:00]", "Europe/Malta" => "Europe/Malta [CEST +02:00]", "Europe/Mariehamn" => "Europe/Mariehamn [EEST +03:00]", "Europe/Minsk" => "Europe/Minsk [MSK +03:00]", "Europe/Monaco" => "Europe/Monaco [CEST +02:00]", "Europe/Moscow" => "Europe/Moscow [MSK +03:00]", "Europe/Oslo" => "Europe/Oslo [CEST +02:00]", "Europe/Paris" => "Europe/Paris [CEST +02:00]", "Europe/Podgorica" => "Europe/Podgorica [CEST +02:00]", "Europe/Prague" => "Europe/Prague [CEST +02:00]", "Europe/Riga" => "Europe/Riga [EEST +03:00]", "Europe/Rome" => "Europe/Rome [CEST +02:00]", "Europe/Samara" => "Europe/Samara [SAMT +04:00]", "Europe/San_Marino" => "Europe/San_Marino [CEST +02:00]", "Europe/Sarajevo" => "Europe/Sarajevo [CEST +02:00]", "Europe/Simferopol" => "Europe/Simferopol [MSK +03:00]", "Europe/Skopje" => "Europe/Skopje [CEST +02:00]", "Europe/Sofia" => "Europe/Sofia [EEST +03:00]", "Europe/Stockholm" => "Europe/Stockholm [CEST +02:00]", "Europe/Tallinn" => "Europe/Tallinn [EEST +03:00]", "Europe/Tirane" => "Europe/Tirane [CEST +02:00]", "Europe/Uzhgorod" => "Europe/Uzhgorod [EEST +03:00]", "Europe/Vaduz" => "Europe/Vaduz [CEST +02:00]", "Europe/Vatican" => "Europe/Vatican [CEST +02:00]", "Europe/Vienna" => "Europe/Vienna [CEST +02:00]", "Europe/Vilnius" => "Europe/Vilnius [EEST +03:00]", "Europe/Volgograd" => "Europe/Volgograd [MSK +03:00]", "Europe/Warsaw" => "Europe/Warsaw [CEST +02:00]", "Europe/Zagreb" => "Europe/Zagreb [CEST +02:00]", "Europe/Zaporozhye" => "Europe/Zaporozhye [EEST +03:00]", "Europe/Zurich" => "Europe/Zurich [CEST +02:00]", "Indian/Antananarivo" => "Indian/Antananarivo [EAT +03:00]", "Indian/Chagos" => "Indian/Chagos [IOT +06:00]", "Indian/Christmas" => "Indian/Christmas [CXT +07:00]", "Indian/Cocos" => "Indian/Cocos [CCT +06:30]", "Indian/Comoro" => "Indian/Comoro [EAT +03:00]", "Indian/Kerguelen" => "Indian/Kerguelen [TFT +05:00]", "Indian/Mahe" => "Indian/Mahe [SCT +04:00]", "Indian/Maldives" => "Indian/Maldives [MVT +05:00]", "Indian/Mauritius" => "Indian/Mauritius [MUT +04:00]", "Indian/Mayotte" => "Indian/Mayotte [EAT +03:00]", "Indian/Reunion" => "Indian/Reunion [RET +04:00]", "Pacific/Apia" => "Pacific/Apia [WSDT +14:00]", "Pacific/Auckland" => "Pacific/Auckland [NZDT +13:00]", "Pacific/Bougainville" => "Pacific/Bougainville [BST +11:00]", "Pacific/Chatham" => "Pacific/Chatham [CHADT +13:45]", "Pacific/Chuuk" => "Pacific/Chuuk [CHUT +10:00]", "Pacific/Easter" => "Pacific/Easter [EASST -05:00]", "Pacific/Efate" => "Pacific/Efate [VUT +11:00]", "Pacific/Enderbury" => "Pacific/Enderbury [PHOT +13:00]", "Pacific/Fakaofo" => "Pacific/Fakaofo [TKT +13:00]", "Pacific/Fiji" => "Pacific/Fiji [FJT +12:00]", "Pacific/Funafuti" => "Pacific/Funafuti [TVT +12:00]", "Pacific/Galapagos" => "Pacific/Galapagos [GALT -06:00]", "Pacific/Gambier" => "Pacific/Gambier [GAMT -09:00]", "Pacific/Guadalcanal" => "Pacific/Guadalcanal [SBT +11:00]", "Pacific/Guam" => "Pacific/Guam [ChST +10:00]", "Pacific/Honolulu" => "Pacific/Honolulu [HST -10:00]", "Pacific/Johnston" => "Pacific/Johnston [HST -10:00]", "Pacific/Kiritimati" => "Pacific/Kiritimati [LINT +14:00]", "Pacific/Kosrae" => "Pacific/Kosrae [KOST +11:00]", "Pacific/Kwajalein" => "Pacific/Kwajalein [MHT +12:00]", "Pacific/Majuro" => "Pacific/Majuro [MHT +12:00]", "Pacific/Marquesas" => "Pacific/Marquesas [MART -09:30]", "Pacific/Midway" => "Pacific/Midway [SST -11:00]", "Pacific/Nauru" => "Pacific/Nauru [NRT +12:00]", "Pacific/Niue" => "Pacific/Niue [NUT -11:00]", "Pacific/Norfolk" => "Pacific/Norfolk [NFT +11:30]", "Pacific/Noumea" => "Pacific/Noumea [NCT +11:00]", "Pacific/Pago_Pago" => "Pacific/Pago_Pago [SST -11:00]", "Pacific/Palau" => "Pacific/Palau [PWT +09:00]", "Pacific/Pitcairn" => "Pacific/Pitcairn [PST -08:00]", "Pacific/Pohnpei" => "Pacific/Pohnpei [PONT +11:00]", "Pacific/Port_Moresby" => "Pacific/Port_Moresby [PGT +10:00]", "Pacific/Rarotonga" => "Pacific/Rarotonga [CKT -10:00]", "Pacific/Saipan" => "Pacific/Saipan [ChST +10:00]", "Pacific/Tahiti" => "Pacific/Tahiti [TAHT -10:00]", "Pacific/Tarawa" => "Pacific/Tarawa [GILT +12:00]", "Pacific/Tongatapu" => "Pacific/Tongatapu [TOT +13:00]", "Pacific/Wake" => "Pacific/Wake [WAKT +12:00]", "Pacific/Wallis" => "Pacific/Wallis [WFT +12:00]", "UTC" => "UTC [UTC  00:00]");
                                                                    foreach ($rTimeZones as $rValue => $rText) { ?>
                                                                    <option <?php if ($rSettings["default_timezone"] == $rValue) { echo "selected "; } ?>value="<?=$rValue?>"><?=$rText?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="bouquet_name">Bouquet Name</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="bouquet_name" name="bouquet_name" value="<?=$rSettings["bouquet_name"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="live_streaming_pass">Live Streaming Pass</label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="live_streaming_pass" name="live_streaming_pass" value="<?=$rSettings["live_streaming_pass"]?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="ddos">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="flood_limit">Flood Limit <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Enter 0 to disable flood detection." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="flood_limit" name="flood_limit" value="<?=$rSettings["flood_limit"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="flood_ips_exclude">Flood IP Exclusions <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Separate each IP with a comma." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="flood_ips_exclude" name="flood_ips_exclude" value="<?=$rSettings["flood_ips_exclude"]?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                            </div>
                                            <div class="tab-pane" id="streaming">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="user_auto_kick_hours">Auto-Kick Users <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Automatically kick users who are online for more than X hours." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="user_auto_kick_hours" name="user_auto_kick_hours" value="<?=$rSettings["user_auto_kick_hours"]?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="disallow_empty_user_agents">Disallow Empty UA <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Don't allow connections from clients with no user-agent." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input name="disallow_empty_user_agents" id="disallow_empty_user_agents" type="checkbox"<?php if ($rSettings["disallow_empty_user_agents"] == 1) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="client_prebuffer">Prebuffer in Seconds <i data-toggle="tooltip" data-placement="top" title="" data-original-title="How much data will be sent to the client when connecting to a stream. Larger values will create larger prebuffers." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="client_prebuffer" name="client_prebuffer" value="<?=$rSettings["client_prebuffer"]?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="show_all_category_mag">MAG - Show All <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Show All category on MAG devices." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input name="show_all_category_mag" id="show_all_category_mag" type="checkbox"<?php if ($rSettings["show_all_category_mag"] == 1) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="split_clients">Split Clients <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Split equally to maintain a better balance." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-8">
                                                                <select name="split_clients" id="split_clients" class="form-control" data-toggle="select2">
                                                                    <option<?php if ($rSettings["split_clients"] == "equal") { echo " checked"; } ?> value="equal">Equally Between Servers</option>
                                                                    <option<?php if ($rSettings["split_clients"] == "load") { echo " checked"; } ?> value="load">After Full Server is Reached</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="stream_max_analyze">Analysis Duration <i data-toggle="tooltip" data-placement="top" title="" data-original-title="How long to analyse a stream, longer duration will increase sample accuracy. 5,000,000 microseconds = 5s." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="stream_max_analyze" name="stream_max_analyze" value="<?=$rSettings["stream_max_analyze"]?>">
                                                            </div>
                                                            <label class="col-md-4 col-form-label" for="probesize">Probe Size <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Amount of data to be probed in bytes." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" id="probesize" name="probesize" value="<?=$rSettings["probesize"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="allowed_ips_admin">Admin Streaming IP's <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Allowed IP's to access admin_live.php using the Live Streaming Pass. Separate each IP with a comma." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-8">
                                                                <input type="text" class="form-control" id="allowed_ips_admin" name="allowed_ips_admin" value="<?=$rSettings["allowed_ips_admin"]?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                            </div>
                                            <div class="tab-pane" id="video">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="show_not_on_air_video">Stream Down Video <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Show this video when a stream isn't on air." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input name="show_not_on_air_video" id="show_not_on_air_video" type="checkbox"<?php if ($rSettings["show_not_on_air_video"] == 1) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="not_on_air_video_path" name="not_on_air_video_path" value="<?=$rSettings["not_on_air_video_path"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="show_banned_video">Banned Video <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Show this video when a banned user accesses a stream." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input name="show_banned_video" id="show_banned_video" type="checkbox"<?php if ($rSettings["show_banned_video"] == 1) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="banned_video_path" name="banned_video_path" value="<?=$rSettings["banned_video_path"]?>">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-4">
                                                            <label class="col-md-4 col-form-label" for="show_expired_video">Expired Video <i data-toggle="tooltip" data-placement="top" title="" data-original-title="Show this video when an expired user accesses a stream." class="mdi mdi-information"></i></label>
                                                            <div class="col-md-2">
                                                                <input name="show_expired_video" id="show_expired_video" type="checkbox"<?php if ($rSettings["show_expired_video"] == 1) { echo "checked "; } ?>data-plugin="switchery" class="js-switch" data-color="#039cfd"/>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" class="form-control" id="expired_video_path" name="expired_video_path" value="<?=$rSettings["expired_video_path"]?>">
                                                            </div>
                                                        </div>
                                                    </div> <!-- end col -->
                                                </div> <!-- end row -->
                                            </div>
                                        </div> <!-- tab-content -->
                                    </div> <!-- end #basicwizard-->
                                </div> <!-- end card-body -->
                            </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                </div> <!-- end container -->
            </form>
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
            $('select').select2({width: '100%'});
            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
            elems.forEach(function(html) {
              var switchery = new Switchery(html);
            });
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
            $("#flood_limit").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#user_auto_kick_hours").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#probesize").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#stream_max_analyze").inputFilter(function(value) { return /^\d*$/.test(value); });
            $("#client_prebuffer").inputFilter(function(value) { return /^\d*$/.test(value); });
            
        });
        </script>
        
        <!-- App js-->
        <script src="assets/js/app.min.js"></script>
    </body>
</html>