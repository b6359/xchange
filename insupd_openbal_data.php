<?php require_once('ConMySQL.php'); ?>
<?php
//initialize the session
session_start();

if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  $date = strftime('%Y-%m-%d %H:%M:%S');

  $id              = "";
  $date_trans      = strftime('%Y-%m-%d');
  $perdoruesi      = "";
  $id_llogfilial   = "";
  $monedha_id      = "";
  $vleftakredituar = ".00";

  if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
    if (isset($_GET['hid'])) {
      $colname_menu_info = $_GET['hid'] ?? addslashes($_GET['hid']);
      //mysql_select_db($database_MySQL, $MySQL);
      $query_menu_info = sprintf("SELECT * FROM openbalance WHERE id = %s", $colname_menu_info);
      $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
      $row_menu_info = $menu_info->fetch_assoc();
      $totalRows_menu_info = $menu_info->num_rows;

      $id              = $row_menu_info['id'];
      $date_trans      = $row_menu_info['date_trans'];
      $perdoruesi      = $row_menu_info['perdoruesi'];
      $id_llogfilial   = $row_menu_info['id_llogfilial'];
      $monedha_id      = $row_menu_info['monedha_id'];
      $vleftakredituar = $row_menu_info['vleftakredituar'];

      mysqli_free_result($menu_info);
    }
  }
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
  if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "upd")) {
    $updateSQL = sprintf(
      "UPDATE openbalance SET date_trans=%s, perdoruesi=%s, id_llogfilial=%s, monedha_id=%s, vleftakredituar=%s, rate_value=%s, datarregjistrimit=%s WHERE id=%s",
      GetSQLValueString($_POST['date_trans'], "date"),
      GetSQLValueString($user_info, "text"),
      GetSQLValueString($_POST['id_llogfilial'], "int"),
      GetSQLValueString($_POST['monedha_id'], "int"),
      GetSQLValueString($_POST['vleftakredituar'], "double"),
      GetSQLValueString($_POST['rate_value'], "double"),
      GetSQLValueString($date, "date"),
      GetSQLValueString($_POST['id'], "int")
    );

    //mysql_select_db($database_MySQL, $MySQL);
    $Result1 = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

    $updateGoTo = "exchange_openbal.php";

    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }


  if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {

    $sql_id_info = "select kodllogari from filiali where id = " . $_POST['id_llogfilial'];
    $id_info = mysqli_query($MySQL, $sql_id_info) or die(mysqli_error($MySQL));
    $row_id_info = $id_info->fetch_assoc();
    $id_llogarie01 = $row_id_info['kodllogari'];

    $insertSQL = sprintf(
      "INSERT INTO openbalance (date_trans, perdoruesi, id_llogfilial, monedha_id, vleftakredituar, rate_value, datarregjistrimit)
                                         VALUES (%s, %s, %s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['date_trans'], "date"),
      GetSQLValueString($user_info, "text"),
      GetSQLValueString($_POST['id_llogfilial'], "int"),
      GetSQLValueString($_POST['monedha_id'], "int"),
      GetSQLValueString($_POST['vleftakredituar'], "double"),
      GetSQLValueString($_POST['rate_value'], "double"),
      GetSQLValueString($date, "date")
    );

    //mysql_select_db($database_MySQL);
    $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));
    $id_calc = mysqli_insert_id($MySQL);

    // shtimi i rreshtave per transaksionet
    if ($_POST['vleftakredituar'] > 0) {

      $insertSQL = sprintf(
        "INSERT INTO tblalltransactions ( id_veprimi, date_trans, tipiveprimit, pershkrimi, id_filiali, id_llogari, id_monedhe, vleradebituar, vlerakredituar, kursi, perdoruesi, datarregjistrimit )
                                                    VALUES ( %s, %s, 'OPN', 'Hapja e balancave', %s, %s, %s, 0, %s, %s, %s, %s )",
        GetSQLValueString($id_calc, "text"),
        GetSQLValueString($_POST['date_trans'], "date"),
        GetSQLValueString($_POST['id_llogfilial'], "int"),
        GetSQLValueString($id_llogarie01, "text"),
        GetSQLValueString($_POST['monedha_id'], "int"),
        GetSQLValueString($_POST['vleftakredituar'], "double"),
        GetSQLValueString($_POST['rate_value'], "double"),
        GetSQLValueString($user_info, "text"),
        GetSQLValueString($date, "date")
      );
      $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));
    }

    $updateGoTo = "exchange_openbal.php";

    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }

  //----------------------------------------------------------------------------------
  $sql_info = "select * from kursi_koka where id = (select max(id) from kursi_koka)";
  $id_kursi = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
  $row_id_kursi = $id_kursi->fetch_assoc();

  $query_monkurs_info = " select kursi_detaje.*, monedha.monedha, monedha.id monid
                          from kursi_detaje, monedha
                         where master_id = " . $row_id_kursi['id'] . "
                           and kursi_detaje.monedha_id = monedha.id ";
  $monkurs_info = mysqli_query($MySQL, $query_monkurs_info) or die(mysqli_error($MySQL));
  $row_monkurs_info = $monkurs_info->fetch_assoc();;
  //----------------------------------------------------------------------------------

