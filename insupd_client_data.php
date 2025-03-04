<?php
require_once('ConMySQL.php');
//initialize the session
session_start();



$id            = "";
$emri          = "";
$atesia        = "";
$mbiemri       = "";
$gender        = "";
$dob           = "";
$emrikompanise = "";
$emriplote     = "";
$nationality   = "";
$nationalitytxt = "";
$telefon       = "";
$fax           = "";
$email         = "";
$adresa        = "";
$tipdokumenti  = "";
$nrpashaporte  = "";
$nipt          = "";
$docname       = "";

if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
  if (isset($_GET['hid'])) {
    $colname_menu_info = $_GET['hid'] ?? addslashes($_GET['hid']);
    //mysql_select_db($database_MySQL, $MySQL);
    $query_menu_info = sprintf("SELECT * FROM klienti WHERE id = %s", $colname_menu_info);
    $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
    $row_menu_info = $menu_info->fetch_assoc();
    $totalRows_menu_info = $menu_info->num_rows;

    $id             = $row_menu_info['id'];
    $emri           = $row_menu_info['emri'];
    $atesia         = $row_menu_info['atesia'];
    $mbiemri        = $row_menu_info['mbiemri'];
    $emrikompanise  = $row_menu_info['emrikompanise'];
    if ($row_menu_info['dob'] != "") {
      $dob            = substr($row_menu_info['dob'], 8, 2) . "." . substr($row_menu_info['dob'], 5, 2) . "." . substr($row_menu_info['dob'], 0, 4);
    }
    $gender         = $row_menu_info['gender'];
    $nationality    = $row_menu_info['nationality'];
    $nationalitytxt = $row_menu_info['nationalitytxt'];
    $emriplote      = $row_menu_info['emriplote'];
    $telefon        = $row_menu_info['telefon'];
    $fax            = $row_menu_info['fax'];
    $email          = $row_menu_info['email'];
    $adresa         = $row_menu_info['adresa'];
    $tipdokumenti   = $row_menu_info['tipdokumenti'];
    $nrpashaporte   = $row_menu_info['nrpashaporte'];
    $nipt           = $row_menu_info['nipt'];
    $docname        = $row_menu_info['docname'];

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
    "UPDATE klienti SET emri=%s, atesia=%s, mbiemri=%s, emriplote=%s, emrikompanise=%s, dob=%s, gender=%s, nationality=%s, nationalitytxt=%s, telefon=%s, fax=%s, email=%s, adresa=%s, tipdokumenti=%s, nrpashaporte=%s, nipt=%s, docname=%s WHERE id=%s",
    GetSQLValueString($_POST['emri'], "text"),
    GetSQLValueString($_POST['atesia'], "text"),
    GetSQLValueString($_POST['mbiemri'], "text"),
    GetSQLValueString($_POST['emri'] . " " . $_POST['mbiemri'], "text"),
    GetSQLValueString($_POST['emrikompanise'], "text"),
    GetSQLValueString(substr($_POST['dob'], 6, 4) . "-" . substr($_POST['dob'], 3, 2) . "-" . substr($_POST['dob'], 0, 2), "text"),
    GetSQLValueString($_POST['gender'], "text"),
    GetSQLValueString($_POST['nationality'], "text"),
    GetSQLValueString($_POST['nationalitytxt'], "text"),
    GetSQLValueString($_POST['telefon'], "text"),
    GetSQLValueString($_POST['fax'], "text"),
    GetSQLValueString($_POST['email'], "text"),
    GetSQLValueString($_POST['adresa'], "text"),
    GetSQLValueString($_POST['tipdokumenti'], "int"),
    GetSQLValueString($_POST['nrpashaporte'], "text"),
    GetSQLValueString($_POST['nipt'], "text"),
    GetSQLValueString(upload_images("docname", "doc/"), "text"),
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
    "INSERT INTO klienti (emri, atesia, mbiemri, emriplote, emrikompanise, dob, gender, nationality, nationalitytxt, telefon, fax, email, adresa, tipdokumenti, nrpashaporte, nipt, docname)
                                     VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
    GetSQLValueString($_POST['emri'], "text"),
    GetSQLValueString($_POST['atesia'], "text"),
    GetSQLValueString($_POST['mbiemri'], "text"),
    GetSQLValueString($_POST['emri'] . " " . $_POST['mbiemri'], "text"),
    GetSQLValueString($_POST['emrikompanise'], "text"),
    GetSQLValueString(substr($_POST['dob'], 6, 4) . "-" . substr($_POST['dob'], 3, 2) . "-" . substr($_POST['dob'], 0, 2), "text"),
    GetSQLValueString($_POST['gender'], "text"),
    GetSQLValueString($_POST['nationality'], "text"),
    GetSQLValueString($_POST['nationalitytxt'], "text"),
    GetSQLValueString($_POST['telefon'], "text"),
    GetSQLValueString($_POST['fax'], "text"),
    GetSQLValueString($_POST['email'], "text"),
    GetSQLValueString($_POST['adresa'], "text"),
    GetSQLValueString($_POST['tipdokumenti'], "int"),
    GetSQLValueString($_POST['nrpashaporte'], "text"),
    GetSQLValueString($_POST['nipt'], "text"),
    GetSQLValueString(upload_images("docname", "doc/"), "text")
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


<script language="JavaScript">
  function checkform(form) {
    debugger
    if (form.emri.value == "") {
      alert("Ju lutem plotesoni fushen: emri");
      form.emri.focus();
      return false;
    }

    if (form.mbiemri.value == "") {
      alert("Ju lutem plotesoni fushen: mbiemri");
      form.mbiemri.focus();
      return false;
    }

    return true;
  }
</script>

<div class="container_12">
  <div class="card">
    <div class="card-body">
      <form enctype="multipart/form-data" ACTION="insupd_client_data.php" METHOD="POST" name="formmenu" onsubmit="return checkform(this);">
        <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>">
        <input name="id" type="hidden" value="<?php echo $id; ?>">

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="emri" class="col-form-label">Emri:</label>
            <input name="emri" type="text" class="form-control" id="emri" value="<?php echo $emri; ?>" maxlength="50">
          </div>
          <div class="col-md-6">
            <label for="atesia" class="col-form-label">Atësia:</label>
            <input name="atesia" type="text" class="form-control" id="atesia" value="<?php echo $atesia; ?>" maxlength="50">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="mbiemri" class="col-form-label">Mbiemri:</label>
            <input name="mbiemri" type="text" class="form-control" id="mbiemri" value="<?php echo $mbiemri; ?>" maxlength="50">
          </div>
          <div class="col-md-6">
            <label for="emrikompanise" class="col-form-label">Emri i kompanisë:</label>
            <input name="emrikompanise" type="text" class="form-control" id="emrikompanise" value="<?php echo $emrikompanise; ?>" maxlength="150">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="dob" class="col-form-label">Datëlindja:</label>
            <input name="dob" type="text" class="form-control" placeholder="dd.mm.yyyy" id="dob" value="<?php echo $dob; ?>" maxlength="10">
            <!-- <script language="JavaScript">
                        var o_cal = new tcal({
                          'formname': 'formmenu',
                          'controlname': 'dob'
                        });
                        o_cal.a_tpl.yearscroll = true;
                        o_cal.a_tpl.weekstart = 1;
                      </script> -->
          </div>
          <div class="col-md-6">
            <label for="gender" class="col-form-label">Gjinia:</label>
            <select name="gender" id="gender" class="form-select">
              <option value="M" <?php if (!(strcmp("M", $gender))) echo "SELECTED"; ?>>Mashkull</option>
              <option value="F" <?php if (!(strcmp("F", $gender))) echo "SELECTED"; ?>>Femer</option>
              <option value="C" <?php if (!(strcmp("C", $gender))) echo "SELECTED"; ?>>Biznese</option>
              <option value="B" <?php if (!(strcmp("B", $gender))) echo "SELECTED"; ?>>Banka</option>
              <option value="Z" <?php if (!(strcmp("Z", $gender))) echo "SELECTED"; ?>>Z.K.Valutor</option>
            </select>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nationality" class="col-form-label">Shtetësia:</label>
            <select name="nationality" id="nationality" class="form-select">
              <option value="0" <?php if (!(strcmp("0", $nationality))) echo "SELECTED"; ?>>Shqiptar</option>
              <option value="1" <?php if (!(strcmp("1", $nationality))) echo "SELECTED"; ?>>I Huaj</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="nationalitytxt" class="col-form-label">Shtetësia tekst:</label>
            <input name="nationalitytxt" type="text" class="form-control" id="nationalitytxt" value="<?php echo $nationalitytxt; ?>" maxlength="150">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="tipdokumenti" class="col-form-label">Tip dokumenti:</label>
            <select name="tipdokumenti" id="tipdokumenti" class="form-select">
              <option value="0" <?php if (!(strcmp("0", $tipdokumenti))) echo "SELECTED"; ?>>Pasaporte</option>
              <option value="1" <?php if (!(strcmp("1", $tipdokumenti))) echo "SELECTED"; ?>>Leternjoftim</option>
              <option value="2" <?php if (!(strcmp("2", $tipdokumenti))) echo "SELECTED"; ?>>Certifikate</option>
              <option value="3" <?php if (!(strcmp("3", $tipdokumenti))) echo "SELECTED"; ?>>Karte Kombe tare Identiteti</option>
              <option value="4" <?php if (!(strcmp("4", $tipdokumenti))) echo "SELECTED"; ?>>Patente</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="nrpashaporte" class="col-form-label">Nr. Dokumenti:</label>
            <input name="nrpashaporte" type="text" class="form-control" id="nrpashaporte" value="<?php echo $nrpashaporte; ?>" maxlength="50">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="nipt" class="col-form-label">NIPT:</label>
            <input name="nipt" type="text" class="form-control" id="nipt" value="<?php echo $nipt; ?>" maxlength="10">
          </div>
          <div class="col-md-6">
            <label for="telefon" class="col-form-label">Telefon:</label>
            <input name="telefon" type="text" class="form-control" id="telefon" value="<?php echo $telefon; ?>" maxlength="50">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="fax" class="col-form-label">Fax:</label>
            <input name="fax" type="text" class="form-control" id="fax" value="<?php echo $fax; ?>" maxlength="50">
          </div>
          <div class="col-md-6">
            <label for="email" class="col-form-label">E-mail:</label>
            <input name="email" type="text" class="form-control" id="email" value="<?php echo $email; ?>" maxlength="100">
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-12">
            <label for="adresa" class="col-sm-2 col-form-label">Adresa:</label>
            <textarea name="adresa" class="form-control" id="adresa" cols="34" rows="5"><?php echo $adresa; ?></textarea>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-12">
            <label for="docname" class="col-sm-2 col-form-label">Dokumenti:</label>
            <div class="col-sm-12">
              <input name="docname" type="file" class="form-control" id="docname" value="<?php echo $docname; ?>" size="250">
            </div>
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
</div>
</div>