<?php
include 'header.php';
if (isset($_SESSION['uid']) && ($_SESSION['Usertype'] ?? '') !== '3') {
  $user_info = $_SESSION['Username'] ?? '';

  /////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////                                                           /////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = ""): string
  {
    $theValue = mysqli_real_escape_string($GLOBALS['MySQL'], $theValue ?? '');

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

  if (isset($_POST["form_action"]) && $_POST["form_action"] === "ins") {

    $date = date('Y-m-d H:i:s');
    $v_dt = $_POST['date_trans'];

    $sql_id_info = "select (max(calculate_id)) nr from exchange_koke where perdoruesi = '" . $user_info . "'";
    $id_info = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = mysqli_fetch_assoc($id_info);
    $id_info_value = ($row_id_info['nr'] ?? 0) + 1;
    $id_calc = $user_info . 'TRN' . $id_info_value;

    $sql_id_info = "select kodllogari from filiali where id = " . (int)$_POST['id_llogfilial'];
    $id_info = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = mysqli_fetch_assoc($id_info);
    $id_llogarie01 = $row_id_info['kodllogari'] ?? '';

    $sql_id_info = "select kodllogari from filiali where id = " . $_POST['id_klienti'];
    $id_info = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = mysqli_fetch_assoc($id_info);
    $id_llogarie02 = $row_id_info['kodllogari'];

    $insertSQL = sprintf(
      "INSERT INTO exchange_koke ( tipiveprimit, pershkrimi, id, calculate_id, id_trans, date_trans, id_llogfilial, id_monkreditim, id_klienti, perqindjekomisioni, vleftakomisionit, vleftapaguar, perdoruesi, datarregjistrimit)
                                           VALUES ( 'TRN', %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['pershkrimi'], "text"),
      GetSQLValueString($id_calc, "text"),
      GetSQLValueString($id_info_value, "int"),
      GetSQLValueString($_POST['id_trans'], "int"),
      GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
      GetSQLValueString($_POST['id_llogfilial'], "int"),
      GetSQLValueString($_POST['id_mondebituar'], "int"),
      GetSQLValueString($_POST['id_klienti'], "int"),
      GetSQLValueString("0", "double"),
      GetSQLValueString("0", "double"),
      GetSQLValueString($_POST['vleftapaguar'], "double"),
      GetSQLValueString($user_info, "text"),
      GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date")
    );
    $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));

    $insertSQL = sprintf(
      "INSERT INTO exchange_detaje ( id_exchangekoke, id_mondebituar, vleftadebituar, vleftakredituar, kursi, kursi_txt, kursi1, kursi1_txt)
                                             VALUES ( %s, %s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($id_calc, "text"),
      GetSQLValueString($_POST['id_mondebituar'], "int"),
      GetSQLValueString($_POST['vleftapaguar'], "double"),
      GetSQLValueString($_POST['vleftapaguar'], "double"),
      GetSQLValueString($_POST['rate_value'], "double"),
      GetSQLValueString($_POST['id_mondebituar'] . "/" . $_POST['id_mondebituar'], "text"),
      GetSQLValueString($_POST['rate_value'], "double"),
      GetSQLValueString($_POST['id_mondebituar'] . "/" . $_POST['id_mondebituar'], "text")
    );
    $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));

    // shtimi i rreshtave per transaksionet
    if ($_POST['vleftapaguar'] > 0) {

      $insertSQL = sprintf(
        "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                    VALUES ( %s, %s, 'TRN', 'Kalim monetar ndermjet filialeve', %s, %s, %s, %s, 0, %s, %s, %s )",
        GetSQLValueString($id_calc, "text"),
        GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
        GetSQLValueString($_POST['id_llogfilial'], "int"),
        GetSQLValueString($id_llogarie01, "text"),
        GetSQLValueString($_POST['id_mondebituar'], "int"),
        GetSQLValueString($_POST['vleftapaguar'], "double"),
        GetSQLValueString($_POST['rate_value'], "double"),
        GetSQLValueString($user_info, "text"),
        GetSQLValueString($date, "date")
      );
      $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));
    }
    if ($_POST['vleftapaguar'] > 0) {

      $insertSQL = sprintf(
        "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                    VALUES ( %s, %s, 'TRN', 'Kalim monetar ndermjet filialeve', %s, %s, %s, 0, %s, %s, %s, %s )",
        GetSQLValueString($id_calc, "text"),
        GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
        GetSQLValueString($_POST['id_klienti'], "int"),
        GetSQLValueString($id_llogarie02, "text"),
        GetSQLValueString($_POST['id_mondebituar'], "int"),
        GetSQLValueString($_POST['vleftapaguar'], "double"),
        GetSQLValueString($_POST['rate_value'], "double"),
        GetSQLValueString($user_info, "text"),
        GetSQLValueString($date, "date")
      );
      $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));
    }



    $updateGoTo = "exchange_kalimp.php?hid=" . $id_info_value;
    header(sprintf("Location: %s", $updateGoTo));
  }

  $sql_id_info = "select opstatus from opencloseday";
  $id_info = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
  $row_id_info = mysqli_fetch_assoc($id_info);
  $opstatus    = $row_id_info['opstatus'] ?? '';

  if ($opstatus == "C") {

    $updateGoTo = "info.php";
    header(sprintf("Location: %s", $updateGoTo));
  }

  //----------------------------------------------------------------------------------

  // Initialize SQL where clauses
  $v_wheresql = "";
  $v_wheresqls = "";
  $v_wheresqle = "";

  // Set conditions based on user type
  if (($_SESSION['Usertype'] ?? '') === '3') {
    $userFilial = (int)($_SESSION['Userfilial'] ?? 0);
    $v_wheresql = " WHERE id = $userFilial";
    $v_wheresqls = " WHERE id <> $userFilial";
    $v_wheresqle = " AND id_llogfilial = $userFilial";
  }

  $query_filiali_info = "SELECT * FROM filiali" . $v_wheresql . " ORDER BY id ASC";
  $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
  $row_filiali_info = mysqli_fetch_assoc($filiali_info);

  $temp_v_wheresqle_query_klienti_info = isset($v_wheresqle) ? $v_wheresqle : "";
  $query_klienti_info = "select * from filiali " . $temp_v_wheresqle_query_klienti_info . " order by id asc";
  $klienti_info = mysqli_query($MySQL, $query_klienti_info) or die(mysqli_error($MySQL));
  $row_klienti_info = mysqli_fetch_assoc($klienti_info);
  $query_monedha_info = "select * from monedha order by mon_vendi desc, id ";
  $monedha_info = mysqli_query($MySQL, $query_monedha_info) or die(mysqli_error($MySQL));
  $row_monedha_info = mysqli_fetch_assoc($monedha_info);
  //----------------------------------------------------------------------------------
  $temp_v_wheresqle = isset($v_wheresqle) ? $v_wheresqle : "";
  $sql_info = "select * from kursi_koka where id = (select max(id) from kursi_koka where 1=1 " . $temp_v_wheresqle . ") " . $temp_v_wheresqle;
  $id_kursi = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
  $row_id_kursi = mysqli_fetch_assoc($id_kursi);
  $query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                          from kursi_detaje, monedha
                         where master_id = " . $row_id_kursi['id'] . "
                           and kursi_detaje.monedha_id = monedha.id ";
  $monkurs_info = mysqli_query($MySQL, $query_monkurs_info) or die(mysqli_error($MySQL));
  $row_monkurs_info = mysqli_fetch_assoc($monkurs_info);
  //----------------------------------------------------------------------------------

