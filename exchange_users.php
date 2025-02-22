<?php include 'header.php'; ?>
<?php
//initialize the session
session_start();

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF'] . "?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")) {
  $logoutAction .= "&" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) && ($_GET['doLogout'] == "true")) {
  // logout
  $GLOBALS['uid']         = "";
  $GLOBALS['Username']    = "";
  $GLOBALS['full_name']   = "";
  $GLOBALS['Usertrans']   = "";
  $GLOBALS['Userfilial']  = "";
  $GLOBALS['Usertype']    = "";
  $_SESSION['uid']        = "";
  $_SESSION['Username']   = "";
  $_SESSION['full_name']  = "";
  $_SESSION['Usertrans']  = "";
  $_SESSION['Userfilial'] = "";
  $_SESSION['Usertype']   = "";

  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php require_once('ConMySQL.php'); ?>
<?php

  $user_id = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

  if ("1" != $_SESSION['Usertype']) {

    $logoutGoTo = "info.php";
    header("Location: $logoutGoTo");
    exit;
  }

?>

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between" >
                <h4 class="card-title">
                    <b>Lista e përdoruesve</b>
                </h4>
                <div>
                    <a class="btn btn-outline-primary" href="javascript:void(0)" onclick="printForm()">
                        <i class="fas fa-print cursor-pointer"></i> Printo
                    </a>
                    <a class="btn btn-outline-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#AddNewUserModal">
                        <i class="fas fa-plus cursor-pointer"></i>Shto përdorues të ri
                    </a>
                </div>
            </div>
            <div class="table-responsive" id="printable-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <td scope="col">P&euml;rdoruesi</td>
                            <td scope="col">Emri i plot&euml;</td>
                            <td scope="col"></td>
                            <td scope="col">Llogaria</td>
                            <td scope="col">Tipi</td>
                            <td scope="col"></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $data_sql_info = "select app_user.*, filiali.filiali, usertype.description from app_user, filiali, usertype where app_user.id_filiali = filiali.id and app_user.id_usertype = usertype.id";
                            $h_data = $MySQL->query($data_sql_info) or die(mysqli_error($MySQL));
                            $row_h_data = $h_data->fetch_assoc();

                            while ($row_h_data) { ?>
                                <tr>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['username']; ?>
                                        </b>
                                    </td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['full_name']; ?>
                                        </b>
                                    </td>
                                    <td></td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['filiali']; ?>
                                        </b>
                                    </td>
                                    <td>
                                        <b>
                                            <?php echo $row_h_data['description']; ?>
                                        </b>
                                    </td>
                                    <td align="center">
                                        <div class="cursor-pointer" onClick="JavaScript: top.location='insupd_user_data.php?action=upd&hid=<?php echo $row_h_data['id']; ?>';">
                                            <i class="fa fa-pen-square"></i>
                                        </div>
                                    </td>
                                </tr>
                        <?php $row_h_data = $h_data->fetch_assoc();
                        };
                        mysqli_free_result($h_data);
                        ?>                    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="AddNewUserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="AddNewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="AddNewUserModalLabel">Filiali</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <?php
                        $id          = "";
                        $username    = "";
                        $password    = "";
                        $full_name   = "";
                        $id_trans    = "111";
                        $id_filiali  = "1";
                        $id_usertype = "2";
                        $phone       = "";
                        $e_mail      = "";
                        $status      = "T";

                        if (isset($_GET['action']) && ($_GET['action'] == "upd")) {
                            if (isset($_GET['hid'])) {
                            $user_info =  $_SESSION['Username'] ?? addslashes($_SESSION['Username']);
                            $colname_menu_info = $user_info ? addslashes($_GET['hid']) : $_GET['hid'];
                            mysqli_select_db($MySQL, $database_MySQL);
                            $query_menu_info = sprintf("SELECT * FROM app_user WHERE id = %s", $colname_menu_info);
                            $menu_info = mysqli_query($MySQL, $query_menu_info) or die(mysqli_error($MySQL));
                            $row_menu_info = $menu_info->fetch_assoc();
                            $totalRows_menu_info = $menu_info->num_rows;

                            $id          = $row_menu_info['id'];
                            $username    = $row_menu_info['username'];
                            $password    = $row_menu_info['password'];
                            $full_name   = $row_menu_info['full_name'];
                            $id_trans    = $row_menu_info['id_trans'];
                            $id_filiali  = $row_menu_info['id_filiali'];
                            $id_usertype = $row_menu_info['id_usertype'];
                            $phone       = $row_menu_info['phone'];
                            $e_mail      = $row_menu_info['e_mail'];
                            $status      = $row_menu_info['status'];

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
                        if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "upd")) {
                            $updateSQL = sprintf(
                            "UPDATE app_user SET password=%s, full_name=%s, id_trans=%s, id_filiali=%s, id_usertype=%s, phone=%s, e_mail=%s, status=%s WHERE id=%s",
                            GetSQLValueString($_POST['password'], "text"),
                            GetSQLValueString($_POST['full_name'], "text"),
                            GetSQLValueString($_POST['id_trans'], "int"),
                            GetSQLValueString($_POST['id_filiali'], "int"),
                            GetSQLValueString($_POST['id_usertype'], "int"),
                            GetSQLValueString($_POST['phone'], "text"),
                            GetSQLValueString($_POST['e_mail'], "text"),
                            GetSQLValueString($_POST['status'], "text"),
                            GetSQLValueString($_POST['id'], "int")
                            );

                            mysqli_select_db($MySQL, $database_MySQL);
                            $Result1 = mysqli_query($MySQL, $updateSQL) or die(mysqli_error($MySQL));

                            $updateGoTo = "exchange_users.php";
                            header(sprintf("Location: %s", $updateGoTo));
                        }

                        if ((isset($_POST["form_action"])) && ($_POST["form_action"] == "ins")) {
                            $insertSQL = sprintf(
                            "INSERT INTO app_user (username, password, full_name, id_trans, id_filiali, id_usertype, phone, e_mail, status) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                            GetSQLValueString($_POST['username'], "text"),
                            GetSQLValueString($_POST['password'], "text"),
                            GetSQLValueString($_POST['full_name'], "text"),
                            GetSQLValueString($_POST['id_trans'], "int"),
                            GetSQLValueString($_POST['id_filiali'], "int"),
                            GetSQLValueString($_POST['id_usertype'], "int"),
                            GetSQLValueString($_POST['phone'], "text"),
                            GetSQLValueString($_POST['e_mail'], "text"),
                            GetSQLValueString($_POST['status'], "text")
                            );

                            mysqli_select_db($MySQL, $database_MySQL);
                            $Result1 = mysqli_query($MySQL, $insertSQL) or die(mysqli_error($MySQL));

                            $updateGoTo = "exchange_users.php";
                            header(sprintf("Location: %s", $updateGoTo));
                        }
                    ?>
                    <script language="JavaScript">
                        function checkform(form) {
                            if (form.username.value == "") {
                                alert("Ju lutem plotesoni fushen: Perdoruesi");
                                form.username.focus();
                                return false;
                            }

                            if (form.password.value == "") {
                                alert("Ju lutem plotesoni fushen: Fjalekalimi");
                                form.password.focus();
                                return false;
                            }

                            if (form.full_name.value == "") {
                                alert("Ju lutem plotesoni fushen: emri i plote");
                                form.full_name.focus();
                                return false;
                            }

                            return true;
                        }
                    </script>
                    <form enctype="multipart/form-data" ACTION="exchange_users.php" METHOD="POST" name="formmenu" onsubmit="return checkform(this);" >
                        <input name="form_action" type="hidden" value="<?php echo $_GET['action']; ?>" >
                        <input name="id" type="hidden" value="<?php echo $id; ?>">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">P&euml;rdoruesi:</lable>
                                        <input class="form-control" name="username" type="text" id="username" value="<?php echo $username; ?>" size="35">
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Fjal&euml;kalimi:</lable>
                                        <input class="form-control" name="password" type="password" id="password" value="<?php echo $password; ?>" size="35">
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Emri i plot&euml;:</lable>
                                        <input class="form-control" name="full_name" type="text" id="full_name" value="<?php echo $full_name; ?>" size="35">
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Grup Trans.:</lable>
                                        <input class="form-control" name="id_trans" type="text" id="id_trans" value="<?php echo $id_trans; ?>" size="35">
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Filiali:</lable>
                                        <select name="id_filiali" class="form-select mr-sm-2">
                                            <?php
                                            $sql_info = "select * from filiali where tstatus='T' order by filiali desc";
                                            $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
                                            $row_h_menu = $h_menu->fetch_assoc();

                                            while ($row_h_menu) { ?>
                                                <option value="<?php echo $row_h_menu['id']; ?>" <?php if ($row_h_menu['id'] == $id_filiali) echo "selected='selected'"; ?>><?php echo $row_h_menu['filiali']; ?></option>
                                            <?php $row_h_menu = $h_menu->fetch_assoc();
                                            };
                                            mysqli_free_result($h_menu);
                                            ?>
                                        </select>
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Tipi:</lable>
                                        <select name="id_usertype" class="form-select mr-sm-2">
                                            <?php
                                            $sql_info = "select * from usertype  where tstatus='T' order by id";
                                            $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
                                            $row_h_menu = $h_menu->fetch_assoc();

                                            while ($row_h_menu) { ?>
                                                <option value="<?php echo $row_h_menu['id']; ?>" <?php if ($row_h_menu['id'] == $id_usertype) echo "selected='selected'"; ?>><?php echo $row_h_menu['description']; ?></option>
                                            <?php $row_h_menu = $h_menu->fetch_assoc();
                                            };
                                            mysqli_free_result($h_menu);
                                            ?>
                                        </select>
                                    </div>
                                </div>              
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Telefon:</lable>
                                        <input class="form-control" name="phone" type="text" id="phone" value="<?php echo $phone; ?>" size="35">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">E-mail:</lable>
                                        <input class="form-control" name="e_mail" type="text" id="e_mail" value="<?php echo $e_mail; ?>" size="35">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <lable class="form-label">Status:</lable>
                                        <select name="status" id="status" class="form-select mr-sm-2">
                                            <option value="T" <?php if (!(strcmp("T", $status))) {
                                                                echo "SELECTED";
                                                                } ?>>Active</option>
                                            <option value="F" <?php if (!(strcmp("F", $status))) {
                                                                echo "SELECTED";
                                                                } ?>>Cancel</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-info" name="insupd" type="submit">
                                Ruaj Informacionin
                                <!-- <input name="insupd" class="inputtext4" type="submit" value=" Ruaj Informacionin "> -->
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php include 'footer.php'; ?>
<script>
    function printForm() {
      var printContents = document.getElementById('printable-table').innerHTML;
      var originalContents = document.body.innerHTML;

      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  </script>