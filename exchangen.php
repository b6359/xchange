<?php include 'header.php'; ?>
<?php

$clid = $_GET['clid'] ?? '';

  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  /////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////                                                           /////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////
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

    $date = strftime('%Y-%m-%d');
    $v_dt = $_POST['date_trans'];

    $prerje = array(
      "100",
      "150",
      "200",
      "100",
      "250",
      "400",
      "350",
      "500",
      "150",
      "250",
      "150",
      "100",
      "50",
      "210",
      "450",
      "170",
      "320",
      "470",
      "120",
      "220",
      "370"
    );
    $id_mondebituar = $_POST['id_mondebituar'];
    if ($id_mondebituar == 1) {

      $vleftakredituartot = $_POST['vleftakredituar'];
      while ($vleftakredituartot > 0) {

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

        $vleftakredituar = $prerje[rand(1, 20)];
        if ($vleftakredituar > $vleftakredituartot) {
          $vleftakredituar = $vleftakredituartot;
        }
        $vleftadebituar  = $vleftakredituar / $_POST['kursi'];

        if ($vleftakredituar > 0) {

          $insertSQL = sprintf(
            "INSERT INTO exchange_koke ( id, calculate_id, id_trans, date_trans, id_llogfilial, id_monkreditim, id_klienti, perqindjekomisioni, vleftakomisionit, vleftapaguar, burimteardhura, perdoruesi, datarregjistrimit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_calc, "text"),
            GetSQLValueString($id_info_value, "int"),
            GetSQLValueString($_POST['id_trans'], "int"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
            GetSQLValueString($_POST['id_llogfilial'], "int"),
            GetSQLValueString($_POST['id_monkreditim'], "int"),
            GetSQLValueString($_POST['id_klienti'], "int"),
            GetSQLValueString("0", "double"),
            GetSQLValueString("0", "double"),
            GetSQLValueString($vleftakredituar, "double"),
            GetSQLValueString($_POST['burimteardhura'], "text"),
            GetSQLValueString($user_info, "text"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date")
          );
          $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));

          $insertSQL = sprintf(
            "INSERT INTO exchange_detaje ( id_exchangekoke, id_mondebituar, vleftadebituar, vleftakredituar, kursi, kursi_txt, kursi1, kursi1_txt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_calc, "text"),
            GetSQLValueString($_POST['id_mondebituar'], "int"),
            GetSQLValueString($vleftadebituar, "double"),
            GetSQLValueString($vleftakredituar, "double"),
            GetSQLValueString($_POST['kursi'], "double"),
            GetSQLValueString($_POST['kursi_txt'], "text"),
            GetSQLValueString($_POST['kursi1'], "double"),
            GetSQLValueString($_POST['kursi1_txt'], "text")
          );
          $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }

        $vleftakredituartot = $vleftakredituartot - $vleftakredituar;
      }
    } else {

      $vleftadebituartot = $_POST['vleftadebituar'];
      while ($vleftadebituartot > 0) {

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

        $vleftadebituar  = $prerje[rand(1, 20)];
        if ($vleftadebituar > $vleftadebituartot) {
          $vleftadebituar = $vleftadebituartot;
        }
        $vleftakredituar = $vleftadebituar * $_POST['kursi'];

        if ($vleftadebituar > 0) {

          $insertSQL = sprintf(
            "INSERT INTO exchange_koke ( id, calculate_id, id_trans, date_trans, id_llogfilial, id_monkreditim, id_klienti, perqindjekomisioni, vleftakomisionit, vleftapaguar, burimteardhura, perdoruesi, datarregjistrimit) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_calc, "text"),
            GetSQLValueString($id_info_value, "int"),
            GetSQLValueString($_POST['id_trans'], "int"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date"),
            GetSQLValueString($_POST['id_llogfilial'], "int"),
            GetSQLValueString($_POST['id_monkreditim'], "int"),
            GetSQLValueString($_POST['id_klienti'], "int"),
            GetSQLValueString("0", "double"),
            GetSQLValueString("0", "double"),
            GetSQLValueString($vleftakredituar, "double"),
            GetSQLValueString($_POST['burimteardhura'], "text"),
            GetSQLValueString($user_info, "text"),
            GetSQLValueString(substr($v_dt, 6, 4) . "-" . substr($v_dt, 3, 2) . "-" . substr($v_dt, 0, 2), "date")
          );
          $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));

          $insertSQL = sprintf(
            "INSERT INTO exchange_detaje ( id_exchangekoke, id_mondebituar, vleftadebituar, vleftakredituar, kursi, kursi_txt, kursi1, kursi1_txt) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString($id_calc, "text"),
            GetSQLValueString($_POST['id_mondebituar'], "int"),
            GetSQLValueString($vleftadebituar, "double"),
            GetSQLValueString($vleftakredituar, "double"),
            GetSQLValueString($_POST['kursi'], "double"),
            GetSQLValueString($_POST['kursi_txt'], "text"),
            GetSQLValueString($_POST['kursi1'], "double"),
            GetSQLValueString($_POST['kursi1_txt'], "text")
          );
          $Result1 = $MySQL->query($insertSQL) or die(mysqli_error($MySQL));
        }

        $vleftadebituartot = $vleftadebituartot - $vleftadebituar;
      }
    }

    $updateGoTo = "exchange_trans.php";
    header(sprintf("Location: %s", $updateGoTo));
  }

  $sql_id_info = "select opstatus from opencloseday ";
  $id_info     = $MySQL->query($sql_id_info) or die(mysqli_error($MySQL));
  $row_id_info = $id_info->fetch_assoc();
  $opstatus    = $row_id_info['opstatus'];

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
  if (($_SESSION['Usertype'] ?? '') === '2') {
      $v_wheresql = " where id = " . (int)$_SESSION['Userfilial'] . " ";
      $v_wheresqls = " where id <> " . (int)$_SESSION['Userfilial'] . " ";
      $v_wheresqle = " and id_llogfilial = " . (int)$_SESSION['Userfilial'] . " ";
  }
  if (($_SESSION['Usertype'] ?? '') === '3') {
      $v_wheresql = " where id = " . (int)$_SESSION['Userfilial'] . " ";
      $v_wheresqls = " where id <> " . (int)$_SESSION['Userfilial'] . " ";
      $v_wheresqle = " and id_llogfilial = " . (int)$_SESSION['Userfilial'] . " ";
  }

  // Now use the initialized variable
  $sql_info = "select * from kursi_koka where id = (select max(id) from kursi_koka where 1=1 " . $v_wheresqls . ") " . $v_wheresqls;
  $id_kursi = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
  $row_id_kursi = $id_kursi->fetch_assoc();

  $query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                          from kursi_detaje, monedha
                         where master_id = " . $row_id_kursi['id'] . "
                           and kursi_detaje.monedha_id = monedha.id ";
  $monkurs_info = $MySQL->query($query_monkurs_info) or die(mysqli_error($MySQL));
  $row_monkurs_info = $monkurs_info->fetch_assoc();
  //----------------------------------------------------------------------------------

