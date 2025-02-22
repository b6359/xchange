<?php include 'header.php'; ?>
<?php

session_start();
date_default_timezone_set('Europe/Tirane');

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

?>>
<?php

  $user_info = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

  $v_date = strftime('%d.%m.%Y');
  if ((isset($_POST['p_date1'])) && ($_POST['p_date1'] != "")) {
    $v_date = $_POST['p_date1'];
  }
  if ((isset($_GET['dt'])) && ($_GET['dt'] != "")) {
    $v_date = $_GET['dt'];
  }

  if (isset($_GET['action']) && ($_GET['action'] == "del")) {
    $sql_info = "UPDATE exchange_koke SET chstatus ='F' WHERE id = '" . $_GET['tid'] . "'";
    $result = $MySQL->query($sql_info);
  }
?>
<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body d-flex align-items-center justify-content-between" >
                <h4 class="card-title">
                    <b>Lista e transaksioneve</b>
                </h4>
                <div class="d-flex align-items-center">
                    <lable class="d-flex align-items-center">P&euml;rzgjidh dat&euml;n:
                        <input class="form-control" name="p_date1" type="text" id="p_date1" value="<?php echo $v_date; ?>" size="10" maxlength="10">
                        <script language="JavaScript">
                            var o_cal = new tcal({
                            'formname': 'formmenu',
                            'controlname': 'p_date1'
                            });
                            o_cal.a_tpl.yearscroll = true;
                            o_cal.a_tpl.weekstart = 1;
                        </script>
                    </lable>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input name="repdata" class="btn btn-info d-block ms-auto inputtext4" type="submit" value=" Shfaq transaksionet ... ">                            
                </div>
                <!-- <a class="btn btn-outline-primary" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#AddNewUserModal">
                    <i class="fas fa-plus cursor-pointer"></i>
                </a> -->
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <td class="OraColumnHeader"> Nr. fature </td>
                            <td class="OraColumnHeader"> Dat&euml; </td>
                            <td class="OraColumnHeader"> Llogaria </td>
                            <td class="OraColumnHeader"> Shuma e Bler&euml; </td>
                            <td class="OraColumnHeader"> Kursi </td>
                            <td class="OraColumnHeader"> Shuma e Shitur </td>
                            <td class="OraColumnHeader"> P&euml;rdoruesi </td>
                            <td class="OraColumnHeader"> </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            set_time_limit(0);
                            $v_perioddate  = " and ek.date_trans = '" . substr($v_date, 6, 4) . "-" . substr($v_date, 3, 2) . "-" . substr($v_date, 0, 2) . "'";

                            $v_wheresql = "";
                            if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and ek.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
                            if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and ek.perdoruesi    = '" . $_SESSION['Username'] . "' ";

                            //mysql_select_db($database_MySQL, $MySQL);
                            $RepInfo_sql = " select ek.*, ed.*, k.filiali, m1.monedha as mon1, m2.monedha as mon2
                            from exchange_koke as ek,
                            exchange_detaje as ed,
                            filiali as k,
                            monedha as m1,
                            monedha as m2
                            where ek.id             = ed.id_exchangekoke
                            and ek.unique_id      > (select max(id_chn) from systembalance)
                            and ek.chstatus       = 'T'
                            and ek.tipiveprimit   = 'TRN'
                            " . $v_perioddate . "
                            " . $v_wheresql   . "
                            and ek.id_klienti     = k.id
                            and ek.id_monkreditim = m1.id
                            and ed.id_mondebituar = m2.id
                            and ek.chstatus       = 'T'
                            order by ek.unique_id desc
                            ";

                            $MySQL->query($RepInfo_sql) or die(mysqli_error($MySQL));
                            $RepInfoRS   = $MySQL->query($RepInfo_sql) or die(mysqli_error($MySQL));
                            $row_RepInfo = $RepInfoRS->fetch_assoc();

                            while ($row_RepInfo) {
                            $rowno++;

                            $v_kursi = 0;
                            if ($row_RepInfo['kursi'] > $row_RepInfo['kursi1']) {
                                $v_kursi = $row_RepInfo['kursi'];
                            } else {
                                $v_kursi = $row_RepInfo['kursi1'];
                            }
                        ?>
                        <tr>
                            <td class="OraCellGroup2"><?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?></td>
                            <td class="OraCellGroup2"><?php echo substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8); ?></td>
                            <td class="OraCellGroup2"><?php echo $row_RepInfo['filiali']; ?></td>
                            <td class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftadebituar'], 2, '.', ','); ?>&nbsp;<?php echo $row_RepInfo['mon2']; ?>&nbsp;</td>
                            <td class="OraCellGroup2"><?php echo number_format($v_kursi, 2, '.', ','); ?>&nbsp;&nbsp;</td>
                            <td class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftapaguar'], 2, '.', ','); ?>&nbsp;<?php echo $row_RepInfo['mon1']; ?>&nbsp;</td>
                            <td class="OraCellGroup2"><?php echo $row_RepInfo['perdoruesi']; ?></td>
                            <td width="20">
                                <?php if ($_SESSION['Usertype'] != 3) {  ?>                                   
                                    <a title="Fshij Informacionin" class="btn btn-outline-danger" href="JavaScript: do_delete('<?php echo $row_RepInfo['id']; ?>'); ">
                                        <i class="fas fa-times cursor-pointer"></i>
                                    </a>
                                <?php  }  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="OraCellGroup2"> &nbsp;P&euml;rshkrimi: &nbsp;<?php echo $row_RepInfo['pershkrimi']; ?></td>
                        </tr>
                        <?php $row_RepInfo = $RepInfoRS->fetch_assoc();
                            };
                            mysqli_free_result($RepInfoRS);
                        ?>                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>