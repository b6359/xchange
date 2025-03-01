<?php
include 'header.php';

$id             = "";
$emri           = "";
$atesia         = "";
$mbiemri        = "";
$gender         = "";
$dob            = "";
$emrikompanise  = "";
$emriplote      = "";
$nationality    = "";
$nationalitytxt = "";
$telefon        = "";
$fax            = "";
$email          = "";
$adresa         = "";
$tipdokumenti   = "";
$nrpashaporte   = "";
$nipt           = "";
$docname        = "";

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
    //$Result1 = mysql_query($insertSQL, $MySQL) or die(mysql_error());

    if (mysqli_query($MySQL, $insertSQL)) {
        $last_id = mysqli_insert_id($MySQL);
        $Result1 = mysqli_query($MySQL, "SELECT * FROM klienti WHERE id = $last_id");
    }
    $updateGoTo = "exchange.php?clid=" . $last_id;
    header(sprintf("Location: %s", $updateGoTo));
}

?>
<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Administrimi i klientëve</h4>

                        <form enctype="multipart/form-data" action="insupd_client_data2.php" method="POST"
                            name="formmenu" onsubmit="return checkform(this);">
                            <input name="form_action" type="hidden" value="ins">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Emri:</label>
                                        <div class="col-sm-8">
                                            <input name="emri" type="text" class="form-control" id="emri"
                                                value="<?php echo $emri; ?>" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Atësia:</label>
                                        <div class="col-sm-8">
                                            <input name="atesia" type="text" class="form-control" id="atesia"
                                                value="<?php echo $atesia; ?>" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Mbiemri:</label>
                                        <div class="col-sm-8">
                                            <input name="mbiemri" type="text" class="form-control" id="mbiemri"
                                                value="<?php echo $mbiemri; ?>" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Emri i kompanisë:</label>
                                        <div class="col-sm-8">
                                            <input name="emrikompanise" type="text" class="form-control" id="emrikompanise"
                                                value="<?php echo $emrikompanise; ?>" maxlength="150">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Datëlindja:</label>
                                        <div class="col-sm-8">
                                            <input name="dob" type="text" class="form-control" placeholder="dd.mm.yyyy" id="dob"
                                                value="<?php echo $dob; ?>" maxlength="10">
                                            <script language="JavaScript">
                                                var o_cal = new tcal({
                                                    'formname': 'formmenu',
                                                    'controlname': 'dob'
                                                });
                                                o_cal.a_tpl.yearscroll = true;
                                                o_cal.a_tpl.weekstart = 1;
                                            </script>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Gjinia:</label>
                                        <div class="col-sm-8">
                                            <select name="gender" id="gender" class="form-select">
                                                <option value="M" <?php if (!(strcmp("M", $gender))) echo "SELECTED"; ?>>Mashkull</option>
                                                <option value="F" <?php if (!(strcmp("F", $gender))) echo "SELECTED"; ?>>Femer</option>
                                                <option value="C" <?php if (!(strcmp("C", $gender))) echo "SELECTED"; ?>>Biznese</option>
                                                <option value="B" <?php if (!(strcmp("B", $gender))) echo "SELECTED"; ?>>Banka</option>
                                                <option value="Z" <?php if (!(strcmp("Z", $gender))) echo "SELECTED"; ?>>Z.K.Valutor</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Shtetësia:</label>
                                        <div class="col-sm-8">
                                            <select name="nationality" id="nationality" class="form-select">
                                                <option value="0"
                                                    <?php if (!(strcmp("0", $nationality))) {
                                                        echo "SELECTED";
                                                    } ?>>Shqiptar
                                                </option>
                                                <option value="1"
                                                    <?php if (!(strcmp("1", $nationality))) {
                                                        echo "SELECTED";
                                                    } ?>>I Huaj
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Shtetësia tekst:</label>
                                        <div class="col-sm-8">
                                            <input name="nationalitytxt" type="text" class="form-control" id="nationalitytxt"
                                                value="<?php echo $nationalitytxt; ?>" maxlength="150">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Tip dokumenti:</label>
                                        <div class="col-sm-8">
                                            <select name="tipdokumenti" id="tipdokumenti" class="form-select">
                                                <option value="0"
                                                    <?php if (!(strcmp("0", $tipdokumenti))) {
                                                        echo "SELECTED";
                                                    } ?>>Pasaporte
                                                </option>
                                                <option value="1"
                                                    <?php if (!(strcmp("1", $tipdokumenti))) {
                                                        echo "SELECTED";
                                                    } ?>>
                                                    Leternjoftim</option>
                                                <option value="2"
                                                    <?php if (!(strcmp("2", $tipdokumenti))) {
                                                        echo "SELECTED";
                                                    } ?>>
                                                    Certifikate</option>
                                                <option value="3"
                                                    <?php if (!(strcmp("3", $tipdokumenti))) {
                                                        echo "SELECTED";
                                                    } ?>>Karte
                                                    Kombe tare Identiteti</option>
                                                <option value="4"
                                                    <?php if (!(strcmp("4", $tipdokumenti))) {
                                                        echo "SELECTED";
                                                    } ?>>Patente
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Nr. Dokumenti:</label>
                                        <div class="col-sm-8">
                                            <input name="nrpashaporte" type="text" class="form-control" id="nrpashaporte"
                                                value="<?php echo $nrpashaporte; ?>" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">NIPT:</label>
                                        <div class="col-sm-8">
                                            <input name="nipt" type="text" class="form-control" id="nipt"
                                                value="<?php echo $nipt; ?>" maxlength="10">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Telefon:</label>
                                        <div class="col-sm-8">
                                            <input name="telefon" type="text" class="form-control" id="telefon"
                                                value="<?php echo $telefon; ?>" maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">Fax:</label>
                                        <div class="col-sm-8">
                                            <input name="fax" type="text" class="form-control" id="fax" value="<?php echo $fax; ?>"
                                                maxlength="50">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="col-sm-4 col-form-label">E-mail:</label>
                                        <div class="col-sm-8">
                                            <input name="email" type="text" class="form-control" id="email"
                                                value="<?php echo $email; ?>" maxlength="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label class="col-sm-3 col-form-label">Adresa:</label>
                                    <div class="col-sm-9">
                                        <textarea name="adresa" cols="34" rows="5" class="form-control" id="adresa"><?php echo $adresa; ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12">
                                    <label class="col-sm-3 col-form-label">Dokumenti:</label>
                                    <div class="col-sm-9">
                                        <input name="docname" type="file" class="form-control" id="docname"
                                            value="<?php echo $docname; ?>" size="250">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-12 text-center">
                                    <button type="submit" name="insupd" class="btn btn-primary">Ruaj Informacionin</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    function checkform(form) {
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