?>

<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">
<div class="page-wrapper">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form enctype="multipart/form-data" ACTION="exchangen.php" METHOD="POST" name="formmenu" id="formmenu" onsubmit="return checkform(this);">
              <input name="form_action" type="hidden" value="ins">
              <input name="rate_value" type="hidden" value="">
              <input name="total_value" type="hidden" value="">
              <div class="form-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                    <lable class="form-label">Grup Trans.:&nbsp;</lable>
                      <input class="form-control"name="id_trans" type="text" id="id_trans" value="<?php echo $_SESSION['Usertrans']; ?>" size="10" readonly>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Dat&euml;:&nbsp;</lable>
                      <div class="row">
                        <div class="col-10">
                          <input class="form-control" name="date_trans" type="text" value="<?php echo strftime('%d.%m.%Y'); ?>" id="date_trans" size="10" readonly>
                        </div>
                        <div class="col-2">
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
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Filiali:&nbsp;</lable>
                      <select name="id_llogfilial" id="id_llogfilial" class="form-select mr-sm-2">
                          <?php
                            while ($row_filiali_info) {
                          ?>
                            <option value="<?php echo $row_filiali_info['id']; ?>" <?php if ($row_filiali_info['id'] == $_SESSION['Userfilial']) {echo "selected";} ?>><?php echo $row_filiali_info['filiali']; ?></option>
                          <?php
                            $row_filiali_info = $filiali_info->fetch_assoc();
                          }
                          mysqli_free_result($filiali_info);
                          ?>
                      </select>
                        &nbsp;&nbsp;
                        <a class="link4" href="JavaScript: Open_Filial_Window();">
                          <img src="images/doc.gif" border="0">
                        </a>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Klienti:&nbsp;</lable>
                      <select name="id_klienti" id="id_klienti" class="form-select mr-sm-2">
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
                      &nbsp;&nbsp;
                      <a class="link4" href="JavaScript: Open_Klient_Window();">
                        <img src="images/doc.gif" border="0">
                      </a>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
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
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Shuma:&nbsp;</lable>
                      <input class="form-control" name="vleftadebituar" type="text" class="inputtext2" id="vleftadebituar" value=".00" size="15" onChange="JavaScript: if (document.formmenu.id_monkreditim.value != '999')  calculate_rate_value (); " onKeyDown="if (event.keyCode == 13) document.formmenu.insupd.focus(); ">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
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
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Kursi: &nbsp;</lable>
                      <input class="form-control" name="kursi_txt" type="text" class="inputtext5" id="kursi_txt" value="LEK/" size="10" readonly>&nbsp;=&nbsp;<input name="kursi" type="text" class="inputtext2" id="kursi" value="" size="10" OnChange="JavaScript: calculate_rate_value3 (); calculate_value ();">
                    </div>
                  </div>
                </div>
                <input name="hkursi" type="hidden" id="hkursi" value="">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Totali: &nbsp;</lable>
                      <input class="form-control" name="vleftakredituar" type="text" class="inputtext2" id="vleftakredituar" value="0.00" size="15" readonly>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group mb-3">
                      <lable class="form-label">Kursi: &nbsp;</lable>
                      <input class="form-control" name="kursi1_txt" type="text" class="inputtext5" id="kursi1_txt" value="/LEK" size="10" readonly>
                      &nbsp;=&nbsp;
                      <input name="kursi1" type="text" class="inputtext2" id="kursi1" value="" size="10" OnChange="JavaScript: calculate_rate_value2 (); calculate_value ();">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <lable class="form-label">Komisioni: &nbsp;</lable>
                      <input class="form-control" name="perqindjekomisioni" type="text" class="inputtext2" id="perqindjekomisioni" value="0.00" size="4" OnChange="JavaScript: llogarit_komisionin ();">
                      &nbsp;%&nbsp;
                      <input class="form-control" name="vleftakomisionit" type="text" class="inputtext2" id="vleftakomisionit" value="0.00" size="10" onChange="JavaScript: llogarit_komisionin_fix ();">
                    </div>
                  </div>                  
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <div class=ctxheading>
                        <lable class="form-label">P&euml;r t'u paguar:&nbsp;</lable>
                        <input class="form-control" name="vleftapaguar" type="text" class="inputtext2" id="vleftapaguar" value="0.00" size="15" onChange="JavaScript: llogarit_mbrapsht ();">
                        &nbsp; &nbsp; &nbsp;
                        <lable class="form-label">Burimi i t&euml; ardhurave:&nbsp;</lable>
                        <input class="form-control" name="burimteardhura" type="text" class="inputtext" id="burimteardhura" value="" size="40">
                      </div>
                    </div>
                  </div>                  
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <input class="btn btn-info" name="insupd" class="inputtext4" type="button" value=" Kryej veprimin " onClick="JavaScript: if (document.formmenu.vleftapaguar.value != 0) { document.formmenu.submit(); }">
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
  <?php include 'footer.php'; ?>
 