?>


  <script language="JavaScript">
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
      $row_monkurs_info = $monkurs_info->fetch_assoc();
    };

    mysqli_free_result($monkurs_info);

    ?>

    function disp_kursitxt(mon_id) {

      document.formmenu.rate_value.value = news[mon_id][2];
    };
  </script>

  <script language="JavaScript">
    document.write(getCalendarStyles());
  </script>
  <form enctype="multipart/form-data" action="insupd_openbal_data.php" method="POST" name="formmenu" class="container mt-4">
    <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>">
    <input name="id" type="hidden" value="<?php echo $id; ?>">
    <input name="rate_value" type="hidden" value="1">

    <script language="JavaScript" id="jscal1xx">
      var cal1xx = new CalendarPopup("datetrans");
      cal1xx.showNavigationDropdowns();
    </script>

    <div class="row mb-3">
      <label for="date_trans" class="col-sm-3 col-form-label">Datë veprimi:</label>
      <div class="col-sm-9">
        <input name="date_trans" type="text" value="<?php echo $date_trans; ?>" 
               id="date_trans" class="form-control" readonly>
      </div>
    </div>

    <div class="row mb-3">
      <label for="id_llogfilial" class="col-sm-3 col-form-label">Filiali:</label>
      <div class="col-sm-9">
        <select name="id_llogfilial" id="id_llogfilial" class="form-select">
          <?php
          $v_wheresql = "";
          if ($_SESSION['Usertype'] == 3)  $v_wheresql = " where id = " . $_SESSION['Userfilial'] . " ";
          $query_filiali_info = "select * from filiali " . $v_wheresql . " order by filiali asc";
          $filiali_info = mysqli_query($MySQL, $query_filiali_info) or die(mysqli_error($MySQL));
          $row_filiali_info = $filiali_info->fetch_assoc();
          while ($row_filiali_info) {
          ?>
            <option value="<?php echo $row_filiali_info['id']; ?>" 
              <?php if (($row_filiali_info['id'] == $_SESSION['Userfilial']) || 
                       ($row_filiali_info['id'] == $id_llogfilial)) echo "selected"; ?>>
              <?php echo $row_filiali_info['filiali']; ?>
            </option>
          <?php
            $row_filiali_info = $filiali_info->fetch_assoc();
          }
          mysqli_free_result($filiali_info);
          ?>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label for="monedha_id" class="col-sm-3 col-form-label">Monedha:</label>
      <div class="col-sm-9">
        <select name="monedha_id" id="monedha_id" class="form-select" 
                onchange="disp_kursitxt(this.value);">
          <?php
          $query_monedha_info = "select * from monedha order by id asc";
          $monedha_info = mysqli_query($MySQL, $query_monedha_info) or die(mysqli_error($MySQL));
          $row_monedha_info = $monedha_info->fetch_assoc();
          while ($row_monedha_info) {
          ?>
            <option value="<?php echo $row_monedha_info['id']; ?>" 
              <?php if ($row_monedha_info['id'] == $monedha_id) echo "selected"; ?>>
              <?php echo $row_monedha_info['monedha']; ?>
            </option>
          <?php
            $row_monedha_info = $monedha_info->fetch_assoc();
          }
          mysqli_free_result($monedha_info);
          ?>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label for="vleftakredituar" class="col-sm-3 col-form-label">Vlera e hyrë:</label>
      <div class="col-sm-9">
        <input name="vleftakredituar" type="text" class="form-control text-end" 
               id="vleftakredituar" value="<?php echo $vleftakredituar; ?>">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-12 text-center">
        <button type="submit" name="insupd" class="btn btn-primary">Ruaj Informacionin</button>
      </div>
    </div>

    <div id="datetrans" style="visibility: hidden; position: absolute; 
         background-color: white; layer-background-color: white"></div>
  </form>



<?php
}
?>
