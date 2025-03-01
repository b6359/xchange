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

?>
<?php require_once('ConMySQL.php'); ?>
<?php

  $user_info = $_SESSION['uid'] ?? addslashes($_SESSION['uid']);

  $v_date = strftime('%d.%m.%Y');
  if ((isset($_POST['p_date1'])) && ($_POST['p_date1'] != "")) {
    $v_date = $_POST['p_date1'];
  }
  if ((isset($_GET['dt'])) && ($_GET['dt'] != "")) {
    $v_date = $_GET['dt'];
  }
  $v_tot = "N";
  if ((isset($_POST['total'])) && ($_POST['total'] != "")) {
    $v_tot = $_POST['total'];
  }

  if (isset($_GET['action']) && ($_GET['action'] == "del")) {
    $sql_info = "UPDATE hyrjedalje SET chstatus ='F' WHERE id = '" . $_GET['tid'] . "'";
    $result = $MySQL->query($sql_info) or die(mysqli_error($MySQL));
  }
?>

<script language="JavaScript" src="calendar_eu.js"></script>
<link rel="stylesheet" href="calendar.css">

<div class="page-wrapper">
    <div class="container-fluid">
        <form action="exchange_transhd.php" method="POST" name="formmenu" target="_self">
            <input name="total" type="hidden" value="N">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between" >
                    <h4 class="card-title">
                        <b>Lista e veprimeve monetare</b>
                    </h4>
                    <div class="d-flex align-items-center">                    
                        <label class="d-flex align-items-center">P&euml;rzgjidh dat&euml;n:
                            <input class="form-control" name="p_date1" type="text" id="p_date1" value="<?php echo $v_date; ?>" size="10" maxlength="10">
                            <script language="JavaScript">
                                var o_cal = new tcal({
                                'formname': 'formmenu',
                                'controlname': 'p_date1'
                                });
                                o_cal.a_tpl.yearscroll = true;
                                o_cal.a_tpl.weekstart = 1;
                            </script>
                        </label>
                        <input name="repdata" class="mx-2 btn btn-info d-block inputtext4" type="submit" value="Shfaq veprimet">
                        <input name="repdataa" class="btn btn-info d-block inputtext4" type="button" value=" Shfaq t&euml; gjith&euml; veprimet " onClick="JavaScript: document.formmenu.total.value='Y'; document.formmenu.submit(); ">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td class="OraColumnHeader"> </td>
                                <td class="OraColumnHeader"> Nr. fature </td>
                                <td class="OraColumnHeader"> Dat&euml; </td>
                                <td class="OraColumnHeader"> Klienti </td>
                                <td class="OraColumnHeader"> Monedha </td>
                                <td class="OraColumnHeader"> Debitim/Kreditim </td>
                                <td class="OraColumnHeader"> Shuma </td>
                                <td class="OraColumnHeader"> P&euml;rdoruesi </td>
                                <td class="OraColumnHeader"> </td>
                                <td class="OraColumnHeader"> </td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                set_time_limit(0);

                                $v_perioddate  = "";
                                if ($v_tot == "N") {
                                    $v_perioddate  = " and h.date_trans = '" . substr($v_date, 6, 4) . "-" . substr($v_date, 3, 2) . "-" . substr($v_date, 0, 2) . "'";
                                } else {
                                    $v_perioddate  = "";
                                }

                                $v_wheresql = "";
                                if ($_SESSION['Usertype'] == 2)  $v_wheresql = " and h.id_llogfilial = " . $_SESSION['Userfilial'] . " ";
                                if ($_SESSION['Usertype'] == 3)  $v_wheresql = " and h.perdoruesi    = '" . $_SESSION['Username'] . "' ";

                                //mysql_select_db($database_MySQL, $MySQL);
                                $RepInfo_sql = " select h.*, k.emri, k.mbiemri, m.monedha as mon
                                    from hyrjedalje as h,
                                        klienti as k,
                                        monedha as m
                                    where h.id_klienti = k.id
                                    and h.id_monedhe = m.id
                                    and h.id > (select max(id_hd) from systembalance)
                                    " . $v_perioddate . "
                                    " . $v_wheresql   . "
                                    and h.chstatus       = 'T'
                                order by h.unique_id desc
                                ";

                                $RepInfoRS   = $MySQL->query($RepInfo_sql) or die(mysqli_error($MySQL));
                                $row_RepInfo = $RepInfoRS->fetch_assoc();

                                while ($row_RepInfo) {
                                    $rowno++;

                            ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="OraCellGroup2"><?php echo $row_RepInfo['id_llogfilial'] . "-" . $row_RepInfo['unique_id']; ?></td>
                                    <td class="OraCellGroup2"><?php echo substr($row_RepInfo['datarregjistrimit'], 8, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 5, 2) . "." . substr($row_RepInfo['datarregjistrimit'], 0, 4) . " " . substr($row_RepInfo['datarregjistrimit'], 11, 8); ?></td>
                                    <td class="OraCellGroup2"><?php echo $row_RepInfo['emri']; ?>&nbsp;<?php echo $row_RepInfo['mbiemri']; ?></td>
                                    <td class="OraCellGroup2"><?php echo $row_RepInfo['mon']; ?></td>
                                    <td class="OraCellGroup2"><?php echo $row_RepInfo['drcr']; ?></td>
                                    <td class="OraCellGroup2"><?php echo number_format($row_RepInfo['vleftapaguar'], 2, '.', ','); ?>&nbsp;&nbsp;</td>
                                    <td class="OraCellGroup2"><?php echo $row_RepInfo['perdoruesi']; ?></td>
                                    <td width="20">
                                        <?php if ($_SESSION['Usertype'] != 3) {  ?>
                                            <a title="Fshij Informacionin" href="JavaScript: do_delete('<?php echo $row_RepInfo['id']; ?>'); ">
                                                <i class="fas fa-times cursor-pointer"></i>
                                            </a> 
                                        <?php  }  ?>
                                    </td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="8" class="OraCellGroup2"> &nbsp;P&euml;rshkrimi: &nbsp;<?php echo $row_RepInfo['pershkrimi']; ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php $row_RepInfo = $RepInfoR->fetch_assoc();
                                };
                                mysqli_free_result($RepInfoRS);
                            ?>      
                                        
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>