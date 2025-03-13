<?php
ob_start(); // Start output buffering
include 'header.php';
if (isset($_SESSION['uid'])) {
    $stmt = mysqli_prepare($MySQL, "SELECT toggle FROM app_user WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['uid']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $_SESSION['toggle'] = $user['toggle'];
    mysqli_stmt_close($stmt);
}
//$clid = $_GET['clid'];
$clid = isset($_GET['clid']) ? $_GET['clid'] : null;

$user_info =  $_SESSION['Username'] ?? addslashes($_SESSION['Username']);


function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
    $theValue = addslashes($theValue) ?? $theValue;

    switch ($theType) {
        case "text":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "long":
        case "int":
            $theValue = ($theValue != "") ? intval($theValue) : "NULL";
            break;
        case "double":
            $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
            break;
        case "date":
            $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
            break;
        case "defined":
            $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
            break;
    }
    return $theValue;
}
/////////////////////////////////////////////////////////////////////////////////////////////////

if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {

    $insertSQL = sprintf(
        "INSERT INTO exchange_ins ( changeinsid, userinsid ) VALUES ( %s, %s )",
        GetSQLValueString($_POST['internid'], "text"),
        GetSQLValueString($user_info, "text")
    );
    $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));

    $id_inscheck = 0;
    $sql_id_info = "select count(*) as nr from exchange_ins where changeinsid = '" . $_POST['internid'] . "' and userinsid = '" . $user_info . "' ";
    $id_info = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = $id_info->fetch_assoc();
    $id_inscheck = $row_id_info['nr'];

    if ($id_inscheck == 1) {

        $date = strftime('%Y-%m-%d %H:%M:%S');
        $v_dt = $_POST['date_trans'];

        $sql_id_info = "select (max(calculate_id)) nr from exchange_koke where perdoruesi = '" . $user_info . "'";
        $id_info = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
        $row_id_info = $id_info->fetch_assoc();
        $id_info_value = $row_id_info['nr'] + 1;
        $id_calc = $user_info . 'CHN' . $id_info_value;

        $sql_id_info = "select kodi from llogarite where chnvl = 'T'";
        $id_info = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
        $row_id_info = $id_info->fetch_assoc();
        $id_llogarie = $row_id_info['kodi'];

        $sql_id_info = "select kodi from llogarite where chnco = 'T'";
        $id_info = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
        $row_id_info = $id_info->fetch_assoc();
        $id_komisioni = $row_id_info['kodi'];

        $insertSQL = sprintf(
            "INSERT INTO exchange_koke ( id, calculate_id, menyrepagese, id_trans, date_trans, id_llogfilial, id_monkreditim, id_klienti, perqindjekomisioni, vleftakomisionit, vleftapaguar, burimteardhura, perdoruesi, datarregjistrimit) 
                                                 VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_calc, "text"),
            GetSQLValueString($id_info_value, "int"),
            GetSQLValueString($_POST['menyrepagese'], "text"),
            GetSQLValueString($_POST['id_trans'], "int"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
            GetSQLValueString($_POST['id_llogfilial'], "int"),
            GetSQLValueString($_POST['id_monkreditim'], "int"),
            GetSQLValueString($_POST['id_klienti'], "int"),
            GetSQLValueString($_POST['perqindjekomisioni'], "double"),
            GetSQLValueString($_POST['vleftakomisionit'], "double"),
            GetSQLValueString($_POST['vleftapaguar'], "double"),
            GetSQLValueString($_POST['burimteardhura'], "text"),
            GetSQLValueString($user_info, "text"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date")
        );
        $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));

        if ($_POST['menyrepagese'] == "CASH") {
            $insertSQL = sprintf(
                "INSERT INTO exchange_detaje ( id_exchangekoke, id_mondebituar, vleftadebituar, vleftadebituarjocash, vleftakredituar, kursi, kursi_txt, kursi1, kursi1_txt) 
                                                       VALUES ( %s, %s, %s, 0, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString($_POST['id_mondebituar'], "int"),
                GetSQLValueString($_POST['vleftadebituar'], "double"),
                GetSQLValueString($_POST['vleftakredituar'], "double"),
                GetSQLValueString($_POST['kursi'], "double"),
                GetSQLValueString($_POST['kursi_txt'], "text"),
                GetSQLValueString($_POST['kursi1'], "double"),
                GetSQLValueString($_POST['kursi1_txt'], "text")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        } else {
            $insertSQL = sprintf(
                "INSERT INTO exchange_detaje ( id_exchangekoke, id_mondebituar, vleftadebituar, vleftadebituarjocash, vleftakredituar, kursi, kursi_txt, kursi1, kursi1_txt) 
                                                       VALUES ( %s, %s, 0, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString($_POST['id_mondebituar'], "int"),
                GetSQLValueString($_POST['vleftadebituar'], "double"),
                GetSQLValueString($_POST['vleftakredituar'], "double"),
                GetSQLValueString($_POST['kursi'], "double"),
                GetSQLValueString($_POST['kursi_txt'], "text"),
                GetSQLValueString($_POST['kursi1'], "double"),
                GetSQLValueString($_POST['kursi1_txt'], "text")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }

        // shtimi i rreshtave per transaksionet
        if (($_POST['vleftadebituar'] > 0) && ($_POST['menyrepagese'] == "CASH")) {
            $insertSQL = sprintf(
                "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, id_klienti, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                          VALUES ( %s, %s, 'CHN', 'Veprim Kembimi Monetar', %s, %s, %s, %s, 0, %s, %s, %s, %s )",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
                GetSQLValueString($_POST['id_llogfilial'], "int"),
                GetSQLValueString($id_llogarie, "text"),
                GetSQLValueString($_POST['id_mondebituar'], "int"),
                GetSQLValueString($_POST['id_klienti'], "int"),
                GetSQLValueString($_POST['vleftadebituar'], "double"),
                GetSQLValueString($_POST['kursi'], "double"),
                GetSQLValueString($user_info, "text"),
                GetSQLValueString($date, "date")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }
        if ($_POST['vleftakredituar'] > 0) {
            $insertSQL = sprintf(
                "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, id_klienti, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                          VALUES ( %s, %s, 'CHN', 'Veprim Kembimi Monetar', %s, %s, %s, %s, %s, 0, %s, %s, %s )",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
                GetSQLValueString($_POST['id_llogfilial'], "int"),
                GetSQLValueString($id_llogarie, "text"),
                GetSQLValueString($_POST['id_monkreditim'], "int"),
                GetSQLValueString($_POST['id_klienti'], "int"),
                GetSQLValueString($_POST['vleftapaguar'], "double"),
                GetSQLValueString($_POST['kursi1'], "double"),
                GetSQLValueString($user_info, "text"),
                GetSQLValueString($date, "date")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }
        if ($_POST['vleftakomisionit'] > 0) {
            $insertSQL = sprintf(
                "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, id_klienti, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                          VALUES ( %s, %s, 'CHN', 'Veprim Kembimi Monetar - Komision', %s, %s, %s, %s, %s, 0, %s, %s, %s )",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
                GetSQLValueString($_POST['id_llogfilial'], "int"),
                GetSQLValueString($id_llogarie, "text"),
                GetSQLValueString($_POST['id_monkreditim'], "int"),
                GetSQLValueString($_POST['id_klienti'], "int"),
                GetSQLValueString($_POST['vleftakomisionit'], "double"),
                GetSQLValueString($_POST['kursi1'], "double"),
                GetSQLValueString($user_info, "text"),
                GetSQLValueString($date, "date")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));

            $insertSQL = sprintf(
                "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, id_klienti, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                          VALUES ( %s, %s, 'CHN', 'Veprim Kembimi Monetar - Komision', %s, %s, %s, %s, 0, %s, %s, %s, %s )",
                GetSQLValueString($id_calc, "text"),
                GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
                GetSQLValueString($_POST['id_llogfilial'], "int"),
                GetSQLValueString($id_komisioni, "text"),
                GetSQLValueString($_POST['id_monkreditim'], "int"),
                GetSQLValueString($_POST['id_klienti'], "int"),
                GetSQLValueString($_POST['vleftakomisionit'], "double"),
                GetSQLValueString($_POST['kursi1'], "double"),
                GetSQLValueString($user_info, "text"),
                GetSQLValueString($date, "date")
            );
            $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }
    }

    $sql_id_info = "select (max(calculate_id)) nr from exchange_koke where perdoruesi = '" . $user_info . "'";
    $id_info = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = $id_info->fetch_assoc();
    $id_info_value = $row_id_info['nr'];

    if ($_POST['download_type'] === 'json') {
        $updateGoTo = "exchange_print.php?hid=" . $id_info_value . "&download_type=json";
    } else {
        // $updateGoTo = "exchange_print.php?hid=" . $id_info_value . "&download_type=regular";
        $updateGoTo = "exchange.php";
    }
    header(sprintf("Location: %s", $updateGoTo));
    exit();
}

// $sql_id_info = "select opstatus from opencloseday ";
// $id_info     = mysql_query($sql_id_info, $MySQL) or die(mysql_error());
$sql_id_info = "SELECT opstatus FROM opencloseday";
$id_info     = $MySQL->query($sql_id_info);

// $row_id_info = mysql_fetch_assoc($id_info);
// $opstatus    = $row_id_info['opstatus'];
$row_id_info = $id_info->fetch_assoc(); // Fetch associative array
$opstatus    = $row_id_info['opstatus'] ?? null; // Avoid undefined index error
if ($opstatus == "C") {

    $updateGoTo = "info.php";
    header(sprintf("Location: %s", $updateGoTo));
}

//----------------------------------------------------------------------------------

$v_wheresql = "";
$v_llog = 1;
if ($_SESSION['Usertype'] == 2)  $v_llog = $_SESSION['Userfilial'];
if ($_SESSION['Usertype'] == 3)  $v_llog = $_SESSION['Userfilial'];
if ($_SESSION['Usertype'] == 2)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";
if ($_SESSION['Usertype'] == 3)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";
if ($_SESSION['Usertype'] == 2)  $v_wheresqls = " and id_llogfilial = " . $_SESSION['Userfilial'] . " ";
if ($_SESSION['Usertype'] == 3)  $v_wheresqls = " and id_llogfilial = " . $_SESSION['Userfilial'] . " ";

// var_dump($_SESSION['Usertype']);
// exit();
$query_filiali_info = "select * from filiali " . $v_wheresql   . " order by filiali asc";
// $filiali_info = mysql_query($query_filiali_info, $MySQL) or die(mysql_error());
// $row_filiali_info = mysql_fetch_assoc($filiali_info);
$filiali_info = $MySQL->query($query_filiali_info);
$row_filiali_info = $filiali_info->fetch_assoc();
//  var_dump($row_filiali_info);
//  exit();
$query_klienti_info = "select * from klienti order by emri, mbiemri";
$klienti_info = $MySQL->query($query_klienti_info);
$row_klienti_info = $klienti_info->fetch_assoc();
// $klienti_info = mysql_query($query_klienti_info, $MySQL) or die(mysql_error());
// $row_klienti_info = mysql_fetch_assoc($klienti_info);

$query_monedha_info = "select * from monedha order by mon_vendi desc, id ";

$monedha_info = $MySQL->query($query_monedha_info);
$row_monedha_info = $monedha_info->fetch_assoc();
// $monedha_info = mysql_query($query_monedha_info, $MySQL) or die(mysql_error());
// $row_monedha_info = mysql_fetch_assoc($monedha_info);

//----------------------------------------------------------------------------------
$temp_v_wheresqls = $v_wheresqls ?? '';
$sql_info = "SELECT * FROM kursi_koka WHERE id = (SELECT MAX(id) FROM kursi_koka WHERE 1=1 " . $temp_v_wheresqls . ") " . $temp_v_wheresqls;
$id_kursi = $MySQL->query($sql_info);

if ($id_kursi === false) {
    // Handle query error
    die('Query Error: ' . $MySQL->error);
}

// Check if we got results
if ($id_kursi->num_rows > 0) {
    $row_id_kursi = $id_kursi->fetch_assoc();
} else {
    // Handle no results case
    $row_id_kursi = null;
    // Or set default values if needed:
    // $row_id_kursi = ['default_key' => 'default_value'];
}

$query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                          from kursi_detaje, monedha
                         where master_id = " . $row_id_kursi['id'] . "
                           and kursi_detaje.monedha_id = monedha.id ";

$monkurs_info = $MySQL->query($query_monkurs_info);
$row_monkurs_info = $monkurs_info->fetch_assoc();

// $monkurs_info = mysql_query($query_monkurs_info, $MySQL) or die(mysql_error());
// $row_monkurs_info = mysql_fetch_assoc($monkurs_info);
//----------------------------------------------------------------------------------
?>

<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">
<div class="page-wrapper">
    <div class="container-fluid">
        <ul class="first-level base-level-line d-flex">
            <!-- <a href="insupd_client_data2.php" class="tab-menu-seaction sidebar-link">
                <span class="hide-menu">Shto klient</span>
            </a> -->
            <a href="exchange_trans.php" class="tab-menu-seaction sidebar-link">
                <span class="hide-menu">Lista e transaksioneve</span>
            </a>
        </ul>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form enctype="multipart/form-data" action="exchange.php" METHOD="POST" name="formmenu" id="formmenu" onsubmit="return checkform(this);">
                            <input name="form_action" type="hidden" value="ins">
                            <input name="rate_value" type="hidden" value="">
                            <input name="total_value" type="hidden" value="">
                            <input name="download_type" type="hidden" value="regular">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Grup Trans.:&nbsp;</lable>
                                            <input class="form-control" name="id_trans" type="text" id="id_trans" value="<?php echo $_SESSION['Usertrans']; ?>" size="10" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Dat&euml;:&nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <input class="form-control me-2" name="date_trans" type="text" value="<?php echo strftime('%d.%m.%Y'); ?>" id="date_trans" size="10" readonly>
                                                <script language="JavaScript">
                                                    var o_cal = new tcal({
                                                        'formname': 'formmenu',
                                                        'controlname': 'date_trans'
                                                    });
                                                    o_cal.a_tpl.yearscroll = true;
                                                    o_cal.a_tpl.weekstart = 1;
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Filiali:&nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <select name="id_llogfilial" id="id_llogfilial" class="form-select mr-sm-2 me-2">
                                                    <?php
                                                    while ($row_filiali_info) {
                                                    ?>
                                                        <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) {
                                                                                                                    echo "selected";
                                                                                                                } ?>><?php echo $row_filiali_info['filiali']; ?></option>
                                                    <?php
                                                        $row_filiali_info = $filiali_info->fetch_assoc();
                                                    }
                                                    mysqli_free_result($filiali_info);
                                                    ?>
                                                </select>
                                                <a class="btn btn-outline-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#FilialiModal">
                                                    <i class="fas fa-file cursor-pointer"></i>
                                                </a>
                                                <!-- <a class="link4" href="JavaScript: Open_Filial_Window();">
                                <img src="images/doc.gif" border="0">
                            </a> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Klienti:&nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <select name="id_klienti" id="id_klienti" class="form-select mr-sm-2 me-2">
                                                    <?php
                                                    while ($row_klienti_info) {
                                                    ?>
                                                        <option value="<?php echo $row_klienti_info['id']; ?>" <?php if ($row_klienti_info['id'] == $clid) {
                                                                                                                    echo "selected";
                                                                                                                } ?>><?php echo $row_klienti_info['emriplote']; ?></option>
                                                    <?php
                                                        $row_klienti_info = $klienti_info->fetch_assoc();
                                                    }
                                                    mysqli_free_result($klienti_info);
                                                    ?>
                                                </select>
                                                <a class="btn btn-outline-primary" onclick="openClientModal('ins')">
                                                    <i class="fas fa-plus cursor-pointer"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Blej:&nbsp;&nbsp;&nbsp;&nbsp;</lable>
                                            <select class="form-select mr-sm-2" name="id_mondebituar" id="id_mondebituar" OnChange="JavaScript: disp_kursitxt( document.formmenu.id_mondebituar.value, document.formmenu.id_monkreditim.value, '/');  calculate_rate_value (); ">
                                                <?php
                                                while ($row_monedha_info) {

                                                    if ($row_monedha_info['id'] == "2") {
                                                ?>
                                                        <option value="<?php echo $row_monedha_info['id']; ?>" selected="selected"><?php echo $row_monedha_info['monedha']; ?> - <?php echo $row_monedha_info['pershkrimi']; ?></option>
                                                    <?php       } else {
                                                    ?>
                                                        <option value="<?php echo $row_monedha_info['id']; ?>"><?php echo $row_monedha_info['monedha']; ?> - <?php echo $row_monedha_info['pershkrimi']; ?></option>
                                                <?php
                                                    }
                                                    $row_monedha_info = $monedha_info->fetch_assoc();
                                                }
                                                mysqli_free_result($monedha_info);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Shuma:&nbsp;</lable>
                                            <input class="form-control text-end" name="vleftadebituar" type="text" class="inputtext2" id="vleftadebituar" value=".00" size="15" onChange="JavaScript: if (document.formmenu.id_monkreditim.value != '999')  calculate_rate_value (); " onKeyDown="if (event.keyCode == 13) document.formmenu.insupd.focus(); ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Shitje:&nbsp;&nbsp;</lable>
                                            <select class="form-select mr-sm-2" name="id_monkreditim" id="id_monkreditim" OnChange="JavaScript: disp_kursitxt( document.formmenu.id_mondebituar.value, document.formmenu.id_monkreditim.value, '/'); calculate_rate_value ();" onKeyDown="if (event.keyCode == 13) document.formmenu.insupd.focus(); ">
                                                <option value="999"></option>
                                                <?php

                                                $monedha_info = $MySQL->query($query_monedha_info) or die(mysqli_error($MySQL));
                                                $row_monedha_info = $monedha_info->fetch_assoc();

                                                while ($row_monedha_info) {

                                                    if ($row_monedha_info['id'] == "1") {
                                                ?>
                                                        <option value="<?php echo $row_monedha_info['id']; ?>" selected="selected"><?php echo $row_monedha_info['monedha']; ?> - <?php echo $row_monedha_info['pershkrimi']; ?></option>
                                                    <?php       } else {
                                                    ?>
                                                        <option value="<?php echo $row_monedha_info['id']; ?>"><?php echo $row_monedha_info['monedha']; ?> - <?php echo $row_monedha_info['pershkrimi']; ?></option>
                                                <?php
                                                    }
                                                    $row_monedha_info = $monedha_info->fetch_assoc();
                                                }
                                                mysqli_free_result($monedha_info);
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Kursi: &nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <input class="form-control" name="kursi_txt" type="text" class="inputtext5" id="kursi_txt" value="LEK/" size="10" readonly>
                                                <span class="d-block px-2">=</span>
                                                <input class="form-control text-end" name="kursi" type="text" class="inputtext2" id="kursi" value="" size="10" OnChange="JavaScript: calculate_rate_value3 (); calculate_value ();">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input name="hkursi" type="hidden" id="hkursi" value="">
                                <input name="internid" type="hidden" id="internid" value="<?php echo strftime('%Y%m%d%H%M%S'); ?>">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Totali: &nbsp;</lable>
                                            <input class="form-control text-end" name="vleftakredituar" type="text" class="inputtext2" id="vleftakredituar" value="0.00" size="15" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Kursi: &nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <input class="form-control" name="kursi1_txt" type="text" class="inputtext5" id="kursi1_txt" value="/LEK" size="10" readonly>
                                                <span class="d-block px-2">=</span>
                                                <input class="form-control text-end" name="kursi1" type="text" class="inputtext2" id="kursi1" value="" size="10" OnChange="JavaScript: calculate_rate_value2 (); calculate_value ();">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Komisioni: &nbsp;</lable>
                                            <div class="d-flex align-items-center">
                                                <input class="form-control text-end" name="perqindjekomisioni" type="text" class="inputtext2" id="perqindjekomisioni" value="0.00" size="4" OnChange="JavaScript: llogarit_komisionin ();">
                                                <span class="d-block px-2">%</span>
                                                <input class="form-control text-end" name="vleftakomisionit" type="text" class="inputtext2" id="vleftakomisionit" value="0.00" size="10" onChange="JavaScript: llogarit_komisionin_fix ();">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group mb-3">
                                            <lable class="form-label">Menyre:</lable>
                                            <select class="form-select mr-sm-2 me-2" name="menyrepagese" id="menyrepagese">
                                                <option value="CASH">CASH</option>
                                                <option value="BANKE">BANK</option>
                                                <option value="POS">POS</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <div class="row ctxheading">
                                                <div class="col-md-5">
                                                    <lable class="form-label">P&euml;r t'u paguar:&nbsp;</lable>
                                                    <input class="form-control text-end" name="vleftapaguar" type="text" class="inputtext2" id="vleftapaguar" value="0.00" size="15" onChange="JavaScript: llogarit_mbrapsht ();">
                                                </div>
                                                <div class="col-md-5">
                                                    <lable class="form-label">Burimi i t&euml; ardhurave:&nbsp;</lable>
                                                    <input class="form-control" name="burimteardhura" type="text" class="inputtext" id="burimteardhura" value="" size="40">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php if ($_SESSION['toggle'] == 'ON') { ?>
                                            <div class="form-group mb-3">
                                                <input class="btn btn-info d-block ms-auto" name="insupd" class="inputtext4" type="button" value="Kryej veprimin" onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.submit(); }">
                                            </div>
                                            <?php } else { ?>
                                            <div class="form-group mb-3 d-flex justify-content-between">
                                                <input class="btn btn-info d-block ms-auto" name="insupd" class="inputtext4" type="button" value="Kryej veprimin" onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.download_type.value='regular'; document.formmenu.submit(); }">
                                                <input class="btn btn-info d-block ms-auto" name="insupd" class="inputtext4" type="button" value="Shkarkoni faturën json" onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.download_type.value='json'; document.formmenu.submit(); }">
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                focusOnMyInputBox();
                                disp_kursitxt(document.formmenu.id_mondebituar.value, document.formmenu.id_monkreditim.value, '/');
                                calculate_rate_value();
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="FilialiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="FilialiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="FilialiLabel">Filiali</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <?php

                        require_once('ConMySQL.php');

                        $v_wheresql = " ";
                        if ($_SESSION['Usertype'] == 3)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";

                        $query_filiali_info = "select * from filiali " . $v_wheresql . " order by filiali asc";
                        $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
                        $row_filiali_info = mysqli_fetch_assoc($filiali_info);

                        ?>
                        <div class=ctxheading>Perzgjidh nga lista</div>

                    </div>
                    <center>
                        <table height="100%" border="0" width="100%">
                            <!-- <tr>
                                <td height="43" colspan="3">
                                    <DIV class=ctxheading>Perzgjidh nga lista</DIV>
                                </td>
                            </tr> -->
                            <tr valign="top">
                                <td width="80%" align="center">

                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td height="1" width="10" align="center" class="titull"></td>
                                            <td height="1" width="280" align="center" class="titull"></td>
                                            <td height="1" width="10" align="center" class="titull"></td>
                                        </tr>
                                        <?php while ($row_filiali_info) {  ?>
                                            <tr bgcolor="#080570">
                                                <td height="1" colspan="3" align="center" class="titull"></td>
                                            </tr>
                                            <tr bgcolor="#99FFCC">
                                                <td class="titull"></td>
                                                <td height="16"><a href="JavaScript: return_value('<?php echo $row_filiali_info['id']; ?>');" onclick="document.getElementById('id_llogfilial').value='<?php echo $row_filiali_info['id']; ?>'; 
                                   document.querySelector('[data-bs-dismiss=modal]').click();" class="link4"><b><?php echo $row_filiali_info['filiali']; ?></b></a></td>
                                                <td class="titull"></td>
                                            </tr>
                                        <?php $row_filiali_info = mysqli_fetch_assoc($filiali_info);
                                        }
                                        mysqli_free_result($filiali_info);
                                        ?>
                                        <tr bgcolor="#080570">
                                            <td height="1" colspan="3" align="center" class="titull"></td>
                                        </tr>
                                        <tr>
                                            <td height="5" colspan="3" align="center" class="titull"></td>
                                        </tr>
                                    </table>
                                    <br>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="clientModal" tabindex="-1" role="dialog" aria-labelledby="clientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientModalLabel">Administrimi i klientëve</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script language="JavaScript">
        function openClientModal(action, id = null) {
            let url = 'insupd_client_data.php?action=' + action;
            url += '&exchange=1';
            if (id) {
                url += '&hid=' + id;
            }

            // Load content into modal
            $('#clientModal').modal('hide'); // Hide any open modal
            $.get(url, function(data) {
                $('#clientModal .modal-body').html(data);
                $('#clientModal').modal('show');
            });
        }

        function focusOnMyInputBox() {
            console.log("dgdf");
            document.getElementById("vleftadebituar").focus();
        }
        rate_value = 0;
        news = new Array();

        news[1] = new Array();
        news[1][1] = "LEK";
        news[1][2] = "1";
        news[1][3] = "1";

        news[999] = new Array();
        news[999][1] = "";
        news[999][2] = "";
        news[999][3] = "";
        <?php
        while ($row_monkurs_info) { ?>

            news[<?php echo $row_monkurs_info['monid']; ?>] = new Array();
            news[<?php echo $row_monkurs_info['monid']; ?>][1] = "<?php echo $row_monkurs_info['monedha']; ?>";
            news[<?php echo $row_monkurs_info['monid']; ?>][2] = "<?php echo $row_monkurs_info['kursiblerje']; ?>";
            news[<?php echo $row_monkurs_info['monid']; ?>][3] = "<?php echo $row_monkurs_info['kursishitje']; ?>";
        <?php
            $row_monkurs_info = $monkurs_info->fetch_assoc();
        };

        mysqli_free_result($monkurs_info);

        ?>

        function disp_kursitxt(inic_id, mon_id, ndares) {

            document.formmenu.kursi_txt.value = news[inic_id][1] + ndares + news[mon_id][1];
            document.formmenu.kursi1_txt.value = news[mon_id][1] + ndares + news[inic_id][1];

        };

        function calculate_rate_value() {

            if ((document.formmenu.id_mondebituar.value != '999') && (document.formmenu.id_monkreditim.value != '999')) {

                rate_value = news[document.formmenu.id_mondebituar.value][2] / news[document.formmenu.id_monkreditim.value][
                    3
                ];

                document.formmenu.hkursi.value = news[document.formmenu.id_mondebituar.value][2] / news[document.formmenu
                    .id_monkreditim.value][3];

                var mrv = new String(rate_value);

                if (mrv.indexOf('.') == -1) {
                    document.formmenu.kursi.value = mrv.substr(0, mrv.length);
                } else {
                    document.formmenu.kursi.value = mrv.substr(0, mrv.indexOf('.') + 7);
                };

                rate_value = news[document.formmenu.id_monkreditim.value][3] / news[document.formmenu.id_mondebituar.value][
                    2
                ];

                var mrv = new String(rate_value);

                if (mrv.indexOf('.') == -1) {
                    document.formmenu.kursi1.value = mrv.substr(0, mrv.length);
                } else {
                    document.formmenu.kursi1.value = mrv.substr(0, mrv.indexOf('.') + 7);
                };

                var v_nr1 = new Number(document.formmenu.vleftadebituar.value);
                var v_nr2 = new Number(document.formmenu.hkursi.value);

                var tv = new String(v_nr1.valueOf() * v_nr2.valueOf());

                if (tv.indexOf('.') == -1) {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                } else {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 5)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
                };
            };
        };

        function calculate_rate_value2() {

            if ((document.formmenu.id_mondebituar.value != '999') && (document.formmenu.id_monkreditim.value != '999')) {

                rate_value = 1 / document.formmenu.kursi1.value;

                document.formmenu.hkursi.value = 1 / document.formmenu.kursi1.value;

                var mrv = new String(rate_value);

                if (mrv.indexOf('.') == -1) {
                    document.formmenu.kursi.value = mrv.substr(0, mrv.length);
                } else {
                    document.formmenu.kursi.value = mrv.substr(0, mrv.indexOf('.') + 7);
                };

                var v_nr1 = new Number(document.formmenu.vleftadebituar.value);
                var v_nr2 = new Number(document.formmenu.hkursi.value);

                var tv = new String(v_nr1.valueOf() * v_nr2.valueOf());

                if (tv.indexOf('.') == -1) {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                } else {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 5)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
                };
            };
        };

        function calculate_rate_value3() {

            if ((document.formmenu.id_mondebituar.value != '999') && (document.formmenu.id_monkreditim.value != '999')) {

                rate_value = 1 / document.formmenu.kursi.value;

                document.formmenu.hkursi.value = document.formmenu.kursi.value;

                var mrv = new String(rate_value);

                if (mrv.indexOf('.') == -1) {
                    document.formmenu.kursi1.value = mrv.substr(0, mrv.length);
                } else {
                    document.formmenu.kursi1.value = mrv.substr(0, mrv.indexOf('.') + 7);
                };

                var v_nr1 = new Number(document.formmenu.vleftadebituar.value);
                var v_nr2 = new Number(document.formmenu.hkursi.value);

                var tv = new String(v_nr1.valueOf() * v_nr2.valueOf());

                if (tv.indexOf('.') == -1) {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                } else {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 5)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
                };
            };
        };

        function calculate_value() {

            if ((document.formmenu.kursi.value != '0') && (document.formmenu.kursi.value != '0.0') && (document.formmenu
                    .kursi.value != '0.00')) {

                var v_nr1 = new Number(document.formmenu.vleftadebituar.value);
                var v_nr2 = new Number(document.formmenu.hkursi.value);

                var tv = new String(v_nr1.valueOf() * v_nr2.valueOf());

                if (tv.indexOf('.') == -1) {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.length)));
                } else {
                    document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 5)));
                    document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
                };
            };
        };

        function llogarit_komisionin() {

            var v_nr1 = new Number(document.formmenu.vleftakredituar.value);
            var v_nr2 = new Number(document.formmenu.perqindjekomisioni.value);

            var kv = new String(v_nr1.valueOf() / 100 * v_nr2.valueOf());

            if (kv.indexOf('.') == -1) {
                document.formmenu.vleftakomisionit.value = parseInt(Math.round(kv.substr(0, kv.length)));
            } else {
                document.formmenu.vleftakomisionit.value = parseInt(Math.round(kv.substr(0, kv.indexOf('.') + 3)));
            };

            var v_nr1 = new Number(document.formmenu.vleftakredituar.value);
            var v_nr2 = new Number(document.formmenu.vleftakomisionit.value);

            var tv = new String(v_nr1.valueOf() - v_nr2.valueOf());

            if (tv.indexOf('.') == -1) {
                document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.length)));
            } else {
                document.formmenu.vleftapaguar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
            };

        };

        function llogarit_komisionin_fix() {

            var v_nr1 = new Number(document.formmenu.vleftakredituar.value);
            var v_nr2 = new Number(document.formmenu.vleftakomisionit.value);

            var tv = new String(v_nr1.valueOf() - v_nr2.valueOf());

            if (tv.indexOf('.') == -1) {
                document.formmenu.vleftapaguar.value = tv.substr(0, tv.length);
            } else {
                document.formmenu.vleftapaguar.value = tv.substr(0, tv.indexOf('.') + 3);
            };

        };

        function llogarit_mbrapsht() {

            if ((document.formmenu.perqindjekomisioni.value != '0') && (document.formmenu.perqindjekomisioni.value !=
                    '0.0') && (document.formmenu.perqindjekomisioni.value != '0.00')) {

                var v_nr1 = new Number(document.formmenu.vleftapaguar.value);
                var v_nr2 = new Number(document.formmenu.perqindjekomisioni.value);

                var kv = new String(v_nr1.valueOf() / (100 - v_nr2.valueOf()) * v_nr2.valueOf());

                if (kv.indexOf('.') == -1) {
                    document.formmenu.vleftakomisionit.value = kv.substr(0, kv.length);
                } else {
                    document.formmenu.vleftakomisionit.value = kv.substr(0, kv.indexOf('.') + 3);
                };
            }

            var v_nr1 = new Number(document.formmenu.vleftapaguar.value);
            var v_nr2 = new Number(document.formmenu.vleftakomisionit.value);
            var v_nr3 = new Number(document.formmenu.hkursi.value);

            var tv = new String(v_nr1.valueOf() + v_nr2.valueOf());

            if (tv.indexOf('.') == -1) {
                document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
            } else {
                document.formmenu.vleftakredituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
            };

            var tv = new String((v_nr1.valueOf() + v_nr2.valueOf()) / v_nr3.valueOf());

            if (tv.indexOf('.') == -1) {
                document.formmenu.vleftadebituar.value = parseInt(Math.round(tv.substr(0, tv.length)));
            } else {
                document.formmenu.vleftadebituar.value = parseInt(Math.round(tv.substr(0, tv.indexOf('.') + 3)));
            };

        };
    </script>

    <script language="JavaScript">
        function Open_Filial_Window() {

            childWindow = window.open('filial_list.php', 'FilialList',
                'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
        }

        function Open_Klient_Window() {

            childWindow = window.open('klient_list.php', 'KlientList',
                'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
        }
    </script>