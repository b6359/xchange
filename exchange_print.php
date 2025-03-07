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
  session_unset();
  session_destroy();

  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

?>
<?php require_once('ConMySQL.php'); ?>
<meta http-equiv="refresh" content="0; URL=exchange.php">
<?php

if (isset($_SESSION['uid'])) {
  $user_info = $_SESSION['Username'] ?? addslashes($_SESSION['Username']);

  mysqli_select_db($MySQL, $database_MySQL);

  $sql_exchange_info = "select * from exchange_koke where id = '" . $user_info . 'CHN' . $_GET['hid'] . "'";
  $exchange_info = mysqli_query($MySQL, $sql_exchange_info) or die(mysqli_error($MySQL));
  $row_exchange_info = mysqli_fetch_assoc($exchange_info);

  $sql_exchange_det_info = "select * from exchange_detaje where id_exchangekoke = '" . $row_exchange_info['id'] . "' ";
  $exchange_det_info = mysqli_query($MySQL, $sql_exchange_det_info) or die(mysqli_error($MySQL));
  $row_exchange_det_info = mysqli_fetch_assoc($exchange_det_info);
  $time = date("H:i");
?>



  <html>

  <head>

    <title><?php echo $_SESSION['CNAME']; ?> - Web Exchange System</title>


    <style type="text/css">
      body,
      td,
      th {
        font-size: 10px;
      }
    </style>

  </head>

  <body leftmargin=0 topmargin=0 marginheight="0" marginwidth="5" bgcolor=#FFFFFF vlink="#0000ff" link="#0000ff" onLoad="JavaScript: window.print();" onKeyDown=" window.location='exchange.php';">
    <TABLE cellSpacing=0 cellPadding=0 width="300" border=0>
      <TBODY>
        <TR>
          <TD align="center">
            <img src="./assets/images/Logo.png" title="GlobalTech.al" alt="GlobalTech.al" border="0" width="100">
          </TD>
        </TR>
      </TBODY>
    </TABLE>
    <TABLE cellSpacing=0 cellPadding=0 width="300" border=0>
      <TBODY style="font-weight: bolder;">
        <TR>
          <TD height="15" colSpan=3 align="left" valign="middle">
            <DIV class=ctxheadingP>
              <b>
                <font size="2">
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CNIPT']; ?><br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CADDR']; ?></strong>
                </font>
              </b>

            </DIV>
          </TD>
        </TR>
      </TBODY>
    </TABLE>

    <TABLE cellSpacing=0 cellPadding=0 width="300" border=0>
      <TBODY style="font-weight: bolder;">
        <td height="15" colspan="3" align="left" valign="middle" style="
    font-size: 20px;
    text-align: center;
    text-transform: capitalize;
">Mandat k&euml;mbimi valutor</td>
        <TR style="font-weight: bolder;">
          <TD align="center" colSpan=3>
            <DIV class=ctxheadingP>

              <table width="300" border="0" cellpadding="0" cellspacing="0" style="font-weight:bolder">
                <tr>
                  <td colspan="7" height="5"></td>
                </tr>
                <tr valign="middle">
                  <td></td>
                  <td width="*" colspan="5">Nr:&nbsp;<b><?php echo
                                                        /*
        $row_exchange_info['id_llogfilial']."/".
        */
                                                        $row_exchange_info['unique_id']; ?>
                    </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Data:&nbsp;<b><?php echo substr($row_exchange_info['datarregjistrimit'], 8, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 5, 2) . "/" . substr($row_exchange_info['datarregjistrimit'], 0, 4) . ""; ?></b></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="5"></td>
                </tr>
                <tr>
                  <td></td>
                  <?php
                  $sql_subinfo = "select * from klienti where id = " . $row_exchange_info['id_klienti'];
                  $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                  $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                  $info = $row_rs_subinfo['emriplote'];
                  mysqli_free_result($rs_subinfo);
                  ?>
                  <td width="*" colspan="5">Klienti:&nbsp;<b><?php echo $info; ?></b></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <?php
                  $sql_subinfo = "select * from monedha where id = " . $row_exchange_det_info['id_mondebituar'];
                  $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                  $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                  $info = $row_rs_subinfo['monedha'];
                  mysqli_free_result($rs_subinfo);
                  ?>
                  <td>Bler&euml;:</td>
                  <td colspan="4" align="right"><b>
                      <font size="2"><?php echo number_format($row_exchange_det_info['vleftadebituar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?>
                    </b></font>
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <?php
                $sql_subinfo = "select * from monedha where id = " . $row_exchange_info['id_monkreditim'];
                $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                $info = $row_rs_subinfo['monedha'];
                mysqli_free_result($rs_subinfo);
                ?>
                <tr>
                  <td></td>
                  <td width="*" rowspan="2" valign="middle">Kursi</td>
                  <td width="*" valign="middle"></td>
                  <?php if ($row_exchange_det_info['kursi'] > $row_exchange_det_info['kursi1']) {  ?>
                    <td width="*" colspan="4"><?php echo $row_exchange_det_info['kursi_txt']; ?>&nbsp;=&nbsp;1&nbsp;/&nbsp;<font size="2"><?php echo $row_exchange_det_info['kursi']; ?></font>
                    </td>
                  <?php  } else {  ?>
                    <td width="*" colspan="4"><?php echo $row_exchange_det_info['kursi1_txt']; ?>&nbsp;=&nbsp;1&nbsp;/&nbsp;<font size="2"><?php echo $row_exchange_det_info['kursi1']; ?></font>
                    </td>
                  <?php  }  ?>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="1"></td>
                </tr>
                <tr>
                  <td></td>
                  <td>Total:</td>
                  <td colspan="4" align="right"><b>
                      <font size="1.7"><?php echo number_format($row_exchange_det_info['vleftakredituar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?>
                    </b></font>
                  </td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <!--
        <td colspan="3">Komisioni:</td>
        <td colspan="2" align="right">&nbsp;<b><?php echo $row_exchange_info['P&euml;rqindjekomisioni']; ?></b>&nbsp;%&nbsp;&nbsp;=&nbsp;<b><?php echo $row_exchange_info['vleftakomisionit']; ?></b></td>
      -->
                  <td></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="5" height="1" bgcolor="000000"></td>
                  <td></td>
                </tr>
                <tr>
                  <td colspan="7" height="1"></td>
                </tr>
                <tr>
                  <td></td>
                  <td colspan="3">P&euml;r t'u paguar:</td>
                  <td colspan="2" align="right"><b>
                      <font size="2.5"><?php echo number_format($row_exchange_info['vleftapaguar'], 2, '.', ','); ?>&nbsp;<?php echo $info; ?></font>
                    </b></td>
                  <td></td>
                </tr>
                <tr>
                  <td width="5" height="1"></td>
                  <td width="10"></td>
                  <td width="5"></td>
                  <td width="30"></td>
                  <td width="70"></td>
                  <td width="55"></td>
                  <td width="5"></td>
                </tr>
              </table>

            </DIV>
          </TD>
        </TR>
      </TBODY>
    </TABLE>

    <TABLE cellSpacing=0 cellPadding=0 width="300" border=0>
      <TBODY style="font-weight: bolder;">
        <TR>
          <TD height="15" colSpan=3 align="left" valign="middle">
            <DIV class=ctxheadingP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ju Faleminderit! - Thank you!&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="Kthehu pas..." class="link4" href="exchange.php"><img src="images/down_arrow.gif" border="0"></a></DIV>
          </TD>
        </TR>
      </TBODY>
    </TABLE>

    <br>
    <br>
    <br>


  </body>

  </html>
<?php
}

?>
