<?php
include 'header.php';
// Fix session variable handling and prevent SQL injection
$user_info = isset($_SESSION['Username']) ? mysqli_real_escape_string($MySQL, $_SESSION['Username']) : '';

mysqli_select_db($MySQL, $database_MySQL);

// Use prepared statements to prevent SQL injection
$sql_exchange_info = "SELECT * FROM exchange_koke WHERE id = ?";
$stmt = mysqli_prepare($MySQL, $sql_exchange_info);
$id_value = $user_info . 'TRN' . $_GET['hid'];
mysqli_stmt_bind_param($stmt, 's', $id_value);
mysqli_stmt_execute($stmt);
$exchange_info = mysqli_stmt_get_result($stmt);
$row_exchange_info = mysqli_fetch_assoc($exchange_info);

// Use prepared statement for second query
$sql_exchange_det_info = "SELECT * FROM exchange_detaje WHERE id_exchangekoke = ?";
$stmt = mysqli_prepare($MySQL, $sql_exchange_det_info);
mysqli_stmt_bind_param($stmt, 's', $row_exchange_info['id']);
mysqli_stmt_execute($stmt);
$exchange_det_info = mysqli_stmt_get_result($stmt);
$row_exchange_det_info = mysqli_fetch_assoc($exchange_det_info);

?>
<div class="page-wrapper">
  <div class="container-fluid">
    <body onload="printForm()">
      <div id="printable-table">
        <TABLE cellSpacing=0 cellPadding=0 width="250" border=0>
          <TBODY>
          <TR>
            <TD height="15" colSpan=3 align="left" valign="middle">
              <DIV class=ctxheadingP><b>
                  <font size="2">&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CNAME']; ?>&nbsp;<br>&nbsp;&nbsp;&nbsp;NIPT: <?php echo $_SESSION['CNIPT']; ?><br>&nbsp;&nbsp;&nbsp;<?php echo $_SESSION['CADDR']; ?></font>
                </b></DIV>
            </TD>
          </TR>
        </TBODY>
      </TABLE>

      <TABLE cellSpacing=0 cellPadding=0 width="250" border=0>
        <TBODY>
          <TR>
            <TD align="center" colSpan=3>
              <DIV class=ctxheadingP>

                <table width="250" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                  <tr valign="middle">
                    <td width="5"></td>
                    <td width="*" colspan="5">Nr. Fat.:&nbsp;<b><?php echo $row_exchange_info['id_llogfilial'] . "-" . $row_exchange_info['unique_id']; ?></b></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                  <tr valign="middle">
                    <td width="5"></td>
                    <td width="*" colspan="5">Date:&nbsp;<b><?php echo substr($row_exchange_info['datarregjistrimit'], 8, 2) . "." . substr($row_exchange_info['datarregjistrimit'], 5, 2) . "." . substr($row_exchange_info['datarregjistrimit'], 0, 4) . "  " . substr($row_exchange_info['datarregjistrimit'], 11, 8); ?></b></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                  <tr>
                    <td width="5"></td>
                    <?php
                    $sql_subinfo = "select * from filiali where id = " . $row_exchange_info['id_llogfilial'];
                    $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                    $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                    $info = $row_rs_subinfo['filiali'];
                    mysqli_free_result($rs_subinfo);
                    ?>
                    <td width="*" colspan="5">Nga Llogaria:&nbsp;<b><?php echo $info; ?></b></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td width="5"></td>
                    <?php
                    $sql_subinfo = "select * from filiali where id = " . $row_exchange_info['id_klienti'];
                    $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                    $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                    $info = $row_rs_subinfo['filiali'];
                    mysqli_free_result($rs_subinfo);
                    ?>
                    <td width="*" colspan="5">Tek Llogaria:&nbsp;<b><?php echo $info; ?></b></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                  <tr>
                    <td width="5"></td>
                    <td colspan="5" height="1" bgcolor="000000"></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                  <?php
                  $sql_subinfo = "select * from monedha where id = " . $row_exchange_det_info['id_mondebituar'];
                  $rs_subinfo = mysqli_query($MySQL, $sql_subinfo) or die(mysqli_error($MySQL));
                  $row_rs_subinfo = mysqli_fetch_assoc($rs_subinfo);
                  $info = $row_rs_subinfo['monedha'];
                  mysqli_free_result($rs_subinfo);
                  ?>
                  <tr>
                    <td width="5"></td>
                    <td width="*" colspan="5" align="right">
                      <font size="2.5">Shuma e kaluar:&nbsp;<b><?php echo $row_exchange_info['vleftapaguar']; ?></b>&nbsp;<b><?php echo $info; ?></b></font>
                    </td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td width="5" height="5"></td>
                    <td width="20"></td>
                    <td width="35"></td>
                    <td width="5"></td>
                    <td width="55"></td>
                    <td width="55"></td>
                    <td width="5"></td>
                  </tr>
                  <tr>
                    <td colspan="7" height="5"></td>
                  </tr>
                </table>

              </DIV>
            </TD>
          </TR>
        </TBODY>
      </TABLE>

      <TABLE cellSpacing=0 cellPadding=0 width="250" border=0>
        <TBODY>
          <TR>
            <TD height="15" colSpan=3 align="left" valign="middle">
              <DIV class=ctxheadingP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Faleminderit / Thanks !&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="Kthehu pas..." class="link4" href="exchange_kalimlog.php"><img src="images/down_arrow.gif" border="0"></a></DIV>
            </TD>
          </TR>
        </TBODY>
      </TABLE>
    </div>
    </body>
    <?php include 'footer.php'; ?>

    <script>
      function printForm() {
        setTimeout(() => {
          
          var printContents = document.getElementById('printable-table').innerHTML;
          var originalContents = document.body.innerHTML;
          
          document.body.innerHTML = printContents;
          window.print();
          document.body.innerHTML = originalContents;
        }, 2000);
      }
    </script>