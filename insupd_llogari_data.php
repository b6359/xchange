<?php require_once('ConMySQL.php'); ?>
<?php
//initialize the session
session_start();


$id            = "";
$llogaria      = "";
$kodi          = "";
$tipi          = "";
$veprimi       = "";
$CHNVL         = "F";
$CHNCO         = "F";
$TRFVL         = "F";
$tstatus       = "T";

if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
  if (isset($_GET['hid'])) {
    $colname_menu_info = $_GET['hid'] ?? addslashes($_GET['hid']);
    //mysql_select_db($database_MySQL, $MySQL);
    $query_menu_info = sprintf("SELECT * FROM llogarite WHERE id = %s", $colname_menu_info);
    $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
    $row_menu_info = $menu_info->fetch_assoc();
    $totalRows_menu_info = $menu_info->num_rows;

    $id            = $row_menu_info['id'];
    $llogaria      = $row_menu_info['llogaria'];
    $kodi          = $row_menu_info['kodi'];
    $tipi          = $row_menu_info['tipi'];
    $veprimi       = $row_menu_info['veprimi'];
    $CHNVL         = $row_menu_info['CHNVL'];
    $CHNCO         = $row_menu_info['CHNCO'];
    $TRFVL         = $row_menu_info['TRFVL'];
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
    "UPDATE llogarite SET llogaria=%s, kodi=%s, tipi=%s, veprimi=%s, CHNVL=%s, CHNCO=%s, TRFVL=%s, tstatus=%s WHERE id=%s",
    GetSQLValueString($_POST['llogaria'], "text"),
    GetSQLValueString($_POST['kodi'], "text"),
    GetSQLValueString($_POST['tipi'], "text"),
    GetSQLValueString($_POST['veprimi'], "text"),
    GetSQLValueString($_POST['CHNVL'], "text"),
    GetSQLValueString($_POST['CHNCO'], "text"),
    GetSQLValueString($_POST['TRFVL'], "text"),
    GetSQLValueString($_POST['tstatus'], "text"),
    GetSQLValueString($_POST['id'], "int")
  );

  //mysql_select_db($database_MySQL, $MySQL);
  $Result1 = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

  $updateGoTo = "exchange_llogari.php";

  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {
  $insertSQL = sprintf(
    "INSERT INTO llogarite (llogaria, kodi, tipi, veprimi, CHNVL, CHNCO, TRFVL, tstatus) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['llogaria'], "text"),
    GetSQLValueString($_POST['kodi'], "text"),
    GetSQLValueString($_POST['tipi'], "text"),
    GetSQLValueString($_POST['veprimi'], "text"),
    GetSQLValueString($_POST['CHNVL'], "text"),
    GetSQLValueString($_POST['CHNCO'], "text"),
    GetSQLValueString($_POST['TRFVL'], "text"),
    GetSQLValueString($_POST['tstatus'], "text")
  );

  //mysql_select_db($database_MySQL, $MySQL);
  $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));

  $updateGoTo = "exchange_llogari.php";

  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



?>
<form enctype="multipart/form-data" ACTION="insupd_llogari_data.php" METHOD="POST" name="formmenu" onsubmit="return checkform(this);">
  <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>">
  <input name="id" type="hidden" value="<?php echo $id; ?>">
  
  <div class="container-fluid">
    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Kod Llogarie:</label>
      <div class="col-sm-9">
        <input name="kodi" type="text" class="form-control" id="kodi" value="<?php echo $kodi; ?>">
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Llogaria:</label>
      <div class="col-sm-9">
        <input name="llogaria" type="text" class="form-control" id="llogaria" value="<?php echo $llogaria; ?>">
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Tipi i llogarisë:</label>
      <div class="col-sm-9">
        <select name="tipi" id="tipi" class="form-select">
          <option value="Aktive" <?php if (!(strcmp("Aktive", $tipi))) { echo "SELECTED"; } ?>>Aktive</option>
          <option value="Pasive" <?php if (!(strcmp("Pasive", $tipi))) { echo "SELECTED"; } ?>>Pasive</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Veprimi i lejuar:</label>
      <div class="col-sm-9">
        <select name="veprimi" id="veprimi" class="form-select">
          <option value="D/C" <?php if (!(strcmp("D/C", $veprimi))) { echo "SELECTED"; } ?>>Debitim / Kreditim</option>
          <option value="D" <?php if (!(strcmp("D", $veprimi))) { echo "SELECTED"; } ?>>Debitim</option>
          <option value="C" <?php if (!(strcmp("C", $veprimi))) { echo "SELECTED"; } ?>>Kreditim</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Llog. për Këmbim Valutor:</label>
      <div class="col-sm-9">
        <select name="CHNVL" id="CHNVL" class="form-select">
          <option value="T" <?php if (!(strcmp("T", $CHNVL))) { echo "SELECTED"; } ?>>Po</option>
          <option value="F" <?php if (!(strcmp("F", $CHNVL))) { echo "SELECTED"; } ?>>Jo</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Llog. për Komisionin e KV:</label>
      <div class="col-sm-9">
        <select name="CHNCO" id="CHNCO" class="form-select">
          <option value="T" <?php if (!(strcmp("T", $CHNCO))) { echo "SELECTED"; } ?>>Po</option>
          <option value="F" <?php if (!(strcmp("F", $CHNCO))) { echo "SELECTED"; } ?>>Jo</option>
        </select>
      </div>
    </div>

    <div class="row mb-3">
      <label class="col-sm-3 col-form-label">Statusi:</label>
      <div class="col-sm-9">
        <select name="tstatus" id="tstatus" class="form-select">
          <option value="T" <?php if (!(strcmp("T", $tstatus))) { echo "SELECTED"; } ?>>Active</option>
          <option value="F" <?php if (!(strcmp("F", $tstatus))) { echo "SELECTED"; } ?>>Cancel</option>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col-12 text-center">
        <button type="submit" name="insupd" class="btn btn-primary">Ruaj Informacionin</button>
      </div>
    </div>
  </div>
</form>
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