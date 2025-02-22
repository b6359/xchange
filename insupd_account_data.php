<?php 
//initialize the session
require_once('ConMySQL.php');
session_start();
date_default_timezone_set('Europe/Tirane');

  $id            = "";
  $filiali       = "";
  $kodllogari    = "";
  $administrator = "";
  $tipi          = "";
  $tstatus       = "T";

  if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
    if (isset($_GET['hid'])) {
      $colname_menu_info = $_GET['hid'] ?? addslashes($_GET['hid']);
      //mysql_select_db($database_MySQL, $MySQL);
      $query_menu_info = sprintf("SELECT * FROM filiali WHERE id = %s", $colname_menu_info);
      $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
      $row_menu_info = $menu_info->fetch_assoc();
      $totalRows_menu_info = $menu_info->num_rows;

      $id            = $row_menu_info['id'];
      $filiali       = $row_menu_info['filiali'];
      $kodllogari    = $row_menu_info['kodllogari'];
      $administrator = $row_menu_info['administrator'];
      $tipi          = $row_menu_info['tipi'];
      $tstatus       = $row_menu_info['tstatus'];

      mysqli_free_result($menu_info);
    }
  }
  /////////////////////////////////////////////////////////////////////////////////////////////////
  //////////////                                                           /////////////////
  /////////////////////////////////////////////////////////////////////////////////////////////////
  function upload_images($img, $path)
  {
    unset($imagename);

    if (!isset($_FILES) && isset($HTTP_POST_FILES))
      $_FILES = $HTTP_POST_FILES;

    if (!isset($_FILES[$img]))
      $error["img_1"] = "An image was not found.";

    $imagename = basename($_FILES[$img]['name']);
    //echo $imagename;

    if (empty($imagename))
      $error["imagename"] = "The name of the image was not found.";

    if (empty($error)) {
      $newimage = $path . $imagename;
      //echo $newimage;
      $result = @move_uploaded_file($_FILES[$img]['tmp_name'], $newimage);
      if (empty($result))
        $error["result"] = "There was an error moving the uploaded file.";
    }
    return $imagename;
  }
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
      "UPDATE filiali SET filiali=%s, kodllogari=%s, administrator=%s, tipi=%s, tstatus=%s WHERE id=%s",
      GetSQLValueString($_POST['filiali'], "text"),
      GetSQLValueString($_POST['kodllogari'], "text"),
      GetSQLValueString($_POST['administrator'], "text"),
      GetSQLValueString($_POST['tipi'], "text"),
      GetSQLValueString($_POST['tstatus'], "text"),
      GetSQLValueString($_POST['id'], "int")
    );

    //mysql_select_db($database_MySQL, $MySQL);
    $Result1 = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

    $updateGoTo = "exchange_account.php";

    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }


  if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {
    $insertSQL = sprintf(
      "INSERT INTO filiali (filiali, kodllogari, administrator, tipi, tstatus) VALUES (%s, %s, %s, %s, %s)",
      GetSQLValueString($_POST['filiali'], "text"),
      GetSQLValueString($_POST['kodllogari'], "text"),
      GetSQLValueString($_POST['administrator'], "text"),
      GetSQLValueString($_POST['tipi'], "text"),
      GetSQLValueString($_POST['tstatus'], "text")
    );

    //mysql_select_db($database_MySQL, $MySQL);
    $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));


    $updateGoTo = "exchange_account.php";

    if (isset($_SERVER['QUERY_STRING'])) {
      $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
      $updateGoTo .= $_SERVER['QUERY_STRING'];
    }
    header(sprintf("Location: %s", $updateGoTo));
  }



?>


  <!-- --------------------------------------- -->
  <!--          Aplikacioni xChange            -->
  <!--                                         -->
  <!--  Kontakt:                               -->
  <!--                                         -->
  <!--           GlobalTech.al                 -->
  <!--                                         -->
  <!--        info@globaltech.al               -->
  <!-- --------------------------------------- -->



    <script language="JavaScript">
      function checkform(form) {
        if (form.filiali.value == "") {
          alert("Ju lutem plotesoni fushen: llogaria");
          form.filiali.focus();
          return false;
        }
        return true;
      }
    </script>

    <div class="container_12">
      <form enctype="multipart/form-data" ACTION="insupd_account_data.php" METHOD="POST" name="formmenu" onsubmit="return checkform(this);">
        <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>">
        <input name="id" type="hidden" value="<?php echo $id; ?>">
        
        <div class="form-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label">Filiali:</label>
                <input class="form-control" name="filiali" type="text" id="filiali" value="<?php echo $filiali; ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label">Administratori:</label>
                <input class="form-control" name="administrator" type="text" id="administrator" value="<?php echo $administrator; ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label">Kod Llogarie:</label>
                <input class="form-control" name="kodllogari" type="text" id="kodllogari" value="<?php echo $kodllogari; ?>">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label">Përshkrimi:</label>
                <input class="form-control" name="tipi" type="text" id="tipi" value="<?php echo $tipi; ?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label">Statusi:</label>
                <select name="tstatus" id="tstatus" class="form-select">
                  <option value="T" <?php if (!(strcmp("T", $tstatus))) { echo "SELECTED"; } ?>>Active</option>
                  <option value="F" <?php if (!(strcmp("F", $tstatus))) { echo "SELECTED"; } ?>>Cancel</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group mb-3">
                <input name="insupd" class="btn btn-info d-block ms-auto" type="submit" value="Ruaj Informacionin">
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>