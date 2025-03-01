<?php
$secondsWait = 2;
header("Refresh:$secondsWait");
include 'header.php';
//echo date('d.m.Y H:i:s');



$GLOBALS['CNAME']   = "EXCHANGE";
$_SESSION['CNAME']  = "EXCHANGE";
$GLOBALS['CADDR']   = "Tiran&euml;";
$_SESSION['CADDR']  = "Tiran&euml;";
$GLOBALS['CNIPT']   = "A12345678B";
$_SESSION['CNIPT']  = "A12345678B";
$GLOBALS['CADMI']   = "Administrator";
$_SESSION['CADMI']  = "Administrator";
$_SESSION['CADMI']  = "Amdinistrator";
$GLOBALS['CMOBI']   = "+355 69 123 4567";
$_SESSION['CMOBI']  = "+355 69 123 4567";
$GLOBALS['DPPPP']   = "1000000";
$_SESSION['DPPPP']  = "1000000";

// Initialize SQL where clauses
$v_wheresql = "";
$v_wheresqls = "";
$v_wheresqle = "";

// Set conditions based on user type
if (($_SESSION['Usertype'] ?? '') === '2') {
  $v_wheresql = " where id = " . (int)$_SESSION['Userfilial'] . " ";
  $v_wheresqls = " where id <> " . (int)$_SESSION['Userfilial'] . " ";
  $v_wheresqle = " and id_llogfilial = " . (int)$_SESSION['Userfilial'] . " ";
}
if (($_SESSION['Usertype'] ?? '') === '3') {
  $v_wheresql = " where id = " . (int)$_SESSION['Userfilial'] . " ";
  $v_wheresqls = " where id <> " . (int)$_SESSION['Userfilial'] . " ";
  $v_wheresqle = " and id_llogfilial = " . (int)$_SESSION['Userfilial'] . " ";
}

?>

<div class="page-wrapper">
  <div class="container-fluid">
    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
      <TBODY>
        <TR>
          <TD align="left" colSpan=0 height="5">

            <table border="0" cellpadding="0" width="100%" cellspacing="0">
              <tr>
                <td height="5" colspan="6"></td>
              </tr>
              <tr>
                <td height="5" colspan="6">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="line">
                      <td height="0">
                        <DIV class=line></DIV>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

              <td colspan="12">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td style="text-align:center;"> <b> Data: <?php echo date('d.m.Y'); ?> </b></td>
                    <td style="text-align:center;"> <b> Ora: <?php echo date('H:i:s'); ?> </b></td>
                  </tr>
                </table>
              </td>
              <tr>
                <td height="5" colspan="6"></td>
              </tr>
              <?php
              $sql_info = "select k.* from kursi_koka as k where id = (select max(id) from kursi_koka where 1=1 " . $v_wheresqls . ") " . $v_wheresqls;
              $h_menu = mysqli_query($MySQL, $sql_info) or die(mysqli_error($MySQL));
              $row_h_menu = mysqli_fetch_assoc($h_menu);
              $totalRows_h_menu = mysqli_num_rows($h_menu);

              if ($row_h_menu) { ?>
                <tr>
                  <td height="1" colspan="6">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr class="line">
                        <td height="0">
                          <DIV class=line></DIV>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td height="5" colspan="6"></td>
                </tr>
                <tr bgcolor="#8181F7">
                  <td colspan="2" align="center">&nbsp; &nbsp; MONEDHA</td>
                  <td width="100"></td>
                  <td align="center">BLIHET</td>
                  <td width="100"></td>
                  <td align="center">SHITET</td>
                </tr>
                <tr>
                  <td height="5" colspan="6"></td>
                </tr>
                <?php
                mysqli_select_db($MySQL, $database_MySQL);
                $data_sql_info = "select kursi_detaje.*, monedha.monedha from kursi_detaje, monedha where master_id = " . $row_h_menu['id'] . " and kursi_detaje.monedha_id = monedha.id and monedha.id not in (8,9,10,11,12,13,20,21) order by kursi_detaje.monedha_id";
                $h_data = mysqli_query($MySQL, $data_sql_info) or die(mysqli_error($MySQL));
                $row_h_data = mysqli_fetch_assoc($h_data);

                $rownum = 0;

                while ($row_h_data) {

                  if ($rownum == 1) {
                    $v_bg = "CED8F6";
                    $rownum = 0;
                  } else {
                    $v_bg = "FFFFFF";
                    $rownum++;
                  }

                ?>
                  <tr>
                    <td height="1" colspan="6">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr class="line">
                          <td height="0">
                            <DIV class=line></DIV>
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr bgcolor="#<?php echo $v_bg; ?>">
                    <td width="100" align="right">&nbsp; &nbsp; <img src="images/flag/<?php echo $row_h_data['monedha']; ?>.png" width="50"></td>
                    <td align="left"> <b><?php echo $row_h_data['monedha']; ?></b></td>
                    <td></td>
                    <td align="center"><b><?php echo number_format($row_h_data['kursiblerje'], 2, '.', ','); ?></b></td>
                    <td></td>
                    <td align="center"><b><?php echo number_format($row_h_data['kursishitje'], 2, '.', ','); ?></b></td>
                  </tr>
                <?php $row_h_data = mysqli_fetch_assoc($h_data);
                };
                ?>
              <?php
              };
              mysqli_free_result($h_menu);
              ?>
              <tr>
                <td height="0" colspan="6">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr class="line">
                      <td height="0">
                        <DIV class=line></DIV>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td colspan="12">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td style="text-align:center;" width="50"><b> Mir&euml; se vini! &nbsp;|&nbsp; No Commission </b> </td>
                      <!--
              <td style="text-align:center;" width="50"><b> <i class="fa fa-calendar"></i> <?php echo date('d.m.Y'); ?> &nbsp; &nbsp; <i class="fa fa-clock-o"></i> <?php echo date('H:i:s'); ?></b></td>
            -->
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

          </TD>
        </TR>
      </TBODY>
    </TABLE>

<?php include 'footer.php'; ?>