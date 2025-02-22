<?php 
require_once('ConMySQL.php');
//initialize the session
session_start();
$id = "";
$monedha = "";
$simboli = "";
$mon_vendi = "";
$kursi_min = "";
$kursi_max = "";
$pershkrimi = "";

if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
  if (isset($_GET['hid'])) {
    $colname_menu_info = $_GET['hid'] ?? addslashes($_GET['hid']);
    //mysql_select_db($database_MySQL, $MySQL);
    $query_menu_info = sprintf("SELECT * FROM monedha WHERE id = %s", $colname_menu_info);
    $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
    $row_menu_info = $menu_info->fetch_assoc();
    $totalRows_menu_info = $menu_info->num_rows;

    $id = $row_menu_info['id'];
    $monedha = $row_menu_info['monedha'];
    $simboli = $row_menu_info['simboli'];
    $mon_vendi = $row_menu_info['mon_vendi'];
    $kursi_min = $row_menu_info['kursi_min'];
    $kursi_max = $row_menu_info['kursi_max'];
    $pershkrimi = $row_menu_info['pershkrimi'];

    mysqli_free_result($menu_info);
  }
}

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
    "UPDATE monedha SET monedha=%s, simboli=%s, mon_vendi=%s, kursi_min=%s, kursi_max=%s, pershkrimi=%s WHERE id=%s",
    GetSQLValueString($_POST['monedha'], "text"),
    GetSQLValueString($_POST['simboli'], "text"),
    GetSQLValueString($_POST['mon_vendi'], "text"),
    GetSQLValueString($_POST['kursi_min'], "double"),
    GetSQLValueString($_POST['kursi_max'], "double"),
    GetSQLValueString($_POST['pershkrimi'], "text"),
    GetSQLValueString($_POST['id'], "int")
  );

  //mysql_select_db($database_MySQL, $MySQL);
  $Result1 = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));
  $updateGoTo = "exchange_currency.php";

  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {
  $insertSQL = sprintf(
    "INSERT INTO monedha (monedha, simboli, mon_vendi, kursi_min, kursi_max, pershkrimi) VALUES (%s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['monedha'], "text"),
    GetSQLValueString($_POST['simboli'], "text"),
    GetSQLValueString($_POST['mon_vendi'], "text"),
    GetSQLValueString($_POST['kursi_min'], "double"),
    GetSQLValueString($_POST['kursi_max'], "double"),
    GetSQLValueString($_POST['pershkrimi'], "text")
  );

  //mysql_select_db($database_MySQL, $MySQL);
  $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));

  $updateGoTo = "exchange_currency.php";

  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}



?>

<script language="JavaScript">
  function checkform(form) {
    if (form.monedha.value == "") {
      alert("Ju lutem plotesoni fushen: monedha");
      form.monedha.focus();
      return false;
    }

    if (form.simboli.value == "") {
      alert("Ju lutem plotesoni fushen: simboli");
      form.simboli.focus();
      return false;
    }

    if (form.pershkrimi.value == "") {
      alert("Ju lutem plotesoni fushen: pershkrimi");
      form.pershkrimi.focus();
      return false;
    }

    if (form.kursi_min.value == "") {
      alert("Ju lutem plotesoni fushen: kursi minimum");
      form.kursi_min.focus();
      return false;
    }

    if (form.kursi_max.value == "") {
      alert("Ju lutem plotesoni fushen: kursi maksimal");
      form.kursi_max.focus();
      return false;
    }

    return true;
  }
</script>
<div class="card">
  <div class="card-body">
    <form enctype="multipart/form-data" action="insupd_currency_data.php" method="POST" name="formmenu" onsubmit="return checkform(this);">
      <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>">
      <input name="id" type="hidden" value="<?php echo $id; ?>">

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="monedha" class="col-form-label">Monedha:</label>
          <input name="monedha" type="text" class="form-control" id="monedha" value="<?php echo $monedha; ?>">
        </div>
        <div class="col-md-6">
          <label for="simboli" class="col-form-label">Simboli:</label>
          <input name="simboli" type="text" class="form-control" id="simboli" value="<?php echo $simboli; ?>">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
          <label for="pershkrimi" class="col-form-label">Përshkrimi:</label>
          <input name="pershkrimi" type="text" class="form-control" id="pershkrimi" value="<?php echo $pershkrimi; ?>">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="kursi_min" class="col-form-label">Min Kursi:</label>
          <input name="kursi_min" type="text" class="form-control" id="kursi_min" value="<?php echo $kursi_min; ?>">
        </div>
        <div class="col-md-6">
          <label for="kursi_max" class="col-form-label">Max Kursi:</label>
          <input name="kursi_max" type="text" class="form-control" id="kursi_max" value="<?php echo $kursi_max; ?>">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="mon_vendi" class="col-form-label">Monedha Lokale:</label>
          <select name="mon_vendi" id="mon_vendi" class="form-select">
            <option value="J" <?php if (!(strcmp("J", $mon_vendi))) echo "SELECTED"; ?>>Jo</option>
            <option value="P" <?php if (!(strcmp("P", $mon_vendi))) echo "SELECTED"; ?>>Po</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-12 text-center">
          <button type="submit" name="insupd" class="btn btn-primary">Ruaj Informacionin</button>
        </div>
      </div>

    </form>
  </div>
</div>