?>


  <script language="JavaScript">
    function focusOnMyInputBox() {
      document.getElementById("vleftapaguar").focus();
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
      news[<?php echo $row_monkurs_info['monid']; ?>][2] = "<?php echo ($row_monkurs_info['kursiblerje'] + $row_monkurs_info['kursishitje']) / 2; ?>";
    <?php
      $row_monkurs_info = mysqli_fetch_assoc($monkurs_info);
    };

    mysqli_free_result($monkurs_info);

    ?>

    function disp_kursitxt(mon_id) {

      document.formmenu.rate_value.value = news[mon_id][2];
    };

    function Open_Filial_Window() {

      childWindow = window.open('filial_list.php', 'FilialList', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
    }

    function Open_Filial2_Window() {

      childWindow = window.open('filial2_list.php', 'FilialList', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=335,height=350');
    }
  </script>

  <script language="JavaScript" src="calendar_eu.js"></script>
  <link rel="stylesheet" href="calendar.css">

  <script language="JavaScript">
    function checkform(form) {
      if (form.vleftapaguar.value == "") {
        alert("Ju lutem plotesoni fushen: vlefta");
        form.vleftapaguar.focus();
        return false;
      }
      if (form.vleftapaguar.value == "0") {
        alert("Ju lutem plotesoni fushen: vlefta");
        form.vleftapaguar.focus();
        return false;
      }
      if (form.vleftapaguar.value == "0.0") {
        alert("Ju lutem plotesoni fushen: vlefta");
        form.vleftapaguar.focus();
        return false;
      }
      if (form.vleftapaguar.value == "0.00") {
        alert("Ju lutem plotesoni fushen: vlefta");
        form.vleftapaguar.focus();
        return false;
      }

      if (form.id_mondebituar.value == "999") {
        alert("Ju lutem plotesoni fushen: jap");
        form.id_mondebituar.focus();
        return false;
      }

      return true;
    }
  </script>
  <div class="page-wrapper">
    <div class="container-fluid">
      <ul class="first-level base-level-line d-flex">
        <a href="exchange_transkl.php" class="tab-menu-seaction sidebar-link">
          <span class="hide-menu">Lista e transaksioneve</span>
        </a>
      </ul>
      <div class="card py-3">
        <div class="card-body d-flex align-items-center justify-content-between">
          <h4 class="card-title">
            <b>Veprime monetare</b>
          </h4>
        </div>
        <div class="container">
          <form enctype="multipart/form-data" action="exchange_kalimlog.php" method="POST" name="formmenu" onsubmit="return checkform(this);">
            <input name="form_action" type="hidden" value="ins">
            <input name="rate_value" type="hidden" value="1">
            <input name="total_value" type="hidden" value="">

            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Grup Trans.:</label>
                  <input name="id_trans" type="text" id="id_trans" value="<?php echo $_SESSION['Usertrans']; ?>" class="form-control" readonly>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Datë:</label>
                  <input name="date_trans" type="text" value="<?php echo strftime('%d.%m.%Y'); ?>" id="date_trans" class="form-control" readonly>
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

            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nga Filiali:</label>
                  <div class="input-group">
                    <select name="id_llogfilial" id="id_llogfilial" class="form-control">
                      <?php while ($row_filiali_info) { ?>
                        <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) echo "selected"; ?>>
                          <?php echo $row_filiali_info['filiali']; ?>
                        </option>
                      <?php
                        $row_filiali_info = mysqli_fetch_assoc($filiali_info);
                      }
                      mysqli_free_result($filiali_info);
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Tek Filiali:</label>
                  <div class="input-group">
                    <select name="id_klienti" id="id_klienti" class="form-control">
                      <?php while ($row_klienti_info) { ?>
                        <option value="<?php echo $row_klienti_info['id']; ?>" <?php if ($row_klienti_info['id'] <> $_SESSION['Userfilial']) echo "selected"; ?>>
                          <?php echo $row_klienti_info['filiali']; ?>
                        </option>
                      <?php
                        $row_klienti_info = mysqli_fetch_assoc($klienti_info);
                      }
                      mysqli_free_result($klienti_info);
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Monedha:</label>
                  <select name="id_mondebituar" id="id_mondebituar" class="form-control" onChange="JavaScript: disp_kursitxt(document.formmenu.id_mondebituar.value);">
                    <?php while ($row_monedha_info) { ?>
                      <option value="<?php echo $row_monedha_info['id']; ?>">
                        <?php echo $row_monedha_info['monedha']; ?> - <?php echo $row_monedha_info['pershkrimi']; ?>
                      </option>
                    <?php
                      $row_monedha_info = mysqli_fetch_assoc($monedha_info);
                    }
                    mysqli_free_result($monedha_info);
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Përshkrimi:</label>
                  <input name="pershkrimi" type="text" class="form-control" id="pershkrimi" value="Kalim monetar ndermjet filialeve" maxlength="100">
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-6">
                <div class="form-group text-right">
                  <label>Shuma për t'u kaluar:</label>
                  <input name="vleftapaguar" type="text" class="form-control text-end" id="vleftapaguar" value=".00">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 text-center">
                <input name="insupd" type="button" class="btn btn-primary" value=" Kryej veprimin "
                  onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.submit(); }">
              </div>
            </div>

          </form>
        </div>
      </div>

    <?php
    include 'footer.php';
  } else {
    header("Location: exchange.php");
  }
    ?>